<?php
    require_once('functions.php');
    $default_regex = 'svg|jpe?g|png|gif';


?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="/style.css" />
</head>
<body>
<header>
    <form method="GET" action="index.php" id="urlform">
    <input name="url" type="text"         id="url"    
        value="<?= (isset($_REQUEST['url']) ? $_REQUEST['url'] : '') ; ?>" />
    <input name="regex" type="text"         id="regex"    
        value="<?= (isset($_REQUEST['regex']) ? $_REQUEST['regex'] : 
                                                $default_regex ) ; ?>" />
    <input name="GET" type="submit"       id="submit" value="GET" />
    <a class="example" href="/index.php?url=http://www.mlkonline.net/images.html">mlk</a> 
    <a class="example" href="index.php?url=http://www.fws.gov/pictures/lineart/bobsavannah/mountaingoat.html">goat</a>
    <a class="example" href="http://www.uscg.mil/top/downloads/coloring.asp">uscg.mil</a>
    </form>
</header>

<iframe height="100%" class="digframe" id="urlframe" src="<?= (isset($_REQUEST['url']) ? $_REQUEST['url'] : '') ; ?>" > 
</iframe>

<div class="digframe" id="twkframe">
<h4>Please review images from the page for submission.</h4>
<form method="GET" action="index.php" id="getform">
<input type="text" id="emailfrom" name="emailfrom" placeholder="Email to submit from" />
<input type="text" id="emailto" name="emailto" value="upload@openclipart.org" />
<br />
<br />
<?php
    if (isset($_REQUEST['url']))
    {
        $base_url = $_REQUEST['url'];
        $html = file_get_contents($base_url);

        // $domain = dirname($_REQUEST['url']);
        // print_r($domain);

        $doc = new DOMDocument();
        @$doc->loadHTML($html);

        $tags = $doc->getElementsByTagName('img');

        // print_r($tags);
        $ct = 0;
        foreach ($tags as $tag) {
            $p = url_to_absolute( $base_url, $tag->getAttribute('src') );

            echo "<div class=\"imagesection\"><h3>Image $ct</h3>\n<a href=\"$p\"><img class=\"thumb\" src=\"$p\" alt=\"\" /></a>
                  <a class=\"caption\" href=\"$p\">$p</a>
                  <input class=\"check\" type=\"checkbox\" name=\"include\" />
                  <label for=\"include\">Yes, Include in PD Submission</label>
                  <input class=\"title\" type=\"text\" name=\"title\" placeholder=\"The Image $ct's Title\"/>
                  <textarea class=\"description\" name=\"description\" placeholder=\"This is the Image $ct's description\"></textarea>
                  <input class=\"tags\" type=\"text\" name=\"tags\" placeholder=\"Change, Default, Tags\" />
                  </div>\n";

            $ct++;
        } 

    }
?>

    <input name="send" type="submit"       id="submit" value="SEND" />
</form>
</div>

</body>
</html>
