<?php
include('functions.php');
$op = $_GET['token'];
if (!$op) {
	$email = $_POST['email'];
	$_SESSION['uid'] = "temp"; //Avoids private emails breaking the sytstem
	$uid = emailToUid($email);
	unset($_SESSION); session_destroy(); //Remove the temp ID
	if (!$uid) echo "I have never seen that email address in my <em>life</em>.";
	else{
		$token = md5($email.rand(123456789,987654321));
		setUserSetting($uid,'reset-token',$token);
		$user = new User($uid);
		$subject = "Confirm your ".setting("sitename")." password reset";
		ob_start(); //Turn on output buffering
		?>
Hi <?php echo $user->name ?>,

Somebody requested to reset your <?php echo(setting("sitename"))?> password.

To create a new password, visit <?php echo(theRoot())?>/reset/<?php echo($token)?>.

If you didn't request a password reset, ignore this email.

	<?
		//copy current buffer contents into $message variable and delete current output buffer
		$message = ob_get_clean();
		if (EmailUser($uid, $subject, $message)) {
			echo("Password reset code sent. Check your email.");
		}
	}
}
else{
	$uid = tokenToUid($op,"reset-token");
	if(!$uid) include('404.php');
	else{
		$user = new User($uid);
		$pass = md5($user->name.$email.rand(12345,54321));
		$pass = substr($pass,0,12);
		setUserSetting($uid,'hash',hashIt($pass));
		$_SESSION['alert'] = "Your new password has been emailed to you.";
		$to = deprivate($email);
		$name = $user->name;
		$subject = "Your ".setting("sitename")." password has been reset";
		
		ob_start(); //Turn on output buffering
		?>
Hi <?php echo $user->name ?>,

Your password for <?php echo(setting("sitename"))?> has been reset.

Go to <?php echo(theRoot())?> to log in with these credentials:
---
Email: <?php echo($email."\n")?>
Password: <?php echo($pass."\n")?>
---

After logging in, you can change your password by:
- Clicking "View my profile page" on the left-hand sidebar
- Clicking the button with a gear on it in the box marked "Control Panel"

	<?
		//copy current buffer contents into $message variable and delete current output buffer
		$message = ob_get_clean();
		EmailUser($uid, $subject, $message);
		header("Location: ".theRoot()."/".$uid);
	}
}



?>