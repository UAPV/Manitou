#!/bin/bash

# Script utilisé pour exécuter les scripts SQL de migration de la 
# base de données de prod. 

# PREREQUIS :
#   * la bdd de prod doit être accessible depuis web00c
#   * le fichier config/databases.yml doit déjà être un lien symbolique

rm config/databases.yml
ln -s databases-prod.yml config/databases.yml
sudo -u www-data symfony-cc.sh manitou-test

./symfony propel:migrate

rm config/databases.yml
ln -s databases-test.yml config/databases.yml
sudo -u www-data symfony-cc.sh manitou-test



