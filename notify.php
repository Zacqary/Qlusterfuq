<?php

function notifyEditEvent($pid,$event){
	$nevent = array(8,9,10,11,12,13,14,15);
	$users = theUsers();
	$post = openPost($pid);
	$author = getAuthor($post);
	$timestamp = time();
	$body = strstr(strtr(parseMarkdown(getPost($post)),array("{{{∵}}}"=>"")),"<p class='edit-timestamp'>",true);
	$type = "event";
	$meta = "modified an event:";
 	$text = $meta." ".$event->name." on ".$event->date; 
	$type = "event";
	$body = "The new details of the event are:\n".$event->name." on ".$event->date." at ".$event->time."\n".$event->location."\n\n".$body;
	foreach ($users as $key => $val){
		if ($author != $val) {
			addNotification($val,$timestamp,$author,$text,"/post/".$pid);
			if ( array_search(userSetting($val,'emailme'),$nevent) ){
				mailNotification($val,$author,$type,$meta,$body,theRoot()."/post/".$pid);
			}
		}
	}
}

function notifyCancelEvent($pid,$author){
	$nevent = array(8,9,10,11,12,13,14,15);
	$users = theUsers();
	$timestamp = time();
	$type = "cancelevent";
	$event = openEvent($pid);
	$meta = "cancelled the event ".$event->name." on ".$event->date;
	$text = $meta;
	$body = false;
	foreach ($users as $key => $val){
		if ($author != $val) {
			addNotification($val,$timestamp,$author,$text,"#");
			if ( array_search(userSetting($val,'emailme'),$nevent) ){
				mailNotification($val,$author,$type,$meta,$body,"#");
			}
		}
	}
}

function notifyNewPost($pid,$event=false){
	$npost = array(1,3,5,7,9,11,13,15);
	$nimage = array(4,5,6,7,12,13,14,15);
	$nevent = array(8,9,10,11,12,13,14,15);
	$types = array(
		"post" => $npost,
		"image" => $nimage,
		"event" => $nevent,
		);
	$users = theUsers();
	$post = openPost($pid);
	$author = getAuthor($post);
	$timestamp = getRawTimestamp($post);
	$body = strtr(parseMarkdown(getPost($post)),array("{{{∵}}}"=>""));
	$excerpt = strip_tags($body)."{{‡}}";
	$excerpt = strtr($excerpt,array("\n{{‡}}"=>""));
	$excerpt = strtr($excerpt,array("\n"=>" "));
	$excerpt = strtr($excerpt,array("{{‡}}"=>""));
	$meta = "wrote a new post:";
	$text = $meta." \"". $excerpt."\"";
	if (strlen($text) > 100) $text = substr($text,0,98)."…\"";
	$type = "post";
	if ($event) { 
		$meta = "announced an event:";
	 	$text = $meta." ".$event->name." on ".$event->date; 
		$type = "event";
		$body = $event->name." on ".$event->date." at ".$event->time."\n".$event->location."\n\n".$body; }
	else if (strpos($body,"span class='image-share'>")) { 
		$meta = "shared an image."; 
		$text = $meta; 
		$type = "image";
	}
	foreach ($users as $key => $val){
		if ($author != $val) {
			addNotification($val,$timestamp,$author,$text,"/post/".$pid);
			if ( array_search(userSetting($val,'emailme'),$types[$type]) ){
				mailNotification($val,$author,$type,$meta,$body,theRoot()."/post/".$pid);
			}
		}
	}
}

function notifyNewComment($pid,$cid){
	$ncomment = array(2,3,6,7,10,11,14,15);
	$users = theUsers();
	$post = openPost($pid);
	$postauthor = getAuthor($post);
	$comment = openComment($pid,$cid);
	$author = getAuthor($comment);
	$timestamp = getRawTimestamp($comment);
	$body = parseMarkdown(getComment($comment));
	$type = "comment";
	$excerpt = strip_tags($body);
	$excerpt = strtr($excerpt,array("\n{{‡}}"=>""));
	$excerpt = strtr($excerpt,array("\n"=>" "));
	$excerpt = strtr($excerpt,array("{{‡}}"=>""));
		$meta = "commented on <span class='notif-author'>".userSetting($postauthor,"name")."</span>'s post:";
		$text = $meta." \"". $excerpt."\"";
		$meta = strtr($meta,array("<span class='notif-author'>"=>"","</span>"=>""));
	if (strlen($text) > 100) $text = substr($text,0,98)."…\"";
	foreach ($users as $key => $val){
		if (isFollowing($val,$pid)){
			if ($author != $val) {
				if ($postauthor == $val) {
					$meta = "commented on your post:";
					$text = $meta." \"". $excerpt."\"";
				}
				else {
					$meta = "commented on <span class='notif-author'>".userSetting($postauthor,"name")."</span>'s post:";
					$text = $meta." \"". $excerpt."\"";
				}
				addNotification($val,$timestamp,$author,$text,"/post/".$pid."/#".$pid."-com-".$cid);
				if ($meta == "commented on <span class='notif-author'>".userSetting($postauthor,"name")."</span>'s post:")
					$meta = "commented on ".userSetting($postauthor,"name")."'s post:";
				if (array_search(userSetting($val,'emailme'),$ncomment)){
					mailNotification($val,$author,$type,$meta,$body,theRoot()."/post/".$pid."/#".$pid."-com-".$cid);
				}
			}
		}
	}
}

function listNotifications($uid,$start=0){
	$notes = openNotifications($uid);
	krsort($notes);
	if (count($notes) > $start+10) $theresmore = true;
	$count = 0;
	$unread = 0;
	$shown = 0;
	foreach($notes as $key => $val){
		$type = getNotificationType($val);
		if ($type) {
			if(($count >= $start) && ($count <= $start+10)){
				if(getNotificationStatus($val) == "unread") {echo("<li class='unread notification' data-id='".$key."'>"); $unread++;}
				else echo("<li class='notification'>");
				echo("<a href='".theRoot().getNotificationLink($val)."'>");
				echo(getAvatar(getAuthor($val),32)."<div class='notification-body'><p><span class='notif-author'>".userSetting(getAuthor($val),"name")."</span> ".getNotificationText($val));
				echo("</p><p class='muted'>".makeTimestamp($key)."</p></div>");
				echo("</a></li>");
				$shown++;
			}
			$count++;
		}
	}
	if ($shown < 11) $theresmore = false;
	if ($theresmore) echo("<li><a class='more-notifications click'>Show more notifications</a></li>");
	echo("<input type='hidden' id='unread-count' data-unread='".$unread."' />");
	echo("<script type='text/javascript'>
		if ($('#unread-count').attr('data-unread') > 0){
			$('#messages-dropdown').append('<span class=\'badge badge-important overbadge\'>'+$('#unread-count').attr('data-unread')+'</span>');
			$('#messages-dropdown').click(function(){
				$('.overbadge').fadeOut(500);
				var dataString = 'uid=".$uid."&op=clearnot';
				$.ajax({
					type: 'POST',
					url: '".theRoot()."/ajax.php',
					data: dataString,
					success: function(data){
						return false;
					}
				});
			});
		}
		$('.more-notifications').click(function(){
			$('.more-notifications').html('Loading…');
			$('.dropdown-notifications').dropdown('toggle');
			var dataString = 'uid=".$uid."&op=morenot&start=".($start+10)."';
			$.ajax({
				type: 'POST',
				url: '".theRoot()."/ajax.php',
				data: dataString,
				success: function(data){
					$('.dropdown-notifications').html(data);
					return false;
				}
			});
		});
	</script>");
}

function mailNotification($uid,$author,$type,$meta,$body,$link){
	$user = new User($uid);
	$to = deprivate($user->email);
	$subject = userSetting($author,"name")." ".substr($meta,0,-1)." at ".siteName();
	$headers = "From: ".siteName()."<".setting("daemon").">\r\n" .
	     "X-Mailer: php";
	ob_start(); //Turn on output buffering
	?>
Hi <?php echo $user->name ?>,

<?php echo userSetting($author,"name")?> <?php echo substr($meta,0,-1)?> at <?php echo siteName()?>:
<?php if($body) {?>
	
----

<?php echo strip_tags($body)?>

----<?php } ?>
<?php if($type != 'cancelevent') {?>
	
You can view or respond to the <?php echo $type?><?php if($type == "comment") {?>, or unfollow the post to stop receiving notifications about it,<?php }?> at <?php echo $link?><?php }?>

To stop receiving these emails, change your notification settings at <?php echo (theRoot()."/".$user->url."#control-panel")?>
<?
	//copy current buffer contents into $message variable and delete current output buffer
	$message = ob_get_clean();
	
	
		mail($to, $subject, $message, $headers);
	
}

?>