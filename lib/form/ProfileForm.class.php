<?php

/**
 * Profile form.
 *
 * @package    Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @subpackage form
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 */
class ProfileForm extends BaseProfileForm
{
  public function configure()
  {
    unset($this['created_at']);
  }
}
