var path = require('path');
var fs = require('fs');
var glob = require('glob');

if (process.argv.length != 3 || process.argv[2] == "--help") {
  console.log();
  console.log("Usage: "+path.basename(process.argv[1])+' [--help] path_to_root_folder');
  console.log();
  return;
}

var root_folder = process.argv[2];

glob(root_folder+'/*px-*.png', function(err, root_files) {
  if (err) throw err;
  glob(root_folder+'/*/*px-*.png', function(err, subdir_files) {
    var all_files = root_files.concat(subdir_files);
    all_files.forEach(function(el) {
      var result = el.match(/(.*\/)[0-9]+px-(.*)\.png/);
      var svg_path = result[1]+result[2]+'.svg';
      if (!fs.existsSync(svg_path))
        console.log(el);
    });
  });
});
