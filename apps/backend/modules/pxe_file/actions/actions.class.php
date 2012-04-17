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
	     $this->getUser()->setFlash('notice', 'Le fichier PXE a été ajouté aux machines');
         $this->redirect ('command_list');
       }
     }  
  }

  public function saveAndReload($form)
  {
    $hosts = $form->getHosts();
    $file = $form->getPxe();
    $finalFile = array();

    foreach($hosts as $host)
    {
      $host->setPxeFileId($file->getId());
      $host->save();
      $filename = 'db.'.$host->getDomainName ();

      if (! array_key_exists($filename, $finalFile))
      {
        $finalFile[$filename] = $filename;
      }

      $ipBase = $host->getSubnet ()->getIpAddress();
      $ipBase = substr ($ipBase, 0, strpos ($ipBase, '.0'));
      $filename = 'db.'.$ipBase;
      if (! array_key_exists($filename, $finalFile))
          $finalFile [$filename] = $filename;
    }

    $finalFile = implode(" ", $finalFile);

    //on la fin, on lance la commande pour mettre a jour le dhcp
    CommandPeer::runDhcpdUpdate ();

    //on met a jout la conf dns
    CommandPeer::runDnsUpdate($finalFile);
  }
}
