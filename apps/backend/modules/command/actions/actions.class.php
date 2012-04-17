<?php

/**
 * command actions.
 *
 * @package    DRBL Admin 2
 * @subpackage command
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 */
class commandActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->commands = CommandQuery::create()
        ->orderByStartedAt(Criteria::DESC)
        ->limit(40)
        ->find();

    $this->imageServers = ImageServerQuery::create ()->find();

    foreach ($this->commands as $command)
      $command->syncStatus();
  }

  public function executeStop (sfWebRequest $request)
  {
    $this->command = $this->getRoute()->getCommand();
    $this->command->stop();
    return sfView::NONE;
  }

  public function executeShow (sfWebRequest $request)
  {
    $this->command = $this->getRoute()->getCommand();
    $this->command->syncStatus();
    
    if ($request->isXmlHttpRequest())
      return $this->renderPartial ('commandInfo', array ('command' => $this->command));
    else
      return $this->renderText ('todo');
  }

  public function executeSvnStatus(sfWebRequest $request)
  {
    $path = sfConfig::get('sf_manitou_dns_conf_path');

    $command = sfConfig::get ('sf_manitou_svn_status');
    $command = str_replace ('%conf_path%', escapeshellarg($path) ,$command);

    exec ($command, $status, $returnCode);

    return $this->renderText (implode ("<br />", $status));
  }
}
