<?php
    require_once('functions.php');
    
    $title="DIG";

    $default_regex = '(?:svg|pdf|jpe?g|png|gif)';
    $random_dig_url = get_random_dig_url();
     
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
<title><?php echo $title; ?></title>
<meta charset='utf-8'>
<link rel="stylesheet" href="./style.css" />
<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="main.js"></script>
<script>
  $(document).ready(function() {
          // This is more like it!
            });
</script> -->
</head>
<body id="indexBody">
<header>
    <h1>dig</h1>
    <form method="GET" action="index.php" id="urlform">
        <input name="url" type="text"         id="url"    
        placeholder="DIG! Enter full url like https://en.wikipedia.org/wiki/Wikipedia:Public_domain_image_resources" 
        value="<?= request_and_clean('url');?>" />
        <input name="regex" type="text"         id="regex"    
        value="<?= request_and_clean('regex',$default_regex ); ?>" />
        <input name="GET" type="submit"       id="submit" value="DIG!" />
        <label for="deeper">Levels Deep</label><input name="deeper" id="deeper" type="text" value="<?=  request_and_clean('deeper',0); ?>" />
        <label for="regex_second">Regex Deeper</label><input name="regex_second" id="regex_second" type="text" 
        placeholder="commons.wikimedia.org" value="<?= isset($_REQUEST['regex_second']) ? $_REQUEST['regex_second'] : '' ; ?>" />
    <br />

    <div id='examples'>
    <?php
    if ( !empty($random_dig_url) ) {
        $href= get_random_dig_url()."?reqex=" . request_and_clean('regex', $default_regex);
        echo "<a href='$href'>random dig</a>";
    }
    ?>
    <a href="digexport.php">digexport</a>
    <a href="digedit.php">digedit</a>
    <a href="./index.php">reload</a>
    <?php
    if ( isset($examples) )  {
        foreach ($examples as $exname => $exurl )  {
            echo '<a href="./index.php?regex=' . $default_regex . 
                '&url=' . $exurl . '">' . 
            $exname . '</a>';
        }
    }
    ?>
    </div>
    </form>
</header>

<iframe class="digframe" name="urlframe" id="urlframe" src="<?=request_and_clean('url');?>" > 
</iframe>
<iframe class="digframe" name="twkframe" id="twkframe" src="dig.php?<?= $_SERVER["QUERY_STRING"]; ?> "></iframe>

</body>
</html>
