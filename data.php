<?php
require('auth.php');
///CONFIG///
date_default_timezone_set(setting('timezone'));
function theRoot(){
	return setting('systemroot');
}
function siteName(){
	return setting('sitename');
}
//Change this if you want to store the database outside of
//the document root for extra security
$DB_ROOT = "";
//////

class User{
	var $uid;
	var $name;
	var $avatar;
	function User($id){
		$this->uid = $id;
		$this->url = userSetting($id,"url");
		if (!userSetting($id,"url")) $this->url = $id;
		$this->name = userSetting($id,"name");
		$this->altnames = userSetting($id,"altnames");
		$this->bio = userSetting($id,"bio");
		$this->location = userSetting($id,"location");
		$this->email = userSetting($id,"email");
		$this->phone = phone_number(userSetting($id,"phone"));
		$this->externalsvcs = userSetting($id,"externalsvcs",true);
		$this->birthday = userSetting($id,"birthday");
		$this->basicinfo = userSetting($id,"basicinfo",true);
		$this->website = userSetting($id,"website");
		if ( ($this->website)  && (strpos($this->website,"://") === false) ) $this->website = "http://".$this->website;
	}
}

class Event{
	function Event($name,$date,$time,$location,$id=0){
		$this->name = $name;
		$this->date = $date;
		$this->time = $time;
		$this->location = $location;
		$this->id = $id;
	}
}


//Checks for differences in events
function differentEvents($e1,$e2){
	if ($e1->name != $e2->name) return true;
	if ($e1->date != $e2->date) return true;
	if ($e1->time != $e2->time) return true;
	if ($e1->location != $e2->location) return true;
	return false;
}

//Lists all the users
function theUsers(){
	$dir = opendir($DB_ROOT.'db/u');
	while($entryName = readdir($dir)) {
		if(!is_dir($entryName)) $users[] = $entryName; //For some reason this removes . and ..
	}
	closedir($dir);
	sort($users);
	return($users);
}

function theAdmins(){
	$users = theUsers();
	foreach ($users as $key => $val){
		if (isAdmin($val)) $admins[] = $val;
	}
	return($admins);
}

//Take a url and get a user id from it
function urlToUid($url){
	$users = theUsers();
	$thisuid = false;
	$aurl = strtolower($url);
	foreach ($users as $key => $val){
		$thisu = new User($val);
		if($thisu->uid == $aurl) $thisuid = $thisu->uid;
		if(strtolower($thisu->url) == $aurl) $thisuid = $thisu->uid;
	}
	return $thisuid;
}

//Get a user id from an email address
function emailToUid($email){
	$users = theUsers();
	$thisuid = false;
	foreach ($users as $key => $val){
		$thisu = new User($val);
		if(strtolower(deprivate($thisu->email)) == strtolower($email)) $thisuid = $thisu->uid;
	}
	return $thisuid;
}

//Get a user id from a token
function tokenToUid($token,$type='confirm-token'){
	$users = theUsers();
	$thisuid = false;
	foreach ($users as $key => $val){
		$thisu = new User($val);
		if(userSetting($thisu->uid,$type) == $token) $thisuid = $thisu->uid;
	}
	return $thisuid;
}

//Creates a new user and returns the id
function createUser(){
	while(1){
		$id = rand(123456789,987654321);
		if (!userExists($id)) break;
	}
	mkdir(userPath().$id);
	return $id;
}

//Returns a setting if the viewer's allowed to see it
function userSetting($id,$setting,$list=false,$char=","){
	if (!file_exists(userPath($id).$setting)) return false;
	if ($list) $data = datoflist(userPath($id).$setting,$char);
	else $data = file_get_contents(userPath($id).$setting);
	return settingAuth($data,$id);
}

//Sets a user setting
function setUserSetting($id, $setting, $change){
	postThing(userPath($id)."/".$setting,$change);
}

//Clears a user setting
function clearUserSetting($id,$setting){
	$file = userPath($id)."/".$setting;
	if (file_exists($file)) unlink(userPath($id)."/".$setting);
}

//Strips the private setting metacharacter
function deprivate($text){
	return strtr($text,array("{{{∵}}}"=>""));
}

//Handles private settings
function settingAuth($input,$id){
	$data = $input;
	//If the setting is an array, check each item
	if (is_array($data)) {
		foreach($data as $key => $val){
			$data[$key] = settingAuth($val,$id);
		}
	}
	
	else if (strpos($data,"{{{∵}}}")) { //{{{∵}}} is the signal for a private setting
		if (!getLoggedInUser()) $data = false; //If nobody's logged in, don't show the setting
		//Only remove the {{{∵}}} if the setting doesn't belong to the logged-in user
		//The {{∵}}} is otherwise used to make the privacy setting editable
		else if (getLoggedInUser() != $id) $data = strtr($data,array("{{{∵}}}"=>""));
	}
	return $data;
}

//Get a system setting
function setting($i,$list=false){
	if ($list) return datoflist($DB_ROOT.'db/set/'.$i);
	return file_get_contents($DB_ROOT.'db/set/'.$i);
}

//Set a system setting
function setSetting($i, $change){
	postThing($DB_ROOT.'db/set/'.$i,$change);
}

//Format a phone number
function phone_number($Phone){
	if(!$Phone) return false;
	if (strpos($Phone,"{{{∵}}}")) $private = "{{{∵}}}"; //Put the private signal aside
	$sPhone = preg_replace("#[^0-9]#",'',$Phone); //Get rid of anything that isn't a number
	
	//If a + or country-code got in there, chop it off
	if((strlen($sPhone) == 12) && ($sPhone{0} == "+")) $sPhone = substr($sPhone,2);
    else if((strlen($sPhone) == 11) && ($sPhone{0} == "1")) $sPhone = substr($sPhone,1);
	
	//If the string's not a 10-digit number, don't even try to reformat it.
	//You'll just end up looking silly.
    else if(strlen($sPhone) != 10) return($Phone);
	
	//Get each section of the number
    $sArea = substr($sPhone,0,3);
    $sPrefix = substr($sPhone,3,3);
    $sNumber = substr($sPhone,6,4);
	//Format it
    $sPhone = "(".$sArea.") ".$sPrefix."-".$sNumber;
    return($sPhone.$private); //If we put the private signal aside, put it back
}

//Make an array of notifications
function openNotifications($uid){
	$data = userSetting($uid,"notifications");
	$array;
	while(strpos($data,"‽")){
		$gob = strstr($data,"‽",true);
		$time = strstr($gob,"⁂",true);
		$glob = strtr($gob,array($time."⁂" => ""));
		$array[$time] = $glob;
		$data = strtr($data,array($gob."‽" => ""));
	}
	return $array;
}

//Add a notification to a user's notification list
function addNotification($uid, $timestamp,$author,$text,$id){
	$data = $timestamp."⁂".$author."•unread•".$text."•".$id."‽";
	if (userSetting($uid, "notifications")) appendThing(userPath($uid)."notifications",$data);
	else postThing(userPath($uid)."notifications",$data);
}

//Mark a user's notifications as read
function markNotificationsAsRead($uid){
	$data = userSetting($uid,"notifications");
	$data = strtr($data,array("•unread•"=>"•read•"));
	setUserSetting($uid,"notifications",$data);
}

function avatarPath($id){
	return "av/".$id."/avatar";
}

function getAvatar($id,$size,$temp=0){
	$path = "av/".$id."/avatar".$size;
	$ext = ".png";
	//Some stuff for the avatar upload form
	if ($temp==2) return("<img class='avatar' src='".theRoot()."/".$path.$ext."?");
	if($temp) return("<img class='avatar' src='".theRoot()."/".$path."temp".$ext."?");
	//If the avatar's missing, return the default
	if(!file_exists($path.$ext)) return("<img class='avatar' src='".theRoot()."/av/default/avatar".$size.$ext."'>");
	//Return the user's avatar
	return("<img class='avatar' src='".theRoot()."/".$path.$ext."'>");
}

//Remove all files in a directory
function destroy($dir) {
    $mydir = opendir($dir);
    while(false !== ($file = readdir($mydir))) {
        if($file != "." && $file != "..") {
            chmod($dir.$file, 0777);
            if(is_dir($dir.$file)) {
                chdir('.');
                destroy($dir.$file.'/');
                rmdir($dir.$file) or DIE("couldn't delete $dir$file<br />");
            }
            else
                unlink($dir.$file) or DIE("couldn't delete $dir$file<br />");
        }
    }
    closedir($mydir);
}

//Make a page title
function makeTitle($data){
	return($data." « ");
}

//Display a link to a user's profile
function authorName($author){
	return("<a class='userlink' href='".theRoot()."/".$author->url."'>".$author->name."</a>");
}

function strip_tags_attributes($sSource, $aAllowedTags = array(), $aDisabledAttributes = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavaible', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragdrop', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterupdate', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmoveout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload', 'style'))
    {
        if (empty($aDisabledAttributes)) return strip_tags($sSource, implode('', $aAllowedTags));

        return preg_replace('/<(.*?)>/ie', "'<' . preg_replace(array('/javascript:[^\"\']*/i', '/(" . implode('|', $aDisabledAttributes) . ")[ \\t\\n]*=[ \\t\\n]*[\"\'][^\"\']*[\"\']/i', '/\s+/'), array('', '', ' '), stripslashes('\\1')) . '>'", strip_tags($sSource, implode('', $aAllowedTags)));
    }

function parseMarkdown($data){
	//Make URLs clickable
	$delimiters = '\\s"\\.\',';
	$schemes = 'https?|ftps?';
	$pattern = sprintf('#(^|[%s])((?:%s)://\\S+[^%1$s])([%1$s]?)#i', $delimiters, $schemes);
	$replacement = '$1<a href="$2">$2</a>$3';
	$data = preg_replace($pattern, $replacement, $data);
	//Parse Markdown and remove <h*> tags
	$data = preg_replace('/<h[1-6](.*?)<\/h[1-6]>/si', '<p class="user-heading"$1</p>', Markdown($data));
	$data = strip_tags_attributes($data,array("<p><a><span><strong><em><b><i><blockquote><img><code><ul><li><table><tr><td><hr>"));
	return $data;
}

function timestamp(){
	return date(setting("timestamp"));
}


function datoflist($i,$char=","){
	$data = array();
	$handle = fopen($i,"r") or die("Error");
	while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
	$data[$row[0]] = $row[1];
	}
	fclose($handle);
	return $data;
}

function postThing($file, $data){
	$fh = fopen($file, 'w');
	fwrite ($fh, $data);
	fclose($fh);
}

function appendThing($file,$data){
	$fh = fopen($file, 'a') or die("Error");
	fwrite ($fh, $data);
	fclose($fh);
}

function postPost($postid, $user, $time, $body, $event=null){
	$pid = $postid;
	
	//If it's a new post, make the post directory
	if ($pid == 0){
		$pid = 1;
		
		//Count how many posts there are
		while(1){
			if (!file_exists(postPath($pid))) break; //If we've found a number that doesn't exist, break
			$pid++;
		}
		
		mkdir(postPath().$pid,0700); //Create the post directory
		followPost($user,$pid); //Have the author follow the post
	}
	//If this post has an event, create it
	if ($event){
		$edata = $event->name."•".$event->date."•".$event->time."•".$event->location;
		postThing(postPath($pid)."e",$edata);
		$body .= "{{{∵}}}"; //Add the event signal
	}
	//Create the post
	$data = $user."•".$time."•".$body;
	postThing(postPath($pid)."p",$data);
	return $pid;
}

function openPost($pid){
	return file_get_contents(postPath($pid)."p");
}

function postComment($pid, $cid, $user, $time, $body){
	$data = $user."•".$time."•".$body;
	postThing(postPath($pid).$cid,$data);
	followPost($user,$pid);
}

function openComment($pid,$cid){
	return file_get_contents(postPath($pid).$cid);
}

//Get event data
function openEvent($pid){
	if (!file_exists(postPath($pid)."e")) return false; //If there's no event, do nothing
	$post = explode("•",file_get_contents(postPath($pid)."e"));
	$name = $post[0];
	$date = $post[1];
	$time = $post[2];
	$location = $post[3];
	if ((!getLoggedInUser()) && $location) $location = "Log in to see location";
	return new Event($name,$date,$time,$location,$pid);
}

function hideThing($file){
	$data = file_get_contents($file);
	postThing($file,"∂•{Removed by ".getLoggedInUser()." at ".timestamp()."}".$data);
}

function unhideThing($file){
	$data = file_get_contents($file);
	if (isDeleted($data)) {
		$hidestring = strstr($data,"}",true)."}";
		$data = strtr($data,array($hidestring => ""));
		postThing($file,$data);
	}
}

function hideComment($pid,$cid){
	hideThing(postPath($pid).$cid);
}

function hidePost($pid,$cid){
	hideThing(postPath($pid).'p');
}

function postPath($i=0){
	if ($i) return ($DB_ROOT.'db/p/'.$i.'/');
	else return ($DB_ROOT.'db/p/');
}

function userPath($i=0){
	if ($i) return ($DB_ROOT.'db/u/'.$i.'/');
	else return ($DB_ROOT.'db/u/');
}

function getAuthor($post){
	$data = explode("•",$post);
	return $data[0];
}

function makeTimestamp($timestamp){
	if($timestamp > (time() - 30)){ //If timestamp is less than 30 seconds
		return("just now");
	}
	else if($timestamp > (time() - 60)) { //If timestamp is less than a minutesold
		return((time()-$timestamp)." seconds ago");
	}
	else if($timestamp > (time() - 120)) { //If timestamp is less than 2 minutes old
		return("a minute ago");
	}
	else if($timestamp > (time() - 3600)) { //If timestamp is less than 1 hour old
		return((int)((time()-$timestamp)/60)." minutes ago");
	}	
	else if ($timestamp > (time() - 3720)) { //If timestamp is less than an hour and two minutes old
		return("an hour ago");
		}
	else if($timestamp > (time() - (3600*2))){ //If timestamp is less than 1 1/2 hours old
		return("about an hour ago");
	}	
	else if($timestamp > (time() - (3600*2))) { //If timestamp is less than 2 hours old
		return("about an hour and a half ago");
	}
	else if($timestamp > (time() - (3600*24))) { //If timestamp is less than 24 hours old
		return((int)((time()-$timestamp)/3600)." hours ago");
	} 
	else if($timestamp > (time() - (3600*72))) { //If timestamp is less than three days old
		return date("l @ g:ia",$timestamp);
	}
	else if (date("Y") == date("Y",$timestamp)) {
		return date("F j @ g:ia",$timestamp);
	}
	else return date(setting("timestamp"),$timestamp);
}

function getTimestamp($post){
	$timestamp = getRawTimestamp($post);
	return makeTimestamp($timestamp);
}

function getExactTimestamp($post){
	$timestamp = getRawTimestamp($post);
	return date(setting("timestamp"),$timestamp);
}

function getRawTimestamp($post){
	$data = explode("•",$post);
	return $data[1];
}

function getNotificationStatus($note){
	$data = explode("•",$note);
	return $data[1];
}

function makeTimelink($post,$url){
	return ("<a href='".$url."' class='time-link' rel='tooltip' title='".getExactTimestamp($post)."'><i class='icon-time'></i> ".getTimestamp($post)."</a>");
}

function getPost($post){
	$data = explode("•",$post);
	return $data[2];
}

function getComment($comment){
	$data = explode("•",$comment);
	return $data[2];
}

function getNotificationText($text){
	$data = explode("•",$text);
	return $data[2];
}

function getNotificationLink($link){
	$data = explode("•",$link);
	return $data[3];
}

function getNotificationType($note){
	$me = getNotificationLink($note);
	if (strpos($me,"com")) {
		$me = strtr($me,array("/post/"=>"","#"=>"","com-"=>""));
		$me = strstr($me,"/");
		$me = strtr($me,array("/"=>""));
		$pid = strstr($me,"-",true);
		$cid = strtr($me,array($pid."-"=>""));
		if (!isDeleted(openComment($pid,$cid))) return "comment";
	}
	else if (strpos($me,"post")) {
		$me = strtr($me,array("/post/"=>""));
		if (!isDeleted(openPost($me))) return "post";
	}
	else if (strpos(getNotificationText($note),"ancelled the event")){
		return "post";
	}
	else return false;
}

function isDeleted($data){
	if (getAuthor($data) != "∂") return false;
	else return true;
}

function postExists($pid){
	return file_exists(postPath($pid)."p");
}

function commentExists($pid,$cid){
	return file_exists(postPath($pid).$cid);
}

function userExists($u){
	return file_exists(userPath($u));
}

function getFollowers($pid){
	if (!file_exists(postPath($pid)."f")) return false; 
	return datoflist(postPath($pid)."f");
}

function isFollowing($uid,$pid){
	$followers = getFollowers($pid);
	if(!$followers) return false;
	return array_search($uid,$followers);
}

function followPost($uid,$pid){
	if(isFollowing($uid,$pid)) return false;
	$followers = getFollowers($pid);
	$data;
	if(is_array($followers)) {
		foreach($followers as $key => $val){
			$data .= $key.",".$val."\n";
		}
	}
	$data .= $uid.",".$uid."\n";
	postThing(postPath($pid)."f",$data);
}

function unfollowPost($uid,$pid){
	if(!isFollowing($uid,$pid)) return false;
	$followers = getFollowers($pid);
	$data;
	if(is_array($followers)) {
		foreach($followers as $key => $val){
			if($key != $uid) $data .= $key.",".$val."\n";
		}
	}
	postThing(postPath($pid)."f",$data);
}

function tempPath($i=0){
	if ($i) return ($DB_ROOT.'db/temp/'.$i.'/');
	else return ($DB_ROOT.'db/temp/');
}

function tempUserExists($id){
	return file_exists(tempPath($id));
}

function setTempUserSetting($id, $setting, $change){
	postThing(tempPath($id)."/".$setting,$change);
}

function tempUserSetting($id,$setting){
	$data = file_get_contents(tempPath($id).$setting);
	return $data;
}

function removeTempUser($id){
	destroy(tempPath($id));
	rmdir(tempPath($id));
}

function applyUser($name,$email){
	$id = md5($email);
	if (tempUserExists($id)) removeTempUser($id);
	mkdir(tempPath().$id,0700);
	setTempUserSetting($id,'name',$name);
	setTempUserSetting($id,'email',$email);
	return $id;
}

?>