<?php

/**
 * Host form base class.
 *
 * @method Host getObject() Returns the current form's model object
 *
 * @package    Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @subpackage form
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 */
abstract class BaseHostForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'profile_id'  => new sfWidgetFormPropelChoice(array('model' => 'Profile', 'add_empty' => true)),
      'room_id'     => new sfWidgetFormPropelChoice(array('model' => 'Room', 'add_empty' => true)),
      'number'      => new sfWidgetFormInputText(),
      'ip_address'  => new sfWidgetFormInputText(),
      'mac_address' => new sfWidgetFormInputText(),
      'comment'     => new sfWidgetFormTextarea(),
      'custom_conf' => new sfWidgetFormTextarea(),
      'pxe_file_id' => new sfWidgetFormPropelChoice(array('model' => 'PxeFile', 'add_empty' => true)),
      'created_at'  => new sfWidgetFormDateTime(),
      'updated_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorPropelChoice(array('model' => 'Host', 'column' => 'id', 'required' => false)),
      'profile_id'  => new sfValidatorPropelChoice(array('model' => 'Profile', 'column' => 'id', 'required' => false)),
      'room_id'     => new sfValidatorPropelChoice(array('model' => 'Room', 'column' => 'id', 'required' => false)),
      'number'      => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'ip_address'  => new sfValidatorString(array('max_length' => 15)),
      'mac_address' => new sfValidatorString(array('max_length' => 17)),
      'comment'     => new sfValidatorString(array('required' => false)),
      'custom_conf' => new sfValidatorString(array('required' => false)),
      'pxe_file_id' => new sfValidatorPropelChoice(array('model' => 'PxeFile', 'column' => 'id', 'required' => false)),
      'created_at'  => new sfValidatorDateTime(array('required' => false)),
      'updated_at'  => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('host[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Host';
  }


}
