<?php
session_start();
	function isAdmin($user){
		if(userSetting($user,'admin')) return true;
		else return false;
	}
	
	function editAuth($user){
		if (isAdmin(getLoggedInUser())) return true;
		else if (getLoggedInUser() == $user) return true;
		else return false;
	}
	
	function getLoggedInUser(){
		$uid = $_SESSION['uid'];
		return $uid;
	}
	
	function hashIt($pass,$salty=false){
		$salt = $salty;
		if(!$salt){
			$salt = "$2a$10$";
			$salt .= substr(md5(md5($salt.rand(123456789,987654321))),0,21);
		}
		$hash = crypt($pass,$salt);
		$hash = crypt($hash.$pass,$salt);
		$hash = crypt($hash.$pass,$salt);
		return $hash;
	}
	
	function passwordAuth($uid, $pass){
		$checksum = userSetting($uid,'hash');
		$salt = substr($checksum,0,28);
		$hash = hashIt($pass,$salt);
		if ($hash == $checksum) return true;
		else return false;
	}
	
	function sessionAuth(){
		if ($_SESSION['uid']) sessionRegen();
		else if ($_COOKIE['session']) restoreSession();
		return new User(getLoggedInUser());
	}
	
	function sessionRegen($force = false){
		if ((!$_SESSION['count']) && (!$force) ) $_SESSION['count'] = 1;
		else if ( ($_SESSION['count'] < 3) && (!$force) ) $_SESSION['count'] = $_SESSION['count'] + 1;
		else {
			session_regenerate_id(true);
			unset($_SESSION['count']);
			if ($_SESSION['remember']) {
				$salt = rand(123456789,987654321);
				$timeout = time() + 60 * 60 * 24 * 30;
				setUserSetting(getLoggedInUser(),'session-token',md5(session_id().$salt));
				setcookie('session',md5(session_id().$salt),$timeout,"/");
			}
		}
	}
	
	function restoreSession(){
		$sid = $_COOKIE['session'];
		$uid = tokenToUid($sid,'session-token');
		if ($uid){
			$_SESSION['uid'] = $uid;
			$_SESSION['remember'] = true;
		}
		else{
			setcookie('session','',1);
		}
	}
	
	function rand_str($length = 21, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
	{
	    // Length of character list
	    $chars_length = (strlen($chars) - 1);

	    // Start our string
	    $string = $chars{rand(0, $chars_length)};

	    // Generate random string
	    for ($i = 1; $i < $length; $i = strlen($string))
	    {
	        // Grab a random character from our list
	        $r = $chars{rand(0, $chars_length)};

	        // Make sure the same two characters don't appear next to each other
	        if ($r != $string{$i - 1}) $string .=  $r;
	    }

	    // Return the string
	    return $string;
	}
?>