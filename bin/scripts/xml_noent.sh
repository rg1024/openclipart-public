#!/bin/bash
mv "$1" "$1.before_noent"
xmllint --noent "$1.before_noent" > "$1"
ls -la "$1.before_noent" "$1"
