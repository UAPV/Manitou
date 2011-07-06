<?php

/**
 * Image form.
 *
 * @package    Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @subpackage form
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 */
class ImageForm extends BaseImageForm
{
  public function configure()
  {
    $stateChoices = array ('poweroff' => 'Éteint', 'reboot' => 'Allumé', 'true' => 'En attente');

    $this->setWidget('state', new sfWidgetFormChoice( array('choices' => $stateChoices)));
    $this->setValidator ('state', new sfValidatorChoice(array('choices' =>  array_keys($stateChoices))));
    $this->widgetSchema->setLabel ('state', 'État après la création');

    //$this->getWidgetSchema()->offsetGet('host_id')->setAttribute ('disabled', 'disabled');
  }
}
