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

    foreach ($this->commands as $command)
    {
      $command->syncTmpOutput();
      $command->save();
    }
  }

  public function executeStart (sfWebRequest $request)
  {
    $this->command = $this->getRoute()->getCommand();
    $this->command->exec ();
    return sfView::NONE;
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
    $this->command->syncTmpOutput();
    
    if ($request->isXmlHttpRequest())
      return $this->renderPartial ('commandInfo', array ('command' => $this->command));
    else
      return $this->renderText ('todo');
  }
}
