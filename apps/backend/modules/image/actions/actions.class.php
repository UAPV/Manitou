<?php

require_once dirname(__FILE__).'/../lib/imageGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/imageGeneratorHelper.class.php';

/**
 * image actions.
 *
 * @package    DRBL Admin 2
 * @subpackage image
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class imageActions extends autoImageActions
{

  public function executeNew(sfWebRequest $request)
  {
    $host = HostQuery::create()->findPk ($request->getGetParameter('host_id'));
    $this->forward404If($host === null);

    $this->Image = new Image ();
    $this->Image->setHost ($host);
    $this->Image->setImageServerId ($host->getSubnet()->getImageServerId());
    $this->form = $this->configuration->getForm($this->Image);
  }

  /**
   * A la création d'une image une commande est exécutée et l'utilisateur est redirigé vers les logs.
   * On ne tient pas compte de la mise à jour, elle ne doit pas avoir lieu... à confirmer...
   *
   * @param sfWebRequest $request
   * @param sfForm $form
   * @return void
   */
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $isNew = $form->getObject()->isNew();
      $notice = $isNew ? 'L\'image est en cours de création.' : 'The item was updated successfully.';
      $Image = $form->save();

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $Image)));

      if ($isNew)
      {
        $command = CommandPeer::getCreateImageCommand($Image);
        $command->setArgument('restart', $form->getValue('state'));
        $command->exec(true);

        $this->getUser()->setFlash('notice', $notice);
        $this->redirect('command_list');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);
        $this->redirect(array('sf_route' => 'image_edit', 'sf_subject' => $Image));
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }

  /**
   * Méthode appelée pour restorer des machines
   */
  public function executeRestore (sfWebRequest $request)
  {
    $ids = $request->getParameter ('ids');
    $this->hosts = HostQuery::create()->filterById ($ids)->find ();
    $this->form = new RestoreForm (array ('hosts' => $ids));

    if ($request->isMethod ('post'))
    {
      $this->form->bind($request->getParameter ($this->form->getName ()));
      if ($this->form->isValid ())
      {
        $this->restore ($this->form);
        $this->getUser()->setFlash('notice', 'La restauration a été lancée');
        $this->redirect ('command_list');
      }
    }
  }

  protected function restore ($form)
  {
    $command = CommandPeer::getRestoreImageCommand();
    $command->setArgument('image', 'test.img');
    $command->setArgument('hosts_mac', implode (' ', $form->getMacAddresses()));
    $command->exec (true);
  }
}