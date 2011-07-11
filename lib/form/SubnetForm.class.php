<?php

/**
 * Subnet form.
 *
 * @package    Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @subpackage form
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 */
class SubnetForm extends BaseSubnetForm
{
  public function configure()
  {
    $this->setValidator ('ip_address', new sfValidatorIpAddress(array('required' => true)));
  }
}
