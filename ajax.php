<?php
	require 'functions.php';
		if ($_POST['op'] == "clearnot") markNotificationsAsRead($_POST['uid']);
		if ($_POST['op'] == "morenot") listNotifications($_POST['uid'],$_POST['start']);
		if ($_POST['op'] == "asterisktip") setUserSetting($_POST['uid'],"asterisktip","no");
?>