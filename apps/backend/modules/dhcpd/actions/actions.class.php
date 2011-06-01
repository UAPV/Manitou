<?php

/**
 * dhcpd actions.
 *
 * @package    DRBL Admin 2
 * @subpackage dhcpd
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class dhcpdActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->dhcpdConf = array();
    foreach (glob (sfConfig::get('sf_manitou_dhcpd_conf_path').'/*.conf') as $file)
      $this->dhcpdConf [basename($file)] = file_get_contents($file);
  }
}
