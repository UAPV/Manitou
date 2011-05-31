
Fonctionnalités
===============

* Gestion de l'architecture réseau d'un parc de machine
* Génération de conf DHCP & DNS
* Création et restauration d'images système en mode batch

Install
=======

Récupérer les sources

    git clone https://github.com/UAPV/Manitou
    cd Manitou

Configurer la base de données en éditant `config/databases.yml` puis l'initialiser :

    ./symfony propel:build-all-load

Initialiser le dépôt contenant la configuration des dhcp :

    svn checkout svn://svn.univ-avignon.fr/dhcpd_conf/trunk data/dhcpdconf
    sudo chown www-data:www-data data/dhcpdconf

Personnaliser la commandes exécutées, la conf svn, etc : `config/settings.yml`.

Configurer l'authentification :

    TODO

À propos
========

GPL3

Ce logiciel est en développement, à utiliser à vos risques et périls.



