<?php
require_once('data.php');
$user = tokenToUid($_GET['token'],"unsub-token");
if ($user){
	setUserSetting($user,"emailme","0");
	$title = makeTitle('Unsubscribed');
	include('header.php');?>
	<div class='span10 box box-page'>
		<h1>Unsubscribed</h1>
		<p>Okay, <?php echo userSetting($user,"name")?>, you won't receive any more emails about new <strong>Posts</strong>, <strong>Comments</strong>, <strong>Images</strong>, <strong>Members</strong>, or <strong>Events</strong>. Sorry about that!</p>
		
		<p>If you'd like to re-enable these emails, please log in, go to your profile page, and open your Control Panel.</p>
		
		<p>You will still receive emails about resetting your password.</p>
	</div>
	<?
	include('footer.php');
}

else{
	include('404.php');
}

?>