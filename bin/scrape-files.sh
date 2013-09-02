#!/bin/bash

# used to get pdf or svg off a webpage in one go

ALLOWED=pdf,svg,png,gif,jpg,jpeg
SITE=

if [ -n "$1" ];
then :
    ALLOWED="$1"
fi

if [ -n "$2" ];
then :
    SITE="$2"
else :
    exit 1;
fi


wget -nd -r -l1 -p -np -A "$ALLOWED" -e robots=off "$2"
