#!/bin/bash
mysql openclipart --disable-column-names -e "select username from aiki_users where num_uploads > 0;"|awk -F, '{ for (i=1;i<=NF;i++) { gsub(/^[ \t]+|[ \t]+$/,"",$i); gsub(/ /, "%20", $i); if ($i != "") print $i }}'|sort -u | php -r "echo htmlspecialchars(file_get_contents(\"php://stdin\"));" | awk '{printf("<url><loc>http://openclipart.org/user-detail/%s</loc></url>\n", $1);}'
