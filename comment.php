<?php
include('functions.php');
$user = $_POST['user'];
if (editAuth($user)) {
	//Count how many comments are there already
	$count = 1;
	while(1){
		if(!commentExists($_POST["pid"],$count)) break; //If that number comment doesn't exist
		$count++; //If that number comment does exist
	}

	//Post ID, new comment ID, user, time, comment body
	postComment($_POST["pid"],$count,$user,time(),$_POST["body"]);
	//Open the newly created comment and show it. The 1 signals that this is from an Ajax request.
	showComment(openComment($_POST["pid"],$count),$_POST["pid"],$count,1);
	notifyNewComment($_POST["pid"],$count);
}
?>