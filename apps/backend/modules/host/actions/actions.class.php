<?php

require_once dirname(__FILE__).'/../lib/hostGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/hostGeneratorHelper.class.php';

/**
 * host actions.
 *
 * @package    Manitou
 * @subpackage host
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
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
    CommandPeer::runDhcpdUpdate();
  }

  /**
   * Action exéctutée lors d'un clic sur le liens "créer une image"
   */
  public function executeListCreateImage(sfWebRequest $request)
  {
    $host = $this->getRoute()->getObject();
    $this->redirect ('@image_new?host_id='.$host->getId());
  }

  /**
   * Action appelée par le biais du menu déroulant sur la liste des machines
   */
  public function executeBatchRestore(sfWebRequest $request)
  {
    $ids = $request->getParameter('ids');
    $this->redirect ($this->getContext()->getRouting()->generate('image_restore', array ('ids' => $ids)));
  }
}
