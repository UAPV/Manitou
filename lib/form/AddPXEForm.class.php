<?php


class AddPXEForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['hosts'] = new sfWidgetFormPropelChoice (array('model' => 'Host', 'multiple' => true, 'expanded' => true));     $this->widgetSchema['pxe'] = new sfWidgetFormPropelChoice (array('model' => 'PxeFile', 'multiple' => false, 'expanded' => false));
   
   $this->validatorSchema['hosts'] = new sfValidatorPropelChoice(array('model' => 'Host', 'multiple' => true));
   $this->validatorSchema['pxe'] = new sfValidatorPropelChoice(array('model' => 'PxeFile'));

    $this->widgetSchema->setLabels (array (
      'hosts' => 'Machines concernées',
      'pxe' => 'Fichier PXE à ajouter',
    ));

    $this->widgetSchema->setNameFormat('add_pxe[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::configure();
  }

  public function getHosts ()
  {
    return HostQuery::create ()->filterById ($this->getValue('hosts'))->find();
  }

  public function getPxe()
  {
    return PxeFileQuery::create()->findPk($this->getValue('pxe'));
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

  public function getImage ()
  {
    return ImageQuery::create()->findPk($this->getValue('image'));
  }
}
