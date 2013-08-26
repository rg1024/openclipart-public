#!/bin/bash
mv "$1" "$1.ISO8859_1"
cat "$1.ISO8859_1" | sed -e 's/encoding="ISO-8859-1"/encoding="UTF-8"/i' | iconv -f ISO-8859-1 -t UTF-8 > "$1"
ls -la "$1.ISO8859_1" "$1"
