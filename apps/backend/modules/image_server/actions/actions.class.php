<?php

require_once dirname(__FILE__).'/../lib/image_serverGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/image_serverGeneratorHelper.class.php';

/**
 * image_server actions.
 *
 * @package    DRBL Admin 2
 * @subpackage image_server
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class image_serverActions extends autoImage_serverActions
{
  /**
   * Retourne le status d'un serveur donné
   *
   * @param sfWebRequest $request
   * @return void
   */
  public function executeStatus (sfWebRequest $request)
  {
    $this->imageServer = $this->getRoute()->getImageServer();

    $command = sfConfig::get ('sf_manitou_image_server_status');
    $command = str_replace ('%image_server%', escapeshellarg($this->imageServer->getHostname()) ,$command);

    exec ($command, $status, $returnCode);

    return $this->renderText (implode (' ', $status));
  }

  /**
   * Stoppe toute les actions en cours du serveur d'image
   *
   * @param sfWebRequest $request
   * @return void
   */
  public function executeStop (sfWebRequest $request)
  {
    $this->imageServer = $this->getRoute()->getImageServer();

    $command = new Command ();
    $command->setCommand (sfConfig::get ('sf_manitou_image_server_stop'));
    $command->setArgument ('image_server', $this->imageServer->getHostname());
    $command->exec();

    $this->redirect ('@command_list');
  }
}
