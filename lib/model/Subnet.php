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
