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
  public static function runDhcpdUpdate ()
  {
    if (!sfContext::hasInstance())
      return;

    HostQuery::create()->find(); // Juste pour remplir le cache de propel
    $subnets  = SubnetQuery::create ()->find ();
    $confPath = sfConfig::get('sf_manitou_dhcpd_conf_path');

    sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
    sfConfig::set('sf_escaping_strategy', false);
    foreach ($subnets as $subnet)
    {
      $filename = $confPath.'/'.$subnet->getName().'.conf';
      file_put_contents ($filename, get_partial ('dhcpd/subnet.conf', array ('subnet' => $subnet)));
    }

  //  CommandPeer::getDhcpdUpdateCommand()->exec ();
  }

  public static function getDhcpdUpdateCommand ()
  {
    $command = new Command ();
    $command->setCommand (sfConfig::get('sf_manitou_dhcp_update_command'));
    $command->setArgument ('conf_path', sfConfig::get('sf_manitou_dhcpd_conf_path'));
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
  public static function runDnsUpdate ()
  {
    self::runDnsPreUpdate();

    $path = sfConfig::get('sf_manitou_dns_conf_path');

    $hosts = HostQuery::create()->orderByRoomId()->orderByProfileId()->find ();
    $dnsConf = new Dns ();
    $dnsConf->setHosts ($hosts);
    $dnsConf->apply ($path);

    $command = new Command ();
    $command->setCommand (sfConfig::get('sf_manitou_dns_update_command'));
    $command->setArgument ('conf_path', $path);
    $command->setLabel ('Mise à jour des entrées du DNS');
    
    return $command->exec ();
  }

  public static function stopImageServer ()
  {
    $command = new Command ();
    $command->setCommand ($script);
    $command->setLabel ('Arrêt du serveur d\'images');

    return $command->exec ();
  }

} // CommandPeer
