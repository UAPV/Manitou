<?php

require_once dirname(__FILE__).'/../lib/hostGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/hostGeneratorHelper.class.php';

/**
 * host actions.
 *
 * @package    Manitou
 * @subpackage host
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 */
class hostActions extends autoHostActions
{
  /**
   * Action exéctutée lors d'un clic sur le liens "créer une image"
   */
  public function executeListCreateImage(sfWebRequest $request)
  {
    $host = $this->getRoute()->getObject();
    $this->redirect ('@image_new?host_id='.$host->getId());
  }

  public function processForm(sfWebRequest $request, sfForm $form)
  {
      $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
      if ($form->isValid())
      {
         $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';
         $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $Host)));

         if ($request->hasParameter('_save_and_add'))
         {
            $this->getUser()->setFlash('notice', $notice.' You can add another one below.');
            $this->redirect('@host_new');
         }
         else
         {
            $this->getUser()->setFlash('notice', $notice);
            $this->redirect(array('sf_route' => 'host_edit', 'sf_subject' => $Host));
         }
      }
      else
      {
          $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
      }
  }

  /**
   * Action appelée par le biais du menu déroulant sur la liste des machines
   */
  public function executeBatchRestore(sfWebRequest $request)
  {
    $ids = $request->getParameter('ids');
    $hosts = HostQuery::create()->findById($ids);
    $message = false;
    $dataDns = array();
    $dataLdap = array();

    foreach($hosts as $host)
    {
       //on regarde si les hotes spécifiés sont dans le dns ou non
       if(!$host->hasDnsRecord())
       {
           $dataDns[] = $host->getHostname();
           $message = false;
       }
    }
    $dataDns = implode(',', $dataDns);

    $ldap = new uapvLdap();
    $this->getContext()->set('ldap', $ldap);
    foreach($hosts as $host)
    {
       //on regarde dans le ldap si l'hote y est
        $data = $ldap->search('cn='.$host->getHostname());
        var_dump($data);die;
        if(count($data) == 0)
        {
            $dataLdap[] = $host->getHostname();
            $message = false;
        }
    }
    $dataLdap = implode(',', $dataLdap);

    if($message)
      $this->getUser()->setFlash('notice','Le(s) hôte(s) <b>'.$dataDns.'</b> n(e) est(sont) pas dans le DNS<br/>Le(s) hôte(s) <b>'.$dataLdap.'</b> n(e) est(sont) pas dans le LDAP');

    $this->redirect ($this->getContext()->getRouting()->generate('image_restore', array ('ids' => $ids)));
  }

 /**
  * Action appelée par le biais du menu déroulant pour ajouter un fichier PXE sur plusieurs machines en un c  * oup
  */
  public function executeBatchAddPxe(sfWebRequest $request)
  {
    $ids = $request->getParameter('ids');;
    $this->redirect ($this->getContext()->getRouting()->generate('add_pxe', array ('ids' => $ids)));
  }

  public function executeStatus ()
  {
    $host = $this->getRoute()->getObject();

    $data = $host->toArray ();
    $data['status'] = $host->isRunning () ? 1 : 0;

    return $this->returnJSON($data);
  }

  /**
   * Action appelée par le biais du menu déroulant sur la liste des machines
   */
  public function executeMassCreate(sfWebRequest $request)
  {
    $this->form = new MultipleHostForm();

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->form->save();

        $msg = 'Machines créées avec succès : ';
        foreach ($this->form->getHosts() as $host) 
          $msg .= $host.' '.$host->getIpAddress().' '.$host->getMacAddress().', ';


        $this->getUser()->setFlash('notice', $msg);
        $this->redirect('@host');
      }
      else
      {
        $this->getUser()->setFlash('error', 'Création échouée.', false);
      }
    }
  }

  public function executeInDns(sfWebRequest $request)
  {
      $ip = $request->getParameter('ip');
      $host = HostQuery::create()->findOneByIpAddress($ip);

      if(count($host) > 0)
      {
          if($host->hasDnsRecord())
             $data['have'] = true;
          else
             $data['have'] = false;
      }
      else
          $data['have'] = false;

      return $this->returnJSON($data);
  }

  public function executeInDnsHostname(sfWebRequest $request)
  {
     $profile = $request->getParameter('profile');
     $room = $request->getParameter('room');
     $suffixe = $request->getParameter('suffixe');

     $hostname = $profile.'-'.$room.'-'.$suffixe;



     if(count($host) > 0)
       $data['have'] = true;
     else
       $data['have'] = false;

     return $this->returnJSON($data);
  }

  /**
   * Return in JSON when requested via AJAX or as plain text when requested directly in debug mode
   *
   */
  public function returnJSON($data)
  {
    $json = json_encode($data);

    if (sfContext::getInstance()->getConfiguration()->isDebug () && !$this->getRequest()->isXmlHttpRequest()) {
      $this->getContext()->getConfiguration()->loadHelpers('Partial');
      $json = get_partial('global/json', array('data' => $data));
    } else {
      $this->getResponse()->setHttpHeader('Content-type', 'application/json');
    }

    return $this->renderText($json);
  }
}
