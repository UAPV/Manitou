<?php
require_once dirname(__FILE__).'/../lib/pxe_fileGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/pxe_fileGeneratorHelper.class.php';

/**
 * pxe_file actions.
 *
 * @package    Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @subpackage pxe_file
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class pxe_fileActions extends autoPxe_fileActions
{
  
  public function executeAdd (sfWebRequest $request)
  {
    $ids = $request->getParameter ('ids');
    $this->hosts = HostQuery::create()->filterById ($ids)->find ();
    $this->form = new AddPXEForm (array ('hosts' => $ids));

     if ($request->isMethod ('post'))
     {
       $this->form->bind($request->getParameter ($this->form->getName ()));
       if ($this->form->isValid ())
       {
         $this->saveAndReload($this->form);
         //on vérifie qu'en base ça ait enregistré
	 //on met a jour la conf dhcp
	   $this->getUser()->setFlash('notice', 'Le fichier PXE a été ajouté aux machines');
          // $this->redirect ('command_list');
       }
     }  
  }

  public function saveAndReload($form)
  {
    $hosts = $form->getHosts();
    $file = $form->getPxe();

    foreach($hosts as $host)
    {
      $host->setPxeFileId($file->getId());
      $host->save();
    }
  }
}
