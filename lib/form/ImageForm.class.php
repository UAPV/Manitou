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

    $this->validatorSchema->setPostValidator(new sfValidatorCallback(array('callback'=> array($this, 'checkAvailability'))));

    //$this->getWidgetSchema()->offsetGet('host_id')->setAttribute ('disabled', 'disabled');
  }

    /**
    * Validation pour l'unicité du nom de l'image
    */
    public function checkAvailability($validator, $values)
    {
      if (! empty($values['filename']))
      {
          $nbr = ImageQuery::create()
              ->filterByFilename($values['filename'])
              ->count();
          if ($nbr==0) {
              // Login dispo
              return $values;
          } else {
              // Login pas dispo
              throw new sfValidatorError($validator, 'Une image existe déjà avec ce nom.');
          }
      }
    }
}
