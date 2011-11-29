<?php

/**
 * Permet de valider les attributs d'un Host. L'host modifié ou créé doit être passé en paramètre afin de savoir
 * ses anciens attributs ou s'il est nouveau.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Arnaud Didry
 * @version    SVN: $Id: sfValidatorString.class.php 12641 2008-11-04 18:22:00Z fabien $
 */
class sfValidatorHost extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Si ce validateur est utilisé lors de la modification d'une machine, l'hôte à passer
   * correspond à l'ancien objet en base (afin de connaître les précédentes valeurs)
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);
    $this->addRequiredOption('host_object'); // Si ce validateur est utilisé lors de la modification d'une machine, l'hôte à passer
                                             // correspond à l'ancien objet en base (afin de connaître les précédentes valeurs)

    $this->addMessage('ip_not_in_subnet',       'L\'adresse ip "%value%" n\'est pas autorisée car elle n\'appartient pas au subnet');
    $this->addMessage('ip_same_as_dns',         'L\'adresse ip "%value%" n\'est pas autorisée car elle correspond au DNS');
    $this->addMessage('ip_same_as_gateway',     'L\'adresse ip "%value%" n\'est pas autorisée car elle correspond à la passerelle');
    $this->addMessage('ip_in_dhcp_range',       'L\'adresse ip "%value%" n\'est pas autorisée car elle est dans la plage d\'adresses IP délivrées par le DHCP');
    $this->addMessage('ip_already_in_dns',      'L\'adresse ip "%value%" existe déjà dans le DNS, veuillez la supprimer puis recommencer.');
    $this->addMessage('hostname_already_in_dns','Le nom d\'hôte "%value%" existe déjà dans le DNS, veuillez le supprimer puis recommencer.');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($values)
  {
    if ($values['subnet_id'] === null)
      return $values;

    $tmpHost = new Host();
    $tmpHost->setProfileId($values['profile_id']);
    $tmpHost->setSubnetId($values['subnet_id']);
    $tmpHost->setRoomId($values['room_id']);
    $tmpHost->setNumber($values['number']);
    $tmpHost->setIpAddress($values['ip_address']);
    $tmpHost->setMacAddress($values['mac_address']);
    $subnet = $tmpHost->getSubnet();

    if (! $subnet->containsIpAddress ($values['ip_address']))
      throw new sfValidatorError($this, 'ip_not_in_subnet', array('value' => $values['ip_address']));

    if ($subnet->getDnsServer()  == $values['ip_address'])
      throw new sfValidatorError($this, 'ip_same_as_dns', array('value' => $values['ip_address']));

    if ($subnet->getGateway()    == $values['ip_address'])
      throw new sfValidatorError($this, 'ip_same_as_gateway', array('value' => $values['ip_address']));

    if ($subnet->isInsideDhcpRange ($values['ip_address']))
      throw new sfValidatorError($this, 'ip_in_dhcp_range', array('value' => $values['ip_address']));

    // Si la machine est nouvelle
    if ($this->getOption('host_object')->isNew())
    {
      if ($tmpHost->hasDnsRecordForIp())
        throw new sfValidatorError($this, 'ip_already_in_dns', array('value' => $values['ip_address']));

      if ($tmpHost->hasDnsRecordForHostname())
        throw new sfValidatorError($this, 'hostname_already_in_dns', array('value' => $tmpHost->getHostname()));
    }
    else if ($this->getOption('host_object')->getIpAddress() != $values['ip_address'] && $tmpHost->hasDnsRecordForIp())
    {
      throw new sfValidatorError($this, 'ip_already_in_dns', array('value' => $values['ip_address']));
    }

    return $values;
  }
}
