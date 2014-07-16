<?php

/**
 * room module configuration.
 *
 * @package    Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @subpackage room
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @version    SVN: $Id: configuration.php 12474 2008-10-31 10:41:27Z fabien $
 */
class roomGeneratorConfiguration extends BaseRoomGeneratorConfiguration
{
    public function getPagerMaxPerPage()
    {
        if ($max = sfContext::getInstance()->getUser()->getAttribute('room.max_per_page'))
        {
            if($max == "all")
                return parent::getPagerMaxPerPage();
            else
                return $max;
        }
        else
            return parent::getPagerMaxPerPage();
    }
}
