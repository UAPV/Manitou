#!/bin/bash

git stash
git pull
git stash apply
./symfony propel:build --all-classes
./symfony propel:migrate
sudo /usr/local/bin/wwwize.sh log cache 
sudo -u www-data symfony-cc.sh manitou-test

