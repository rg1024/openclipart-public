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

        // $emailto    = "wanglizhong@rejon.org"; 
        $emailto        = $_REQUEST['emailto'];
        // $emailfrom  = "jon@rejon.org";
        $emailfrom      = $_REQUEST['emailfrom'];

        $fields = array('url', 'title', 'description', 'tags');

        echo "<h3>Saving and Sending Files.</h3>";

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
                echo "<p><strong>$f:</strong> " . $_REQUEST["$f-$ct"] . "</p>";
            }
            
            $fn = curl_save_file($_REQUEST["url-$ct"], $my_image_name);
            if ( !empty($fn) ) 
                echo "<p><strong>SAVED:</strong> SAVED.</p>";
            else
                echo "<p class=\"alert\"><strong>SAVED:</strong> NOT SAVED. Check it out.</p>";
                
            // mailer($emailto, $emailfrom, $my_subject, $my_description);
            $sent_ok = remail($emailto, $emailfrom, 
                              $my_subject, $my_description, array($fn) );
            if ( !empty($sent_ok) ) 
                echo "<p><strong>SENT:</strong> SENT.</p>";
            else
                echo "<p><strong class=\"alert\">SENT:</strong> NOT SENT. Check it out.</p>";

            echo '<hr class="sep" />';


        }
// echo "<strong>SEND</strong><br />\n<pre>";
// print_r( $_REQUEST );
// echo "</pre>";
    } 
    else if (isset($_REQUEST['url']))
    {

echo "<strong>URL</strong>: " . $_REQUEST['url'] . "<br />\n";
echo "<strong>REG</strong>: " . $_REQUEST['regex'] . "<br />\n";
//print_r( $_REQUEST );
// echo "</pre>";

?>

<h4>Please review images from the page for submission.</h4>
<form method="POST" action="dig.php" id="getform">
<input type="text" id="emailfrom" name="emailfrom" placeholder="Email to submit from" />
<input type="text" id="emailto" name="emailto" value="upload@openclipart.org" />
<br />
<br />
<?php
        $base_url   = $_REQUEST['url'];
        $html       = file_get_contents($base_url);
        $regex      = $_REQUEST['regex'];

        // $domain = dirname($_REQUEST['url']);

        $doc = new DOMDocument();
        @$doc->loadHTML($html);

        // $tags = $doc->getElementsByTagName('img'); 
        $tags_img   = $doc->getElementsByTagName('img'); 
        $tags_a     = $doc->getElementsByTagName('a'); 
        // echo "<pre>";
        // print_r($tags);
        // echo "</pre>";

        $ct = 0;
        $ct = print_results($tags_img,  $base_url, $regex, 'src', $ct);
        $ct = print_results($tags_a,    $base_url, $regex, 'href', $ct);
        
        if ( $ct > 0 )
        {
echo <<< ENDER
<input type="hidden" name="ct" value="$ct" />
    <input name="send" type="submit"       id="submit" value="SEND" />
</form>
ENDER;
        } else {
            echo "<h4>Sorry, no results. Try again.</h4>\n";
        }



    } else {
        echo "<h4>Try a search above, or include the right variables.</h4>";
    }
?>

</body>
</html>
