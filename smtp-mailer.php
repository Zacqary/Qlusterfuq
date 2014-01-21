<?php
include("Mailer.php");
function QFSendEmail($to, $name, $subject, $message) {
	$mail = new PHPMailer_Mailer();
	$mail->SMTPDebug = 0;  
	$mail->SMTPAuth = true;
	$mail->SMTPSecure = 'ssl';
	$mail->Host = setting("mailhost");
	$mail->Username = setting("smtp-mailer");
	$mail->Password = setting("smtp-password");
	$mail->Port = setting("smtp-port");
	$mail->AddAddress($to, $name);
	$mail->SetFrom( setting("daemon"), siteName() );
	$mail->Subject = $subject;
	$mail->Body = html_entity_decode($message);
	if(!$mail->Send()) {
			return false;
		} else {
			return true;
		}
}
?>