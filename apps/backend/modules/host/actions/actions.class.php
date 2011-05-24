<?php

require_once dirname(__FILE__).'/../lib/hostGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/hostGeneratorHelper.class.php';

/**
 * host actions.
 *
 * @package    Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @subpackage host
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class hostActions extends autoHostActions
{
  public function preExecute()
  {
    $this->dispatcher->connect('admin.save_object', array ($this, 'updateDhcp'));
    return parent::preExecute ();
  }

  public function updateDhcp ()
  {
    HostQuery::create ()->find (); // Juste pour remplir le cache de propel
    $subnets = SubnetQuery::create ()->find ();

    $conf = $this->getPartial ('dhcpd.conf', array ('subnets' => $subnets));
    $filename = sfConfig::get('sf_data_dir').'/dhcpd/dhcpd.conf';
    file_put_contents ($filename, $conf);
  }
}
