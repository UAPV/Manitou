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
    $this->Image = new Image ();
    $this->Image->setHostId ($request->getGetParameter('host_id'));
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
        $script = sfConfig::get('sf_manitou_create_image_command');
        $script = str_replace(
          array('%filename%', '%ip%', '%mac%', '%restart%'),
          array($Image->getFilename(), $Image->getHost()->getIpAddress(), $Image->getHost()->getMacAddress(), $Image->getRestart()),
          $script
        );

        $command = new Command();
        $command->setCommand($script);
        $command->setUserId ('foobar'); // TODO
        $command->save();
        $command->backgroundExec ();

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
}
