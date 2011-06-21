
Fonctionnalités
===============

* Gestion de l'architecture réseau d'un parc de machines
* Génération et versionning de conf DHCP & DNS
* Création et restauration d'images système en mode batch

Prérequis
=========

* fping
* svn


Install
=======

Récupérer les sources

    git clone https://github.com/UAPV/Manitou
    cd Manitou

Configurer la base de données en éditant `config/databases.yml` puis l'initialiser :

    ./symfony propel:build-all-load

Initialiser le dépôt contenant la configuration des DHCP :

    svn checkout svn://svn.univ-avignon.fr/XXXX_DHCP data/dhcpdconf
    sudo chown www-data:www-data data/dhcpdconf -R

Initialiser le dépôt contenant la configuration DNS :

    svn checkout svn://svn.univ-avignon.fr/XXXX_DNS data/dnsconf
    sudo chown www-data:www-data data/dnsconf -R

Personnaliser la commandes exécutées, la conf svn, etc : `config/settings.yml`.

Configurer l'authentification :

    TODO

À propos
========

GPL3

Ce logiciel est en développement, à utiliser à vos risques et périls.



