#!/bin/bash

# used to get pdf or svg off a webpage in one go

# test: mlkonline.net/images.html

ALLOWED=pdf,svg,png,gif,jpg,jpeg
SITE=
OPTS="-r -H"
# OPTS="-nd -r -l1 -p -np"

if [ -n "$2" ];
then :
    ALLOWED="$2"
fi

if [ -n "$1" ];
then :
    SITE="$1"
else :
    exit 1;
fi


wget $OPTS -A "$ALLOWED" -e robots=off "$1"
