var path = require('path');
var glob = require('glob');
var child_process = require('child_process');

if (process.argv.length != 3 || process.argv[2] == "--help") {
  console.log();
  console.log("Usage: "+path.basename(process.argv[1])+' [--help] path_to_svg');
  console.log();
  return;
}

var svg_path = process.argv[2];
if (path.extname(svg_path) != ".svg")
  throw "No .svg extension found: "+svg_path;

[250, 800, 2000].forEach(function(el) {
  var png_path = path.dirname(svg_path)+'/'+el+'px-'+path.basename(svg_path, '.svg')+'.png';
  var cmd = "rsvg-convert -h "+el+" '"+svg_path+"' -o '"+png_path+".rsvg'";
  child_process.exec(cmd, function(err, stdout, stderr) {
    if (stdout || stderr || err) console.log(cmd);
    process.stdout.write(stdout);
    process.stderr.write(stderr);
    if (err) {
      console.log('Error '+err);
      throw new Error();
    }
  });
});
