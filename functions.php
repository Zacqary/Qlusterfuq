<?php
require_once('markdown.php');
require_once('data.php');
require_once('notify.php');
require_once('smtp-mailer.php');
$functioning = true;

function showComment($comment, $i, $commentCount,$ajax=0){
	$author = new User(getAuthor($comment)); //Get User name, picture, etc.
	$body = parseMarkdown(getComment($comment)); //Get the comment text and parse the Markdown
	/////////////////
	echo ("
		<div class='comment row row-".$i."-com-".$commentCount."'>
			<div class='span1 avatar48 hidden-phone hidden-tablet'>".getAvatar($author->uid,48)."</div>
			<div class='span1 avatar48 hidden-desktop'>".getAvatar($author->uid,32)."</div>
			<div class='span5' id='".$i."-com-".$commentCount."'>
				<span class='comment-author'>".authorName($author)."</span>
				<div class='comment-body'>
					".$body."
				</div>
				<p class='post-meta' id='".$i."-com-".$commentCount."'>");
		if(editAuth($author->uid))echo("<a role='button' tabindex='0' class='c-edit-click click'><i class='icon-edit'></i> Edit</a> ·
				<a role='button' tabindex='0'class='c-delete-click click'><i class='icon-trash'></i> Delete</a> · ");
		echo(makeTimelink($comment,theRoot()."/post/".$i."/#".$i."-com-".$commentCount)."</p>
			</div><!--.span4-->
		</div><!--#".$i."-com-".$commentCount."-->");
}

function showComments($i, $collapse=false){
	$commentTotal = 1;
	while(1){ //Count comments
		if (!commentExists($i,$commentTotal)) break; //If we've counted all the comments or if there are none, stop
		$commentTotal++;
	}
	if (($commentTotal > 1) || (getLoggedInUser())) { //If nobody's logged in, make sure we don't display a blank comments div
		echo("<div id='comments-".$i."' class='comments'>");
		if ($collapse) echo("<div class='collapse-wrap' id='collapse-wrap-".$i."'>"); //Wrapper for expanding comments;
		else ($wrapped = true);
			//Display the list of comments
	
			//Get an adjusted comment total that excludes deleted comments
			$adjCommentTotal = $commentTotal-1;
			for($commentCount=1;$commentCount<$commentTotal;$commentCount++){
				$comment = openComment($i,$commentCount);
				if (isDeleted($comment)) $adjCommentTotal--;
			}
			for($commentCount=1;$commentCount<$commentTotal;$commentCount++){
				$comment = openComment($i,$commentCount); //Read db/p/Post ID/Comment ID
				if (!isDeleted($comment)) { //If we successfully find an author, the comment hasn't been deleted
						showComment($comment,$i,$commentCount);
						$adjCommentCount++;
					}
				if(($collapse) && ($adjCommentTotal-$adjCommentCount == setting('collapsemax')) &&  ($adjCommentTotal > setting('collapsemax')) && (!$wrapped)) {
					echo("</div>"); //Close collapse wrapper
					$wrapped = true;
					echo("<script type='text/javascript'>
						$('#collapse-wrap-".$i."').after('<div class=\'comment-decollapse\' id=\'decollapse-".$i."\'>View all ".$adjCommentTotal." comments</div>');
						$('#collapse-wrap-".$i."').hide();
						$('#decollapse-".$i."').click(function(){
							$('#decollapse-".$i."').hide();
							$('#collapse-wrap-".$i."').show();
						});
					</script>");
				}
			}
			if (!$wrapped) echo("</div>");
			if (getLoggedInUser()) showCommentsForm($i); //Place the Post Comment form at the bottom
		echo("</div>");
	}
}

function showPost($i, $stream=false){
	$post = openPost($i); //Read the post data
	if (!isDeleted($post)) { //Check if the post is hidden
		$author = new User(getAuthor($post)); //Get User name, picture, etc.
		$body = parseMarkdown(getPost($post)); //Get the post text and parse the Markdown
		//Parse event
		if (strpos($body,"{{{∵}}}")){
			$body = strtr($body,array("{{{∵}}}"=>""));
			$event = openEvent($i);
			$eventHTML = "
				<div class='event-details'>
				<h3 class='event-title'>".$event->name."</h3>
				<p class='event-meta'>Date: <span class='event-date'>".$event->date."</span> · Time: <span class='event-time'>".$event->time."</span>";
			if ($event->location) { 
				if ($event->location != "Log in to see location") 
				$eventHTML .= "<br><a target='_new' href='https://maps.google.com/?q=".$event->location."' class='event-location'>".$event->location."</a>";
				else $eventHTML .= "<br>".$event->location;
			}
			$eventHTML .= "</div>";
		}
		//////////////////
		echo ("<div class='row row-post-".$i."'>
			<div class='span7 post'>");
		$streamid = "";
		if ($stream) $streamclass = " stream-post-body";
		echo("
				<div class='row'>
				<div class='span1 avatar72 visible-desktop'>".getAvatar($author->uid,72)."</div>
				<div class='span1 avatar72 hidden-desktop'>".getAvatar($author->uid,48)."</div>
				<div class='span6' id='post-".$i."'>
					<span class='post-author'>".authorName($author)."</span>");
		echo($eventHTML."
					<div class='post-body".$streamclass."'>
						".$body."
					</div>
					<p class='post-meta' id='post-".$i."'>");
		if(editAuth($author->uid))echo("<a role='button' tabindex='0'class='edit-click click'><i class='icon-edit'></i> Edit</a> · <a role='button' tabindex='0'class='delete-click click'><i class='icon-trash'></i> Delete</a> · ");
		if(getLoggedInUser()) {
			if(!isFollowing(getLoggedInUser(),$i)) echo("<a role='button' tabindex='0'class='click follow-click' id='follow-".$i."' data-uid='".getLoggedInUser()."' data-pid='".$i."' data-follow='follow' rel='tooltip' title='Get notified about comments on this post'><i class='icon-star-empty'></i> Follow post</a> · ");
			else echo("<a role='button' tabindex='0'class='click follow-click' id='follow-".$i."' data-uid='".getLoggedInUser()."' data-pid='".$i."' data-follow='unfollow' rel='tooltip' title='Stop getting notified about comments on this post'><i class='icon-star'></i> Unfollow post</a> · ");
		}
		echo("<a href='".theRoot()."/post/".$i."/' rel='tooltip' title='Link to this post'>Link</a> · ".makeTimelink($post,theRoot()."/post/".$i."/")."</p>");
		showComments($i,$stream);
		echo("
				</div><!--.span5-->
				</div><!--.row-->
			</div><!--#post-".$i."-->
		</div><!--.row-post-".$i."-->");
		if ($stream) return true;
	}
	else if ($stream) return false;
}


function showCommentsForm($i){
	echo(
		"<div class='placeholder' id='comment-placeholder-".$i."'>Leave a comment…</div>
		<form action='' method='post' id='comment-form-".$i."'>
			<div class='row'>
			<div class='span1 avatar48' id='comment-form-av'>".getAvatar(getLoggedInUser(),48)."</div>
			<div class='span5'><textarea name='body' rows='5' id='comment-form-body-".$i."' class='comment-editor'></textarea>
			<input type='submit' value='Post' id='submit-button-".$i."' class='submit-button btn btn-primary btn-large'></input>
			<input type='button' value='Cancel' id='cancel-button-".$i."' class='submit-button btn btn-warning btn-large'></input></div>
			</div>
			</form>\n
			
			<script type='text/javascript'>	
			$('#comment-form-".$i."').hide();
				$('#comment-placeholder-".$i."').click(function(){
					$('#comment-placeholder-".$i."').hide();
					$('#comment-form-".$i."').show();
					$('#comment-form-body-".$i."').focus();					
				});
				$('#comment-link-".$i."').click(function(){
					$('#comment-placeholder-".$i."').hide();
					$('#comment-form-".$i."').show();					
				});	
				$('#cancel-button-".$i."').click(function(){
					$('#comment-form-".$i."').hide();
					$('#comment-placeholder-".$i."').show();
					$('#comment-form-body-".$i."').val('');
					$('#comment-error-".$i."').remove();
				});
				$('#submit-button-".$i."').click(function(){
					var body = $('#comment-form-body-".$i."').val();
					if (body == ''){
						$('#comment-error-".$i."').remove();
						$('#cancel-button-".$i."').after('<div class=\'alert alert-error comment-error\' id=\'comment-error-".$i."\'>At least type <em><strong>something!</strong></em></div>');
						return false;
					}
					$(this).after('<img class=\'loader\' src=\''+theRoot+'img/ajax-loader.gif\'>');
					$('#comment-error-".$i."').remove();
					$('#comment-form-body-".$i."').val('');
					var pid = '".$i."';
					var user = '".getLoggedInUser()."';
					var dataString = 'body=' + encodeURIComponent(body) + '&pid=' + pid + '&user=' + user;
					$.ajax({
						type: 'POST',
						url: theRoot+'comment.php',
						data: dataString,
						success: function(data){
							$('#comment-form-".$i."').hide();
							$('#comment-placeholder-".$i."').show();
							$('#comment-placeholder-".$i."').before(data);
							$('#follow-".$i."').html('<i class=\'icon-star\'></i> Unfollow post');
							$('#follow-".$i."').attr('data-follow','unfollow');
							$('#follow-".$i."').attr('data-original-title','Stop getting notified about comments on this post');
							$('.loader').remove();
						}
					});
					return false;
				});
			</script>"
		);
}

function showPostForm(){
	echo(
		"<div id='post-form-wrapper'>
		<ul class='nav nav-tabs' id='post-form-tabs'>
			<li class='active' id='write-post-tab'><a>Write Post</a></li>
			<li id='share-image-tab'><a>Share Image</a></li>
			<li id='announce-event-tab'><a>Announce Event</a></li>
		</ul>
		<div id='post-tab' class='form-tab'>
		<div class='placeholder' id='post-placeholder'>Write new post…</div>
		<form action='' method='post' id='post-form'>
			<div class='event-tab'>
			<input type='text' id='event-name' maxlength='140'></input>
			<label for='event-name' class='up'>Event Name</label>
			<div class='left-field'>
			<input type='text' name='date' id='datepicker'></input>
			<label for='date' class='up'>Date</label>
			</div>
			<div class='right-field'>
			<input type='text' id='hour' name='hour' class='time-field' maxlength='2' value='12'></input> : <input type='text' name='minute' id='minute' class='time-field time-nudge' maxlength='2' value='00'></input> <select name='ampm' class='ampm' id='ampm'><option>pm</option><option>am</option></select>
			<label for='time' class='up'>Time</label>
			</div>
			<div class='rightmost-field'>
			<input type='text' name='location' class='location-field' id='location'></input>
			<label for='location' class='up'>Location</label>
			</div>
			</div>
			<div id='post-editor' class='body-editor'></div>
			<span class='event-tab'><label>Description</label></span>
			<textarea name='body' rows='10' id='post-form-body'></textarea>
			<input type='submit' value='Post' id='submit-button' class='submit-button btn btn-primary btn-large'></input>
			<input type='button' value='Preview' data-preview='preview' id='preview-button' class='submit-button btn btn-success btn-large'></input>
			<input type='button' value='Cancel' id='cancel-button' class='submit-button btn btn-warning btn-large'></input>
			<div id='text-toolbar' class='hidden-phone'>
				<button id='bold-button' class='button btn btn-inverse'><i class='icon-bold icon-white'></i></button>
				<button id='italic-button' class='button btn btn-inverse'><i class='icon-italic icon-white'></i></button>
				<button id='quote-button' class='button  btn btn-inverse'>Quote</button>
				<button id='header-button' class='button  btn btn-inverse'>Header</button>
				<button id='link-button' class='button btn btn-info'>Link</button>
				<button id='img-button' class='button btn btn-info'><i class='icon-picture icon-white'></i></button>
			</div>
			<br><div class='alert alert-error' id='post-error'></div>");
			if(userSetting(getLoggedInUser(),"asterisktip") == 'yes') echo("<div class='alert alert-block alert-info' id='asterisk-tip'><a class='close' data-dismiss='alert' href='#'>×</a><h4>Psst!</h4><p>Putting <strong>*words between asterisks*</strong> makes them <em>italic</em>. So *pokes you* = <em>pokes you</em>. Use backslashes to type literal asterisks like this: \*pokes you\* = *pokes you*. Hit the Preview button to make sure you're doing it right.</p>
			<p><a role='button' taborder='0' class='click' id='asterisk-tip-remove' data-dismiss='alert'>Don't show me this again</a></p></div>");
		echo("
		</form>
		</div>");
		echo("<div id='image-tab' class='form-tab'>
		<form enctype='multipart/form-data' action='image.php' method='POST' id='image-form' target='upload-target'>
		<div class='file-upload'><p>Select an Image:</p><input name='image_file' id='image-file' type='file' class='btn' /></div>
		<input type='hidden' value='".getLoggedInUser()."' name='user' />
		<textarea name='description' rows='5' id='img-description' class='comment-editor'>Image description</textarea>
		<button id='upload-button' class='submit-button btn btn-large btn-primary'><i class='icon-upload icon-white'></i> Upload</button>
		<br><div class='alert alert-error' id='image-error'></div>
		</form>
		<iframe id='upload-target' name='upload-target' href='#'></iframe>
		</div>");
		echo("
		<script type='text/javascript'>
			$('#write-post-tab').click(function(){
				$('#write-post-tab').addClass('active');
				$('#share-image-tab').removeClass('active');
				$('#announce-event-tab').removeClass('active');
				$('#post-tab').show();
				$('#image-tab').hide();
				$('.event-tab').hide();
				$('#post-editor').addClass('body-editor');
				$('#post-editor').removeClass('event-editor');
				$('#post-placeholder').hide();
				$('#post-placeholder').html('Write new post…');
				$('#post-form').show();
				$('#post-error').hide();
				editor.load();
				editor.focus();
			});
			$('#share-image-tab').click(function(){
				$('#write-post-tab').removeClass('active');
				$('#share-image-tab').addClass('active');
				$('#announce-event-tab').removeClass('active');
				$('#post-tab').hide();
				$('#image-tab').show();
				$('.event-tab').hide();
				$('#image-error').hide();
			});
			$('#announce-event-tab').click(function(){
				$('#write-post-tab').removeClass('active');
				$('#share-image-tab').removeClass('active');
				$('#announce-event-tab').addClass('active');
				$('#post-tab').show();
				$('#image-tab').hide();
				$('.event-tab').show();
				$('#post-editor').removeClass('body-editor');
				$('#post-editor').addClass('event-editor');
				$('#post-placeholder').hide();
				$('#post-placeholder').html('Post new event…');
				$('#post-form').show();
				editor.load();
				$('#event-name').focus();
				$('.alert').hide();
			});
			
			element = document.getElementById('post-editor');
			var editor = new EpicEditor(element).options({
				focusOnLoad:true,
				file:{
				    name:'PostForm',
				    defaultContent:''
				  }
			}).load();
			$('#post-form-body').hide();
			$('#image-tab').hide();
			$('.event-tab').hide();
			$( '#datepicker').datepicker({
				showAnim: 'slide',
				showOtherMonths: true,
				selectOtherMonths: true
			});
			$('#hour').blur(function(){
				var val = $(this).val();
				if (val == '') $(this).val('12');
				if (val.length == 1) $(this).val('0'+val);
			})
			$('#minute').blur(function(){
				var val = $(this).val();
				if (val == '') $(this).val('00');
				if (val.length == 1) $(this).val('0'+val);
			})
			$('#asterisk-tip').hide();
			$('#asterisk-tip-remove').click(function(){
				$.ajax({
					type: 'POST',
					url: theRoot+'ajax.php',
					data: 'op=asterisktip&uid=".getLoggedInUser()."',
					success: function(data){
						$('#asterisk-tip').remove();
					}
				});
			});
			editor.on('save',function(){
				var text = editor.get('editor').value;
				$('#post-form-body').val(text);
				if($('#post-form-body').val().indexOf('*') > -1) $('#asterisk-tip').show();
				});
			editor.save();
			$('#post-form').hide();
			$('#post-placeholder').click(function(){
				$('#post-placeholder').hide();
				$('#post-form').show();
				editor.focus();
				if($('.event-tab').is(':visible')) $('#event-name').focus();
			});
			$('#cancel-button').click(function(){
				editor.remove('PostForm');
				editor.open('PostForm');
				editor.edit();
				$('#preview-button').show();
				$('#depreview-button').remove();
				$('#post-form').hide();
				$('#post-placeholder').show();
				$('.alert').hide();
				$('#datepicker').val('');
				$('#event-name').val('');
				$('#hour').val('12');
				$('#minute').val('00');
				$('#ampm').val('pm');
				$('#location').val('');
			});
			$('.alert').hide();
			$('#ampm').val('pm');
			$('#submit-button').click(function(){
				if ($('.event-tab').is(':visible')){
					if ($('#event-name').val() == '') {
						$('#post-error').show().html('You forgot to name the event.');
						return false;
					}
					if ($('#datepicker').val() == '') {
						$('#post-error').show().html('Oh, when\'s this event happening? Never? That\'s kind of dumb. Pick a date.');
						return false;
					}
					if ($('#post-form-body').val() == '') {
						$('#post-error').show().html('No description. Right. Because your event is <em>sooooooo</em> self-explanatory. Look, just humor me and type something in the description box, okay? It\'s not gonna kill you.');
						return false;
					}
				}
				if ($('#post-form-body').val() == '') {
					$('#post-error').show().html('At least type <em><strong>something!</strong></em>');
					return false;
				}
				else {
					editor.remove('PostForm');
					$(this).after('<img class=\'loader\' src=\''+theRoot+'img/ajax-loader.gif\'>');
					var body = $('#post-form-body').val();
					var user = '".getLoggedInUser()."';
					var dataString = 'body=' + encodeURIComponent(body) + '&user=' + user;
					if ($('.event-tab').is(':visible')){
						var name = $('#event-name').val();
						var date = $('#datepicker').val();
						var hour = $('#hour').val();
						var minute = $('#minute').val();
						var ampm = $('#ampm').val();
						var location = $('#location').val();
						dataString += '&name=' + encodeURIComponent(name) + '&date=' + date + '&hour=' + hour + '&minute=' + minute + '&ampm=' + ampm + '&location=' + encodeURIComponent(location);
					}
					$.ajax({
						type: 'POST',
						url: theRoot+'post.php',
						data: dataString,
						success: function(data){
							editor.load();
							$('#preview-button').show();
							$('#depreview-button').remove();
							$('.alert').hide();
							$('#datepicker').val('');
							$('#event-name').val('');
							$('#hour').val('12');
							$('#minute').val('00');
							$('#ampm').val('pm');
							$('#location').val('');
							$('#post-form').hide();
							$('#post-placeholder').show();
							$('#post-form-wrapper').after(data);
							$('.loader').remove();
						}
					});
					return false;
				}
			});
			$('#upload-button').click(function(){
				if($('#image-file').val() == '') {
					$('#image-error').show().html('You should probably pick an image to upload before clicking that button.');
					return false;
				}
				else if( ($('#image-file').val().indexOf('.bmp') > -1) || ($('#image-file').val().indexOf('.psd') > -1) || ($('#image-file').val().indexOf('.tif') > -1) || ($('#image-file').val().indexOf('.pcx') > -1) || ($('#image-file').val().indexOf('.tga') > -1) || ($('#image-file').val().indexOf('.ai') > -1) || ($('#image-file').val().indexOf('.eps') > -1)) {
					$('#image-error').show().html('Really? You\'re trying to upload an image in that format? PNG, JPEG, or GIF are too unrefined for your <em>exquisite</em> taste in image filetype? Well, you know what? Sometimes we all just have to bite the bullet and upload a file type that the server can handle. So stop being difficult and give me something I can work with.');
					return false;
				}
				else if( ($('#image-file').val().indexOf('.jpg') == -1) && ($('#image-file').val().indexOf('.jpeg') == -1) && ($('#image-file').val().indexOf('.gif') == -1) && ($('#image-file').val().indexOf('.png') == -1)) {
					$('#image-error').show().html('That\'s not an image.');
					return false;
				}
				$(this).after('<img class=\'loader\' src=\''+theRoot+'img/ajax-loader.gif\'>');
				$('.alert').hide();
				$('#upload-target').on('load',function(){
						var data = $('#upload-target').contents().find('.row').outerHTML();
						$('#img-description').val('Image description');
						$('#image-file').val('');
						$('#image-tab').hide();
						$('#post-placeholder').show();
						$('#post-form').hide();
						$('#write-post-tab').addClass('active');
						$('#share-image-tab').removeClass('active');
						$('#post-tab').show();
						$('#image-tab').hide();
						$('#post-form-wrapper').after(data);
						$('#upload-target').contents().replaceWith('');
						$('.loader').remove();
						editor.load();
						$('#upload-target').off();
				});
			});
			$('#img-description').click(function(){
				$('#img-description').val('');
			});
			$('#img-description').blur(function(){
				if ($('#img-description').val() == '') $('#img-description').val('Image description');
			});
			$('#preview-button').click(function(){
				if ($(this).attr('data-preview') == 'preview') { editor.preview(); $(this).attr('data-preview','edit'); $(this).addClass('active');}
				else if ($(this).attr('data-preview') == 'edit') { editor.edit(); $(this).attr('data-preview','preview'); $(this).removeClass('active');}
			});
			$('#bold-button').click(function(){
				var insert = editor.get('editor').value + '**put bold text here**';
				editor.import('PostForm',insert);
				return false;
			});
			$('#italic-button').click(function(){
				var insert = editor.get('editor').value + '*put italic text here*';
				editor.import('PostForm',insert);
				return false;
			});
			$('#header-button').click(function(){
				var insert = editor.get('editor').value + '\\n#put header after this hash';
				editor.import('PostForm',insert);
				return false;
			});
			$('#quote-button').click(function(){
				var insert = editor.get('editor').value + '\\n>put quoted text after this angle';
				editor.import('PostForm',insert);
				return false;
			});
			$('#link-button').click(function(){
				var insert = editor.get('editor').value + '[put link text here](put://url.here)';
				editor.import('PostForm',insert);
				return false;
			});
			$('#img-button').click(function(){
				var insert = editor.get('editor').value + '![put alt text here](put://url.of/image/here)';
				editor.import('PostForm',insert);
				return false;
			});
			
		</script>
		</div>"
	);
}

function streamPosts($start,$count){
 	$done = 0;
	$i = $start;
	while(1){
		if ($done==$count) break; //If $count posts have been displayed, stop
		if ($i==0) break;
		if (showPost($i,true)) $done++; //If the post hasn't been deleted, count it towards $done
		$i--; //Decrease post ID
	}
	return $i;
}

function dateToNum($date){
	$year = substr($date,6,4);
	$month = substr($date,0,2);
	$day = substr($date,3,2);
	return (int)($year.$month.$day);
}

function month($num){
	if ($num=='01') return 'Jan'; 
	if ($num=='02') return 'Feb'; 
	if ($num=='03') return 'Mar'; 
	if ($num=='04') return 'Apr'; 
	if ($num=='05') return 'May';
	if ($num=='06') return 'Jun';
	if ($num=='07') return 'Jul';
	if ($num=='08') return 'Aug';
	if ($num=='09') return 'Sep';
	if ($num=='10') return 'Oct';
	if ($num=='11') return 'Nov';
	if ($num=='12') return 'Dec'; 
}

function upcomingEvents(){
	$postCount = 1;
	$events = array();
	while(1){
		if (!postExists($postCount)) break;
		if (!isDeleted(openPost($postCount))){
			$event = openEvent($postCount);
			if ($event){
				$date = dateToNum($event->date);
				$events[$date] = $event;
			}
		}
		$postCount++;
	}
	$now = (int)(date('Ymd')); //Get today's date as a raw number
	ksort($events);
	echo("<div class='event-list'>");
	echo("<h3>Upcoming Events</h3>");
	echo("<ul class='nav nav-pills nav-stacked'>");
	foreach($events as $key=>$val){
		if ($key >= $now){ //Only show events that are in the future
			$date = month(substr($val->date,0,2))." ".substr($val->date,3,2);
			echo("<li><a href='".theRoot()."/post/".$val->id."'><strong>".$val->name."</strong> ― ".$date."</a></li>");
		}
	}
	echo("</ul></div>");
}

?>