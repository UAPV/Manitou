
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

    ./symfony propel:build-all

Initialiser le dépôt contenant la configuration des DHCP :

    svn checkout svn://svn.univ-avignon.fr/XXXX_DHCP data/dhcpdconf
    sudo chown www-data:www-data data/dhcpdconf -R

Initialiser le dépôt contenant la configuration DNS :

    svn checkout svn://svn.univ-avignon.fr/XXXX_DNS data/dnsconf
    sudo chown www-data:www-data data/dnsconf -R

Copier les scripts d'exemple et les éditer (vérifier les droits d'exécution) :

    cp scripts/examples/*.sh scripts/
    vi scripts/*

Personnaliser la commandes exécutées, etc : `config/settings.yml`.

Configurer l'authentification en modifiant `apps/backend/config/app.yml`


Mise à jour
===========

Un script existe déjà sur le serveur de test (`update-project.sh`) et contient :

    #!/bin/bash
    git stash           # Enregistrement temporaire des modifs locales
    git pull            # récupération et intégration des modifs du dépôt
    git stash apply     # Application des modifs temporaires
    ./symfony propel:build --all-classes    # reconstruction des classes générées
    ./symfony propel:migrate                # Mise à jour de la base de données
    sudo /usr/local/bin/wwwize.sh log cache # chown www-data: log cache
    sudo -u www-data symfony-cc.sh manitou-test # symfony cc (RAZ du cache de symfony)


FAQ
===

Quelles commandes sont exécutées ? quand et comment ?
-----------------------------------------------------

Voir le fichier [/config/settings.yml](https://github.com/UAPV/Manitou/blob/master/config/settings.yml.example)

Comment modifier les arguments des commandes exécutées ?
--------------------------------------------------------

Dans la plupart des cas la classe [CommandePeer](https://github.com/UAPV/Manitou/blob/master/lib/model/CommandPeer.php)
centralise la manipulation et la configuration des commandes.

Comment la conf du DHCP est elle intégrée ?
-------------------------------------------



Comment modifier le template utilisé pour la génération de la conf DHCP ?
-------------------------------------------------------------------------


Comment fonctionne l'intégration du DNS ?
-----------------------------------------

Comment modifier la génération de la conf DNS ?
-----------------------------------------------


À propos
========

GPL3

Ce logiciel est en développement, à utiliser à vos risques et périls.



