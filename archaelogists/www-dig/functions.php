<?php
/** 
 *
 */
$REALPATH = realpath(dirname(__FILE__));
define("DIGFILE_PATH", $REALPATH . '/tmp');
define("DIGFILE", DIGFILE_PATH . '/dig.csv');

date_default_timezone_set('UTC');

function request_and_clean($value,$default='') {
    return addslashes(isset($_REQUEST[$value]) ? $_REQUEST[$value]: $default);
}


/**
 * http://nadeausoftware.com/node/79
 */
function url_to_absolute( $baseUrl, $relativeUrl )
{
    // If relative URL has a scheme, clean path and return.
    $r = split_url( $relativeUrl );
    if ( $r === FALSE )
        return FALSE;
    if ( !empty( $r['scheme'] ) )
    {
        if ( !empty( $r['path'] ) && $r['path'][0] == '/' )
            $r['path'] = url_remove_dot_segments( $r['path'] );
        return join_url( $r );
    }
 
    // Make sure the base URL is absolute.
    $b = split_url( $baseUrl );
    if ( $b === FALSE || empty( $b['scheme'] ) || empty( $b['host'] ) )
        return FALSE;
    $r['scheme'] = $b['scheme'];
 
    // If relative URL has an authority, clean path and return.
    if ( isset( $r['host'] ) )
    {
        if ( !empty( $r['path'] ) )
            $r['path'] = url_remove_dot_segments( $r['path'] );
        return join_url( $r );
    }
    unset( $r['port'] );
    unset( $r['user'] );
    unset( $r['pass'] );
 
    // Copy base authority.
    $r['host'] = $b['host'];
    if ( isset( $b['port'] ) ) $r['port'] = $b['port'];
    if ( isset( $b['user'] ) ) $r['user'] = $b['user'];
    if ( isset( $b['pass'] ) ) $r['pass'] = $b['pass'];
 
    // If relative URL has no path, use base path
    if ( empty( $r['path'] ) )
    {
        if ( !empty( $b['path'] ) )
            $r['path'] = $b['path'];
        if ( !isset( $r['query'] ) && isset( $b['query'] ) )
            $r['query'] = $b['query'];
        return join_url( $r );
    }
 
    // If relative URL path doesn't start with /, merge with base path
    if ( $r['path'][0] != '/' )
    {
        $base = mb_strrchr( $b['path'], '/', TRUE, 'UTF-8' );
        if ( $base === FALSE ) $base = '';
        $r['path'] = $base . '/' . $r['path'];
    }
    $r['path'] = url_remove_dot_segments( $r['path'] );
    return join_url( $r );
}

function url_remove_dot_segments( $path )
{
    // multi-byte character explode
    $inSegs  = preg_split( '!/!u', $path );
    $outSegs = array( );
    foreach ( $inSegs as $seg )
    {
        if ( $seg == '' || $seg == '.')
            continue;
        if ( $seg == '..' )
            array_pop( $outSegs );
        else
            array_push( $outSegs, $seg );
    }
    $outPath = implode( '/', $outSegs );
    if ( $path[0] == '/' )
        $outPath = '/' . $outPath;
    // compare last multi-byte character against '/'
    if ( $outPath != '/' &&
        (mb_strlen($path)-1) == mb_strrpos( $path, '/', 'UTF-8' ) )
        $outPath .= '/';
    return $outPath;
}

/**
 * Extract URLs from a web page. 
 * http://nadeausoftware.com/articles/2008/01/php_tip_how_extract_urls_web_page
 */
function extract_html_urls( $text )
{
    $match_elements = array(
        // HTML
        array('element'=>'a',       'attribute'=>'href'),       // 2.0
        array('element'=>'a',       'attribute'=>'urn'),        // 2.0
        array('element'=>'base',    'attribute'=>'href'),       // 2.0
        array('element'=>'form',    'attribute'=>'action'),     // 2.0
        array('element'=>'img',     'attribute'=>'src'),        // 2.0
        array('element'=>'link',    'attribute'=>'href'),       // 2.0
 
        array('element'=>'applet',  'attribute'=>'code'),       // 3.2
        array('element'=>'applet',  'attribute'=>'codebase'),   // 3.2
        array('element'=>'area',    'attribute'=>'href'),       // 3.2
        array('element'=>'body',    'attribute'=>'background'), // 3.2
        array('element'=>'img',     'attribute'=>'usemap'),     // 3.2
        array('element'=>'input',   'attribute'=>'src'),        // 3.2
 
        array('element'=>'applet',  'attribute'=>'archive'),    // 4.01
        array('element'=>'applet',  'attribute'=>'object'),     // 4.01
        array('element'=>'blockquote','attribute'=>'cite'),     // 4.01
        array('element'=>'del',     'attribute'=>'cite'),       // 4.01
        array('element'=>'frame',   'attribute'=>'longdesc'),   // 4.01
        array('element'=>'frame',   'attribute'=>'src'),        // 4.01
        array('element'=>'head',    'attribute'=>'profile'),    // 4.01
        array('element'=>'iframe',  'attribute'=>'longdesc'),   // 4.01
        array('element'=>'iframe',  'attribute'=>'src'),        // 4.01
        array('element'=>'img',     'attribute'=>'longdesc'),   // 4.01
        array('element'=>'input',   'attribute'=>'usemap'),     // 4.01
        array('element'=>'ins',     'attribute'=>'cite'),       // 4.01
        array('element'=>'object',  'attribute'=>'archive'),    // 4.01
        array('element'=>'object',  'attribute'=>'classid'),    // 4.01
        array('element'=>'object',  'attribute'=>'codebase'),   // 4.01
        array('element'=>'object',  'attribute'=>'data'),       // 4.01
        array('element'=>'object',  'attribute'=>'usemap'),     // 4.01
        array('element'=>'q',       'attribute'=>'cite'),       // 4.01
        array('element'=>'script',  'attribute'=>'src'),        // 4.01
 
        array('element'=>'audio',   'attribute'=>'src'),        // 5.0
        array('element'=>'command', 'attribute'=>'icon'),       // 5.0
        array('element'=>'embed',   'attribute'=>'src'),        // 5.0
        array('element'=>'event-source','attribute'=>'src'),    // 5.0
        array('element'=>'html',    'attribute'=>'manifest'),   // 5.0
        array('element'=>'source',  'attribute'=>'src'),        // 5.0
        array('element'=>'video',   'attribute'=>'src'),        // 5.0
        array('element'=>'video',   'attribute'=>'poster'),     // 5.0
 
        array('element'=>'bgsound', 'attribute'=>'src'),        // Extension
        array('element'=>'body',    'attribute'=>'credits'),    // Extension
        array('element'=>'body',    'attribute'=>'instructions'),//Extension
        array('element'=>'body',    'attribute'=>'logo'),       // Extension
        array('element'=>'div',     'attribute'=>'href'),       // Extension
        array('element'=>'div',     'attribute'=>'src'),        // Extension
        array('element'=>'embed',   'attribute'=>'code'),       // Extension
        array('element'=>'embed',   'attribute'=>'pluginspage'),// Extension
        array('element'=>'html',    'attribute'=>'background'), // Extension
        array('element'=>'ilayer',  'attribute'=>'src'),        // Extension
        array('element'=>'img',     'attribute'=>'dynsrc'),     // Extension
        array('element'=>'img',     'attribute'=>'lowsrc'),     // Extension
        array('element'=>'input',   'attribute'=>'dynsrc'),     // Extension
        array('element'=>'input',   'attribute'=>'lowsrc'),     // Extension
        array('element'=>'table',   'attribute'=>'background'), // Extension
        array('element'=>'td',      'attribute'=>'background'), // Extension
        array('element'=>'th',      'attribute'=>'background'), // Extension
        array('element'=>'layer',   'attribute'=>'src'),        // Extension
        array('element'=>'xml',     'attribute'=>'src'),        // Extension
 
        array('element'=>'button',  'attribute'=>'action'),     // Forms 2.0
        array('element'=>'datalist','attribute'=>'data'),       // Forms 2.0
        array('element'=>'form',    'attribute'=>'data'),       // Forms 2.0
        array('element'=>'input',   'attribute'=>'action'),     // Forms 2.0
        array('element'=>'select',  'attribute'=>'data'),       // Forms 2.0
 
        // XHTML
        array('element'=>'html',    'attribute'=>'xmlns'),
 
        // WML
        array('element'=>'access',  'attribute'=>'path'),       // 1.3
        array('element'=>'card',    'attribute'=>'onenterforward'),// 1.3
        array('element'=>'card',    'attribute'=>'onenterbackward'),// 1.3
        array('element'=>'card',    'attribute'=>'ontimer'),    // 1.3
        array('element'=>'go',      'attribute'=>'href'),       // 1.3
        array('element'=>'option',  'attribute'=>'onpick'),     // 1.3
        array('element'=>'template','attribute'=>'onenterforward'),// 1.3
        array('element'=>'template','attribute'=>'onenterbackward'),// 1.3
        array('element'=>'template','attribute'=>'ontimer'),    // 1.3
        array('element'=>'wml',     'attribute'=>'xmlns'),      // 2.0
    );
 
    $match_metas = array(
        'content-base',
        'content-location',
        'referer',
        'location',
        'refresh',
    );
 
    // Extract all elements
    if ( !preg_match_all( '/<([a-z][^>]*)>/iu', $text, $matches ) )
        return array( );
    $elements = $matches[1];
    $value_pattern = '=(("([^"]*)")|([^\s]*))';
 
    // Match elements and attributes
    foreach ( $match_elements as $match_element )
    {
        $name = $match_element['element'];
        $attr = $match_element['attribute'];
        $pattern = '/^' . $name . '\s.*' . $attr . $value_pattern . '/iu';
        if ( $name == 'object' )
            $split_pattern = '/\s*/u';  // Space-separated URL list
        else if ( $name == 'archive' )
            $split_pattern = '/,\s*/u'; // Comma-separated URL list
        else
            unset( $split_pattern );    // Single URL
        foreach ( $elements as $element )
        {
            if ( !preg_match( $pattern, $element, $match ) )
                continue;
            $m = empty($match[3]) ? $match[4] : $match[3];
            if ( !isset( $split_pattern ) )
                $urls[$name][$attr][] = $m;
            else
            {
                $msplit = preg_split( $split_pattern, $m );
                foreach ( $msplit as $ms )
                    $urls[$name][$attr][] = $ms;
            }
        }
    }
 
    // Match meta http-equiv elements
    foreach ( $match_metas as $match_meta )
    {
        $attr_pattern    = '/http-equiv="?' . $match_meta . '"?/iu';
        $content_pattern = '/content'  . $value_pattern . '/iu';
        $refresh_pattern = '/\d*;\s*(url=)?(.*)$/iu';
        foreach ( $elements as $element )
        {
            if ( !preg_match( '/^meta/iu', $element ) ||
                !preg_match( $attr_pattern, $element ) ||
                !preg_match( $content_pattern, $element, $match ) )
                continue;
            $m = empty($match[3]) ? $match[4] : $match[3];
            if ( $match_meta != 'refresh' )
                $urls['meta']['http-equiv'][] = $m;
            else if ( preg_match( $refresh_pattern, $m, $match ) )
                $urls['meta']['http-equiv'][] = $match[2];
        }
    }
 
    // Match style attributes
    $urls['style'] = array( );
    $style_pattern = '/style' . $value_pattern . '/iu';
    foreach ( $elements as $element )
    {
        if ( !preg_match( $style_pattern, $element, $match ) )
            continue;
        $m = empty($match[3]) ? $match[4] : $match[3];
        $style_urls = extract_css_urls( $m );
        if ( !empty( $style_urls ) )
            $urls['style'] = array_merge_recursive(
                $urls['style'], $style_urls );
    }
 
    // Match style bodies
    if ( preg_match_all( '/<style[^>]*>(.*?)<\/style>/siu', $text, $style_bodies ) )
    {
        foreach ( $style_bodies[1] as $style_body )
        {
            $style_urls = extract_css_urls( $style_body );
            if ( !empty( $style_urls ) )
                $urls['style'] = array_merge_recursive(
                    $urls['style'], $style_urls );
        }
    }
    if ( empty($urls['style']) )
        unset( $urls['style'] );
 
    return $urls;
}

function extract_all_make_abs ($baseUrl, $text) {
        // Extract URLs and convert to a single list
        $groupedUrls = extract_html_urls( $text );
        $pageUrls    = array( );
        foreach ( $urls as $element_entry )
            foreach ( $element_entry as $attribute_entry )
                $pageUrls = array_merge( $pageUrls, $attribute_entry );
             
                // Convert each URL to absolute
                $n = count( $pageUrls );
                for ( $i = 0; $i < $n; $i++ )
                     $pageUrls[$i] = 
                        url_to_absolute( $baseUrl, $pageUrls[$i] );
	return $pageUrls;
}

function curl_get_file_contents($URL)
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    curl_close($c);

    if ($contents) return $contents;
    else return FALSE;
}


function curl_save_file($image_url, $filename)
{
    // $savepath = "/tmp/$filename";
    $savepath = DIGFILE_PATH . "/$filename";

    // so what it overwrites something
    // print_r($savepath);
    // if ( file_exists($savepath) )
    //    return false;

    $ch = curl_init();
    $fp = fopen($savepath , 'wb');
    curl_setopt($ch, CURLOPT_URL, $image_url);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    // print_r($savepath);
    return $savepath;

}

function split_url( $url, $decode=TRUE )
{
    $xunressub     = 'a-zA-Z\d\-._~\!$&\'()*+,;=';
    $xpchar        = $xunressub . ':@%';

    $xscheme       = '([a-zA-Z][a-zA-Z\d+-.]*)';

    $xuserinfo     = '((['  . $xunressub . '%]*)' .
                     '(:([' . $xunressub . ':%]*))?)';

    $xipv4         = '(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})';

    $xipv6         = '(\[([a-fA-F\d.:]+)\])';

    $xhost_name    = '([a-zA-Z\d-.%]+)';

    $xhost         = '(' . $xhost_name . '|' . $xipv4 . '|' . $xipv6 . ')';
    $xport         = '(\d*)';
    $xauthority    = '((' . $xuserinfo . '@)?' . $xhost .
                     '?(:' . $xport . ')?)';

    $xslash_seg    = '(/[' . $xpchar . ']*)';
    $xpath_authabs = '((//' . $xauthority . ')((/[' . $xpchar . ']*)*))';
    $xpath_rel     = '([' . $xpchar . ']+' . $xslash_seg . '*)';
    $xpath_abs     = '(/(' . $xpath_rel . ')?)';
    $xapath        = '(' . $xpath_authabs . '|' . $xpath_abs .
                     '|' . $xpath_rel . ')';

    $xqueryfrag    = '([' . $xpchar . '/?' . ']*)';

    $xurl          = '^(' . $xscheme . ':)?' .  $xapath . '?' .
                     '(\?' . $xqueryfrag . ')?(#' . $xqueryfrag . ')?$';
 
    $parts         = array();
 
    // Split the URL into components.
    if ( !preg_match( '!' . $xurl . '!', $url, $m ) )
        return FALSE;
 
    if ( !empty($m[2]) )        $parts['scheme']  = strtolower($m[2]);
 
    if ( !empty($m[7]) ) {
        if ( isset( $m[9] ) )   $parts['user']    = $m[9];
        else            $parts['user']    = '';
    }
    if ( !empty($m[10]) )       $parts['pass']    = $m[11];
 
    if ( !empty($m[13]) )       $h=$parts['host'] = $m[13];
    else if ( !empty($m[14]) )  $parts['host']    = $m[14];
    else if ( !empty($m[16]) )  $parts['host']    = $m[16];
    else if ( !empty( $m[5] ) ) $parts['host']    = '';
    if ( !empty($m[17]) )       $parts['port']    = $m[18];
 
    if ( !empty($m[19]) )       $parts['path']    = $m[19];
    else if ( !empty($m[21]) )  $parts['path']    = $m[21];
    else if ( !empty($m[25]) )  $parts['path']    = $m[25];
 
    if ( !empty($m[27]) )       $parts['query']   = $m[28];
    if ( !empty($m[29]) )       $parts['fragment']= $m[30];
 
    if ( !$decode )
        return $parts;
    if ( !empty($parts['user']) )
        $parts['user']     = rawurldecode( $parts['user'] );
    if ( !empty($parts['pass']) )
        $parts['pass']     = rawurldecode( $parts['pass'] );
    if ( !empty($parts['path']) )
        $parts['path']     = rawurldecode( $parts['path'] );
    if ( isset($h) )
        $parts['host']     = rawurldecode( $parts['host'] );
    if ( !empty($parts['query']) )
        $parts['query']    = rawurldecode( $parts['query'] );
    if ( !empty($parts['fragment']) )
        $parts['fragment'] = rawurldecode( $parts['fragment'] );
    return $parts;
}


function join_url( $parts, $encode=TRUE )
{
    if ( $encode )
    {
        if ( isset( $parts['user'] ) )
            $parts['user']     = rawurlencode( $parts['user'] );
        if ( isset( $parts['pass'] ) )
            $parts['pass']     = rawurlencode( $parts['pass'] );
        if ( isset( $parts['host'] ) &&
            !preg_match( '!^(\[[\da-f.:]+\]])|([\da-f.:]+)$!ui', $parts['host'] ) )
            $parts['host']     = rawurlencode( $parts['host'] );
        if ( !empty( $parts['path'] ) )
            $parts['path']     = preg_replace( '!%2F!ui', '/',
                rawurlencode( $parts['path'] ) );
        if ( isset( $parts['query'] ) )
            $parts['query']    = rawurlencode( $parts['query'] );
        if ( isset( $parts['fragment'] ) )
            $parts['fragment'] = rawurlencode( $parts['fragment'] );
    }
 
    $url = '';
    if ( !empty( $parts['scheme'] ) )
        $url .= $parts['scheme'] . ':';
    if ( isset( $parts['host'] ) )
    {
        $url .= '//';
        if ( isset( $parts['user'] ) )
        {
            $url .= $parts['user'];
            if ( isset( $parts['pass'] ) )
                $url .= ':' . $parts['pass'];
            $url .= '@';
        }
        if ( preg_match( '!^[\da-f]*:[\da-f.:]+$!ui', $parts['host'] ) )
            $url .= '[' . $parts['host'] . ']'; // IPv6
        else
            $url .= $parts['host'];             // IPv4 or name
        if ( isset( $parts['port'] ) )
            $url .= ':' . $parts['port'];
        if ( !empty( $parts['path'] ) && $parts['path'][0] != '/' )
            $url .= '/';
    }
    if ( !empty( $parts['path'] ) )
        $url .= $parts['path'];
    if ( isset( $parts['query'] ) )
        $url .= '?' . $parts['query'];
    if ( isset( $parts['fragment'] ) )
        $url .= '#' . $parts['fragment'];
    return $url;
}

function get_tags ($tags)
{
    $output = '';
    foreach ($tags as $t)
    {
        if ( empty($t) )
            continue;
        $output .= "#$t ";
    }
    return $output;
}

function mailer ( $to, $from, $subject, $message ) 
{

    # $to      = 'nobody@example.com';
    # $subject = 'the subject';
    # $message = 'hello';
    $headers =   "From: $from" . "\r\n" .
                 "Reply-To: $from" . "\r\n" .
                 'X-Mailer: PHP/' . phpversion();

    mail($to, $subject, $message, $headers);

}

function remail($to,$from,$subject,$message,$files)
{
    // print_r($files);
    $from_pure = $from;
    // email fields: to, from, subject, and so on
    //$from = $from; 
    // $message = date("Y.m.d H:i:s")."\n".count($files)." attachments";
    // $headers = "From: $from";
    $headers =   "From: $from" . "\r\n" .
                 "Reply-To: $from" . "\r\n" .
                 'X-Mailer: PHP/' . phpversion();

    // boundary 
    $semi_rand = md5(time()); 
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 

    // headers for attachment 
    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 

    // multipart boundary 
    $message = "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
    "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 

    // preparing attachments
    for($i=0;$i<count($files);$i++){
        if(is_file($files[$i])){
            $message .= "--{$mime_boundary}\n";
            $fp =    @fopen($files[$i],"rb");
            $data =    @fread($fp,filesize($files[$i]));
            @fclose($fp);
            $data = chunk_split(base64_encode($data));
            $message .= "Content-Type: application/octet-stream; name=\"".basename($files[$i])."\"\n" . 
            "Content-Description: ".basename($files[$i])."\n" .
            "Content-Disposition: attachment;\n" . " filename=\"".basename($files[$i])."\"; size=".filesize($files[$i]).";\n" .
            "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        }
    }
    $message .= "--{$mime_boundary}--";
    $returnpath = "-f" . $from_pure;
    $ok = @mail($to, $subject, $message, $headers, $returnpath); 
    if($ok){ return $i; } else { return 0; }
}

// @returns count
function print_results ($tags, $base_url, $regex, $attrib, $ct, 
                        $global_tags = '', $global_title = '' )
{
        foreach ($tags as $tag) {
            $p = url_to_absolute( $base_url, $tag->getAttribute($attrib) );
            $suggested_title =  basename($p);
            $info = pathinfo($p);
            // skip files types we are not looking for
            if ( ! isset($info['extension']) || 
                 ! preg_match( "/$regex/i", $info['extension'] ) )
                continue;
            $urlparts = parse_url($info['dirname']);
            $mytags = explode('/', $urlparts['path']);
            // print_r($urlparts);
            $mytags[] = $urlparts['host'];
            $suggested_title =  $info['filename'];
            $mytags[] = $suggested_title;
            $mytags[] = $global_title;

            if ( is_array($global_tags) && count($global_tags) > 0 ) {
                foreach ( $global_tags as $t )
                {
                    $mytags[] = $t;
                }
            }
            
            $suggested_title = preg_replace("/^(.+px)/", '', $suggested_title);
            $suggested_title = strtr($suggested_title, array('_'=>' ',
                                                             '-'=>' '));
            $suggested_title = ltrim($suggested_title);
            $suggested_title = $suggested_title . " $global_title";

            $tagstring = get_tags($mytags);

            $getdate = date('Y-m-d H:i:s');

            $description = "\nThis public domain image comes from $base_url and as of $getdate is this file, $p.";

echo <<< END
<div class="imagesection">
<h3>Image $ct</h3>
<a href="$p"><img class="thumb" src="$p" alt="" /></a>
<a class="caption" href="$p">$p</a>
<input type="hidden" name="url-$ct" value="$p" />
<input class="check" type="checkbox" name="include-$ct" />
<label for="include">Yes, Include in Public Domain Submission</label>
<input class="title" type="text" name="title-$ct" placeholder="The Image $ct's Title" value="$suggested_title" />
<textarea class="description" name="description-$ct" placeholder="This is the Image $ct's description">$description</textarea>
<input class="tags" type="text" name="tags-$ct" placeholder="#change #Default #tags" value="$tagstring" />
</div>
END;

            $ct++;
        } 
        return $ct;
}

function get_dig_file_remote ($digfile_url = 'https://raw.github.com/openclipart-dev/openclipart-public/master/archaelogists/dig.csv')
{
    $digfile = DIGFILE;
    
    // TODO: check the access time, and don't allow some crazy
    //       amount of remote getting and saving this file

    if ( file_exists($digfile) )
        return $digfile;

    if ( ! is_writable( DIGFILE_PATH ) )
    {
        // die( "The path is not writable for the master digfile." );
        error_log("ERROR: The DIGFILE_PATH is not writable. " . DIGFILE_PATH);
        return 0;
    }

   
    $ch = curl_init();
    // print_r($digfile);
    $fp = fopen($digfile, "w");
    curl_setopt($ch, CURLOPT_URL, $digfile_url);

    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    return $digfile;

}

/**
 * read in the file as csv, and return the data structure 
 */
function get_dig_file ()
{
    $digfile = get_dig_file_remote();
    // print_r($digfile);
    
    if ( $digfile == FALSE )
        return FALSE;
    $dig_file_array = array();
    $row = 1;
    if (($handle = fopen($digfile, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $dig_file_array[] = $data;
        }
        fclose($handle);

        // print_r($dig_file_array);
        return $dig_file_array;
    } else {
        log("Can't open the dig file handle.");
        return FALSE;
    }
}

function merge_digs ( $digs )
{
    // merge
    // remove duplicates
    // return dig
}

/**
 * Save a dig to the dig file.
 */
function save_dig_to_dig_file ($dig, $digfile_path = DIGFILE, 
                               $rw_flag='w+')
{
    save_dig_file($dig, $digfile_path, $rw_flag);
}

/**
 * Take in the digfile data structure, and save to the csv file
 */
function save_dig_file ($digs, $digfile_path = DIGFILE, 
                        $rw_flag = 'w')
{
/*
$list = array (
    array('aaa', 'bbb', 'ccc', 'dddd'),
        array('123', '456', '789'),
            array('"aaa"', '"bbb"')
            );
*/

    $fp = fopen($digfile_path, $rw_flag);

    foreach ($digs as $fields) {
        fputcsv($fp, $fields);
    }

    fclose($fp);
}

/**
 * Save the dig file remotely. For now, just email it to love@openclipart.org
 */
function save_dig_file_remote ()
{
}

/**
 * Take the current dig file and email it to someone.
 */
function mail_dig_file ($emailto   = 'jon@rejon.org', 
                        $emailfrom = 'love@openclipart.org', 
                        $digfile   = DIGFILE)
{
    $my_subject     = 'Dig File';
    $my_description = $my_subject;

    return remail($emailto, $emailfrom,
           $my_subject, $my_description, array($digfile) );
}


/**
 * Get a random dig site from the dig file, and return it.
 */
function get_random_dig_site ()
{
    $dig_file_array = get_dig_file();
    if ( $dig_file_array == FALSE ) {
        log("ERROR: Can't get_random_dig_site.");
        return FALSE;
    }
    // print_r($dig_file_array);
    return $dig_file_array[array_rand($dig_file_array)];

}

/**
 * Get a random dig site as a url.
 */
function get_random_dig_url ()
{
    $dig_file_array = get_dig_file();
    if ( $dig_file_array == FALSE )
    {
        error_log("ERROR: Can't get random_dig_url.");
        return FALSE;
    }

    $dct = count($dig_file_array);

    $ct = 0;
    $url = '';
    do {

        $random_dig_list = get_random_dig_site();

        if ( $random_dig_list == FALSE )
        {
            log("ERROR: Can't get_random_dig_site.");
            return FALSE;
        }

        list($url,$summary,$image_count, $tags, $access_time, 
             $completion_time,$name) = $random_dig_list;
        $ct++;

        /*
        echo "<pre>";
        echo "url: $url\n";
        echo "atime: $access_time\n";
        echo "ctime: $completion_time\n";
        echo "ct: $ct\n";
        echo "dct: $dct\n";
        echo "</pre>";
        */

       
    } while ( empty($url) || 
              (!empty($completion_time) && !empty($url)) && 
              $ct < $dct ) ;

    return 'index.php?url=' . $url;
}

/**
 * Look at the current dig site, and return the next dig site in the list
 */
function get_next_dig_site ()
{
    // return "Not implemented yet!";
    return 0;
}


function clean_field ($field)
{
    return strtr($field, array(',' =>' '));
}

function dump_dig_file ()
{
$file_name = 'dig.csv';
// $file_url = 'http://www.myremoteserver.com/' . $file_name;
$file_path = DIGFILE_PATH . "/$file_name";
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
header("Content-disposition: attachment; filename=\"".$file_name."\""); 
readfile($file_path);


}

function get_elements_from_url ($url, $element = 'img')
{
    $html       = file_get_contents($url);
    // $domain     = dirname($url);
    $doc        = new DOMDocument();
    @$doc->loadHTML($html);


    $results = array();
    if ( is_array($element) ) 
    {
        foreach ( $element as $e)
            $results[$e] = $doc->getElementsByTagName($e);
        return $results;
    } 

    return  $doc->getElementsByTagName($element); 

}


