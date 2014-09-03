<?php
require_once('config.php');

function addLog($type, $message){
	$file = DB_ROOT()."db/log/".$type."-".date("m-Y").".log";
	$handle = fopen($file, "a");
	fwrite($handle, date("** D d H:i:s -- "));
	fwrite($handle, $message);
	fwrite($handle, "\n");
	fclose($handle);
}

?>