#!/bin/bash

mysql openclipart -e 'drop table if exists _tmp_svglist; create table _tmp_svglist(id int(11),link varchar(255),upload_description mediumtext, upload_name varchar(255), upload_date datetime, upload_tags mediumtext, user_name varchar(255), path varchar(255), key(path)) as (select id, link, upload_description, upload_name, upload_date, upload_tags, user_name, concat(full_path,filename) as path from ocal_files);'

find people/ -iname '*.svg' -print0 | xargs -0 -I {} mysql openclipart --disable-column-names -e "select path, id, link, upload_name, upload_date, upload_description, user_name, upload_tags from _tmp_svglist a where path = \"{}\";" | ./ocalmeta_all.php

mysql openclipart -e 'drop table _tmp_svglist;'
