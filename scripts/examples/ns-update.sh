#!/bin/sh

#----------------------------Add--------------
# OPTION | ZONE | REVERSE ZONE |  HOST | IP

if [ $1 = 'add' ];then

   echo "--> DNS Add host "$4
   nsupdate -k rndc.key -v << eof

   server 10.5.0.11
   update add $4.$2 86400 A $5
   show
   send
eof
   echo "--> DNS Add host for reverse "$4
   nsupdate -k rndc.key -v << eof

   server 10.5.0.11
   update add $3 86400 PTR $4.$2
   show
   send
eof
fi

#-------------------------------Delete ----------
# OPTION | ZONE | REVERSE ZONE | IP

if [ $1 = 'delete' ];then

   host $4
   if [ $? = '0' ];then

   hostname=`host $4 | cut -d " " -f5 | cut -d "." -f1`

   echo "--> DNS Delete host "$hostname
   nsupdate -k rndc.key -v << eof

   server 10.5.0.11
   update delete $hostname.$2 IN A $4
   show
   send
eof
   echo "--> DNS Delete host for Reverse"$hostname
   nsupdate -k rndc.key -v << eof

   server 10.5.0.11
   update delete $3 PTR $hostname.$2
   show
   send
eof
fi
fi


if [ $1 = 'help' ] ;then

echo "USAGE : "
echo "ADD : ZONE | REVERSE ZONE | HOST | IP "
echo "DELETE : OPTION | ZONE | HOST "
echo "Exemple : ./do-update.sh delete univ-avignon.fr 206.163.83.195.in-addr.arpa 195.83.163.206: "
echo "Exemple :./do-update.sh add univ-avignon.fr 206.163.83.195.in-addr.arpa blabla1 195.83.163.206 "
fi
