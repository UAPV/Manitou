<?php

require_once dirname(__FILE__).'/../lib/subnetGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/subnetGeneratorHelper.class.php';

/**
 * subnet actions.
 *
 * @package    Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @subpackage subnet
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class subnetActions extends autoSubnetActions
{
  public function preExecute()
  {
    $this->dispatcher->connect('admin.save_object', array ($this, 'updateDhcp'));
    return parent::preExecute ();
  }

  public function updateDhcp ()
  {
    CommandPeer::runDhcpdUpdate();
  }
}
