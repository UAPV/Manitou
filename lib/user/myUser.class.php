<?php

class myUser extends uapvBasicSecurityUser
{
  public function __toString ()
  {
    return $this->getProfileVar('fullname', '');
  }

  /**
   * Configuration des credentials et du profil par défaut à la connexion de l'utilisateur
   *
   * @return void
   */
  public function configure ()
  {
    if (strpos ($this->getProfileVar ('supannaffectation'), 'D.O.S.I.') === 0 || $this->getProfileVar ('uid') == 'fouad')
      $this->addCredential ('dosi');

    if (in_array($this->getProfileVar ('uid'), array('juanedam', 'charpenm', 'tranv', 'marcelf'))) {
      $this->addCredential('infra');
    }

    if (in_array($this->getProfileVar ('uid'), array('juanedam', 'charpenm', 'charpinj', 'marcelf'))) {
      $this->addCredential('superadmin');
    }
  }
}
