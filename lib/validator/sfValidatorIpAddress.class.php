<?php

/**
 * sfValidatorString validates an IP address
 *
 * @package    symfony
 * @subpackage validator
 * @author     Arnaud Didry
 * @version    SVN: $Id: sfValidatorString.class.php 12641 2008-11-04 18:22:00Z fabien $
 */
class sfValidatorIpAddress extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);
    $this->addMessage('ip_format', '"%value%" ne respecte pas le format d\'une adresse IP.');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    if (! preg_match_all("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" .
            "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $value, $matches))
    {
      throw new sfValidatorError($this, 'ip_format', array('value' => $value));
    }

    return $value;
  }
}
