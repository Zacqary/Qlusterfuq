<?php
include('functions.php');
$token = $_GET['t'];
$uid = tokenToUid($token);
if(!$uid) include('404.php');
else {
	$user = new User($uid);
	$newemail = userSetting($uid,'newemail');
	$email = userSetting($uid,'email');
	setUserSetting($uid,'email',$newemail);
	clearUserSetting($uid,'newemail');
	clearUserSetting($uid,'confirm-token');
	$_SESSION['alert'] = "Success! Your email address is now on file as ".$newemail.".";
	
	$to = $email;
	$subject = "Your ".setting("sitename")." email address was changed";
	$headers = "From: ".setting("sitename")."<".setting("daemon").">\r\n" .
	     "X-Mailer: php";
	ob_start(); //Turn on output buffering
	?>
Hi <?php echo $user->name ?>,

Somebody changed the email address you use to log into <?php echo(setting('sitename'))?>. If you did
not intend to do this, please contact the administrator at <?php echo(setting('admincontact'))?> right away.
<?
	//copy current buffer contents into $message variable and delete current output buffer
	$message = ob_get_clean();
	mail($to, $subject, $message, $headers);
	
	header("Location: ".theRoot()."/".$user->url);
}

?>