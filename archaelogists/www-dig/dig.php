<?php
    require_once('functions.php');
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="/style.css" />
</head>
<body>

<?php



    if (isset($_REQUEST['send']) && $_REQUEST['ct'] > 0 && 
        ! empty($_REQUEST['emailfrom']) && ! empty($_REQUEST['emailto']) )
    {

        $emailto    = "wanglizhong@rejon.org"; 
        $emailfrom  = "jon@rejon.org";

        $fields = array('url', 'title', 'description', 'tags');

        $lct = $_REQUEST['ct']; 
        for ($ct = 0; $ct < $lct; $ct++)
        {
            if ( ! isset($_REQUEST["include-$ct"]) )
                continue;

            $my_subject     = $_REQUEST["title-$ct"];
            $my_description = $_REQUEST["description-$ct"] . "\n\n" . 
                              $_REQUEST["tags-$ct"];

            $my_image_url   = $_REQUEST["url-$ct"];
            $my_image_name  = basename($my_image_url);

            foreach ($fields as $f)
            {
                echo "<p>$f: " . $_REQUEST["$f-$ct"] . "</p>";
            }
            $fn = curl_save_file($_REQUEST["url-$ct"], $my_image_name);
            // test if filename is wrong, then don't send

            // NEXT need to add in some mailer that can handle 
            // attachments, and send this attachment to the mailing script
            // print_r($fn);
            // mailer($emailto, $emailfrom, $my_subject, $my_description);
            // echo $ct;
        }
// echo "<strong>SEND</strong><br />\n<pre>";
// print_r( $_REQUEST );
// echo "</pre>";
    } 
    else if (isset($_REQUEST['url']))
    {

echo "<strong>URL</strong><br />\n<pre>";
print_r( $_REQUEST );
echo "</pre>";

?>

<h4>Please review images from the page for submission.</h4>
<form method="GET" action="dig.php" id="getform">
<input type="text" id="emailfrom" name="emailfrom" placeholder="Email to submit from" />
<input type="text" id="emailto" name="emailto" value="upload@openclipart.org" />
<br />
<br />
<?php
        $base_url = $_REQUEST['url'];
        $html = file_get_contents($base_url);

        // $domain = dirname($_REQUEST['url']);

        $doc = new DOMDocument();
        @$doc->loadHTML($html);

        $tags = $doc->getElementsByTagName('img');

        $ct = 0;
        foreach ($tags as $tag) {
            $p = url_to_absolute( $base_url, $tag->getAttribute('src') );
            $suggested_title =  basename($p);
            $info = pathinfo($p);
            $urlparts = parse_url($info['dirname']);
            $tags = explode('/', $urlparts['path']);
            $tags[] = $urlparts['host'];
            $suggested_title =  $info['filename'];
            $tags[] = $suggested_title;
            $tags[] = "dig";
            $tags[] = "publicdomain";
            $tags[] = "pd";
            $suggested_title = strtr($suggested_title, array('_'=>' '));
            $tagstring = get_tags($tags);

            $getdate = date('Y-m-d H:i:s');

            $description = "\nThis public domain image comes from $base_url and as of $getdate is this file, $p.";

echo <<< END
<div class="imagesection">
<h3>Image $ct</h3>
<a href="$p"><img class="thumb" src="$p" alt="" /></a>
<a class="caption" href="$p">$p</a>
<input type="hidden" name="url-$ct" value="$p" />
<input class="check" type="checkbox" name="include-$ct" value="" />
<label for="include">Yes, Include in PD Submission</label>
<input class="title" type="text" name="title-$ct" placeholder="The Image $ct's Title" value="$suggested_title" />
<textarea class="description" name="description-$ct" placeholder="This is the Image $ct's description">$description</textarea>
<input class="tags" type="text" name="tags-$ct" placeholder="#change #Default #tags" value="$tagstring" />
</div>
END;

            $ct++;
        } 
echo <<< ENDER
<input type="hidden" name="ct" value="$ct" />
    <input name="send" type="submit"       id="submit" value="SEND" />
</form>
ENDER;



    } else {
    }
?>

</body>
</html>
