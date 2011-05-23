<?php

/**
 * Host filter form base class.
 *
 * @package    Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @subpackage filter
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 */
abstract class BaseHostFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'profile_id'  => new sfWidgetFormPropelChoice(array('model' => 'Profile', 'add_empty' => true)),
      'room_id'     => new sfWidgetFormPropelChoice(array('model' => 'Room', 'add_empty' => true)),
      'number'      => new sfWidgetFormFilterInput(),
      'ip_address'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'mac_address' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'comment'     => new sfWidgetFormFilterInput(),
      'custom_conf' => new sfWidgetFormFilterInput(),
      'pxe_file_id' => new sfWidgetFormPropelChoice(array('model' => 'PxeFile', 'add_empty' => true)),
      'created_at'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'updated_at'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'profile_id'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Profile', 'column' => 'id')),
      'room_id'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Room', 'column' => 'id')),
      'number'      => new sfValidatorPass(array('required' => false)),
      'ip_address'  => new sfValidatorPass(array('required' => false)),
      'mac_address' => new sfValidatorPass(array('required' => false)),
      'comment'     => new sfValidatorPass(array('required' => false)),
      'custom_conf' => new sfValidatorPass(array('required' => false)),
      'pxe_file_id' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'PxeFile', 'column' => 'id')),
      'created_at'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('host_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Host';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'profile_id'  => 'ForeignKey',
      'room_id'     => 'ForeignKey',
      'number'      => 'Text',
      'ip_address'  => 'Text',
      'mac_address' => 'Text',
      'comment'     => 'Text',
      'custom_conf' => 'Text',
      'pxe_file_id' => 'ForeignKey',
      'created_at'  => 'Date',
      'updated_at'  => 'Date',
    );
  }
}
