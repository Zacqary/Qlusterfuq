<?php
require_once('functions.php');
require_once('rss.php');
$body = $_POST["body"];
$user = $_POST["user"];
$time = time();
if (editAuth($user)) {
	if ($_POST["date"]) {
		$event = new Event($_POST["name"],$_POST["date"],$_POST["hour"].":".$_POST["minute"].$_POST["ampm"],$_POST["location"]);
	}

	$pid = postPost(0,$user,$time,$body,$event);
	showPost($pid);
	notifyNewPost($pid,$event);
	updateFeed();
}
?>