#!/bin/bash
rm -f sitemap-enum-*.xml

if [ ! -d people ]; then ln -s /srv/www/openclipart.org/htdocs/people; fi

node sitemap_detail.js | split -l 1000 -d -a 4 - sitemap-enum-detail-
./sitemap_tags.sh | split -l 8000 -d -a 4 - sitemap-enum-tags-
./sitemap_users.sh | split -l 8000 -d -a 4 - sitemap-enum-users-
./sitemap_collections.sh | split -l 8000 -d -a 4 - sitemap-enum-collections-

for i in sitemap-enum-*; do
	echo -e '<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"\n        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' > $i.xml
	cat $i >> $i.xml
	echo "</urlset>" >> $i.xml
	rm $i
done

cat << 'EOF' > sitemap.xml
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
 <sitemap><loc>http://openclipart.org/sitemap-static.xml</loc></sitemap>
EOF
find sitemap-enum-*.xml -print0 | xargs -0 -n 1 | awk '{print " <sitemap><loc>http://openclipart.org/"$1"</loc></sitemap>"}' >> sitemap.xml
echo '</sitemapindex>' >> sitemap.xml
