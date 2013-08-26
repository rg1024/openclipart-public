var path = require('path');
var glob = require('glob');
var child_process = require('child_process');

var async = require('async');
var mysql = require('mysql');
var connection = mysql.createConnection({
  host     : 'localhost',
  user     : 'root',
  password : '_password_',
  database : 'openclipart',
});

var root_folder = 'people/';

function escapeHtml(text) {
  return text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;")
      .replace(/\n/g, " ");
}

async.waterfall([
  function(callback){
    connection.connect(callback);
  },
  function(arg1, callback){
   connection.query('CREATE TEMPORARY TABLE '+
     '_svglist(id int(11),link varchar(255),upload_description mediumtext, '+
               'upload_name varchar(255), path varchar(255), key(path)) '+
     'as (select id, link, upload_description, upload_name, concat(full_path,filename) as path from ocal_files)', callback);
  },
  function(fields, err, callback) {
    svg_files = glob.sync(root_folder+'/*.svg').concat(glob.sync(root_folder+'/*/*.svg'));
    async.eachSeries(svg_files, function(item, callback) {
      connection.query('SELECT id, link, path, upload_name, upload_description from _svglist where path = ?', [item], function(err, rows) {
        if (err) throw err;
        if (!rows || !rows[0]) {
          process.stderr.write('No db entry for '+item+'\n');
          callback();
          return;
        }
        var id = rows[0].id;
        var link = escapeHtml(rows[0].link);
        var svg_path = escapeHtml(rows[0].path);
        var upload_name = escapeHtml(rows[0].upload_name);
        var upload_description = escapeHtml(rows[0].upload_description);

        var png_filename = path.basename(svg_path, '.svg')+'.png';

        console.log('<url><loc>http://openclipart.org/detail/'+id+'/'+link+'</loc>'
         +'<image:image>'
         +  '<image:loc>http://openclipart.org/'+path+'</image:loc>'
         +  '<image:title>'+upload_name+'</image:title>'
         +  '<image:caption>'+upload_description+'</image:caption>'
         +  '<image:license>http://creativecommons.org/publicdomain/zero/1.0/</image:license>'
         +'</image:image>'
         +'<image:image>'
         +  '<image:loc>http://openclipart.org/image/250px/svg_to_png/'+id+'/'+png_filename+'</image:loc>'
         +  '<image:title>'+upload_name+'</image:title>'
         +  '<image:caption>'+upload_description+'</image:caption>'
         +  '<image:license>http://creativecommons.org/publicdomain/zero/1.0/</image:license>'
         +'</image:image>'
         +'<image:image>'
         +  '<image:loc>http://openclipart.org/image/800px/svg_to_png/'+id+'/'+png_filename+'</image:loc>'
         +  '<image:title>'+upload_name+'</image:title>'
         +  '<image:caption>'+upload_description+'</image:caption>'
         +  '<image:license>http://creativecommons.org/publicdomain/zero/1.0/</image:license>'
         +'</image:image>'
         +'<image:image>'
         +  '<image:loc>http://openclipart.org/image/2000px/svg_to_png/'+id+'/'+png_filename+'</image:loc>'
         +  '<image:title>'+upload_name+'</image:title>'
         +  '<image:caption>'+upload_description+'</image:caption>'
         +  '<image:license>http://creativecommons.org/publicdomain/zero/1.0/</image:license>'
         +'</image:image>'
         +'</url>');
        callback();
      });
    }, callback);
  }],
function (err) {
  if (err) throw err;
  connection.end();
});
