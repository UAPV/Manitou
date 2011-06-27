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
    $tagRegex = '/\n'.$startTag.'.*'.$endTag.'\n/s';
    $serialId = sfConfig::get('sf_manitou_dns_serial_identifier');
    $serialRegex = '/.*'.$serialId.'.*/';
    $serial = "\t\t".time().' ; '.$serialId.' '.date('c');

    foreach ($this->conf as $filename => $entries)
    {
      $content = file_get_contents($path.'/'.$filename);
      $content = preg_replace ($tagRegex, '', $content);
      $content = preg_replace ($serialRegex, $serial, $content);

      $newContent = "\n$startTag\n;===================\n";
      foreach ($entries as $entry)
      {
        // Si l'entrée existe déjà on la commente
        if (preg_match('/^'.$entry['hostname'].'\s+IN\s+A/', $content) !== 0)
          $newContent .= '; MANITOU_ERROR hostname already exists : ';
        if (preg_match('/IN\s+A\s+'.$entry['ip'].'/', $content) !== 0)
          $newContent .= '; MANITOU_ERROR ip already exists : ';

        $newContent .= str_pad ($entry['hostname'], 24).'IN      A       '.$entry['ip']."\n";
      }
      $newContent .= $endTag."\n";


      file_put_contents ($path.'/'.$filename, $content.$newContent);
    }

    foreach ($this->reverseConf as $filename => $entries)
    {
      $content = file_get_contents($path.'/'.$filename);
      $content = preg_replace ($tagRegex, '', $content);
      $content = preg_replace ($serialRegex, $serial, $content);

      $newContent = "\n$startTag\n;===================\n";
      foreach ($entries as $entry)
      {
        // Si l'entrée existe déjà on la commente
        if (preg_match('/^'.$entry['ip'].'\s+IN\s+PTR/', $content) !== 0)
          $newContent .= '; MANITOU_ERROR ip already exists : ';
        if (preg_match('/IN\s+PTR\s+'.$entry['fqdn'].'/', $content) !== 0)
          $newContent .= '; MANITOU_ERROR fqdn already exists : ';

        $newContent .= str_pad ($entry['ip'], 16).'IN PTR '.$entry['fqdn'].".\n";
      }
      $newContent .= $endTag."\n";
      file_put_contents ($path.'/'.$filename, $content.$newContent);
    }
  }
}
