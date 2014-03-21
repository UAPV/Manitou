<?php

/**
 * Host form base class.
 *
 * @method Host getObject() Returns the current form's model object
 *
 * @package    DRBL Admin 2
 * @subpackage form
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 */
class RestoreForm extends BaseForm
{
  public function setup()
  {
    $stateChoices = array ('poweroff' => 'Éteint', 'reboot' => 'Allumé', 'wait' => 'En attente');
    // $diskChoices = array ('sda' => 'Disque entier', 'sda1' => 'Windows', 'sda2' => 'Ubuntu');

    $this->setWidgets (array(
      'image'       => new sfWidgetFormPropelChoice (array('model' => 'Image', 'order_by' => array('Filename','desc'))),
      'hosts'       => new sfWidgetFormPropelChoice (array('model' => 'Host', 'multiple' => true, 'expanded' => true)),
      'state'       => new sfWidgetFormChoice (array('choices' => $stateChoices)),
      'partition'   => new sfWidgetFormInputText(array('default'=>'sda')),
      'resize'      => new sfWidgetFormInputCheckbox (array('default'=>'checked')),
      'post_script' => new sfWidgetFormInputCheckbox (array('default'=>'checked')),
      'pre_script'  => new sfWidgetFormInputCheckbox (),
    ));

    $this->setValidators(array(
      'image'       => new sfValidatorPropelChoice (array('model' => 'Image', 'column' => 'id', 'required' => true)),
      'hosts'       => new sfValidatorPropelChoice (array('model' => 'Host', 'column' => 'id', 'required' => true, 'multiple' => true)),
      'state'       => new sfValidatorChoice (array('choices' =>  array_keys($stateChoices))),
      'partition'   => new sfValidatorPass(),
      'resize'      => new sfValidatorBoolean(),
      'post_script' => new sfValidatorBoolean(),
      'pre_script'  => new sfValidatorBoolean(),
    ));

    $this->widgetSchema->setLabels (array (
      'image'       => 'Image à restaurer',
      'hosts'       => 'Machines à restaurer',
      'state'       => 'État',
      'partition'   => 'Partition',
      'resize'      => 'Redimensionnement',
      'post_script' => 'Post installation',
      'pre_script'  => 'Pre installation',
    ));

    $this->widgetSchema->setHelps (array (
      'state'       => 'État des machines après restauration',
      'partition'   => 'Partition à restaurer (sda=Disque entier, sda1=Windows, sda2=Ubuntu)',
      'resize'      => '<p style="color:red;">Décocher si installation Ubuntu avec VirtualBox</p>Redimentionner les partitions à la taille du disque ?',
      'post_script' => 'Exécuter le script POST_RUN',
      'pre_script'  => 'Exécuter le script PRE_RUN',
    ));

    $this->widgetSchema->setNameFormat('restore[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }


  public function getMacAddresses ()
  {
    $macs = array ();
    foreach ($this->getHosts() as $host)
      $macs [] = $host->getMacAddress ();

    return $macs;
  }

  public function getIpAddresses ()
  {
    $macs = array ();
    foreach ($this->getHosts() as $host)
      $macs [] = $host->getIpAddress ();

    return $macs;
  }

  public function getHosts ()
  {
    // TODO ne pas exécuter la requête 2 fois !
    return HostQuery::create ()->filterById ($this->getValue('hosts'))->find();
  }

  public function getImage ()
  {
    return ImageQuery::create()->findPk($this->getValue('image'));
  }
}
