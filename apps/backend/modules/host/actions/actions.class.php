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
  public function preExecute()
  {
    $this->dispatcher->connect('admin.save_object', array ($this, 'updateDhcp'));
    return parent::preExecute ();
  }

  public function updateDhcp ()
  {
    HostQuery::create ()->find (); // Juste pour remplir le cache de propel
    $subnets = SubnetQuery::create ()->find ();

    $confPath = sfConfig::get('sf_manitou_dhcpd_conf_path');
    foreach ($subnets as $subnet) {
        $conf = $this->getPartial ('dhcpdSubnet.conf', array ('subnet' => $subnet));
        $filename = $confPath.'/'.$subnet->getName().'.conf';
        file_put_contents ($filename, $conf);
    }

    $script = 'cd '.$confPath.'; svn add *.conf; svn commit '
        .' --no-auth-cache'
        .' --non-interactive'
        .' --username '.sfConfig::get('sf_manitou_svn_username')
        .' --password '.sfConfig::get('sf_manitou_svn_password')
        .' -m "manitou update"';

    $command = new Command();
    $command->setCommand($script);
    $command->setUserId ('foobarhost'); // TODO
    $command->save();
    $command->backgroundExec ();
  }

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
    $this->redirect ($this->getContext()->getRouting()->generate('image_restore', array ('ids' => $ids)));
  }
}
