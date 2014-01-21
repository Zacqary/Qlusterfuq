<?php require('functions.php');
$currentPage = 'index';
$subtitle = makeSubtitle(setting('index-subtitle'));
require('header.php');
	require('sidebar.php');
	require('stream.php');
require('footer.php');
?>
