<?php
require("PHPMailer/class.phpmailer.php");
require_once("log.php");
require_once("data.php");

function QFSendEmail($to, $name, $subject, $message) {
	$mail = new PHPMailer(true);
	try {
		$mail->DKIM_domain = setting("dkim-domain");
		$mail->DKIM_private = setting("dkim-private");
		$mail->DKIM_selector = setting("dkim-selector");
		$mail->AddAddress($to, $name);
		$mail->SetFrom( setting("daemon"), siteName() );
		$mail->Subject = $subject;
		$mail->Body = html_entity_decode($message);
		$mail->Send();
		addLog("email", "Message successfully sent to ".$name." <".$to.">: '".$subject."'");
		return true;
	}
	catch (phpmailerException $e){
		addLog("email",$e->errorMessage());
		return false;
	}
	catch (Exception $e){
		addLog("email",$e->getMessage());
		return false;
	}
}

function EmailUser($uid, $subject, $message){
	$to = deprivate( file_get_contents(userPath($uid)."email") );
	$name = userSetting($uid,'name');
	return QFSendEmail($to, $name, $subject, $message);
}
?>