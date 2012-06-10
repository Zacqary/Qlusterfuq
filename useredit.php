<?php
include('functions.php');
session_start();
$uid = $_POST["uid"];
$mode = $_POST["mode"];
if (!editAuth($uid)) $mode = false;
if ($mode == 'header'){
	$name = $_POST["name"];
	$altnames = $_POST["altnames"];
	$bio = $_POST["bio"];
	if ($name) setUserSetting($uid,'name',$name);
	if ($altnames) setUserSetting($uid,'altnames',$altnames);
	else clearUserSetting($uid,'altnames');
	if ($bio) setUserSetting($uid,'bio',$bio);
	else clearUserSetting($uid,'bio');
}
if ($mode == 'basicinfo'){
	$birthday = $_POST["birthday"];
	$basicinfo = $_POST["basicinfo"];
	if ($birthday) setUserSetting($uid,'birthday',$birthday);
	else clearUserSetting($uid,'birthday');
	if ($basicinfo) setUserSetting($uid,'basicinfo',$basicinfo);
}
if ($mode == 'contactinfo'){
	$email = $_POST["email"];
	$website = $_POST["website"];
	$phone = $_POST["phone"];
	$externalsvcs = $_POST["externalsvcs"];
	if ($email != userSetting($uid,'email')) $doConfirm = confirmEmail($uid,$email);
	if ($website) setUserSetting($uid,'website',$website);
	else clearUserSetting($uid,'website');
	if ($phone) setUserSetting($uid,'phone',$phone);
	else clearUserSetting($uid,'phone');
	if ($externalsvcs) {
		setUserSetting($uid,'externalsvcs',$externalsvcs);
		validateExternals($uid);
	}
	echo($doConfirm);
}
if ($mode == 'location'){
	$location = $_POST["location"];
	if ($location) setUserSetting($uid,'location',$location);
	else clearUserSetting($uid,'location');
}
if ($mode == 'urlchange'){
	$url = $_POST['url'];
	$oldurl = userSetting($uid,"url");
	setUserSetting($uid,"url",$uid);
	$done = "Done!";
	$forbiddens = array("post","users","register","confirm","approve","upload","reset","members");
	if(urlToUid($url) !== false) { $url = $oldurl; $done = "That URL's taken."; }
	if( array_search(strtolower($url),$forbiddens) !== false) { $url = $oldurl; $done = "That URL's taken."; }
	setUserSetting($uid,"url",$url);
	echo($done);
}
if ($mode == 'emailme'){
	setUserSetting($uid,"emailme",$_POST['checks']);
}

if ($mode == 'passchange'){
	$oldpass = $_POST['oldpass'];
	$newpass = $_POST['newpass'];
	$confirmpass = $_POST['confirmpass'];
	if (!passwordAuth($uid,$oldpass)) {
		echo "That's not your current password.";
	}
	else if($newpass != $confirmpass) {
		echo "Your new passwords don't match. Try typing both again.";
	}
	else if(strlen($newpass) < 8){
		echo "Your new password must be at least 8 characters.";
	}
	else {
		$salt = "$2a$10$";
		$salt .= rand_str();
		setUserSetting($uid,"hash",hashIt($newpass,$salt));
		echo "Success";
		$user = new User($uid);
		$to = deprivate($user->email);
		$subject = "Your ".setting("sitename")." password was changed";
		$headers = "From: ".setting("daemon")."\r\n" .
		     "X-Mailer: php";
		ob_start(); //Turn on output buffering
		?>
	Hi <?php echo $user->name ?>,

	Somebody changed your password for <?php echo(setting("sitename"))?>. If you did not intend to do
	this, please contact the administrator at <?php echo(setting('admincontact'))?> right away.
	<?
		//copy current buffer contents into $message variable and delete current output buffer
		$message = ob_get_clean();
		mail($to, $subject, $message, $headers);

	}
}


function confirmEmail($uid,$email){
	$to = deprivate($email);
	if ($to == deprivate(userSetting($uid,"email"))) {
		setUserSetting($uid,"email",$email);
		return "No";
	}
	else {
		$token = md5($email.rand(123456789,987654321));
		setUserSetting($uid,'newemail',$email);
		setUserSetting($uid,'confirm-token',$token);
		$user = new User($uid);
		$subject = "Confirm your new email address for ".setting("sitename");
		$headers = "From: ".setting("daemon")."\r\n" .
		     "X-Mailer: php";
		ob_start(); //Turn on output buffering
		?>
	Hi <?php echo $user->name ?>,

	Looks like you're trying to change the email address you use for <?php echo(setting("sitename")) ?>.
	Please visit <?php echo(theRoot()) ?>/confirm/<?php echo($token) ?> to confirm this change.

	If you're not <?php echo $user->name ?> and you believe you've received this message in error, please
	feel free to ignore it.
	<?
		//copy current buffer contents into $message variable and delete current output buffer
		$message = ob_get_clean();
	
	
		mail($to, $subject, $message, $headers);
		return $to;	
	}
}

function validateExternals($uid){
	$user = new User($uid);
	$externalsvcs = setting("externalsvcs",true);
	foreach($externalsvcs as $key => $val){
		$data = "";
		$pattern = $val;
		$pattern = "/".strtr($pattern,array(
			"http://"=>"((http(s?):\/\/?)?)((www\.?)?)",
			"https://"=>"((http(s?):\/\/?)?)((www\.?)?)",
			"."=>"\.",
			"/"=>"\/",
			"?"=>"\?",
			"$"=>"([A-Za-z0-9]+)")
			);
		$pattern .= "(\/?)/";
		if($user->externalsvcs[$key]){
			$private=false;
			$data = $user->externalsvcs[$key];
			if (strpos($data,"{{{∵}}}")) {
				$data = strtr($data,array("{{{∵}}}"=>""));
				$private = true;
			}
			preg_match($pattern,$data,$matches);
			if($matches[6]) $data = $matches[6];
			if($private) $data .= "{{{∵}}}";
		}
		$exsv .= $key.",".$data."\n";
	}
	setUserSetting($uid,"externalsvcs",$exsv);
}

?>