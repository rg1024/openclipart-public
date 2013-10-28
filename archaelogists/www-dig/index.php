<?php
    require_once('functions.php');
    $default_regex = '(?:svg|pdf|jpe?g|png|gif)';

     
    if ( $_SERVER['HTTP_HOST'] == 'dig.localhost' ||
         $_SERVER['HTTP_HOST'] == 'localhost' ) {
        $examples = array(
            'mlk'            => 'http://www.mlkonline.net/images.html',
            'goat'           => 'http://www.fws.gov/pictures/lineart/bobsavannah/mountaingoat.html',
            'uscg.mil'       => 'http://www.uscg.mil/top/downloads/coloring.asp',
            'mlk local'      => 'http://examples.localhost/mlk/images.html',
            'uscg.mil local' => 'http://examples.localhost/uscg.mil/index.html',
            'ti'             => 'http://examples.localhost/test/index.html',
        );
    }
    


?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="/style.css" />
<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="main.js"></script>
<script>
  $(document).ready(function() {
          // This is more like it!
            });
</script> -->
</head>
<body>
<header>
    <form method="GET" action="index.php" id="urlform">
    <input name="url" type="text"         id="url"    
        placeholder="DIG! Enter full url like https://en.wikipedia.org/wiki/Wikipedia:Public_domain_image_resources" 
        value="<?= (isset($_REQUEST['url']) ? $_REQUEST['url'] : '') ; ?>" />
    <input name="regex" type="text"         id="regex"    
        value="<?= (isset($_REQUEST['regex']) ? $_REQUEST['regex'] : 
                                                $default_regex ) ; ?>" />
    <input name="GET" type="submit"       id="submit" value="DIG!" />
    <label for="deeper">Levels Deep</label><input name="deeper" id="deeper" type="text" value="<?= isset($_REQUEST['deeper']) ? $_REQUEST['deeper'] : 0 ; ?>" />
    <label for="regex_second">Regex Deeper</label><input name="regex_second" id="regex_second" type="text" placeholder="commons.wikimedia.org" value="<?= isset($_REQUEST['regex_second']) ? $_REQUEST['regex_second'] : '' ; ?>" />
    <br />
    <a class="example" href="<?= get_random_dig_url(); ?>&regex=<?= (isset($_REQUEST['regex']) ? $_REQUEST['regex'] : $default_regex ) ; ?>">random dig</a>
    <a class="example" href="digexport.php">digexport</a>
    <a class="example" href="digedit.php">digedit</a>
    <a class="example" href="/index.php">reload</a>
    <?php
    if ( isset($examples) ) 
    {
        foreach ($examples as $exname => $exurl )
        {
    ?>
    <?php
            echo '<a class="example" href="/index.php?regex=' . $default_regex . 
                '&url=' . $exurl . '">' . 
            $exname . '</a>';
        }
    }
    ?>
    </form>
</header>

<iframe class="digframe" name="urlframe" id="urlframe" src="<?= (isset($_REQUEST['url']) ? $_REQUEST['url'] : '') ; ?>" > 
</iframe>

<iframe class="digframe" name="twkframe" id="twkframe" src="dig.php?<?= $_SERVER["QUERY_STRING"]; ?> "></iframe>

</body>
</html>
