<?php

require_once dirname(__FILE__).'/../lib/roomGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/roomGeneratorHelper.class.php';

/**
 * room actions.
 *
 * @package    Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @subpackage room
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class roomActions extends autoRoomActions
{
    public function executeSetMaxPerPage(sfWebRequest $request)
    {
        $this->getUser()->setAttribute('room.max_per_page', $max = $request->getParameter('max'));
        $this->redirect('@room');
    }
}
