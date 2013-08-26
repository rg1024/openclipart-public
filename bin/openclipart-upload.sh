#!/bin/bash

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
for FILE in $FILEPATH/*.{jpg,JPG,png,PNG,JPEG,jpeg}; do \
    echo "$FILE" && echo -e "$SUMMARY" | mutt -e "$HEADER" -a "$FILE" -s "$TITLE $CT" -- "$TO" && sleep 2 && CT=$((CT+1)) ;
done;
