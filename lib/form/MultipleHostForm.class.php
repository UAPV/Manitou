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
  /* @var array $host   Tableau contenant les instances d'Host générées par les données entrées dans le formulaire */
  protected $hosts = array();

  public function configure()
  {

    $this->setWidgets(array(
      'profile_id'           => new sfWidgetFormPropelChoice(array('model' => 'Profile', 'add_empty' => true)),
      'room_id'              => new sfWidgetFormPropelChoice(array('model' => 'Room', 'add_empty' => true)),
      'first_ip_address'     => new sfWidgetFormInputText(),
      'subnet_id'            => new sfWidgetFormPropelChoice(array('model' => 'Subnet', 'add_empty' => true)),
      'pxe_file_id'          => new sfWidgetFormPropelChoice(array('model' => 'PxeFile', 'add_empty' => true)),
      'mac_addresses'        => new sfWidgetFormTextarea(array(), array('rows' => 20, 'cols' => 20)),
    ));

    $this->setValidators(array(
      'profile_id'           => new sfValidatorPropelChoice(array('model' => 'Profile', 'column' => 'id', 'required' => true)),
      'room_id'              => new sfValidatorPropelChoice(array('model' => 'Room', 'column' => 'id', 'required' => true)),
      'first_ip_address'     => new sfValidatorIpAddress(array('required' => true)),
      'subnet_id'            => new sfValidatorPropelChoice(array('model' => 'Subnet', 'column' => 'id', 'required' => false, 'required' => true)),
      'pxe_file_id'          => new sfValidatorPropelChoice(array('model' => 'PxeFile', 'column' => 'id', 'required' => false)),
      'mac_addresses'        => new sfValidatorMacAddress(array('multiple' => true)),
    ));

    $this->widgetSchema->setLabels(array(
      'profile_id'           => 'Profil',
      'room_id'              => 'Salle',
      'first_ip_address'     => 'Adresse IP',
      'subnet_id'            => 'Subnet',
      'pxe_file_id'          => 'Fichier PXE',
      'mac_addresses'        => 'Adresses MAC',
    ));

    $this->widgetSchema->setHelps(array(
      'profile_id'           => 'Profil de l\'utilisateur',
      'room_id'              => 'Salle',
      'first_ip_address'     => 'Adresse IP de début',
      'subnet_id'            => 'Subnet',
      'pxe_file_id'          => 'Fichier PXE par défaut',
      'mac_addresses'        => 'Adresses MAC (une par ligne au format 11:22:33:44:55:66)',
    ));


    $this->widgetSchema->setNameFormat('hosts[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->validatorSchema->setPostValidator(
        new sfValidatorCallback(array('callback' => array($this, 'checkHosts')))
    );
  }

  public function checkHosts ($validator, $values)
  {
      echo "<pre>";var_dump($values);echo "</pre>";die;
    $macAddresses = $values[mac_addresses]; // Tableau (transformé depuis un string par le sfValidatorMacAddress)

      var_dump($macAddresses);die;
    $hostNumber = count ($macAddresses);
    $ipBase = explode ('.', $values['first_ip_address']);
    $ipCounter = (int) array_pop ($ipBase);

    $conflicts = array();

    for ($i=0; $i < $hostNumber && $ipCounter < 255; $i++)
    {
      try
      {
        $data = $values + array (
          'number'      => $ipCounter,
          'ip_address'  => implode($ipBase, '.').'.'.$ipCounter,
          'mac_address' => $macAddresses[$i],
        );

        $host = new Host();
        $host->fromArray($data, BasePeer::TYPE_FIELDNAME);

        // On vérifie si les infos sont cohérentes d'un point de vue archi réseau
        // et si les critères d'unicité avec **les entrées du DNS** sont respectés
        $validator = new sfValidatorHost(array('host_object' => $host));
        $validator->clean ($data);

        // On vérifie si les critères d'unicité sont bien respectés en **base de données**
        $uniqCheck = new sfValidatorAnd(array(
          new sfValidatorPropelUnique(array('model' => 'Host', 'column' => array('mac_address'))),
          new sfValidatorPropelUnique(array('model' => 'Host', 'column' => array('ip_address'))),
          new sfValidatorPropelUnique(array('model' => 'Host', 'column' => array('profile_id', 'room_id', 'number'))),
        ));
        $uniqCheck->clean($data);

        $this->hosts [] = $host;
      }
      catch (sfValidatorError $e)
      {
        $conflicts [] = $e->getMessage();
        $i--;
      }

      $ipCounter++;
    }

    if (count ($conflicts))
    {
      throw new sfValidatorError (new sfValidatorString(), implode("<br />", $conflicts));
    }
  }

  public function save ()
  {
    foreach ($this->hosts as $host)
      $host->save();
  }

  public function getHosts ()
  {
    return $this->hosts;
  }

}
