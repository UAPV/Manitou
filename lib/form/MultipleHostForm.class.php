<?php

/**
 * Host form.
 *
 * @package    Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @subpackage form
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 */
class MultipleHostForm extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'profile_id'           => new sfWidgetFormPropelChoice(array('model' => 'Profile', 'add_empty' => true)),
      'room_id'              => new sfWidgetFormPropelChoice(array('model' => 'Room', 'add_empty' => true)),
      'first_ip_address'     => new sfWidgetFormInputText(),
      'subnet_id'            => new sfWidgetFormPropelChoice(array('model' => 'Subnet', 'add_empty' => true)),
      'pxe_file_id'          => new sfWidgetFormPropelChoice(array('model' => 'PxeFile', 'add_empty' => true)),
      'count'                => new sfWidgetFormInputText(array ('default' => 0)),
    ));

    $this->setValidators(array(
      'profile_id'           => new sfValidatorPropelChoice(array('model' => 'Profile', 'column' => 'id', 'required' => false)),
      'room_id'              => new sfValidatorPropelChoice(array('model' => 'Room', 'column' => 'id', 'required' => false)),
      'first_ip_address'     => new sfValidatorString(array('max_length' => 15)),
      'subnet_id'            => new sfValidatorPropelChoice(array('model' => 'Subnet', 'column' => 'id', 'required' => false)),
      'pxe_file_id'          => new sfValidatorPropelChoice(array('model' => 'PxeFile', 'column' => 'id', 'required' => false)),
      'count'                => new sfValidatorInteger(),
    ));

    $this->widgetSchema->setNameFormat('hosts[%s]');

    for ($i=0; $i<$this->getValue('count'); $i++)
    {
      // TODO $this->embedForm ();
    }

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }
}
