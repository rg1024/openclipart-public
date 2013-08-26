#!/bin/bash
mysql openclipart --disable-column-names -e "select upload_tags from ocal_files;"|awk -F, '{ for (i=1;i<=NF;i++) { gsub(/^[ \t]+|[ \t]+$/,"",$i); gsub(/ /, "%20", $i); if ($i != "") print $i }}'|sort -u | php -r "echo htmlspecialchars(file_get_contents(\"php://stdin\"));" | awk '{printf("<url><loc>http://openclipart.org/tags/%s</loc></url>\n", $1);}'
