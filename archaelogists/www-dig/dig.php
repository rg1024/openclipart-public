<?php
    require_once('functions.php');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<link rel="stylesheet" href="./style.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="main.js"></script>
<script>
    $(document).ready(function() {
        // This is more like it!
    });
</script>
</head>
<body id='digBody'>

<?php

    if (isset($_REQUEST['send']) && $_REQUEST['ct'] > 0 && 
        ! empty($_REQUEST['emailfrom']) && ! empty($_REQUEST['emailto']) )
    {

        // $emailto    = "wanglizhong@rejon.org"; 
        $emailto        = $_REQUEST['emailto'];
        // $emailfrom  = "jon@rejon.org";
        $emailfrom      = $_REQUEST['emailfrom'];

        $global_tags  = $_REQUEST['global_tags'];
        $global_title = $_REQUEST['global_title'];

        $fields = array('url', 'title', 'description', 'tags');

        echo "<h3>Saving and Sending Files.</h3>";

        $lct = $_REQUEST['ct']; 
        for ($ct = 0; $ct < $lct; $ct++)
        {
            if ( ! isset($_REQUEST["include-$ct"]) ){
                continue;
            }    

            $my_subject     = $_REQUEST["title-$ct"] . " $global_title";
            $my_description = $_REQUEST["description-$ct"] . "\n\n" . 
                              $_REQUEST["tags-$ct"] . " $global_tags";

            $my_image_url   = $_REQUEST["url-$ct"];
            $my_image_name  = basename($my_image_url);


            echo "<p><strong>global_title:</strong> " . $global_title . "</p>";
            echo "<p><strong>global_tags:</strong> " . $global_tags . "</p>";
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
    } 
    else if (isset($_REQUEST['url']))
    {

        $deeper       = isset($_REQUEST['deeper']) ?
                        $_REQUEST['deeper'] : 0;
        // not reading the is_int even if int, must need to convert to 
        // int first!
        //if ( ! is_int($deeper) )
        //    $deeper = 0;
        $regex        = $_REQUEST['regex'];
        $regex_second = isset($_REQUEST['regex_second']) ?
                        $_REQUEST['regex_second'] : '';
        $base_url     = $_REQUEST['url'];

        $global_tags  = isset($_REQUEST['global_tags']) ?
                        $_REQUEST['global_tags'] : '';
        $global_title = isset($_REQUEST['global_title']) ? 
                        $_REQUEST['global_title'] : '';

        /*
        echo "<strong>URL</strong>: " . $_REQUEST['url'] . "<br />\n";
        echo "<strong>REG</strong>: " . $_REQUEST['regex'] . "<br />\n";
        echo "<strong>REG2</strong>: ". $regex_second ."<br />\n";
        echo "<strong>LEV</strong>: " . $deeper . "<br />\n";
        */

?>

<strong>Please review images from the page for submission.</strong>
<form method="POST" action="dig.php" id="diggetform">
<p><label for="global_tags">Global Tags</label><input type="text" id="global_tags" name="global_tags" placeholder="#tag #like #this" value="#publicdomain #pd #dig" /></p>
<p><label for="global_title">Global Title Suffix</label><input type="text" id="global_title" name="global_title" placeholder="Suffix for Title" /></p>
<p><lable for="checkall">Yes, Check All Public Domain</a><input type="checkbox" name="checkall" onclick="toggleChecks();" /></p>
<p><label for="emailfrom">From</label><input type="text" id="emailfrom" name="emailfrom" placeholder="Email to submit from" /></p>
<p><label for="emailto">Send To</label><input type="text" id="emailto" name="emailto" value="share@openclipart.org" /></p>

<?php

        $ct = 0;


        // fixing duplicates, first save each url
        // then removeduplicates either by comparing current url to
        // array or at the end
        $dig_organizer  = array();

        $dig_queue      = array($base_url);
        $dig_queue_post = array();

        // limit how deep we can go here, or this is really gonna slow down
        for ( $d = 0; $d <= $deeper; $d++)
        {
            if ( $d > 0 ){
                $dig_queue = array_unique($dig_queue_post);
                $dig_queue_post = array();
            }

            while ( $dig_url = array_shift($dig_queue) )
            {
                $tags_array = 
                    get_elements_from_url( $dig_url, array('img','a') );


                $ct = print_results($tags_array['img'],  $dig_url, $regex, 
                                   'src', $ct, 
                                   $global_tags, $global_title);

                $ct = print_results($tags_array['a'],  $dig_url, $regex, 
                                   'href', $ct, 
                                   $global_tags, $global_title);

                if ( count($tags_array['a']) > 0 && !empty($regex_second) )
                {
                    $last_url = '';
                    foreach ($tags_array['a'] as $t) 
                    {
                        $u = $t->getAttribute('href');
                        $p = url_to_absolute( $dig_url, $u );
                        // print_r($p);

                        // cleanses some duplication was seeing
                        if ( $last_url == $u )
                            continue;

                        if (preg_match( "/$regex_second/i", $p ) ) 
                        {
                            array_push($dig_queue_post, $p);
                        }
                        $last_url = $u;
                    }
                }
            }
        }

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
