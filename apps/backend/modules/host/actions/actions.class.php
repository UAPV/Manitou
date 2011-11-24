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

 /**
  * Action appelée par le biais du menu déroulant pour ajouter un fichier PXE sur plusieurs machines en un c  * oup
  */
  public function executeBatchAddPxe(sfWebRequest $request)
  {
    $ids = $request->getParameter('ids');;
    $this->redirect ($this->getContext()->getRouting()->generate('add_pxe', array ('ids' => $ids)));
  }

  public function executeStatus ()
  {
    $host = $this->getRoute()->getObject();

    $data = $host->toArray ();
    $data['status'] = $host->isRunning () ? 1 : 0;

    return $this->returnJSON($data);
  }

  /**
   * Action appelée par le biais du menu déroulant sur la liste des machines
   */
  public function executeMassCreate(sfWebRequest $request)
  {
    $this->form = new MultipleHostForm();

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->form->save();

        $msg = 'Machines créées avec succès : ';
        foreach ($this->form->getHosts() as $host) 
          $msg .= $host.' '.$host->getIpAddress().' '.$host->getMacAddress().', ';


        $this->getUser()->setFlash('notice', $msg);
        $this->redirect('@host');
      }
      else
      {
        $this->getUser()->setFlash('error', 'Création échouée.', false);
      }
    }
  }

  /**
   * Return in JSON when requested via AJAX or as plain text when requested directly in debug mode
   *
   */
  public function returnJSON($data)
  {
    $json = json_encode($data);

    if (sfContext::getInstance()->getConfiguration()->isDebug () && !$this->getRequest()->isXmlHttpRequest()) {
      $this->getContext()->getConfiguration()->loadHelpers('Partial');
      $json = get_partial('global/json', array('data' => $data));
    } else {
      $this->getResponse()->setHttpHeader('Content-type', 'application/json');
    }

    return $this->renderText($json);
  }
}
