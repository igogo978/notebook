#!/bin/sh

pdbedit -x $1
userdel $1 -r
