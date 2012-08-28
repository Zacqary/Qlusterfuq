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
		$to = deprivate($user->email);
		$subject = "Confirm your ".setting("sitename")." password reset";
		$headers = "From: ".setting("sitename")."<".setting("daemon").">\r\n" .
		     "X-Mailer: php";
		ob_start(); //Turn on output buffering
		?>
Hi <?php echo $user->name ?>,

Somebody requested to reset your <?php echo(setting("sitename"))?> password.

To create a new password, visit <?php echo(theRoot())?>/reset/<?php echo($token)?>.

If you didn't request a password reset, ignore this email.

	<?
		//copy current buffer contents into $message variable and delete current output buffer
		$message = ob_get_clean();
		mail($to, $subject, $message, $headers);
		echo("Password reset code sent. Check your email.");
	}
}
else{
	$uid = tokenToUid($op,"reset-token");
	if(!$uid) include('404.php');
	else{
		$user = new User($uid);
		$pass = md5($user->name.$user->email.rand(12345,54321));
		$pass = substr($pass,0,12);
		setUserSetting($uid,'hash',hashIt($pass));
		$_SESSION['alert'] = "Your new password has been emailed to you.";
		$to = deprivate($user->email);
		$subject = "Your ".setting("sitename")." password has been reset";
		$headers = "From: ".setting("sitename")."<".setting("daemon").">\r\n" .
		     "X-Mailer: php";
		ob_start(); //Turn on output buffering
		?>
Hi <?php echo $user->name ?>,

Your password for <?php echo(setting("sitename"))?> has been reset.

Go to <?php echo(theRoot())?> to log in with these credentials:
---
Email: <?php echo($user->email."\n")?>
Password: <?php echo($pass."\n")?>
---

After logging in, you can change your password by:
- Clicking "View my profile page" on the left-hand sidebar
- Clicking the button with a gear on it in the box marked "Control Panel"

	<?
		//copy current buffer contents into $message variable and delete current output buffer
		$message = ob_get_clean();
		mail($to, $subject, $message, $headers);
		header("Location: ".theRoot()."/".$uid);
	}
}



?>