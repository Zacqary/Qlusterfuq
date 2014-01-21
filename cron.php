#!/usr/local/bin/php -q
<?php

require_once("data.php");
require("smtp-mailer.php");
require_once("rss.php");

//	Send queued emails
$emails = countEmails();
echo($emails);
if ($emails > 0){
	for ($i = 0; $i < $emails; $i++){
		$e = openEmail($i);
		$to = getEmailTo($e);
		$name = getEmailName($e);
		$subject = getEmailSubject($e);
		$message = getEmailMessage($e);
		if(QFSendEmail($to, $name, $subject, $message)){
			deleteEmail($i);
		}
	}
}
else echo("no emails");

//	Update feed
	updateFeed();
?>