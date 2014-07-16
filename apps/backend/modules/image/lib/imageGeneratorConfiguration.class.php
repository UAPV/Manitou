<?php

/**
 * image module configuration.
 *
 * @package    DRBL Admin 2
 * @subpackage image
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @version    SVN: $Id: configuration.php 12474 2008-10-31 10:41:27Z fabien $
 */
class imageGeneratorConfiguration extends BaseImageGeneratorConfiguration
{
    public function getPagerMaxPerPage()
    {
        if ($max = sfContext::getInstance()->getUser()->getAttribute('image.max_per_page'))
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
