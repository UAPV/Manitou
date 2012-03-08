<?php

/**
 * Host filter form.
 *
 * @package    Arnaud Didry <arnaud.didry@univ-avignon.fr>
 * @subpackage filter
 * @author     Arnaud Didry <arnaud.didry@univ-avignon.fr>
 */
class HostFormFilter extends BaseHostFormFilter
{
  public function configure()
  {
    $this->manageFieldContain();
  }

  protected function manageFieldContain()
  {
    $this->widgetSchema ['contain'] = new sfWidgetFormInputText();
    $this->validatorSchema ['contain'] = new sfValidatorPass();
  }
}
