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
    $stateChoices = array ('poweroff' => 'Éteint', 'reboot' => 'Allumé', 'true' => 'En attente');
    $diskChoices = array ('sda' => 'Disque entier', 'sda1' => 'Windows', 'sda2' => 'Ubuntu');

    $this->setWidgets (array(
      'hosts'       => new sfWidgetFormPropelChoice (array('model' => 'Host', 'multiple' => true)),
      'state'       => new sfWidgetFormChoice (array('choices' => $stateChoices)),
      'disk'        => new sfWidgetFormChoice (array('choices' => $diskChoices)),
      'resize'      => new sfWidgetFormInputCheckbox (array('default'=>'checked')),
      'post_script' => new sfWidgetFormInputCheckbox (array('default'=>'checked')),
      'pre_script'  => new sfWidgetFormInputCheckbox (),
    ));

    $this->setValidators(array(
      'hosts'       => new sfValidatorPropelChoice (array('model' => 'Host', 'column' => 'id', 'required' => true, 'multiple' => true)),
      'state'       => new sfValidatorChoice (array('choices' =>  array_keys($stateChoices))),
    ));

    $this->widgetSchema->setLabels (array (
      'hosts'       => 'Machines à restaurer',
      'state'       => 'État',
      'disk'        => 'Partitions',
      'resize'      => 'Redimentionnement',
      'post_script' => 'Post installation',
      'pre_script'  => 'Pre installation',
    ));

    $this->widgetSchema->setHelps (array (
      'state'       => 'État des machines après restauration',
      'disk'        => 'Partition à restaurer',
      'resize'      => 'Redimentionner les partitions à la taille du disque ?',
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

  public function getHosts ()
  {
    // TODO ne pas exécuter la requête 2 fois !
    return HostQuery::create ()->filterById ($this->getValue('hosts'))->find();
  }
}
