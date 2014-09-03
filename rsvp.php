<?php
	require('data.php');
	require('notify.php');
	sessionRegen(true);
	if($_POST["op"] == "a"){
		if ( attendEvent($_POST["uid"],$_POST["pid"])  == "new"){
			notifyNewRSVP($_POST["pid"],$_POST["uid"]);
		}
	}
	if($_POST["op"] == "r"){
		if ( attendEvent($_POST["uid"],$_POST["pid"],true) == "new"){
			notifyNewRSVP($_POST["pid"],$_POST["uid"], true);
		}
	}
	if($_POST["op"] == "u"){
		unattendEvent($_POST["uid"],$_POST["pid"]);
	}
	
	$eventHTML = "";
	$attendeeList = getAttendees($_POST["pid"]);
	$attendees = [];
	$rides = [];
	foreach ($attendeeList as $key => $val){
		if (userExists($key)) {
			if ($val == "ride") array_push($rides, $key);
			else array_push($attendees, $key);
		}
	}
	if (sizeof($attendees) ) {	
		$eventHTML .= "<h4>Attending:</h4>";
		foreach ($attendees as $key => $val){
			$eventHTML .= "<a title='".userSetting($val,"name")."' rel='tooltip' href='".userSetting($val,"url")."'>";
			$eventHTML .= getAvatar($val,32);
			$eventHTML .= "</a>";
		}
	}
	if (sizeof($rides) ) {	
		$eventHTML .= "<h4>Needs a Ride:</h4>";
		foreach ($rides as $key => $val){
			$eventHTML .= "<a title='".userSetting($val,"name")."' rel='tooltip' href='".userSetting($val,"url")."'>";
			$eventHTML .= getAvatar($val,32);
			$eventHTML .= "</a>";
		}
	}
	echo $eventHTML;
?>