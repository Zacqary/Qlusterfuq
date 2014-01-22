<?php
require_once('functions.php');
require_once('rss.php');
$hash = $_GET['hash'];
if(!tempUserExists($hash)) include('404.php');
else{
	$name = tempUserSetting($hash,"name");
	$email = tempUserSetting($hash,"email");
	$intro = tempUserSetting($hash,"intro");
	$uid = createUser();
	setUserSetting($uid,'name',$name);
	setUserSetting($uid,'email',$email);
	$pass = md5($name.$email.rand(12345,54321));
	$pass = substr($pass,0,12);
	setUserSetting($uid,'hash',hashIt($pass));
	setUserSetting($uid,'notifications',"");
	setUserSetting($uid,'asterisktip',"yes");
	setUserSetting($uid,'emailme',"15");
	setUserSetting($uid,'url',$uid);
	mkdir('av/'.$uid,0755);
	removeTempUser($hash);
	$_SESSION['alert'] = "User successfully approved!";
	updateSiteMap();
	
	postUserIntroduction($uid, $intro);
	
	$user = new User($uid);
	$to = $user->email;
	$name = $user->name;
	$subject = "Your ".setting("sitename")." membership has been approved!";
	ob_start(); //Turn on output buffering
	?>
Hi <?php echo $user->name ?>,

Your application to join <?php echo(setting("sitename"))?> has been approved!

Go to <?php echo(theRoot())?> to log in with these credentials:
---
Email: <?php echo($user->email."\n")?>
Password: <?php echo($pass."\n")?>
---

After logging in, you can change your password by:
- Clicking "View my profile page" on the left-hand sidebar
- Clicking the button with a gear on it in the box marked "Control Panel"

You can also edit your profile from this page by clicking the wrench buttons.

Thanks for joining, and welcome to <?php echo(setting("sitename"))?>!

<?
	//copy current buffer contents into $message variable and delete current output buffer
	$message = ob_get_clean();
	QFSendEmail($to, $name, $subject, $message);
	header("Location: ".theRoot()."/".$uid);
}

function postUserIntroduction($user, $intro){
	$time = time();
	$body = "**has just joined ".setting("sitename")."**\n\n> ".$intro."\n\n";
	$pid = postPost(0,$user,$time,$body);
	notifyNewUser($user, $intro, $pid, $time);
}

?>