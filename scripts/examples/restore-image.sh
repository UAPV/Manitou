#!/bin/bash
#
# Script de restauration avec DRBL
#
# Usage :
#   restore-image.sh [hosts_ip] [hosts_mac] [interface] [image_server] [resize] [partition] [state] [image_filename] [pre_run] [post_run]
#                       $1          $2          $3            $4          $5        $6        $7           $8            $9         $10

hosts_ip=$1
hosts_mac=$2
interface=$3
image_server=$4
resize=$5
partition=$6
state=$7
image_filename=$8
pre_run=$9
post_run=$10

command='sudo /opt/drbl/sbin/drbl-ocs -b -g auto -e1 auto -x -r '


# resize
if [[ $resize = 1 ]]; then
    command="$command -k1"
fi

# PRE_RUN script
if [[ $pre_run = 1 ]]; then
    command="$command -o0"
fi

# POST_RUN script
if [[ $pre_run = 1 ]]; then
    command="$command -o1"
fi

# Etat des machines post-restauration
if [[ $state == 'wait' ]]; then
    command="$command -p true"
elif [[ $state == 'poweroff' ]]; then
    command="$command -p poweroff"
elif [[ $state == 'reboot' ]]; then
    command="$command -p reboot"
fi

# Timeout
clientToWait=$(echo $hosts_ip | sed "s/ /\n/g" | wc -l)
command="$command --clients-to-wait $clientToWait --max-time-to-wait 140 "

# Hôtes à restaurer
command="$command -h $hosts_ip"

# LANG
command="$command -l en_US"

# partition
if [[ $partition = 'sda' ]]; then
    command="$command startdisk multicast_restore $image_filename $partition"
else
    command="$command startparts multicast_restore $image_filename $partition"
fi

echo "COMMANDE DRBL > $command"

command="$command < /dev/null"

# sudo -u drbl ssh manitou@$3 <<eof
#
#    $command < /dev/null
#
#    for mac in $2; do
#       sudo etherwake -i $3 $mac
#    done
#    
#eof
