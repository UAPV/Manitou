<?php

/**
 * Host form.
 *
 * @package    Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @subpackage form
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 */
class HostForm extends BaseHostForm
{
  public function configure()
  {
    unset ($this['created_at']);
    unset ($this['updated_at']);
    
    $this->widgetSchema->setLabels (array (
      'profile_id'           => 'Profil',
      'room_id'              => 'Salle',
      'number'               => 'Suffixe',
      'ip_address'           => 'Adresse IP',
      'mac_address'          => 'Adresse MAC',
      'comment'              => 'Commentaire',
      'custom_conf'          => 'Conf DHCP',
      'cloned_from_image_id' => 'Image système',
      'subnet_id'            => 'Subnet',
      'pxe_file_id'          => 'Fichier PXE',
    ));

    $this->widgetSchema->setHelps (array (
      'profile_id'           => 'Profil de l\'utilisateur',
      'room_id'              => 'Salle où se situe la machine',
      'number'               => 'Numéro utilisé comme suffixe dans le hostname',
      'ip_address'           => 'Adresse IP',
      'mac_address'          => 'Adresse MAC (format 00:11:22:33:44:55)',
      'comment'              => 'Commentaire (ex: numéro de prise, etc)',
      'custom_conf'          => 'Conf DHCP personnalisée',
      'cloned_from_image_id' => 'Dernière image système appliquée',
      'pxe_file_id'          => 'Fichier PXE (par défaut celui configuré pour le subnet sera utilisé)',
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        $this->validatorSchema->getPostValidator(),
        new sfValidatorCallback(array('callback' => array($this, 'checkIpAddress')))
      ))
    );
  }

  public function checkIpAddress ($validator, $values)
  {
    if ($values['subnet_id'] === null)
      return $values;

    $tmpHost = new Host();
    $tmpHost->setProfileId($values['profile_id']);
    $tmpHost->setSubnetId($values['subnet_id']);
    $tmpHost->setRoomId($values['room_id']);
    $tmpHost->setNumber($values['number']);
    $tmpHost->setIpAddress($values['ip_address']);

    $subnet = $tmpHost->getSubnet();

    if (! $subnet->isIpAddressAuthorized($values['ip_address']))
      throw new sfValidatorError($validator, 'L\'adresse ip n\'est pas autorisée (déjà prise par la passerelle, le dns, n\'appartient pas au subnet, etc)');

    // Si la machine est nouvelle ou si on modifie son adresse IP
    if ($this->getObject()->isNew())
    {
      $hostnameRecord = $tmpHost->hasDnsRecordForHostname();
      $ipRecord       = $tmpHost->hasDnsRecordForIp();

      if (! $hostnameRecord && $ipRecord)
        throw new sfValidatorError($validator, 'L\'adresse ip existe déjà dans le DNS, veuillez la supprimer puis recommencer.');

      if ($hostnameRecord && ! $ipRecord)
        throw new sfValidatorError($validator, 'Le nom d\'hôte existe déjà dans le DNS, veuillez le supprimer puis recommencer.');
    }
    else if ($this->getObject()->getIpAddress() != $values['ip_address'] && $tmpHost->hasDnsRecordForIp())
    {
      throw new sfValidatorError($validator, 'L\'adresse ip existe déjà dans le DNS, veuillez la supprimer puis recommencer.');
    }
    
    return $values;
  }
}
  