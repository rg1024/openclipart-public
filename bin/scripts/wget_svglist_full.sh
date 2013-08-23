#!/bin/bash
echo "<!doctype html>"
echo "<html>"
echo "<body>"
echo "Run the following command to download all .svg files currently available from the Open Clip Art Library:<br>"
echo "<pre>wget -r -e robots=off http://openclipart.org/wget_svglist_full.html</pre>"
find people/ -iname '*.svg'|awk '{print "<a href=\"http://openclipart.org/"$0"\"></a>"}'
echo "</body>"
echo "</html>"
