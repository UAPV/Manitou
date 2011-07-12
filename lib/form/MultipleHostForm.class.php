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

  /**
   * On pré-valide le formulaire pour précalculer les adresses IP qui vont être affectées. Si une des
   * machines est en conflit (IP ou Hostname) on ajoute une case à cocher pour demander à la personne
   * valider le formulaire même si la plage d'adresses affectées n'est pas continue.
   *
   * @param array $taintedValues  An array of input values
   */
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    try
    {
      $macAddresses = $this->validatorSchema['mac_addresses']->clean ($taintedValues['mac_addresses']);

      $hostNumber = count ($macAddresses);
      $ipBase = explode ('.', $taintedValues['first_ip_address']);
      $ipCounter = (int) array_pop ($ipBase);

      $conflicts = array();

      for ($i=0; $i < $hostNumber && $ipCounter < 255; $i++)
      {
        try
        {
          $data = $taintedValues + array (
            'number' => $ipCounter,
            'ip_address' => implode($ipBase, '.').'.'.$ipCounter,
          );

          $validator = new sfValidatorHost(array('host_object' => new Host()));
          $validator->clean ($data);

          $host = new Host();
          $host->fromArray($data, BasePeer::TYPE_FIELDNAME);

          $form = new HostForm($host);
          $this->embedForm ($i, $form); // FIXME !!!
        }
        catch (sfValidatorError $e)
        {
          $conflicts [] = $e->getMessage();
          $i--;
        }

        if (count ($conflicts))
        {
          throw new sfValidatorError (new sfValidatorString(), implode("\n", $conflicts));
        }

        $ipCounter++;
      }

      //$taintedValues['mac_addresses'] = '';
    }
    catch (Exception $e)
    {
      throw $e;
    }

    return parent::bind ($taintedValues, $taintedFiles);
  }

  public function checkHosts ($values)
  {
    //$this->getValues();
    //print_r($values);
    //die;
  }

  public function save ()
  {
    var_dump($this->getValues());

    //die('hell');
  }

}
