#!/bin/sh

#echo -e "rcw578\nrcw578" | smbpasswd -s -a s1041
echo -e "$2\n$2" | smbpasswd -s -a $1 
