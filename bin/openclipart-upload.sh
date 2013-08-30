#!/bin/bash

# This script helps you upload png, jpg or svg files to openclipart fast.

# add your own FROM to make sure that these don't get sent as jon@rejon.org's
# clipart files...but sure, I like the stats for my account to go up. I can
# take credit for your work sure :)

USAGE="$0 \"Some Title\" \"Some Summary\" \"./my-file.pdf\" \"from@email.com\""

DEBUG=0

FROM=jon@rejon.org
TO=upload@openclipart.org
# TO=jon@rejon.org

FROM_NAME="Jon Phillips"


#TITLE="$1"
#SUMMARY="$2"
#FILE="$3"


if [ -n "$1" ]; 
then :
    TITLE="$1"; 
else : 
    echo "$USAGE"
    exit 1; 
fi

if [ -n "$2" ]; then SUMMARY="$2"; else exit 1; fi
if [ -n "$3" ]; 
then :
    FILE="$3";
    # should check here to make sure its a valid file type or error out
else : 
    exit 1; 
fi

if [ -n "$4" ]; then FROM="$4"; fi
#if [ -n "$5" ]; then FROM_NAME="$5"; fi

HEADER="my_hdr From:$FROM;"

if [ $DEBUG == 1 ]; 
then :
    echo "FROM: $FROM"
    echo "TO: $TO"
    echo "TITLE: $TITLE"
    echo "SUMMARY: $SUMMARY"
    echo "FILE: $FILE"
    exit 1
fi


#FILE="IMG_20100818_145428.jpg"

#CT=0
#for FILE in $FILEPATH/*.{jpg,JPG,png,PNG,JPEG,jpeg,svg,SVG}; do \
#    echo "$FILE" && echo -e "$SUMMARY" | mutt -e "$HEADER" -a "$FILE" -s "$TITLE $CT" -- "$FROM_NAME <$TO>" && sleep 2 && CT=$((CT+1)) ;
#done;

echo -e "$SUMMARY" | mutt -e "$HEADER" -a "$FILE" -s "$TITLE" -- "$TO"
