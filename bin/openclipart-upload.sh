#!/bin/bash

# This script helps you upload png, jpg or svg files to openclipart fast.

# add your own FROM to make sure that these don't get sent as jon@rejon.org's
# clipart files...but sure, I like the stats for my account to go up. I can
# take credit for your work sure :)

FROM=jon@rejon.org
TO=upload@openclipart.org

FROM_NAME="Jon Phillips"
HEADER="my_hdr From:$EMAIL;"

if [ -n "$4" ]; then TO="$4"; fi
# if [ -n "$5" ]; then FROM_NAME="$5"; fi

TITLE="$1"
SUMMARY="$2"
FILEPATH="$3"


if [ -n "$1" ]; then TITLE="$1"; else exit 1; fi
if [ -n "$2" ]; then SUMMARY="$2"; else exit 1; fi
if [ -n "$3" ]; then FILEPATH="$3"; else exit 1; fi


echo $FROM
echo $TO
echo $TITLE
echo $SUMMARY
echo $FILEPATH
echo ""

#FILE="IMG_20100818_145428.jpg"

CT=0
for FILE in $FILEPATH/*.{jpg,JPG,png,PNG,JPEG,jpeg,svg,SVG}; do \
    echo "$FILE" && echo -e "$SUMMARY" | mutt -e "$HEADER" -a "$FILE" -s "$TITLE $CT" -- "$TO" && sleep 2 && CT=$((CT+1)) ;
done;
