
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

Comment sont gérés les machines, quelles conventions sont utilisées ?
---------------------------------------------------------------------

Les machines sont organisées par Salle, Profil (étudiant, administratif, etc) et Subnet.

Leur hostname est au format `[profil]-[salle]-[suffixe]`. Par convention le suffixe correspond
au derniers octet de l'IP de la machine, mais ce paramètre reste modifiable lors de l'éditiond d'une machine.

Quelles commandes sont exécutées ? quand et comment ?
-----------------------------------------------------

Voir le fichier [/config/settings.yml](https://github.com/UAPV/Manitou/blob/master/config/settings.yml.example)

Les commandes sont exécutées à chaque modification du modèle par le biais des hook Propel (postSave, preSave, etc.).
Les classes concernées sont celles susceptibles de modifier directement ou indirectement les informations réseau d'une
machine; il s'agit principalement des classes :

* [Host](https://github.com/UAPV/Manitou/blob/master/lib/model/Host.php)
* [Subnet](https://github.com/UAPV/Manitou/blob/master/lib/model/Subnet.php)
* [Room](https://github.com/UAPV/Manitou/blob/master/lib/model/Room.php)

Lorsqu'un modification est détectée, l'exécution des commandes est délégué à la classe
[CommandPeer](https://github.com/UAPV/Manitou/blob/master/lib/model/CommandPeer.php). Cette classe
se charge de lancer dans 90% des cas des scripts en arrière plan (à l'aide de la commande `nohup` et du `&`)
afin de ne pas bloquer l'exécution de la page. La sortie standart, d'erreur et le code de retour, sont
redirigées vers des fichiers tampons afin d'obtenir l'état des commandes en temps réel, même si
celles-ci sont exécutées en arrière plan.

Les chemins vers ces fichiers tampons sont mis en base de données par la classe [Command](https://github.com/UAPV/Manitou/blob/master/lib/model/Command.php)
et sont régulièrement interrogés par le module [command](https://github.com/UAPV/Manitou/blob/master/apps/backend/modules/command/actions/actions.class.php).

Lorsque ce module détecte la complétion d'une commande (en vérifiant le fichier tampon contenant le code de retour) la classe met
automatiquement le contenu des fichiers tampon en base de données et puis les supprime du système.

Pour plus de détails sur l'exécution d'une commande, voir la méthode [Command::exec](https://github.com/UAPV/Manitou/blob/master/lib/model/Command.php).




Comment modifier les arguments des commandes exécutées ?
--------------------------------------------------------

Dans la plupart des cas la classe [CommandPeer](https://github.com/UAPV/Manitou/blob/master/lib/model/CommandPeer.php)
centralise la configuration des commandes. Cette classe utilise par contre la classe [Command](https://github.com/UAPV/Manitou/blob/master/lib/model/Command.php)
pour spécifier la valeur des arguments d'une commande (voir la méthode `Command::setArgument()`)

Les arguments sont définis dans le fichier [/config/settings.yml](https://github.com/UAPV/Manitou/blob/master/config/settings.yml.example)
à l'aide des délimiteurs `%`. NB : Lors de la substitution d'un argument sa valeur est automatiquement échappée pour ne pas interférer 
involontairement avec le shell : ne pas "quoter" les arguments dans le fichier `settings.yml`.

Comment la conf du DHCP est elle intégrée ?
-------------------------------------------

Un fichier de configuration de bind est généré par subnet. Ils sont écrasés à chaque regénération.

Pour des modifications exceptionnels, il suffit d'utiliser les attributs `custom_conf` du subnet ou d'un host.

Mais s'il s'agit d'une modification plus profonde, il est possible de modifier le template
utilisé [`_subnet.conf.php`](apps/backend/modules/dhcpd/templates/_subnet.conf.php) (Ce template est un simple 'partial' _ou vue_ symfony.


Comment fonctionne l'intégration du DNS ?
-----------------------------------------

Contrairement à la conf du DHCP, celle du DNS est intégrée aux fichiers existants du DNS. Manitou les nouvelles entrées en les plaçant entre les balises
`MANIOU_CONF_BEGIN` et `MANITOU_CONF_END`. Attention, toute modification manuelle à l'intérieur de ces balises sera écrasée lors d'une prochaine 
mise à jour de la conf.

Une fois la mise à jour du fichier terminée, le serial du fichier est mise à jour en suivant le format YYYYMMDDXX (où XX est un nombre incrémenté à
chaque modification d'une même journée).

Comment sont gérés les conflits entre Manitou et la configuration existante du DNS ?
------------------------------------------------------------------------------------

Il peut arriver que des conflits se présentent entre les machines enregistrées dans manitou et les entrée existante du DNS. Plusieurs cas
peuvent se présenter :

* Si le type d'entrée, l'IP et l'hostname sont strictement identiques, l'ancienne entrée du DNS est mise en commentaire et un tag `MARKED FOR DELETION` est 
  ajouté sur la même ligne afin de pouvoir supprimer aisément ces lignes manuellement. Manitou ajoute ensuite l'entrée en ses balise BEGIN et END.
* Pour tout autre conflt, l'entrée qui allait être ajoutée est mise en commentaire avec le Tag `MANITOU_ERROR`.

Comment modifier la génération de la conf DNS ?
-----------------------------------------------

La génération du DNS passe pas la classe [Dns](https://github.com/UAPV/Manitou/blob/master/lib/model/Dns.php).


À propos
========

GPL3

Ce logiciel est en développement, à utiliser à vos risques et périls.



