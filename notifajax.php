<?php
	require 'functions.php';
	if ($_POST['op'] == "clear") markNotificationsAsRead($_POST['uid']);
	if ($_POST['op'] == "more") listNotifications($_POST['uid'],$_POST['start']);
?>