<?php
include('functions.php');
sessionRegen(true);
$op = $_POST["op"];
$pid = "x".$_POST["pid"]; //I don't know why we need to add an x to the beginning for strpos to work. PHP is a fucking idiot.
$oldpid = $pid;

//Get a raw number from the pid
if (strpos($pid, "post-")) $pid = strtr($pid,array("xpost-"=>""));
if (strpos($pid, "-com-")) { $pid = strtr(strstr($pid,"-com-",true),array("x"=>"")); $cid = strtr(strstr($_POST["pid"],"-com-"),array("-com-"=>"")); }

if ($cid) $post = openComment($pid,$cid);
else $post = (openPost($pid));
if (!editAuth(getAuthor($post))) $op = 'authfail'; //Check to make sure the user has permission to edit


if ($op == "o"){ //Open the raw text of a post
	$body = o($post);
	if (strpos($body,"span class='image-share'>")) $body = strtr(strstr($body,"</span>"),array("</span>"=>""));
	if (strpos($body,"{{{∵}}}")){ $body = strtr($body,array("{{{∵}}}"=>"")); }
	echo($body);
}

else if ($op == "oc"){ //Open the raw text of a comment
	echo(oc($post));
}

else if ($op == 'up') { //Update a post
	$body = $_POST["body"];
	if (strpos(o($post), "span class='image-share'>")) {
		if (strpos(o($post), "</span><br><br>")) $img = strstr(o($post),"</span><br><br>",true)."</span><br><br>";
		else $img = strstr(o($post),"</span>",true)."</span>";
		$body = $img.$body;
	}
	else if (!$body) $body = o($post);
	if (strpos(o($post), "{{{∵}}}"))  {
		$body .= "{{{∵}}}";
		$event = new Event($_POST["name"],$_POST["date"],$_POST["hour"].":".$_POST["minute"].$_POST["ampm"],$_POST["location"],$pid);
		$prevevent = openEvent($pid);
	}
	$user = getAuthor($post);
	$time = getRawTimestamp($post);
	if ( ($body != o($post)) || (strpos(o($post), "{{{∵}}}")) ){ //Make sure an edit has actually occurred, unless it's an event
		$timestamp = "";//"<p class='edit-timestamp'>Last edited ".timestamp()."</p>"; //Create timestamp
		$body .= "\n\n".$timestamp;	//Add timestamp
		postPost($pid,$user,$time,$body,$event);
		if (differentEvents($event,$prevevent)) notifyEditEvent($pid,$event);
	}
	echo(parseMarkdown(strtr($body,array("{{{∵}}}"=>""))));
}
else if ($op == 'upc') { //Update a comment
	if ($_POST["body"] != oc($comment)){ 
		$timestamp = "";// "<p class='edit-timestamp'>Last edited ".timestamp()."</p>";
		$data .= "\n\n".$timestamp;
		postComment($pid,$cid,getAuthor($post),getRawTimestamp($post),$_POST["body"]);
	}
	echo(parseMarkdown($_POST["body"]));
}
else if ($op == 'd') { //Delete something
	if ($cid) hideComment($pid,$cid);
	else {
		$author = getAuthor($post);
		hidePost($pid);
		if ($_POST['ename']) notifyCancelEvent($pid,$author);
	}
}
else if ($op == 'authfail'){
	echo('Error: Not authorized');
	echo($oldpid);
}
else { //This should never, ever, ever happen. But if it does, at least you'll know.
	echo("Error: Invalid operation");
}
////
function o($post){
	$body = getPost($post);
	//Wipe away the previous edit timestamp
	if (strpos($body, "\n\n<p class='edit-timestamp'>")) $body = strstr($body,"\n\n<p class='edit-timestamp'>",true);
	return $body;
}

function oc($post){
	$body = getComment($post);
	if (strpos($body, "\n\n<p class='edit-timestamp'>")) $body = strstr($body,"\n\n<p class='edit-timestamp'>",true);
	return $body;
}
?>