var path = require('path');
var glob = require('glob');
var child_process = require('child_process');
var async = require('async');
var mysql = require('mysql');
var dateFormat = require('dateformat');

var connection = mysql.createConnection({
  host     : 'localhost',
  user     : 'root',
  password : '_password_',
  database : 'openclipart',
});

var root_folder = 'people/';

// http://www.libpng.org/pub/png/book/chapter11.html
function png_text(text) {
  // The maximum length for a PNG text field is 2047
  // characters, but we should switch to -itxt and
  // utf-8 first - right now we are dumping the utf-8
  // characters into the text field. 1024 characters
  // provides enough buffer for that.
  // -itxt b Title en-US Title Supergirl
  // -zitxt b Title en-US Title Supergirl
  // 2047 characters is the maximum text field length
  return text
      .replace(/\r/g, '')
      .replace(/\n/g, ' ')
      .replace(/<br>/g, ' ')
      .trim()
      .substr(0, 1024)
      .trim();
}

async.waterfall([
  function(callback){
    connection.connect(callback);
  },
  function(arg1, callback){
   connection.query('CREATE TEMPORARY TABLE '+
     '_svglist(id int(11), link varchar(255),upload_description mediumtext, '+
               'upload_name varchar(255), upload_date datetime, upload_tags mediumtext, user_name varchar(255), path varchar(255), key(path)) '+
     'as (select id, link, upload_description, upload_name, upload_date, upload_tags, user_name, concat(full_path,filename) as path from ocal_files)', callback);
  },
  function(fields, err, callback) {
    svg_files = glob.sync(root_folder+'/*.svg').concat(glob.sync(root_folder+'/*/*.svg'));
    async.eachLimit(svg_files, 3, function(svg_item, callback) {

      connection.query('SELECT id, link, upload_name, upload_date, '
		+'upload_description, user_name, upload_tags from _svglist where path = ?', [svg_item], function(err, rows) {
        if (err) {
          process.stderr.write(err+'\n');
          callback();
          return;
        }
        if (!rows || !rows[0]) {
          process.stderr.write('No db entry for '+svg_item+'\n');
          callback();
          return;
        }
        png_files = glob.sync(path.dirname(svg_item)+'/*px-'+path.basename(svg_item, '.svg')+'.png.rsvg');
        async.eachLimit(png_files, 4, function(png_item, png_callback) {

          var params = ['-q'];
          var title = png_text(rows[0].upload_name);
          if (title)
            params = params.concat(['-text', 'b', 'Title', title]);
          var author = png_text(rows[0].user_name);
          if (author)
            params = params.concat(['-text', 'b', 'Author', author]);
          var description = png_text(rows[0].upload_description);
          if (description)
            params = params.concat(['-text', 'b', 'Description', description]);
          params = params.concat(
		['-text', 'b', 'Copyright', 'Public Domain',
		 '-text', 'b', 'Creation Time', dateFormat(rows[0].upload_date, "dd mmm yyyy hh:mm Z"),
		 '-text', 'b', 'Source', 'Openclipart',
		 '-text', 'b', 'URL', 'http://openclipart.org/detail/'+rows[0].id+'/'+png_text(rows[0].link),
		 '-text', 'b', 'Comment', 'Published by openclipart.org']);
          var tags = png_text(rows[0].upload_tags);
          if (tags)
            params = params.concat(['-text', 'b', 'Tags', tags]);
          params = params.concat([png_item, png_item.replace(/.rsvg$/, '')]);

          console.log('pngcrush '+params.join(' '));
          var pngcrush = child_process.spawn('pngcrush', params, { stdio: 'inherit' });
          pngcrush.on('close', function(code) { png_callback(); });
        }, function() { callback(); });
      });
    }, callback);
  }],
function (err) {
  if (err) throw err;
  connection.end();
});
