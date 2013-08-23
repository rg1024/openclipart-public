#!/usr/bin/php
<?php
# in each stdin line, tab-separated: file id link title date description creator comma_separated_tags

$fields = array();
$tags = array();

print "Start time: ".date('r'."\n");

$linenum = 0;
$f = fopen('php://stdin', 'r');
while ($line = fgets($f)) {
	$linenum++;
	$fields = explode("\t", $line);
	$num_fields = sizeof($fields);
	if ($num_fields != 8)
		printf("Unexpected line ".$linenum.": ".$line);
	else {
		# prepare for shell processing
		$filename = str_replace("'", "'\''", $fields[0]);
		$id = $fields[1];
		$link = str_replace("'", "'\''", htmlspecialchars($fields[2]));
		$title = str_replace("'", "'\''", htmlspecialchars($fields[3]));
		$date = $fields[4];
		$date[10] = 'T';
		$date = htmlspecialchars($date);
		$description = str_replace("'", "'\''", htmlspecialchars($fields[5]));
		$creator = str_replace("'", "'\''", htmlspecialchars($fields[6]));
		$tags_str = str_replace("'", "'\''", htmlspecialchars($fields[7]));

		print("l".$linenum." - Updating metadata in '".$filename."'\n");
		$cmd = "./ocalmeta_empty.sh '".$filename."'";
		system($cmd, $retval);
		if ($retval) print("#E code ".$retval.": ".$cmd."\n");

		$cmd = "./ocalmeta_dc.sh '".$filename."' title '".$title."'";
		system($cmd, $retval);
		if ($retval) print("#E code ".$retval.": ".$cmd."\n");

		$cmd = "./ocalmeta_dc.sh '".$filename."' date '".$date."'";
		system($cmd, $retval);
		if ($retval) print("#E code ".$retval.": ".$cmd."\n");

		$cmd = "./ocalmeta_dc.sh '".$filename."' description '".$description."'";
		system($cmd, $retval);
		if ($retval) print("#E code ".$retval.": ".$cmd."\n");

		$cmd = "./ocalmeta_dc.sh '".$filename."' source 'http://openclipart.org/detail/".$id."/".$link."'";
		system($cmd, $retval);
		if ($retval) print("#E code ".$retval.": ".$cmd."\n");

		$cmd = "./ocalmeta_creator.sh '".$filename."' '".$creator."'";
		system($cmd, $retval);
		if ($retval) print("#E code ".$retval.": ".$cmd."\n");

		$tags = explode(",", $tags_str);
		for ($i = 0; $i < sizeof($tags); $i++) {
			$tags[$i] = trim($tags[$i]);
			if ($tags[$i] != "") {
				$cmd = "./ocalmeta_tag.sh '".$filename."' '".$tags[$i]."'";
				system($cmd, $retval);
				if ($retval) print("#E code ".$retval.": ".$cmd."\n");
			}
		}
	}
}
fclose($f);
print "End time: ".date('r')."\n";
?>
