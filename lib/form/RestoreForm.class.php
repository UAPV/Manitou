<?php

/**
 * Host form base class.
 *
 * @method Host getObject() Returns the current form's model object
 *
 * @package    DRBL Admin 2
 * @subpackage form
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 */
class RestoreForm extends BaseForm
{
  public function setup()
  {
    $this->setWidgets(array(
      'hosts'       => new sfWidgetFormPropelChoice (array('model' => 'Host', 'multiple' => true)),
    ));

    $this->setValidators(array(
      'hosts'       => new sfValidatorPropelChoice(array('model' => 'Host', 'column' => 'id', 'required' => true, 'multiple' => true)),
    ));

    $this->widgetSchema->setLabels (array (
      'hosts' => 'Machines à restaurer',
    ));

    $this->widgetSchema->setNameFormat('restore[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }


  public function getMacAddresses ()
  {
    $macs = array ();
    foreach ($this->getHosts() as $host)
      $macs [] = $host->getMacAddress ();

    return $macs;
  }

  public function getHosts ()
  {
    // TODO ne pas exécuter la requête 2 fois !
    return HostQuery::create ()->filterById ($this->getValue('hosts'))->find();
  }
}
