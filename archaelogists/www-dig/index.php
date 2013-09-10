<?php
    require_once('functions.php');
    $default_regex = '(?:svg|jpe?g|png|gif)';

    $examples = array(
        'mlk'            => 'http://www.mlkonline.net/images.html',
        'goat'           => 'http://www.fws.gov/pictures/lineart/bobsavannah/mountaingoat.html',
        'uscg.mil'       => 'http://www.uscg.mil/top/downloads/coloring.asp',
        'mlk local'      => 'http://examples.localhost/mlk/images.html',
        'uscg.mil local' => 'http://examples.localhost/uscg.mil/index.html',
        'ti'             => 'http://examples.localhost/test/index.html',
    );


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
    <a class="example" href="/index.php">reset</a>
    <?php
    foreach ($examples as $exname => $exurl )
    {
    ?>
    <?php
        echo '<a class="example" href="/index.php?regex=' . $default_regex . 
            '&url=' . $exurl . '">' . 
        $exname . '</a>';
    }
    ?>
    </form>
</header>

<iframe class="digframe" name="urlframe" id="urlframe" src="<?= (isset($_REQUEST['url']) ? $_REQUEST['url'] : '') ; ?>" > 
</iframe>

<iframe class="digframe" name="twkframe" id="twkframe" src="dig.php?<?= $_SERVER["QUERY_STRING"]; ?> "></iframe>

</body>
</html>
