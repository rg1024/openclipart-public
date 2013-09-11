<?php

require_once('functions.php');


$fields = array('url','summary','image_count','tags','access_time', 
                'completion_time','name');


if ( isset($_REQUEST['save']) && !empty($_REQUEST['mct']) )
{
    // print_r($_REQUEST);
    $my_mct = $_REQUEST['mct'];
    $my_dig_array = array();

    $lct = 0;
    for ( $c = 0; $c <= $my_mct; $c++ )
    {
        $is_all_empty = true;
        foreach ( $fields as $field )
        {
            $my_field = clean_field($_REQUEST["$field-$lct"]);
            if ( !empty($my_field) )
                $is_all_empty = false;
            $my_dig_array[$c][] = (isset($my_field) ?
                                 $my_field : '');
        }
        if ( $is_all_empty )
            unset($my_dig_array[$c]);
        $lct++;
    }
    save_dig_file($my_dig_array);
    // print_r($my_dig_array);
}


$dig_files = get_dig_file();
// print_r($dig_files);

?>
<html>
<head></head>
<body>
<?php echo 'max_input_vars: ' . ini_get('max_input_vars'); ?>
<form method="POST" action="digedit.php" id="digedit-form">
<table id="digtable">
<tr><td><!-- ct --></td>
<?php
foreach ( $fields as $field )
{
    echo "<td>$field</td>\n";
}
?>
</tr>
<?php
$mct = 0;
foreach ( $dig_files as $dig )
{
    $ct = 0;
    echo "<tr><td>$mct</td>\n";
    foreach ( $fields as $field )
    {
        echo '<td><input name="' . "$field-$mct" . '" type="text" value="' . 
            (isset($dig[$ct]) ? $dig[$ct] : '') . '" /></td>' . "\n";
        $ct++;
    }
    echo "</tr>\n";
    $mct++;
}
?>
<tr><td><?= $mct ?></td>
<?php
foreach ( $fields as $field )
{
    echo '<td><input name="' . "$field-$mct" . 
         '" type="text" value="" /></td>' . "\n";
}
?>
</tr>
</table>

<input type="hidden" value="<?= $mct ?>" name="mct" />
<input type="submit" value="SAVE" name="save" id="btn-save" />
</form>
</body>
</html>
