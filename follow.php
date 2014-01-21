<?php
	require('data.php');
	sessionRegen(true);
	if($_POST["op"] == "f"){
		followPost($_POST["uid"],$_POST["pid"]);
	}
	if($_POST["op"] == "u"){
		unfollowPost($_POST["uid"],$_POST["pid"]);
	}
?>