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
    //$this->widgetSchema['hostname'] = new sfWidgetFormInputText();

    unset ($this['created_at']);
    unset ($this['updated_at']);
    
    $this->widgetSchema->setLabels (array (
      'hostname'             => 'Nom',
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
	  'commentSvn'			 => 'Commentaires SVN',
      'ldap'                 => 'Poste authentifié ?'
    ));

    $this->widgetSchema->setHelps (array (
      'hostname'             => 'Nom de l\'hôte',
      'profile_id'           => 'Profil de l\'utilisateur',
      'room_id'              => 'Salle où se situe la machine',
      'number'               => "Mettre le numéro de prise, si ce champ n'est pas renseigné, ce sera le dernier numéro de l'IP",
      'ip_address'           => 'Adresse IP',
      'mac_address'          => 'Adresse MAC (format 00:11:22:33:44:55)',
      'comment'              => 'Commentaire (ex: numéro de prise, etc)',
      'custom_conf'          => 'Conf DHCP personnalisée',
      'cloned_from_image_id' => 'Dernière image système appliquée',
      'ldap'                 => 'Ajouter la machine au domaine',
      'pxe_file_id'          => 'Fichier PXE (par défaut celui configuré pour le subnet sera utilisé)',
	  'commentSvn'			 => 'Commentaire affiché lors du commit SVN'
    ));


    if($this->getObject()->inLdap())
    {
      $this->widgetSchema['ldap'] = new sfWidgetFormChoice(array('expanded' => true, 'multiple' => true, 'choices' => array(1 => '')));
      $this->setDefault('ldap',1);
    }
    else
      $this->widgetSchema['ldap'] = new sfWidgetFormChoice(array('expanded' => true, 'multiple' => true, 'choices' => array(1 => '')));

    $this->validatorSchema['ldap'] = new sfValidatorChoice(array('multiple'=>true,'choices' => array(1),'required'=>false));


    $this->widgetSchema['cloned_from_image_id'] = new sfWidgetFormPropelChoice (array('model' => 'Image', 'order_by' => array('Filename','asc')));
    $this->widgetSchema['cloned_from_image_id']->setLabel('Image système');

    $this->widgetSchema['profile_id'] = new sfWidgetFormPropelChoice(array('model' => 'Profile', 'order_by' => array('Name','asc'), 'add_empty' => true));

	$this->widgetSchema['commentSvn'] = new sfWidgetFormInputText(array('label' => 'Commentaire SVN'));
    $this->setValidator ('ip_address', new sfValidatorIpAddress(array('required' => true)));
    $this->setValidator ('mac_address', new sfValidatorMacAddress(array('required' => true)));
	$this->setValidator('commentSvn', new sfValidatorString(array('required' => false)));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        $this->validatorSchema->getPostValidator(),
        new sfValidatorHost(array('host_object' => $this->getObject()))
      ))
    );
  }
}
  
