<?php

/**
 * Permet de valider les attributs d'un Host. L'host modifié ou créé doit être passé en paramètre afin de savoir
 * ses anciens attributs ou s'il est nouveau.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Arnaud Didry
 * @version    SVN: $Id: sfValidatorString.class.php 12641 2008-11-04 18:22:00Z fabien $
 */
class sfValidatorMacAddress extends sfValidatorString
{
  /**
   * Si l'option multiple est à true, le champs pourra avoir plusieurs adresses mac, séparées par des virgules
   * ou des retours à la ligne.
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);
    $this->addOption('multiple', true);
    $this->addOption('masse', false);
    $this->setOption('required', true);
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    parent::doClean($value);

    $pattern = '/^\s*[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}\s*$/i';
    $matches = array ();

    if ($this->getOption('multiple') && !$this->getOption('masse'))
    {
      if (preg_match_all ($pattern.'m', $value, $matches) < 1)
        throw new sfValidatorError($this, 'Format "%value%" des adresses MAC incorrect', array('value' => $value));

     $matches = $matches[0];
    }
    elseif ($this->getOption('multiple') && $this->getOption('masse'))
      {
          if (preg_match_all ($pattern.'m', $value, $matches) < 1)
              throw new sfValidatorError($this, 'Format "%value%" des adresses MAC incorrect', array('value' => $value));

          var_dump($matches);
          return $matches;
      }
    else
    {
      if (preg_match ($pattern, $value, $matches) !== 1)
        throw new sfValidatorError($this, 'Format "%value%" de l\'adresse MAC incorrect', array('value' => $value));
    }

      var_dump($matches);die;
    return $matches[0];
  }
}
