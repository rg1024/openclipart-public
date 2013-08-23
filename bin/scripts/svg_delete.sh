#!/usr/bin/php
<?php
# each stdin line contains the path to one .svg file

$f = fopen('php://stdin', 'r');
while ($line = rtrim(fgets($f))) {
	if (substr($line, -4, 4) != ".svg") {
		print "Not .svg: ".$line;
		continue;
	}
	$path_parts = pathinfo($line);

	# delete .svg file
	$cmd = "rm ".escapeshellarg($path_parts['dirname']."/".$path_parts['filename'].".svg");
	system($cmd, $retval);
	if ($retval) {
		# let's be careful, don't delete pngs if there was an error
		continue;
	}

	# delete related .png files
	$cmd = "rm -f ".escapeshellarg($path_parts['dirname'])."/*px-".escapeshellarg($path_parts['filename']).".png";
	system($cmd, $retval);
}
fclose($f);
?>
