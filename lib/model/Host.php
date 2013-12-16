<?php



/**
 * Skeleton subclass for representing a row from the 'host' table.
 *
 * 
 *
 * This class was autogenerated by Propel 1.6.1-dev on:
 *
 * Mon May 23 21:11:36 2011
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.lib.model
 */
class Host extends BaseHost {

  protected $needDnsUpdate = null;
  protected $oldSubnet = null;
  protected static $commentSvn = 'Mise a jour SVN';
  protected $new = null;
  protected static $cn = null;

  public function __toString ()
  {
    return $this->getHostname ();
  }

	public static function setCommentSvn($v)
	{
		if($v != null)
			self::$commentSvn = $v;
	}

  public function getHostname ()
  {
    return $this->getProfile().'-'.$this->getRoom ().'-'.$this->getNumber ();
  }

  public static function setCn($cn)
  {
    if( self::$cn == null)
      self::$cn = $cn;
  }

  public function getDomainName ()
  {
    return $this->getSubnet()->getDomainName();
  }

  public function getRevDomainName ()
  {
    return $this->getSubnet()->getRevDomainName();
  }

  public function getFqdn ()
  {
    return $this->getHostname().'.'.$this->getDomainName();
  }

  public function getPxe ()
  {
    $id = $this->getPxeFileId();
    if($id != null)
    {
      $objet = PxeFileQuery::create()->findPk($id);
      return $objet->getDescription();  
    }
    else
     return "";
  }

  public function isRunning ()
  {
    $ignored = $code = -1;
    exec ('fping -q -r 1 -t 50 '.$this->getIpAddress(), $ignored, $code);
    return ($code == 0);
  }

	/**
	 * Set the value of [custom_conf] column.
	 *
	 * @param      string $v new value
	 * @return     Host The current object (for fluent API support)
	 */
	public function setMacAddress($v)
	{
    return parent::setMacAddress(strtoupper($v));
  }

	/**
	 * Set the value of [custom_conf] column.
	 *
	 * @param      string $v new value
	 * @return     Host The current object (for fluent API support)
	 */
	public function setCustomConf($v)
	{
    return parent::setCustomConf(str_replace("\r", '', $v));
  }

	/**
	 * Set the value of [custom_conf] column.
	 *
	 * @param      string $v new value
	 * @return     Host The current object (for fluent API support)
	 */
	public function setComment($v)
	{
    parent::setComment(str_replace("\r", '', $v));
  }

  /**
   * Détermine si la machine a déjà une entrée dans le DNS (couple IP & Hostname)
   *
   * @return boolean
   */
  public function hasDnsRecord ()
  {
    return ($this->hasDnsRecordForHostname() && $this->hasDnsRecordForIp());
  }

  /**
   * Détermine si la machine a déjà une entrée dans le DNS
   *
   * @return boolean
   */
  public function hasDnsRecordForIp ()
  {
    $ip = implode('.', array_reverse( explode('.', $this->getIpAddress())));
    return dns_check_record ($ip.'.in-addr.arpa', 'PTR');
  }

  /**
   * Détermine si la machine a déjà une entrée dans le DNS
   *
   * @return boolean
   */
  public function hasDnsRecordForHostname ()
  {
    return dns_check_record ($this->getHostname(), 'ANY');
  }

	public function setIpAddress($v)
	{
		$this->oldIp = $this->getIpAddress();
		return parent::setIpAddress($v);
	}

	public function setSubnetId($v)
	{
		$oldSubnetId = $this->getSubnetId();
		$this->oldSubnet = SubnetPeer::retrieveByPK($oldSubnetId);

		return parent::setSubnetId($v);
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
    // Une fois dans postSave il est impossible de savoir ce qui a été modifié,
    // on enregistre donc l'info dans l'objet
    $this->needDnsUpdate = ($this->isColumnModified (HostPeer::PROFILE_ID) ||
                            $this->isColumnModified (HostPeer::ROOM_ID)    ||
                            $this->isColumnModified (HostPeer::SUBNET_ID)  ||
                            $this->isColumnModified (HostPeer::NUMBER)     ||
                            $this->isColumnModified (HostPeer::IP_ADDRESS  ));

		$this->subnetChanged = $this->isColumnModified (HostPeer::SUBNET_ID);

		if($this->getNumber() == '')
		{
			$tab =	explode('.',$this->getIpAddress());
			$this->setNumber($tab[3]);
		}

		if($this->isNew())
			$this->new = true;
		else
			$this->new = false;


		return parent::preSave ($con);
  }

  public function delete(PropelPDO $conn = null)
  {
    self::setCn($this->getHostname());

    return parent::delete($conn);
  }

  /**
   * Code to be after before deleting the object in database
   * @param PropelPDO $con
   * @return boolean
   */
  public function postDelete(PropelPDO $con = null)
  {
      parent::postDelete ($con);

		$arrayFilesToChange = array();
		$filenameReverse = 'db.'.$this->getDomainName();
		$ipBase = $this->getSubnet ()->getIpAddress();
		$ipBase = substr ($ipBase, 0, strpos ($ipBase, '.0'));
		$filename = 'db.'.$ipBase;
		$arrayFilesToChange[] = $filenameReverse;
		$arrayFilesToChange[] = $filename;

    //On supprime la machine du ldap
    CommandPeer::getLdapCommand('d', self::$cn);

    CommandPeer::runDnsUpdate (array($this),$arrayFilesToChange, self::$commentSvn, $this);
    CommandPeer::runDhcpdUpdate (self::$commentSvn);
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

    if($_SERVER['PATH_INFO'] != "/addPxe")
    {
       CommandPeer::runDhcpdUpdate (self::$commentSvn);

      // Mise à jour du DNS si nécessaire
      if ($this->needDnsUpdate === true)
			{
				if($this->subnetChanged && !$this->new)
				{
					$otherFile = array($this->oldSubnet->getDomainName(), $this->oldSubnet->getIpAddress());
					CommandPeer::runDnsUpdate (array($this), array($otherFile),self::$commentSvn);
				}
				else
					CommandPeer::runDnsUpdate (array($this), null, self::$commentSvn);
			}

    }
  }

  public function ipAlreadyExist()
  {
     $host = HostQuery::create()->findOneByIpAddress($this->getIpAddress());

     if(count($host) > 0)
       return true;
     else
       return false;

  }
  public function hostnameAlreadyExist()
  {
      $host = HostQuery::create()
         ->filterByRoomId($this->getRoomId())
         ->filterBySubnetId($this->getSubnetId())
         ->filterByNumber($this->getNumber())
         ->find();

     if(count($host) > 0)
       return true;
     else
       return false;
  }


  //On vérifie que l'hôte est dans le ldap pour cocher la checkbox associée
  public function inLdap()
  {
    $retour = array();
    $command = "ldapsearch -x -LLL -h ldap.univ-avignon.fr -b 'ou=people,dc=univ-avignon,dc=fr' '(cn=".$this->getHostname().")'";
    echo "on regarde ce que nous renvoie $command";
    exec($command, $retour);

    var_dump($retour);die;
  }
} // Host
