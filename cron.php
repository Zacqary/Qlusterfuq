#!/usr/local/bin/php -q
<?php

require_once("data.php");
require("smtp-mailer.php");
require_once("rss.php");

//	Send queued emails
$emails = [];
if ($handle = opendir('db/e')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            array_push($emails,$entry);
        }
    }
    closedir($handle);
}

$time_start = microtime(true);
foreach ($emails as $val){
	$e = openEmail($val);
	$to = getEmailTo($e);
	$name = getEmailName($e);
	$subject = getEmailSubject($e);
	$message = getEmailMessage($e);
	if(QFSendEmail($to, $name, $subject, $message)){
		deleteEmail($val);
	}
	
	// Stop sending emails after 5 seconds and try again later
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	if ($time >= 5) break;
}

//	Update feed
	updateFeed();
?>