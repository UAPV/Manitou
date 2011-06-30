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
      'profile_id'           => new sfValidatorPropelChoice(array('model' => 'Profile', 'column' => 'id', 'required' => true)),
      'room_id'              => new sfValidatorPropelChoice(array('model' => 'Room', 'column' => 'id', 'required' => true)),
      'first_ip_address'     => new sfValidatorIpAddress(array('required' => true)),
      'subnet_id'            => new sfValidatorPropelChoice(array('model' => 'Subnet', 'column' => 'id', 'required' => false, 'required' => true)),
      'pxe_file_id'          => new sfValidatorPropelChoice(array('model' => 'PxeFile', 'column' => 'id', 'required' => false)),
      'count'                => new sfValidatorInteger(array('required' => true, 'min' => 1)),
    ));

    $this->widgetSchema->setLabels(array(
      'profile_id'           => 'Profil',
      'room_id'              => 'Salle',
      'first_ip_address'     => 'Adresse IP',
      'subnet_id'            => 'Subnet',
      'pxe_file_id'          => 'Fichier PXE',
      'count'                => 'Total',
    ));

    $this->widgetSchema->setHelps(array(
      'profile_id'           => 'Profil de l\'utilisateur',
      'room_id'              => 'Salle',
      'first_ip_address'     => 'Adresse IP de début',
      'subnet_id'            => 'Subnet',
      'pxe_file_id'          => 'Fichier PXE par défaut',
      'count'                => 'Nombre de machines',
    ));

    $this->widgetSchema->setNameFormat('hosts[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }

  /**
   * Binds the form with input values.
   *
   * It triggers the validator schema validation.
   *
   * @param array $taintedValues  An array of input values
   * @param array $taintedFiles   An array of uploaded files (in the $_FILES or $_GET format)
   */
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    $validator = new sfValidatorIpAddress();
    $isClean = true;
    try {$validator->clean ($taintedValues['first_ip_address']);} catch (Exception $e) {$isClean = false;}
    $hostNumber = (int) $taintedValues['count'];

    if ($isClean && $hostNumber > 1)
    {
      $ipBase = explode ('.', $taintedValues['first_ip_address']);
      $ipCounter = (int) array_pop ($ipBase);

      for ($i=0; $i < $hostNumber; $i++)
      {
        $host = new Host();
        $host->setProfileId     ($taintedValues['profile_id']);
        $host->setRoomId        ($taintedValues['room_id']);
        $host->setIpAddress     (implode($ipBase, '.').'.'.$ipCounter);
        $host->setNumber        ($ipCounter);
        $host->setSubnetId      ($taintedValues['subnet_id']);
        $host->setPxeFileId     ($taintedValues['pxe_file_id']);

        $form = new HostForm($host);
        $this->embedForm ('host_'.$i, $form);
        $ipCounter++;
      }
    }

    return parent::bind ($taintedValues, $taintedFiles);
  }

}
