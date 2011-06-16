#!/bin/bash

cd "$1"

svn update --accept mine-full
svn add *.conf -q
svn commit --no-auth-cache --non-interactive --username manitou --password m4n1t00 -m "manitou update"