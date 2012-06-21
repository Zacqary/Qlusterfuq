<?php
require 'data.php';
$email = $_POST['email'];
$pass = $_POST['pass'];
session_start();
$_SESSION['uid'] = "temp"; //Avoids private emails breaking the sytstem
$uid = emailToUid($email);
unset($_SESSION); session_destroy(); //Remove the temp ID
if (!$uid) echo "I have never seen that email address in my <em>life</em>.";
else if (passwordAuth($uid,$pass)){
	session_start();
	session_regenerate_id(true);
	$_SESSION['uid'] = $uid;
	if ($_POST['remember'] == "yes") $_SESSION['remember'] = true;
	sessionRegen(true);
	echo "Success";
}
else {
	session_start();
	unset($_SESSION);
	session_destroy();
	echo "That's not the password.";
}
?>