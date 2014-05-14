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

  public function addHost ($host, $remove = false)
  {
    $filename = 'db.'.$host->getDomainName ();
    if(!$remove)
    {
        if (! array_key_exists($filename, $this->conf))
          $this->conf [$filename] = array();

        $this->conf [$filename][] = array (
          'ip'        => $host->getIpAddress (),
          'hostname'  => $host->getHostname (),
        );
    }

    $ipBase = $host->getSubnet ()->getIpAddress();
    $ipBase = substr ($ipBase, 0, strpos ($ipBase, '.0'));
    $filename = 'db.'.$ipBase;
    if (! array_key_exists($filename, $this->reverseConf))
      $this->reverseConf [$filename] = array();

    if(!$remove)
	{
		$ip = substr ($host->getIpAddress (), strlen($ipBase) + 1);
		$this->reverseConf [$filename][] = array (
			'ip'        => implode ('.', array_reverse( explode ('.', $ip))),
			'fqdn'      => $host->getFqdn (),
		);
	}
	else
		$this->reverseConf [$filename][] = array();
  }

 /**
   *  Mise à jour des fichiers de conf en modifiant le contenu des balise MANITOU_CONF_[START|END]
   *
   * @param  $path
   * @param $filesToChange fichiers qu'il faut modifier et commiter si un seul host est touché
   * @return void
   */
  public function apply ($path, $filesToChange = null)
  {
      $startTag = '; MANITOU_CONF_BEGIN';
      $endTag   = '; MANITOU_CONF_END';
      $tagRegex = '/\n*'.$startTag.'.*'.$endTag.'\n*/s';

      foreach ($this->reverseConf as $filename => $entries)
      {
          //si il s'agit du fichier à modifier
          if(in_array($filename, $filesToChange))
          {
              $contentTest = file_get_contents($path.$filename);
          $content = preg_replace ($tagRegex, '', $contentTest);
          $content = $this->updateSerial($content);
          $content = explode("\n", $content);

          //on récupére tout ce qu'il y a après startTag.
          $lengh = count($content);
          $comment = array();
          $arrayDns = array();

          $first = true;
          $header = array();
          for($i=0; $i < $lengh; $i++)
          {
              //on garde le header tant qu'on n'a pas trouvé le premier host
              if($first)
                  $header[] = $content[$i];

              //on regarde si la ligne en cours de lecture est un nouvel host
              $regex = '/\s+IN\s+PTR\s/i';
              $regexCom = '/^;+\s/i';

              if(preg_match('/;\s+UPDATED\s+BY\s+MANITOU\s+/i', $content[$i]) === 1)
              {
                  $i = $i+1;
              }
              elseif(preg_match($regex,$content[$i]) === 1)
              {
                //on récupère le numéro pour le mettre en clé dans le tableau final
  			    $tmp = preg_split("/[\s]+/", $content[$i]);
				$keyArray = str_replace(';','',$tmp[0]);
                $arrayDns["$keyArray"] = array($comment,$content[$i]);
                unset($comment);
                $comment = array();
                $first = false;
              }
              //on est tjs dans le header mais on croise le premier commentaire
              elseif($first)
              {
                if(preg_match($regexCom,$content[$i]) === 1)
                {
                    $first = false;
                    unset($header[$i]);
                    $comment[] = $content[$i];
                }
              }
              //sinon si elle est marquée "DELETION MARKED", on la supprime
              elseif(preg_match('/;\s+\[MANITOU\]\s+MARKED\s+FOR\s+DELETION/i', $content[$i]) === 0)
              {
                  //on sauvergarde le commentaire en cours pour l'assigner à l'host suivant
                  if(isset($content[$i]) && $content[$i] != '')
                      $comment[] = $content[$i];
              }
          }

          //on rajoute les fichiers de Manitou puis on trie le tableau
          foreach ($entries as $cle => $entry)
          {
							if(count($entry) > 0)
							{
								foreach($arrayDns as $key => $line)
								{

								// Si une entrée STRICTEMENT identique existe on écrit la nouvelle et on envoie un mail pour donner le nom de la machine remplacée
								$regex = '/^'.preg_quote($entry['ip']).'\s+IN\s+PTR\s+'.preg_quote($entry['fqdn']).'\.\s*$/m';
								if (preg_match($regex, $line[1], $matches) === 1)
								{
										//on récupère l'entrée dans le tableau et on la supprime du tableau d'origine (arrayDns)
										$key = str_pad ($entry['ip'], 16);
										unset($arrayDns["$key"]);
								}
								//sinon si l'ip existe deja
								else if (preg_match('/^'.preg_quote($entry['ip']).'\s+IN\s+PTR+\s*/', $line[1], $matches)  === 1 )
								{
										//on supprime l'entrée du tableau
										unset($arrayDns["$key"]);

										//on envoie un mail
										$host = $entry['fqdn'];
										$message = sfContext::getInstance()->getMailer()->compose(
												array('manitou@univ-avignon.fr' => 'Manitou'),
												'fanny.marcel@univ-avignon.fr',
												//'root-admin@listes.univ-avignon.fr',
												'Modification DNS',
												<<<EOF
	Manitou a écrasé une ancienne adresse ip pour le fichier <b>$filename</b>.

	Ancienne ip : $lastIp
	Nouvelle ip :   $key
	Ancien host :   $host
	Nouvel host :   $host


	Ce message a été envoyé automatiquement. Merci de ne pas y répondre.
EOF
										);

									//sfContext::getInstance()->getMailer()->send($message);
								}
								else if (preg_match('/^[^;].*IN\s+PTR\s+'.preg_quote($entry['fqdn']).'.*$/m', $line[1]) > 0)
								{
										//on supprime l'entrée du tableau
										unset($arrayDns["$key"]);

										//on envoie un mail
										$newFqdn = $entry['fqdn'];
										$ip = $entry['ip'];
										$message = sfContext::getInstance()->getMailer()->compose(
												array('manitou@univ-avignon.fr' => 'Manitou'),
												'fanny.marcel@univ-avignon.fr',//'root-admin@listes.univ-avignon.fr',
												'Modification DNS',
												<<<EOF
	Manitou a écrasé une ligne pour le fichier $filename.

	Ancienne ip :   $ip
	Nouvelle ip :   $ip
	Nouvel host :   $newFqdn


	Ce message a été envoyé automatiquement. Merci de ne pas y répondre.
EOF
										);

									//sfContext::getInstance()->getMailer()->send($message);
								}
							}

                                //On récupère le commentaire si il existe
                                $tmp = explode('.',$filename);
                                $tmpIp = explode('.',$entry['ip']);

                                if(count($tmp) == 3)
                                    $ip = $tmp[1].'.'.$tmp[2].'.'.$tmpIp[1].'.'.$tmpIp[0];
                                else
                                    $ip = $tmp[1].'.'.$tmp[2].'.'.$tmp[3].'.'.$tmpIp[0];

                                $obj = HostQuery::create()->findOneByIpAddress($ip);
                                $com = "; UPDATED BY MANITOU --> DON'T TOUCH";

                                if(count($obj) > 0)
                                {
                                    $commentObj = str_replace("\n",' ', $obj->getComment());
                                    $newContent = $entry['ip']."\t \t".'IN'."\t".'PTR'."\t".$entry['fqdn'].".".$com." ; ".$commentObj;
                                }
                                else
                                    $newContent = $entry['ip']."\t \t".'IN'."\t".'PTR'."\t".$entry['fqdn'].".".$com;

                                $key = $entry['ip'];
                                $arrayDns["$key"] = array($newContent);
         	 }
		    }


          if(!function_exists('compare'))
          {
             function compare($a,$b)
             {
              $dataA = explode('.',$a);
              $dataB = explode('.',$b);

              if(count($dataA) > 1 && count($dataB) > 1)
              {
                  if($dataA[1] == $dataB[1])
                  {
                      if($dataA[0] >= $dataB[0])
                      {
                          return 1;
                      }
                      else
                      {
                          return -1;
                      }
                  }
                  elseif($dataA[1] > $dataB[1])
                  {
                      return 1;
                  }
                  else
                  {
                      return -1;
                  }
              }
              else
              {
                  if($dataA[0] >= $dataB[0])
                      return 1;
                  else
                      return -1;
              }
            }
          }

          uksort($arrayDns, 'compare');

          $data = array();
          //on écrit dans le fichier les lignes
          foreach($arrayDns as $key => $ligne)
          {
              if( $key != "")
              {
                  foreach($ligne as $nvLigne)
                  {
                      if(is_array($nvLigne))
                      {
                          foreach($nvLigne as $comment)
                              $data[] = $comment;
                      }
                      else
                          $data[] = $nvLigne;
                  }
              }
          }

          //on récupère le tableau de content en string puis on l'écrit dans le fichier
          $nvContent = implode("\n",$data);
          $contentHeader = implode("\n", $header);

          file_put_contents ($path.$filename, $contentHeader."\n".$nvContent."\n");
        }
      }

      foreach ($this->conf as $filename => $entries)
      {
          echo "<pre>";var_dump($this->conf);echo "</pre>";die;
         if(in_array($filename, $filesToChange))
         {
         $contentTest = file_get_contents($path.'/'.$filename);
         $content = preg_replace ($tagRegex, '', $contentTest);
         $content = $this->updateSerial($content);
         $content = explode("\n", $content);

         //on récupére tout ce qu'il y a après startTag.
         $lengh = count($content);
         $comment = array();
         $arrayDns = array();
         $first = true;
         $header = array();
         $cpt = 0;


         for($i=0; $i < $lengh; $i++)
         {
           //on garde le header tant qu'on n'a pas trouvé le premier host
           if($first)
             $header[] = $content[$i];

           //on regarde si la ligne en cours de lecture est un nouvel host
           $regex = '/^[A-Za-z0-9].*\s+IN\s+A/';
           $regexCom = '/^;+\s/';

           if(preg_match('/^;;;;;/', $content[$i]) === 1)
               $i = $i+1;
           elseif($content[$i] == "")
               $i = $i+1;
           elseif(!$first && preg_match('/;\s+UPDATED\s+BY\s+MANITOU\s+/', $content[$i]) === 0)
           {
              //on récupère l'adresse ip pour le mettre en clé dans le tableau final
              $hostname = preg_replace('/\s+IN\s+A\s+.*/','',$content[$i]);
              $hostname = str_replace(' ','',$hostname);

              if(!array_key_exists($hostname, $arrayDns))
              {
                $arrayDns["$hostname"] = array($comment,$content[$i]);
              }
              else
              {
                  $arrayDns["$hostname-$cpt"] = array($comment,$content[$i]);
                  $cpt++;
              }

              unset($comment);
              $comment = array();
              $first = false;
           }
           //on est tjs dans le header mais on croise le premier commentaire
           elseif($first && preg_match('/;\s+UPDATED\s+BY\s+MANITOU\s+/', $content[$i]) === 0)
           {
               if(preg_match($regexCom,$content[$i]) === 1)
               {
                   $first = false;
                   unset($header[$i]);
                   $comment[] = $content[$i];
               }
           }
           elseif(preg_match('/;\s+UPDATED\s+BY\s+MANITOU\s+/', $content[$i]) === 1)
           {
              $i = $i+1;
           }
           //sinon si elle est marquée "DELETION MARKED", on la supprime
           elseif(preg_match('/;\s+\[MANITOU\]\s+MARKED\s+FOR\s+DELETION/', $content[$i]) === 0)
           {
             //on sauvergarde le commentaire en cours pour l'assigner à l'host suivant
             if(isset($content[$i]))
               $comment[] = $content[$i];
           }
       }

          foreach ($entries as $entry)
          {
             foreach($arrayDns as $key => $line)
             {
                 $ipClean = trim($entry['ip']);

                 // Si une entrée STRICTEMENT identique existe on écrit la nouvelle et on envoie un mail pour donner le nom de la machine remplacée
                 $regex = '/^[^;]*'.preg_quote($entry['hostname']).'\s+IN\s+A\s+'.preg_quote($entry['ip']).'\s*$/';
                 if (preg_match($regex, $line[1], $matches) === 1)
                 {
                     //on récupère l'entrée dans le tableau et on la supprime du tableau d'origine (arrayDns)
                     $key = $entry['hostname'];
                     unset($arrayDns["$key"]);
                 }
                 //sinon si l'ip existe deja
                 else if (preg_match('/^[^;]*IN\s*A\s*'.preg_quote($ipClean).'\s*$/', $line[1], $matches)  === 1 )
                 {
                     //on supprime l'entrée du tableau, on recherche l'hote correspondant a l'ip
                     $oldHost = $key;
                     unset($arrayDns["$key"]);

                     //on envoie un mail
                     $host = $entry['hostname'];
                     $lastIp = $entry['ip'];
                     $message = sfContext::getInstance()->getMailer()->compose(
                         array('manitou@univ-avignon.fr' => 'Manitou'),
                         'fanny.marcel@univ-avignon.fr',//'root-admin@listes.univ-avignon.fr',
                         'Modification DNS',
                         <<<EOF
                                              Manitou a écrasé un hote pour l'adresse ip suivante pour le fichier <b>$filename</b>.

Ip concernée : $lastIp
Ancien host : $oldHost
Nouvel host : $host


Ce message a été envoyé automatiquement. Merci de ne pas y répondre.
EOF
                     );

                     //sfContext::getInstance()->getMailer()->send($message);
                 }
                 //si le hostname existe deja
                 else if (preg_match('/^[^;]*'.preg_quote($entry['hostname']).'\s*+IN\s*+A/', $line[1]) > 0)
                 {
                     //$key = $entry['hostname'];

                     //on récupère l'ancienne ip
                     $ligne = $arrayDns["$key"][1];
                     $ipN = preg_replace('/.*\s+IN\s+A\s/','',$ligne);
                     $ipN = str_replace(' ','',$ipN);

                     //on supprime l'entrée du tableau
                     unset($arrayDns["$key"]);

                     //on envoie un mail
                     $newHostname = $entry['hostname'];
                     $ip = $entry['ip'];
                     $message = sfContext::getInstance()->getMailer()->compose(
                         array('manitou@univ-avignon.fr' => 'Manitou'),
                         'fanny.marcel@univ-avignon.fr',//'root-admin@listes.univ-avignon.fr',
                         'Modification DNS',
                         <<<EOF
                                              Manitou a écrasé une ligne pour le fichier <b>$filename</b>.

Ancienne ip :   $ipN
Nouvelle ip :   $ip
Hostname concerné : $key


Ce message a été envoyé automatiquement. Merci de ne pas y répondre.
EOF
                     );

                     //sfContext::getInstance()->getMailer()->send($message);
                 }
             }

             $key = str_pad($entry['hostname'], 24);
             $com = "; UPDATED BY MANITOU --> DON'T TOUCH";
             $key = str_replace(' ','',$key);

		     $newContent = $entry['hostname']."\t\t".'IN'."\t".'A'."\t".$entry['ip']."  $com";
             $arrayDns[$key] = array($newContent);
         }

         $data = array();

         //on écrit dans le fichier les lignes
         $separation = false;
         foreach($arrayDns as $key=>$ligne)
         {
           if($key != "")
           {
             foreach($ligne as $nvLigne)
             {
               if(is_array($nvLigne))
               {
                  foreach($nvLigne as $comment)
                      $data[] = $comment;
               }
               else
               {
                   if(!$separation)
                   {
                       if(preg_match('/;\s+UPDATED\s+BY\s+MANITOU\s+/', $nvLigne) === 1)
                       {
                         $separation = true;
                         $separateur = ";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;\n;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;";
                         $data[] = "\n".$separateur."\n\n".$nvLigne;
                       }
                       else
                         $data[] = $nvLigne;
                   }
                   else
                       $data[] = $nvLigne;
               }

              }
            }
          }

          //on récupère le tableau de content en string puis on l'écrit dans le fichier
          $nvContent = implode("\n",$data);
          $contentHeader = implode("\n", $header);
          $filePath = $path.$filename;

          file_put_contents ($filePath, $contentHeader."\n".$nvContent."\n");
        }
      }
  }

      /**
   * Ancienne mise à jour les fichiers de conf en modifiant le contenu des balise MANITOU_CONF_[START|END]
   *
   * @param  $path
   * @return void
   */
  public function apply2 ($path)
  {

    $startTag = "; MANITOU_CONF_BEGIN";
    $endTag   = "; MANITOU_CONF_END";
    $tagRegex = "/\n*".$startTag.".*".$endTag."\n*/";

    foreach ($this->conf as $filename => $entries)
    {
      $content = file_get_contents($path.'/'.$filename);
      $content = preg_replace ($tagRegex, '', $content);
      $content = $this->updateSerial($content);

      $newContent = "\n\n$startTag\n;===================\n";
      foreach ($entries as $entry)
      {
        // Si une entrée STRICTEMENT identique existe on commente l'originale
        $regex = '/^'.preg_quote($entry['hostname']).'\s+IN\s+A\s+'.preg_quote($entry['ip']).'\s*$/m';
        if (preg_match($regex, $content, $matches) === 1)
          $content = preg_replace ($regex, '; [MANITOU] MARKED FOR DELETION : '.$matches[0], $content);
        // Sinon manitou perd la main et on commente l'ajout prévu
        else if (preg_match('/^'.$entry['hostname'].'\s+IN\s+A/m', $content) > 0)
          $newContent .= '; MANITOU_ERROR hostname already exists : ';
        else if (preg_match('/^[^;].*IN\s+A\s+'.preg_quote($entry['ip']).'\s*$/m', $content) > 0)
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
        // Si une entrée STRICTEMENT identique existe on commente l'originale
        $regex = '/^'.preg_quote($entry['ip']).'\s+IN\s+PTR\s+'.preg_quote($entry['fqdn']).'\.\s*$/m';
        if (preg_match($regex, $content, $matches) === 1)
          $content = preg_replace ($regex, '; [MANITOU] MARKED FOR DELETION : '.$matches[0], $content);
        // Sinon manitou perd la main et on commente l'ajout prévu
        else if (preg_match('/^'.preg_quote($entry['ip']).'\s+IN\s+PTR/m', $content) > 0)
          $newContent .= '; MANITOU_ERROR ip already exists : ';
        else if (preg_match('/^[^;].*IN\s+PTR\s+'.preg_quote($entry['fqdn']).'\s*$/m', $content) > 0)
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

    $serial = "\n\t\t".$currentDate.$counter."\t; ".$serialId;//.' '.date('c');

    return preg_replace ($serialRegex, $serial, $conf);
  }

}
