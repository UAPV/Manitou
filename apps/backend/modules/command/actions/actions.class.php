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
    $this->commands = $this->getRoute()->getCommand();
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
    return sfView::NONE;
  }
}
