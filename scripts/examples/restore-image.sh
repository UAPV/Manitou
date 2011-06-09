#!/bin/bash
#
# Usage :
#   restore-image.sh [hosts_ip] [hosts_mac] [interface] [image_server] [restart_after] [image_filename]
#                       $1          $2          $3            $4             $5              $6


# sudo -u drbl ssh manitou@$3 <<eof
#
#    sudo /opt/drbl/sbin/drbl-ocs -b -g auto -e1 auto -x -r $script_pre $script_post $redim -p $action --clients-to-wait $nb_client --max-time-to-wait $time -h $1 -l en_US startdisk multicast_restore $image $partition < /dev/null
#
#    sudo /opt/drbl/sbin/drbl-ocs -b -g auto -e1 auto -x -r $script_pre $script_post $redim -p $action --clients-to-wait $nb_client --max-time-to-wait $time -h $1 -l en_US startparts multicast_restore $image $partition < /dev/null
#
#    for mac in $2
#       sudo etherwake -i $interface $mac
#    
#eof
