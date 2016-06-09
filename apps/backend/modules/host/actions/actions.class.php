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
  * Action appelée par le biais du menu déroulant pour ajouter un fichier PXE sur plusieurs machines en un coup
  */
  public function executeBatchAddPxe(sfWebRequest $request)
  {
    $ids = $request->getParameter('ids');;
    $this->redirect ($this->getContext()->getRouting()->generate('add_pxe', array ('ids' => $ids)));
  }

  /**
   * Action appelée par le biais du menu déroulant pour exporter un CSV avec les machines sélectionnées
   */
  public function executeBatchCsvExport(sfWebRequest $request)
  {
    $ids = $request->getParameter('ids');
    $this->redirect ($this->getContext()->getRouting()->generate('csv_export', array ('ids' => $ids)));
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
          {
             $data['host'] = $host->getHostname();
             $data['have'] = true;
          }
          else
             $data['have'] = false;
      }
      else
      {
          $ip = implode('.', array_reverse( explode('.', $ip)));
          if(dns_check_record ($ip.'.in-addr.arpa', 'PTR') > 0)
          {
            $data['have'] = true;
            $valDns = dns_get_record($ip.'.in-addr.arpa');
            $data['host'] = $valDns[0]['target'];
          }
          else
            $data['have'] = false;
      }

      return $this->returnJSON($data);
  }

  public function executeInDnsHostname(sfWebRequest $request)
  {
     $profileId = $request->getParameter('profile');
     $roomId = $request->getParameter('room');
     $suffixe = $request->getParameter('suffixe');

     $profile = ProfileQuery::create()
                ->findPk($profileId);
     $room = RoomQuery::create()
                ->findPk($roomId);

     $hostname = $profile->getName().'-'.$room->getName().'-'.$suffixe.".univ-avignon.fr";

     if(dns_check_record ($hostname, 'A'))
         $data['have'] = true;
     else
       $data['have'] = false;

     return $this->returnJSON($data);
  }

  public function executeCommentProfil(sfWebRequest $request)
  {
      $profileId = $request->getParameter('profile');
      $data = array();

      $profile = ProfileQuery::create()
          ->findPk($profileId);

      $data['profil'] = $profile->getComment();

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

  public function executeSetMaxPerPage(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('host.max_per_page', $max = $request->getParameter('max'));
    $this->redirect('@host');
  }

	protected function processForm(sfWebRequest $request, sfForm $form)
	{
		$form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
		if ($form->isValid())
		{
			$commentSVN = $form->getValue('commentSvn');
            $ldap = $form->getValue('ldap');
            $suffixe = $form->getValue('number');
            $ip = $form->getValue('ip_address');
            $mac = $form->getValue('mac_address');
            $commentaire = $form->getValue('comment');

			$notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

			Host::setCommentSvn($commentSVN);
			$Host = $form->save();
            $Host->setNumber(trim($suffixe));
            $Host->setIpAddress(trim($ip));
            $Host->setMacAddress(trim($mac));
            $Host->setComment(trim($commentaire));
            $Host->save();

            //On veut ajouter/supprimer la machine dans le ldap
            if(is_array($ldap) && $ldap[0] == 1)
            {
              CommandPeer::getLdapCommand('a',$Host->getHostname());
            }
            else
            {
              CommandPeer::getLdapCommand('d',$Host->getHostname());
            }

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
     * Récupère les ids des hosts passés en paramètres et les écrit dans un CSV
     */
    public function executeExport (sfWebRequest $request)
    {
        $ids = $request->getParameter ('ids');
        $hosts = HostQuery::create()->filterById ($ids)->find ();
        $fp = fopen(sfConfig::get('sf_root_dir').'/web/hosts.csv', 'w');
        fputcsv($fp, array('Nom','IP','Mac','Subnet','Commentaires', 'LDAP'));

        //Pour chaque machine, on écrit la ligne dans le CSV
        foreach ($hosts as $host) {
            $ligne = array($host->getHostname(),$host->getIpAddress(),$host->getMacAddress(),$host->getSubnet(),$host->getComment(), 0);
            fputcsv($fp, $ligne);
        }

        fclose($fp);

        header('Content-Type: application/octet-stream');
        header('Content-disposition: attachment; filename=hosts.csv');
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        readfile(sfConfig::get('sf_root_dir').'/web/hosts.csv');
        exit();

        return sfView::NONE;
    }

    /**
     * Récupère le fichier importé + ajoute les machines après vérification
     */
    public function executeImport (sfWebRequest $request)
    {
        $file = $request->getFiles()['file'];
        $sourcePath = $file['tmp_name'];

        $erreur = false;
        $row = 1;
        $salles = RoomQuery::create()->find();
        foreach($salles as $s)
            $array['salle'][] = trim($s->getName());

        $pro = ProfileQuery::create()->find();
        foreach($pro as $p)
            $array['profile'][] = trim($p->getName());

        $sub = SubnetQuery::create()->find();
        foreach($sub as $su)
            $array['subnet'][] = trim($su->getName());

        $message = "--- ERREURS ---\n\r\n\r";
        if (($handle = fopen($sourcePath, "r")) !== FALSE)
        {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
            {
                //On passe la première ligne
                if($row == 1)
                {
                    $nbLignes = count($data);
                    if($nbLignes < 5)
                        return $this->renderText(json_encode(array('message' => 'Le CSV donné ne comporte pas les 5 colonnes requises', 'erreur' => true)));
                    else
                        $row++;
                }
                else
                {
                    $row++;

                    //On lit une machine
                    $nom = $data[0];
                    $ip = $data[1];
                    $mac = $data[2];
                    $subnet = $data[3];
                    $comm = $data[4];
                    $ldap = $data[5];

                    // 1 => On teste l'existence du profil
                    $tmp = explode('-', $nom);
                    if(!in_array(trim($tmp[0]), $array['profile']))
                    {
                        $message .= "Le profil ".trim($tmp[0])." n'existe pas sur la ligne ".$row."\n\r";
                        $erreur = true;
                    }

                    // 2 => On teste l'existence de la salle
                    if(!in_array(trim($tmp[1]), $array['salle']))
                    {
                        $message .= "La salle ".trim($tmp[1])." n'existe pas sur la ligne ".$row."\n\r";
                        $erreur = true;
                    }

                    // 3 => On teste l'existence du subnet
                    if(!in_array(trim($subnet), $array['subnet']))
                    {
                        $message .= "Le subnet ".trim($subnet)." n'existe pas sur la ligne ".$row."\n\r";
                        $erreur = true;
                    }

                    // 4 => On teste la non-existence de l'IP
                    $host = HostQuery::create()->findOneByIpAddress(trim($ip));
                    if(count($host) > 0)
                    {
                        $message .= "L'IP ".trim($ip)." existe déjà dans la base (ligne ".$row.")"."\n\r";
                        $erreur = true;
                    }

                    // 5 => On teste la non-existence de la MAC
                    $host = HostQuery::create()->findOneByMacAddress(trim($mac));
                    if(count($host) > 0)
                    {
                        $message .= "L'adresse MAC ".trim($mac)." existe déjà dans la base (ligne ".$row.")"."\n\r";
                        $erreur = true;
                    }

                    // 6 => On teste la non-existence du hostname
                    if(isset($tmp[2]))
                        $host = HostQuery::create()->findByHostname(trim($nom));
                    else
                    {
                        $t = explode('.',$ip);
                        $host = HostQuery::create()->findByHostname(trim($nom) . '-' .$t[3]);
                    }

                    if(count($host) > 0)
                    {
                        $message .= "Le hostname ".trim($nom)." existe déjà dans la base (ligne ".$row.")"."\n\r";
                        $erreur = true;
                    }

                    // 7 => Tout est ok pour ce host, on garde les infos dans le tableau
                    $profileObj = ProfileQuery::create()->findOneByName(trim($tmp[0]));
                    $roomObj = RoomQuery::create()->findOneByName(trim($tmp[1]));
                    $subnetObj = SubnetQuery::create()->findOneByName(trim($subnet));

                    if(!$erreur)
                    {
                        if (!isset($tmp[2])) {
                            $t = explode('.', $ip);
                            $number = $t[3];
                        } else
                            $number = $tmp[2];

                        if($ldap == '1')
                            $ldap = true;
                        else
                            $ldap = false;

                        $dataFinal[] = array('cn' => trim($nom), 'profile_id' => $profileObj->getId(), 'room_id' => $roomObj->getId(), 'number' => trim($number), 'ip_address' => $ip, 'mac_address' => $mac, 'comment' => $comm, 'subnet_id' => $subnetObj->getId(), 'ldap' => $ldap);
                    }
                    else
                        $message .= "\n\r";
                }
            }

            // Si on a rencontré une erreur, on affiche les messages d'erreur
            if($erreur)
                return $this->renderText(json_encode(array('message' => $message, 'erreur' => true)));

            fclose($handle);
        }

        // Tout est ok, on ajoute les machines dans la base
        foreach($dataFinal as $host)
        {
            $h = new Host();
            $h->setProfileId(trim($host['profile_id']));
            $h->setRoomId(trim($host['room_id']));
            $h->setNumber(trim($host['number']));
            $h->setIpAddress(trim($host['ip_address']));
            $h->setMacAddress(trim($host['mac_address']));
            $h->setComment(trim($host['comment']));
            $h->setSubnetId(trim($host['subnet_id']));
            $h->save();

            //On ajoute la machine dans le LDAP
            if($host['ldap'])
                CommandPeer::getLdapCommand('a', $host['cn']);
        }

        //On relance l'ajout dans le DNS + DHCP
        //CommandPeer::runDnsUpdate();

        return $this->renderText(json_encode(array('message' => 'Import bien passé !', 'erreur' => false)));
    }
}
