#!/bin/bash
#
# Script de création d'image système avec DRBL
#
# Usage :
#   create-image.sh [host_ip] [host_mac] [interface] [image_server] [state] [image_filename]
#                       $1       $2          $3            $4          $5         $6

host_ip=$1
host_mac=$2
interface=$3
image_server=$4
state=$5
image_filename=$8

command='sudo /opt/drbl/sbin/drbl-ocs -b -q2 -rm-win-swap-hib -fsck-src-part -z0 -i 0'

# Etat des machines post-restauration
if [[ $state == 'wait' ]]; then
    command="$command -p true"
elif [[ $state == 'poweroff' ]]; then
    command="$command -p poweroff"
elif [[ $state == 'reboot' ]]; then
    command="$command -p reboot"
fi

# Hôtes à restaurer
command="$command -h $host_ip"

# LANG
command="$command -l fr_FR.UTF-8"

# Partition & co
command="$command -sc startdisk save $image_filename sda"

echo "COMMANDE DRBL > $command"

command="$command  < /dev/null"

# sudo -u drbl ssh manitou@$image_server '
#   sudo /usr/local/bin/drbl-ocs stop ; \
#   sudo etherwake -i $interface host_mac  ; \
#   $command'
