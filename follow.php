<?php
	require('data.php');
	if($_POST["op"] == "f"){
		followPost($_POST["uid"],$_POST["pid"]);
	}
	if($_POST["op"] == "u"){
		unfollowPost($_POST["uid"],$_POST["pid"]);
	}
?>