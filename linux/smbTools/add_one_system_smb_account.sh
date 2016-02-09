#!/bin/sh

#usage:
#./command id passwdord
useradd -m -g teachers $1 -d /data/teachers/$1 -s /sbin/nologin
echo -e "$2\n$2" | passwd $1
echo -e "$2\n$2" | smbpasswd -s -a $1

#pdbedit -x $1
#userdel $1
