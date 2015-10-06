<?php



/**
 * Skeleton subclass for representing a row from the 'room' table.
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
class Room extends BaseRoom {

  protected $needConfUpdate = null;

  public function __toString () {
    return $this->getName ();
  }

	public function setName($v){
		return parent::setName(strtolower($v));
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
    parent::preSave ($con);

    $this->needConfUpdate = $this->isColumnModified (RoomPeer::NAME);
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

    if ($this->needConfUpdate)
    {
      CommandPeer::runDhcpdUpdate ();
      CommandPeer::runDnsUpdate ();
    }
  }

} // Room
