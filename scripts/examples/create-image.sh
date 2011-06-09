#!/bin/bash
#
# Usage :
#   create-image.sh [host_ip] [host_mac] [interface] [image_server] [restart_after] [image_filename]
#                       $1       $2          $3            $4             $5               $6

# sudo -u drbl ssh manitou@$4 '
#   sudo /usr/local/bin/drbl-ocs stop ; \
#   sudo etherwake -i $3 $2  ; \
#   sudo /opt/drbl/sbin/drbl-ocs -b -q2 -rm-win-swap-hib -fsck-src-part -p $5 -z0 -i 0 -h $1 -l fr_FR.UTF-8 -sc startdisk save $6 sda < /dev/null'
