<?php



/**
 * Skeleton subclass for performing query and update operations on the 'command' table.
 *
 * 
 *
 * This class was autogenerated by Propel 1.6.1-dev on:
 *
 * Wed May 25 23:31:12 2011
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.lib.model
 */
class CommandPeer extends BaseCommandPeer {

  /**
   * Met à jour les fichiers de conf du DHCP et exécute la commande pour
   * mettre à jour le dépôt svn.
   *
   * @static
   * @return void
 */
  public static function runDhcpdUpdate ($comm)
  {
    if (!sfContext::hasInstance())
      return;

    HostQuery::create()->find(); // Juste pour remplir le cache de propel
    $subnets  = SubnetQuery::create ()->find ();
    $confPath = sfConfig::get('sf_manitou_dhcpd_conf_path');
		$tabDrbl = array();

    sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
    sfConfig::set('sf_escaping_strategy', false);
    foreach ($subnets as $subnet)
    {
      $filename = $confPath.'/'.$subnet->getName().'.conf';
      file_put_contents ($filename, get_partial ('dhcpd/subnet.conf', array ('subnet' => $subnet)));

			//On récupère le nom des serveurs associé au subnet
			$hostnameDrbl = $subnet->getImageServer()->getHostname();
			$tabHost = explode('.', $hostnameDrbl);

			if(!in_array($tabHost[0], $tabDrbl))
			{
				$tabDrbl[] = $tabHost[0];
			}
    }

    CommandPeer::getDhcpdUpdateCommand($tabDrbl,$comm)->exec ();
  }

  public static function getDhcpdUpdateCommand ($tabDrbl = null, $comm)
  {
		$labelDrbl = implode(' , ',$tabDrbl);
    $command = new Command ();
    $command->setCommand (sfConfig::get('sf_manitou_dhcp_update_command'));
    $command->setArgument ('conf_path', sfConfig::get('sf_manitou_dhcpd_conf_path'));
    $command->setArgument ('user_name', sfContext::getInstance()->getUser()->getProfileVar('displayname') );

		if(isset($comm))
		{
			if(count(explode("'",$comm)) > 1)
				$comm = implode("\'", $comm);
		}

		$command->setArgument('comments', $comm);
		$command->setLabel ($labelDrbl.' - Regénération de la conf DHCP');

   return $command;
  }

  public static function getCreateImageCommand (Image $image)
  {
    $command = new Command();
    $command->setCommand(sfConfig::get('sf_manitou_create_image_command'));

    $host = $image->getHost();
    $imageServer = $host->getSubnet()->getImageServer();

    $command->setArgument('host_ip',        $host->getIpAddress());
    $command->setArgument('host_mac',       $host->getMacAddress());
    $command->setArgument('interface',      $imageServer->getIface());
    $command->setArgument('image_server',   $imageServer->getHostname());
    $command->setArgument('image_filename', $image->getFilename());
    $command->setLabel ('Creation de l\'image "'.$image->getFilename().'" à partir de la machine '.$image->getHost());

    return $command;
  }

  public static function getRestoreImageCommand ()
  {
    $command = new Command();
    $command->setCommand(sfConfig::get('sf_manitou_restore_image_command'));
		$command->setLabel('Restauration d\'image');

    return $command;
  }

  public static function runDnsPreUpdate ()
  {
    $command = new Command ();
    $command->setCommand (sfConfig::get('sf_manitou_dns_pre_update_command'));
    $command->setArgument ('conf_path', sfConfig::get('sf_manitou_dns_conf_path'));
    $command->setLabel ('Mise à jour des fichiers de conf du DNS');

    return $command->exec (false); // obligé d'attendre le retour de la commande pour que le svn commit
                                   // qui va suivre fonctionne correctement
  }

  /**
   * Lance la mise à jour le DNS.
   * Commande à lancer lorsqu'une(des) machine(s) à été ajoutée ou modifiée.
   *
   *
   * @static
   * @param  $hosts     Tableau ou Object de la classe Host
   * @return Command
   */
  public static function runDnsUpdate ($host = null, $otherFiles = null, $comment = null, $host = null)
  {
    self::runDnsPreUpdate();
    $path = sfConfig::get('sf_manitou_dns_conf_path');

    //si le filesHost n'est pas nul et qu'un tableau de hosts est passé en parametre (plusieurs hosts touchées par une même action)
    if($host != null && is_array($host))
    {
        $arrayFilesToChange = array();
				$tabDrbl = array();

        foreach($host as $h)
        {
            $filenameReverse = 'db.'.$h->getDomainName ();
            $ipBase = $h->getSubnet ()->getIpAddress();
            $ipBase = substr ($ipBase, 0, strpos ($ipBase, '.0'));
            $filenameConf = 'db.'.$ipBase;
            $arrayFilesToChange[] = $filenameReverse;
            $arrayFilesToChange[] = $filenameConf;

						//On récupère le nom des serveurs associé au subnet
						$hostnameDrbl = $h->getSubnet ()->getImageServer()->getHostname();
						$tabHost = explode('.', $hostnameDrbl);

						if(!in_array($tabHost[0], $tabDrbl))
							$tabDrbl[] = $tabHost[0];
				}

				foreach($otherFiles as $file)
				{
					$filenameReverse = 'db.'.$file[0];
					$filenameConf = 'db.'.substr ($file[1], 0, strpos ($file[1], '.0'));
					$arrayFilesToChange[] = $filenameReverse;
					$arrayFilesToChange[] = $filenameConf;
				}
			$labelDrbl = implode(' , ',$tabDrbl);
    }
    //si une string contenant les filenames a changer est passée en paramètre
    elseif($host != null && !is_array($host))
        $arrayFilesToChange = explode(' ',$host);

    //on récupère les hosts modifiés
    $hosts = HostQuery::create()->withColumn('INET_ATON(Host.IpAddress)','a')->orderBy('a','asc')->find ();

		//Si $host existe, on le rajoute car c'est peut-être le dernier host de manitou dans ce fichier
		if($host != null)
		{
			$filenameReverse = 'db.'.$host->getDomainName ();
			$ipBase = $host->getSubnet ()->getIpAddress();
			$ipBase = substr ($ipBase, 0, strpos ($ipBase, '.0'));
			$filename = 'db.'.$ipBase;
			$arrayFilesToChange[] = $filenameReverse;
			$arrayFilesToChange[] = $filename;
		}

    $dnsConf = new Dns ();
    $dnsConf->setHosts ($hosts, $host);

		if($arrayFilesToChange != null)
		{
			array_unique($arrayFilesToChange);
    	$dnsConf->apply ($path, $arrayFilesToChange);
		}
		else
			$dnsConf->apply ($path);

die;
    $command = new Command ();
    $command->setCommand (sfConfig::get('sf_manitou_dns_update_command'));
    $command->setArgument ('conf_path', $path);
    $command->setArgument ('user_name', sfContext::getInstance()->getUser()->getProfileVar('displayname') );

		if(isset($comment))
		{
			if(count(explode("'",$comment)) > 1)
				$comm = implode("\'", $comment);
		}

		$command->setArgument('comments', $comment);
    $command->setLabel ($labelDrbl.' - Mise à jour des entrées du DNS');

    return $command->exec ();
  }

  public static function stopImageServer ()
  {
    $command = new Command ();
    $command->setCommand ($script);
    $command->setLabel ('Arrêt du serveur d\'images');

    return $command->exec ();
  }

 /**
  *
  */
  public static function runPxeFilesDnsUpdate($host)
  {
    $path = sfConfig::get('sf_manitou_dns_conf_path');
		$hostnameDrbl = $host->getSubnet()->getImageServer()->getHostname();
		$labelDrbl = explode('.',$hostnameDrbl);

    $command = new Command ();
    $command->setCommand (sfConfig::get('sf_manitou_dns_update_specific_files_command'));
    $command->setArgument ('conf_path', $path);
    $command->setArgument ('list_files', $host);
    $command->setLabel ($labelDrbl[0].' - Modification fichier PXE- Mise à jour des entrées du DNS');

    return $command->exec ();
  }

 /* public static function getSvnStatus()
  {
      echo "on arrive ici";die;
    $path = sfConfig::get('sf_manitou_dns_conf_path');

    $command = new Command ();
    $command->setCommand (sfConfig::get('sf_manitou_svn_status'));
    $command->setArgument ('conf_path', $path);
    $command->setLabel ('Statut SVN');

    return $command->exec ();
  }*/

} // CommandPeer
