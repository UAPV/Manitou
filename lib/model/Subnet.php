<?php



/**
 * Skeleton subclass for representing a row from the 'subnet' table.
 *
 * 
 *
 * This class was autogenerated by Propel 1.6.1-dev on:
 *
 * Wed May 25 00:43:57 2011
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.lib.model
 */
class Subnet extends BaseSubnet {

  protected $needDnsUpdate = null;

  public function __toString ()
  {
    return $this->getName ();
  }

  public function getRevDomainName ()
  {
    $address = $this->getIpAddress();
    $networkPart = substr ($address, 0, strpos ($address, '.0'));
    $networkPart = array_reverse (explode ('.', $networkPart));
    return implode ($networkPart, '.').'.in-addr.arpa';
  }

	/**
	 * Set the value of [custom_conf] column.
	 *
	 * @param      string $v new value
	 * @return     Host The current object (for fluent API support)
	 */
	public function setCustomConf($v)
	{
    parent::setCustomConf(str_replace("\r", '', $v));
  }

  /**
   * Détermine si une IP donnée appartient au range d'adresses IP délivrées par le serveur DHCP
   *
   * @param string $ipAddress
   * @return boolean
   */
  public function isInsideDhcpRange ($ipAddress)
  {
    return (ip2long($this->getRangeBegin()) <= ip2long($ipAddress)
         && ip2long($this->getRangeEnd())   >= ip2long($ipAddress));
  }

  /**
   * Détermine si une IP donnée peut être affectée, celle ci ne doit pas appartenir :
   *   - aux serveurs DNS du subnet
   *   - à la passerelle
   *   - au range d'adresse IP délivrée par le serveur DHCP
   *
   * @param string $ipAddress
   * @return boolean
   */
  public function isIpAddressAuthorized ($ipAddress)
  {
    return ($this->containsIpAddress ($ipAddress) &&
          ! $this->isInsideDhcpRange ($ipAddress) &&
            $this->getDnsServer()  != $ipAddress  &&
            $this->getGateway()    != $ipAddress);
  }

  /**
   * Détermine si une adresse IP est bien contenu dans la plage d'IP du subnet
   *
   * TODO: Pour le moment on utilise une méthode naïve avec des masques de
   * TODO: sous-réseau simples (255 ou 0), à améliorer
   *
   * @param string $ipAddress
   * @return void
   */
  public function containsIpAddress ($ipAddress)
  {
    $octetsRef = explode ('.', $this->getIpAddress());
    $octetsNew = explode ('.', $ipAddress);

    foreach (explode ('.', $this->getNetmask()) as $i => $octetMask)
    {
      if ((int) $octetMask == 0)
        return true;

      if ($octetsNew[$i] != $octetsRef[$i])
        return false;
    }

    return true;
  }

	/**
	 * Code to be run before persisting the object
   *
   * On détermine si le DNS devra être mis à jour après l'enregistrement en base
   *
	 * @param PropelPDO $con
	 */
	public function preSave(PropelPDO $con = null)
  {
    $this->needDnsUpdate = ($this->isColumnModified (SubnetPeer::IP_ADDRESS) ||
                            $this->isColumnModified (SubnetPeer::DOMAIN_NAME));
    return parent::preSave ($con);
  }

	/**
	 * Code to be run after persisting the object
   *
   * On exécute alors la commande de mise à jour du DHCP
   *
	 * @param PropelPDO $con
	 */
	public function postSave(PropelPDO $con = null)
  {
    parent::postSave ($con);
    CommandPeer::runDhcpdUpdate ();

    if ($this->needDnsUpdate === true)
      CommandPeer::runDnsUpdate ();
  }

  /**
   * Code to be after before deleting the object in database
   * @param PropelPDO $con
   * @return boolean
   */
  public function postDelete(PropelPDO $con = null)
  {
    parent::postDelete ($con);
    CommandPeer::runDhcpdUpdate ();
    CommandPeer::runDnsUpdate ();
  }

} // Subnet
