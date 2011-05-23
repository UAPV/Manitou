<?php

/**
 * Host form.
 *
 * @package    Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @subpackage form
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 */
class HostForm extends BaseHostForm
{
  public function configure()
  {
    unset ($this['created_at']);
    unset ($this['updated_at']);

  }
}
