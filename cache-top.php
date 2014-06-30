<?php
$url = $_SERVER["SCRIPT_NAME"];
$break = Explode('/', $url);
$file = $break[count($break) - 1];
$cachefile = 'cache/cached-'.substr_replace($file ,"",-4).'.html';
$cachetime = 43200;

// Serve from the cache if it is younger than $cachetime
if ( (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile) ) && ($_GET['recache'] != "true") ){
    echo "<!-- Cached copy, generated ".date('H:i', filemtime($cachefile))." -->\n";
    include($cachefile);
    exit;
}
ob_start(); // Start the output buffer
?>