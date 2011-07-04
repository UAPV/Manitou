<?php

class Dns
{
  protected $conf         = array ();
  protected $reverseConf  = array ();

  public function setHosts ($hosts)
  {
    foreach ($hosts as $host)
      $this->addHost($host);
  }

  public function addHost ($host)
  {
    $filename = 'db.'.$host->getDomainName ();
    if (! array_key_exists($filename, $this->conf))
      $this->conf [$filename] = array();

    $this->conf [$filename][] = array (
      'ip'        => $host->getIpAddress (),
      'hostname'  => $host->getHostname (),
    );


    $ipBase = $host->getSubnet ()->getIpAddress();
    $ipBase = substr ($ipBase, 0, strpos ($ipBase, '.0'));
    $filename = 'db.'.$ipBase;
    if (! array_key_exists($filename, $this->reverseConf))
      $this->reverseConf [$filename] = array();

    $ip = substr ($host->getIpAddress (), strlen($ipBase) + 1);
    $this->reverseConf [$filename][] = array (
      'ip'        => implode ('.', array_reverse( explode ('.', $ip))),
      'fqdn'      => $host->getFqdn (),
    );
  }

  /**
   * Met à jour les fichiers de conf en modifiant le contenu des balise MANITOU_CONF_[START|END]
   *
   * @param  $path
   * @return void
   */
  public function apply ($path)
  {
    $startTag = '; MANITOU_CONF_BEGIN';
    $endTag   = '; MANITOU_CONF_END';
    $tagRegex = '/\n*'.$startTag.'.*'.$endTag.'\n*/s';

    foreach ($this->conf as $filename => $entries)
    {
      $content = file_get_contents($path.'/'.$filename);
      $content = preg_replace ($tagRegex, '', $content);
      $content = $this->updateSerial($content);

      $newContent = "\n\n$startTag\n;===================\n";
      foreach ($entries as $entry)
      {
        // Si l'entrée existe déjà on la commente
        if (preg_match('/^'.$entry['hostname'].'\s+IN\s+A/', $content) !== 0)
          $newContent .= '; MANITOU_ERROR hostname already exists : ';
        if (preg_match('/IN\s+A\s+'.str_replace('.', '\.', $entry['ip']).'/', $content) !== 0)
          $newContent .= '; MANITOU_ERROR ip already exists : ';

        $newContent .= str_pad ($entry['hostname'], 24).'IN      A       '.$entry['ip']."\n";
      }
      $newContent .= $endTag."\n\n";


      file_put_contents ($path.'/'.$filename, $content.$newContent);
    }

    foreach ($this->reverseConf as $filename => $entries)
    {
      $content = file_get_contents($path.'/'.$filename);
      $content = preg_replace ($tagRegex, '', $content);
      $content = $this->updateSerial($content);

      $newContent = "\n\n$startTag\n;===================\n";
      foreach ($entries as $entry)
      {
        // Si l'entrée existe déjà on la commente
        if (preg_match('/^'.str_replace('.', '\.', $entry['ip']).'\s+IN\s+PTR/', $content) !== 0)
          $newContent .= '; MANITOU_ERROR ip already exists : ';
        if (preg_match('/IN\s+PTR\s+'.$entry['fqdn'].'/', $content) !== 0)
          $newContent .= '; MANITOU_ERROR fqdn already exists : ';

        $newContent .= str_pad ($entry['ip'], 16).'IN PTR '.$entry['fqdn'].".\n";
      }
      $newContent .= $endTag."\n\n";
      file_put_contents ($path.'/'.$filename, $content.$newContent);
    }
  }

  /**
   * Met à jour le serial dans une conf de bind
   *
   * @param string $conf
   * @return string       La conf avec le serial màj
   */
  function updateSerial ($conf)
  {
    $currentDate = date('Ymd');
    $counter = '00';

    $serialId = sfConfig::get('sf_manitou_dns_serial_identifier');
    $serialRegex = '/\s*([0-9]{8})([0-9]{2})\s*;.*'.$serialId.'.*/';

    if (preg_match($serialRegex, $conf, $matches) < 1)
    {
      if (sfContext::hasInstance ())
        sfContext::getInstance()->getLogger()->log ('DNS Serial not found', sfLogger::CRIT);

      return $conf;
    }

    if ($matches[1] == $currentDate)
      $counter = str_pad(((int) $matches[2]) + 1, 2, '0', STR_PAD_LEFT);

    $serial = "\n\t\t".$currentDate.$counter.' ; '.$serialId.' '.date('c');

    return preg_replace ($serialRegex, $serial, $conf);
  }
}
