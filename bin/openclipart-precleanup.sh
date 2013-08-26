#!/bin/bash

# precleanup before upload

echo $PWD


# for file in *.{bmp,BMP}; do convert "$file" ${file%.*}.jpg && rm "$file"; done

for file in *.*;
do
    mv "$file" "$RANDOM-$file" && \
    mv -i "$file" "$(dirname "$file")/$(basename "${file// /_}")";
done
