<?php

/**
 * dns actions.
 *
 * @package    Manitou
 * @subpackage dns
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class dnsActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->files = array();
    $this->dnsErrors = array();

    foreach (glob (sfConfig::get('sf_manitou_dns_conf_path').'/db.*') as $file)
    {
      $filename = basename ($file);
      $this->files [] = $filename;
      if (preg_match_all ('/.*MANITOU_ERROR.*/', file_get_contents($file), $result) > 0)
        $this->dnsErrors[$filename] = $result[0];
    }
  }

 /**
  * Affiche le contenu d'un fichier de conf du DNS
  *
  * @param sfRequest $request A request object
  */
  public function executeShow(sfWebRequest $request)
  {
    $this->filename = basename ($request->getParameter ('filename'));
    $this->fileContent = file_get_contents(sfConfig::get('sf_manitou_dns_conf_path').'/'.$this->filename);
  }

 /**
  * Force la regénération de la conf DNS
  *
  * @param sfRequest $request A request object
  */
  public function executeReload(sfWebRequest $request)
  {
    CommandPeer::runDnsPreUpdate();
    CommandPeer::runDnsUpdate();

    $this->redirect ('dns/index');
  }
}
