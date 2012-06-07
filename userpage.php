<?php
include('functions.php');
$u = urlToUid($_GET['u']);
if(!userExists($u)) include('404.php');
else {
	$externalsvcs = setting("externalsvcs",true);
	$basicinfo = setting("basicinfo",true);
	$pageuser = new User($u);
	$title = makeTitle($pageuser->name);
	$currentPage = $pageuser->name;
	include('header.php');?>
	<div class='row'>
		<?php if(getLoggedInUser() == $pageuser->uid) { ?>
			<div class="modal fade" id="avatar-setting">
		    	<div class="modal-header">
		    		<button class="close" data-dismiss="modal">×</button>
		    		<h3>Change Avatar</h3>
		    	</div>
		    	<div class="modal-body">
					<div class='orig-modal'>
					<div class='span2 modal-av'>
		    			<?php echo(getAvatar($pageuser->uid,250));?>
					</div>
					<div class='span3'>
						<form enctype='multipart/form-data' action='avatar.php' method='POST' id='avatar-form' target='upload-target'>
							<div class='file-upload av-pop'><p>Select an Image:</p><input name='image_file' id='image-file' type='file' class='btn' /></div>
							<input type='hidden' value='".getLoggedInUser()."' name='user' />
							<button id='upload-button' class='btn btn-large btn-success'><i class='icon-upload icon-white'></i> Upload</button>
							<button id='remove-button' class='btn btn-large btn-danger'><i class='icon-trash icon-white'></i> Remove Current</button>
							<br><div class='alert alert-error' id='image-error'></div>
						</form>
					</div>
					</div>
		    	</div>
				<iframe id='upload-target' name='upload-target' href='#'></iframe>
		    	<div class="modal-footer">
		    		<a role='button' tabindex='0' class="btn btn-warning" data-dismiss="modal" id="av-cancel-button">Cancel</a>
		    		<a role='button' tabindex='0' class="btn disabled" id="av-save-button">Save changes</a>
		    	</div>
		    </div>
			<div class="row userpage">
				<div class="span12 box settingsbox">
					<button class='btn btn-inverse btn-large btn-settings-box' data-toggle='toggle' data-edit='edit' rel='tooltip' title='Open' id='change-settings'><i class='icon-cog icon-white'></i></button>
					<h1 class='settings-title'>Control Panel</h1>
					<div class='user-control-panel row'>
						<div class='span4 notification-settings'>
							<h2>Notifications</h2>
							<h3>Email me about:</h3>
							<p>Posts<input id='post-not-check' class='not-check' type='checkbox' data-notes='1'></p>
							<p>Comments<input id='comment-not-check' class='not-check' type='checkbox' data-notes='2'></p>
							<p>Images<input id='image-not-check' class='not-check' type='checkbox' data-notes='4'></p>
							<p>Events<input id='event-not-check' class='not-check' type='checkbox' data-notes='8'></p>
						</div>
						<div class='span4'>
							<h2>My URL</h2>
							<?php echo(theRoot())?>/<input type='text' id='my-url-box' value='<?php echo($pageuser->url)?>'>
							<br><button id='change-url-btn' class='btn btn-primary'>Change URL</button>
						</div>
						<div class='span3'>
							<h2>Change Password</h2>
							<input type="password" id='current-password'/>
							<label class='up'>Current Password</label>
							<input type="password" id='new-password1'/>
							<label class='up'>New Password</label>
							<input type="password" id='new-password2'/>
							<label class='up'>Confirm New Password</label>
							<div class='alert alert-error' id='pass-change-error'></div>
							<button id='change-password-btn' class='btn btn-danger'>Change Password</button>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
		
		<div class='span12 box headbox'>
			<?php if(getLoggedInUser()==$pageuser->uid) { ?><button class='btn btn-inverse btn-large btn-edit-box' data-toggle='toggle' data-edit='edit' rel='tooltip' title='Edit' id='edit-headbox'><i class='icon-wrench icon-white'></i></button><?php } ?>
			
			<div class='row'>
				<div class='span2' id='avcol'>
					<?php if(getLoggedInUser() == $pageuser->uid) { ?><a role='button' tabindex='0' class='edit-av click' rel='popover' href='#avatar-setting' title='Avatar' data-content='Click to change it.' data-toggle='modal'><?php } ?>
					<?php echo(getAvatar($pageuser->uid,250));?>
					<?php if(getLoggedInUser() == $pageuser->uid) { ?></a><?php } ?>
				</div>
				<div class='span8'>
					<h1 class='userpage-name setting'><?php echo($pageuser->name);?></h1>
					<p class='alt-names setting'><?php if(($pageuser->altnames) || (getLoggedInUser() == $pageuser->uid)) { if($pageuser->altnames) echo('aka'); ?> <span id='alt-names'><?php echo($pageuser->altnames)?></span><?php } ?></p>
					<p class='bio setting'><?php if(($pageuser->bio) || (getLoggedInUser() == $pageuser->uid)) echo($pageuser->bio);?></p>
				</div>
			</div>
		</div>
	</div>
	<div class="row userpage">
		<div class='basic-info profile-box span3 box'>
			<?php if(getLoggedInUser()==$pageuser->uid) { ?><button class='btn btn-inverse btn-edit-box' data-toggle='toggle' data-edit='edit' rel='tooltip' title='Edit' id='edit-basic-info'> <i class='icon-wrench icon-white'></i></button><?php } ?>
			<h2>Basic Info</h2>
			<?php if( ($pageuser->birthday) || (getLoggedInUser() == $pageuser->uid) ){
				if (!$pageuser->birthday) $hide = " noshow";
				else $hide = '';?>
				<div class="row<?php echo($hide)?>" id="birthday">
				<div class='span1 lcol'>Birthday:</div><div class="span2 rcol setting"><?php echo($pageuser->birthday)?></div>
				</div>
			<?php } ?>
			<?php if( ($pageuser->basicinfo) || (getLoggedInUser() == $pageuser->uid) ) { ?>
				<?php $basicinfocount = 0; foreach($basicinfo as $key => $val){
					$basicinfocount++; ?>
					<?php if( ($pageuser->basicinfo[$key]) || (getLoggedInUser() == $pageuser->uid) ) {
						if (!$pageuser->basicinfo[$key]) $hide = " noshow";
						else $hide = '';?>
						<div class="row<?php echo($hide)?>" id="basic-info-<?php echo($basicinfocount)?>">
						<div class='span1 lcol'><?php echo($key.": ");?></div><div class='span2 rcol setting'><?php echo($pageuser->basicinfo[$key])?></div>
						</div>
					<?php } ?>
				<?php } ?>
			<?php } ?>
		</div>
		
		<div class='contact-info profile-box span4 box'>
			<?php if(getLoggedInUser()==$pageuser->uid) { ?><button class='btn btn-inverse btn-edit-box' data-toggle='toggle' data-edit='edit' rel='tooltip' title='Edit' id='edit-contact-info'><i class='icon-wrench icon-white'></i></button><?php } ?>
			<h2>Contact Info</h2>
			<?php if( ($pageuser->email) || (getLoggedInUser() == $pageuser->uid) ) {
				if (!$pageuser->email) $hide = " noshow";
				else $hide = '';?>
				<div class="row<?php echo($hide)?>" id="email">
				<div class="span1 lcol">Email:</div><div class="span3 rcol setting"><a href="mailto:<?php echo($pageuser->email)?>"><?php 
				$uemail = $pageuser->email;
				if (strlen($uemail) > 36) $uemail = substr($uemail,0,36)."…";
				echo($uemail);
				?></a></div>
				</div>
			<?php } ?>
			<?php if( ($pageuser->phone) || (getLoggedInUser() == $pageuser->uid) ){
				if (!$pageuser->phone) $hide = " noshow";
				else $hide = '';?>
				<div class="row<?php echo($hide)?>" id="phone">
				<div class='span1 lcol'>Phone:</div><div class="span3 rcol setting"><a href="tel:<?php echo(strtr($pageuser->phone,array("{{{∵}}}"=>"")))?>"><?php echo($pageuser->phone)?></a></div>
				</div>
			<?php } ?>
			<?php if( ($pageuser->website) || (getLoggedInUser() == $pageuser->uid) ){
				if (!$pageuser->website) $hide = " noshow";
				else $hide = '';?>
				<div class="row<?php echo($hide)?>" id="website">
				<div class="span1 lcol">Website:</div><div class="span3 rcol setting"><a href="<?php echo($pageuser->website)?>"><?php 
				$uweb = $pageuser->website;
				if (strlen($uweb) > 36) $uemail = substr($uweb,0,36)."…";
				echo($uweb);
				?></a></div>
				</div>
			<?php } ?>
			<?php if( ($pageuser->externalsvcs) || (getLoggedInUser() == $pageuser->uid) ) { ?>
				<?php $externalcount = 0; foreach($externalsvcs as $key => $val){
					$externalcount++;?>
					<?php if( ($pageuser->externalsvcs[$key]) || (getLoggedInUser() == $pageuser->uid) ) {
						if (!$pageuser->externalsvcs[$key]) $hide = " noshow";
						else $hide = '';?>
						<div class="row<?php echo($hide)?>" id="contact-info-<?php echo($externalcount)?>">
						<div class='span1 lcol'><?php echo($key.": ");?></div><div class='span3 rcol setting'><?php $uval = $pageuser->externalsvcs[$key]; if($val){?><a href="<?php echo(strtr($val,array("$"=>strtr($pageuser->externalsvcs[$key],array("{{{∵}}}"=>""))))); ?>"><?php
						if (strlen($uval) > 36) $uval = substr($uval,0,36)."…";}
						echo($uval); if($val){ ?></a><?php }?></div>
						</div>
					<?php } ?>
				<?php } ?>
			<?php } ?>	
		</div>
		<?php if( ($pageuser->location) || (getLoggedInUser() == $pageuser->uid) ){?>
			<div class='span4 box profile-box location-box'>
				<?php if(getLoggedInUser()==$pageuser->uid) { ?><button class='btn btn-inverse btn-edit-box' data-toggle='toggle' data-edit='edit' rel='tooltip' title='Edit' id='edit-location-box'><i class='icon-wrench icon-white'></i></button><?php } ?>
				<h2>Location</h2>
				<?php if(getLoggedInUser()==$pageuser->uid) { ?><p class="ziplabel">Zip Code<input type='text' value='<?php echo($pageuser->location);?>' class='zip' id='zip-field' max-length='5' /></p> <? } ?>
				<?php if($pageuser->location) { ?><iframe class='map' width="100%" height="250" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/?q=<?php echo($pageuser->location);?>&amp;output=embed"></iframe> <?php }
				else {?><h3 class='no-location'>Click to set your location</h3><?php } ?>
			</div>
		<?php } ?>
	</div>
	<?php if(getLoggedInUser()==$pageuser->uid) { 
		$akainsert = "<span class=\'edit-alt-names-label'>aka</span>"?>
		<script type="text/javascript">
			var uid = "<?php echo($u) ?>";
			var akainsert = "<?php echo($akainsert)?>";
			var name = "<?php echo($pageuser->name);?>";
			var altnames = "<?php echo($pageuser->altnames);?>";
			var bio = "<?php echo($pageuser->bio);?>";
			var checks = <?php echo(userSetting($pageuser->uid,'emailme'))?>;
			$('.user-control-panel').hide();
			$('.btn-edit-box').tooltip();
			$('.btn-settings-box').tooltip();
			$('.edit-av').popover();
			$('.btn').button();
			$('.ziplabel').hide();
			$('.noshow').hide();
			$('#image-error').hide();
			
			if(checks == 1){
				$('#post-not-check').prop("checked",true);
			}
			if(checks == 2){
				$('#comment-not-check').prop("checked",true);
			}
			if(checks == 3){
				$('#post-not-check').prop("checked",true);
				$('#comment-not-check').prop("checked",true);
			}
			if(checks == 4){
				$('#image-not-check').prop("checked",true);
			}
			if(checks == 5){
				$('#post-not-check').prop("checked",true);
				$('#image-not-check').prop("checked",true);
			}
			if(checks == 6){
				$('#image-not-check').prop("checked",true);
				$('#comment-not-check').prop("checked",true);
			}
			if(checks == 7){
				$('#post-not-check').prop("checked",true);
				$('#comment-not-check').prop("checked",true);
				$('#image-not-check').prop("checked",true);
			}
			if(checks == 8){
				$('#event-not-check').prop("checked",true);
			}
			if(checks == 9){
				$('#event-not-check').prop("checked",true);
				$('#post-not-check').prop("checked",true);
			}
			if(checks == 10){
				$('#event-not-check').prop("checked",true);
				$('#comment-not-check').prop("checked",true);
			}
			if(checks == 11){
				$('#event-not-check').prop("checked",true);
				$('#post-not-check').prop("checked",true);
				$('#comment-not-check').prop("checked",true);
			}
			if(checks == 12){
				$('#event-not-check').prop("checked",true);
				$('#image-not-check').prop("checked",true);
			}
			if(checks == 13){
				$('#event-not-check').prop("checked",true);
				$('#image-not-check').prop("checked",true);
				$('#post-not-check').prop("checked",true);
			}
			if(checks == 14){
				$('#event-not-check').prop("checked",true);
				$('#image-not-check').prop("checked",true);
				$('#comment-not-check').prop("checked",true);
			}
			if(checks == 15){
				$('#event-not-check').prop("checked",true);
				$('#image-not-check').prop("checked",true);
				$('#comment-not-check').prop("checked",true);
				$('#post-not-check').prop("checked",true);
			}
			
			$('#post-not-check').click(function(){
				if($(this).prop("checked")) checks += 1;
				else checks -= 1;
			});
			$('#comment-not-check').click(function(){
				if($(this).prop("checked")) checks += 2;
				else checks -= 2;
			});
			$('#image-not-check').click(function(){
				if($(this).prop("checked")) checks += 4;
				else checks -= 4;
			});
			$('#event-not-check').click(function(){
				if($(this).prop("checked")) checks += 8;
				else checks -= 8;
			});
			$('.not-check').click(function(){
				var dataString = "uid=<?php echo($pageuser->uid)?>&mode=emailme&checks="+checks;
				$('.notification-settings').append("<img class='loader' src='<?php echo(theRoot())?>/img/ajax-loader.gif'>");
				$.ajax({
					type: 'POST',
					url: theRoot+"useredit.php",
					data: dataString,
					success: function(data){
						setTimeout(function(){$('.loader').remove()},500);
						return false;
					}
				});
			});
			$('#my-url-box').keyup(function(){
				var regex=/^[0-9A-Za-z_-]+$/;
				var val = $('#my-url-box').val();
				if(regex.test(val)){
					$('#change-url-btn').removeClass('disabled');
					$('#change-url-btn').html('Change URL');
				}
				else{
					$('#change-url-btn').addClass('disabled');
					$('#change-url-btn').html('A-Z, 0-9, - or _ only');
				}
			})
			$('#change-url-btn').click(function(){
				if (!$(this).hasClass('disabled')){
					$('#change-url-btn').html("Changing…");
					var newurl = $('#my-url-box').val();
					if (newurl=="") {
						newurl = "<?php echo($pageuser->uid)?>";
						$('#my-url-box').val(newurl);
					}
					var dataString = "mode=urlchange&uid=<?php echo($pageuser->uid)?>&url="+newurl;
					$.ajax({
						type: "POST",
						url: theRoot+"useredit.php",
						data: dataString,
						success: function(data){
							$('#change-url-btn').html(data);
							setTimeout(function(){$('#change-url-btn').html("Change URL")},1250);
						}
					});
				}
			});
			$('#pass-change-error').hide();
			$('#change-password-btn').click(function(){
				var oldpass = $('#current-password').val();
				var newpass = $('#new-password1').val();
				var confirmpass = $('#new-password2').val();
				if (oldpass=="") {
					$('#pass-change-error').show();
					$('#pass-change-error').html("You should probably enter your current password.");
					return false;
				}
				if (newpass=="") {
					$('#pass-change-error').show();
					$('#pass-change-error').html("You should probably enter a new password.");
					return false;
				}
				if (confirmpass=="") {
					$('#pass-change-error').show();
					$('#pass-change-error').html("You should probably reenter your new password.");
					return false;
				}
				$('#change-password-btn').html("Changing…");
				var dataString = "mode=passchange&uid=<?php echo($pageuser->uid)?>&oldpass="+oldpass+"&newpass="+newpass+"&confirmpass="+confirmpass;
				$.ajax({
					type: "POST",
					url: theRoot+"useredit.php",
					data: dataString,
					success: function(data){
						if (data == "Success"){
							$('#pass-change-error').hide();
							$('#change-password-btn').html("Done!");
							setTimeout(function(){$('#change-password-btn').html("Change Password")},1250);
						}
						else {
							$('#change-password-btn').html("Change Password");
							$('#pass-change-error').show();
							$('#pass-change-error').html(data);
						}
					}
				});
				return false;
			});
			
			$('.setting').each(function(){
				if($(this).html().indexOf("{{{∵}}}") != -1){
					var before = $(this).html().substring(0,$(this).html().indexOf("{{{∵}}}"));
					var after = $(this).html().substring($(this).html().indexOf("{{{∵}}}")+7,$(this).html().length)
					$(this).html(before+after);
					$(this).attr("data-private","yes");
				}
			});
			$('.setting>a').each(function(){
				if($(this).html().indexOf("{{{∵}}}") != -1){
					var before = $(this).html().substring(0,$(this).html().indexOf("{{{∵}}}"));
					var after = $(this).html().substring($(this).html().indexOf("{{{∵}}}")+7,$(this).html().length)
					$(this).html(before+after);
					$(this).attr("data-private","yes");
				}
			});
			$('.zip').each(function(){
				if($(this).val().indexOf("{{{∵}}}") != -1){
					var before = $(this).val().substring(0,$(this).val().indexOf("{{{∵}}}"));
					var after = $(this).val().substring($(this).val().indexOf("{{{∵}}}")+7,$(this).val().length)
					$(this).val(before+after);
					$(this).attr("data-private","yes");
				}
			});
			
			
			$('#avatar-setting').hide();
			$('#avatar-setting').modal({
				show:false
			});
			
			$('.btn-edit-box').click(function(){
				if($(this).hasClass("active")){
					$(this).attr("data-original-title","Edit");
					$(this).tooltip("show");
				}
				else {
					$(this).attr("data-original-title","Save changes");
					$(this).tooltip("show");
				}
			});
			
			$('#change-settings').click(function(){
					if($('#change-settings').hasClass("active")){
					$('#change-settings').removeClass("active");
					$('#change-settings').attr("data-original-title","Open");
					$('#change-settings').tooltip("show");
					$('.settings-title').show();
					$('.user-control-panel').hide();
					window.location.hash = "";
				}
				else {
					$('#change-settings').addClass("active");
					$('#change-settings').attr("data-original-title","Close");
					$('#change-settings').tooltip("show");
					$('.settings-title').hide();
					$('.user-control-panel').show();
				}
			})
			if(location.hash == "#control-panel") {
				$('#change-settings').addClass("active");
				$('#change-settings').attr("data-original-title","Close");
				$('#change-settings').tooltip("show");
				$('.settings-title').hide();
				$('.user-control-panel').show();
			}
			$('#remove-button').click(function(){
				$('#image-error').hide();
				var uid = "<?php echo(getLoggedInUser())?>";
				var dataString = "uid=" + uid + "&op=clear";
				$.ajax({
					type: "POST",
					url: theRoot+"avatar.php",
					data: dataString,
					success: function(data){
						$('.modal-av').html("<?php echo(getAvatar('default',250))?>");
						$('#av-save-button').removeClass("disabled");
						$('#av-save-button').addClass("btn-primary");
					}
				});
				return false;
			});
			$('#av-save-button').click(function(){
				if ($('#av-save-button').hasClass("btn-primary")) {
					$('#av-save-button').html("<i class='icon-refresh icon-white'></i> Saving…");
					var uid = "<?php echo(getLoggedInUser())?>";
					var dataString = "uid=" + uid + "&op=save";
					$.ajax({
						type: "POST",
						url: theRoot+"avatar.php",
						data: dataString,
						success: function(data){
							$('.modal-av').html("<?php echo(getAvatar($pageuser->uid,250,2))?>"+Math.floor(Math.random()*123456789)+"'>");
							$('.edit-av').html("<?php echo(getAvatar($pageuser->uid,250,2))?>"+Math.floor(Math.random()*123456789)+"'>");
							$('.navatar').html("<?php echo(getAvatar($pageuser->uid,32,2))?>"+Math.floor(Math.random()*123456789)+"'>");
							$('#avatar-setting').modal("hide");
							$('#image-error').hide();
							$('.orig-modal').show();
							$('.crop-modal').remove();
							$('#av-save-button').html("Save changes");
							$('#upload-target').contents().empty();
							$('#av-save-button').removeClass("btn-primary");
							$('#av-save-button').addClass("disabled");
							$('#image-file').val("");
						}
					});
				}
				return false;
			});
			$('#av-cancel-button').click(function(){
				$('.modal-av').html("<?php echo(getAvatar($pageuser->uid,250))?>");
				$('.orig-modal').show();
				$('.crop-modal').remove();
				$('#upload-target').contents().empty();
				$('#av-save-button').removeClass("btn-primary");
				$('#av-save-button').addClass("disabled");
				$('#image-file').val("");
				$('#image-error').hide();
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
				$('#upload-button').html("<i class='icon-refresh icon-white'></i> Upload");
				$('#upload-button').after("<img class='loader' src='"+theRoot+"img/ajax-loader.gif'>");
				$('#upload-target').on("load",function(){
					$('#upload-target').off();
					var data = $('#upload-target').contents().find('.row').outerHTML();
					$('.orig-modal').hide();
					$('.loader').remove();
					$('.orig-modal').after(data);
					var trueX;
					var trueY;
					var selWidth;
					var selHeight;
					//Originally this next part was much simpler, but then WebKit browsers had
					//to fucking break it for some reason. Firefox and Opera knew what the hell
					//they were doing, but Chrome and Safari were being little bitches
					selHeight = $('.modal-body').find('#crop-me').height();
					selWidth = $('.modal-body').find('#crop-me').width();
					//Both of those values return as 0 in Chrome and Safari. Because they're
					//retarded. So the next four lines of code aren't going to work.
					var sq = 9999;
					if (selHeight){
						if (selHeight < selWidth) sq = selHeight;
						else sq = selWidth;
					}
					function previewUpdate(img,selection){
						var scaleX = 250 / (selection.width || 1);
						var scaleY = 250 / (selection.height || 1);

					    $('#crop-preview>img').css({
					        width: Math.round(scaleX * selWidth) + 'px',
					        height: Math.round(scaleY * selHeight) + 'px',
					        marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
					        marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
					    });
					}
					//Notice how x2 and y2 are sq? Which will always be 9999 in Chrome or
					//Safari? Because fuck you, Chrome and Safari. You'll just have to
					//force the user to start the selection manually. Fucking WebKit.
					//Also I couldn't put this comment right above the line where I
					//set x2 and y2 because Javascript is almost as retarded as Chrome
					//and Safari.
					var selector = $('.modal-body').find('#crop-me').imgAreaSelect({
						instance: true,
					    handles: "corners",
						aspectRatio: "1:1",
						fadeSpeed: 500,
						x1: 0, y1: 0, x2: sq, y2: sq,
						onInit: function(img,selection){
							//We could have put all of this around that spot where trueX and stuff
							//are getting declared, and just called previewUpdate for onInit, but
							//NOOOOOOOOO said Chrome and Safari. So we had to dumb it down for them.
							selHeight = $('.modal-body').find('#crop-me').height(); 
							selWidth = $('.modal-body').find('#crop-me').width();
							if (selHeight > 360) $('.modal-body').find('#crop-me').css("height","360px");
							if ($('.modal-body').find('#crop-me').width() > 250) $('.modal-body').find('#crop-me').css("width","250px");
							selHeight = $('.modal-body').find('#crop-me').height(); 
							selWidth = $('.modal-body').find('#crop-me').width();
							//Originally this was going to be $('#crop-preview').find('img').height() and .width()
							//But, again, Chrome and Safari. They kept returning 0. So I had to go into the fucking
							//PHP and make it spoon-feed it to them in data- attributes because they're just too
							//fucking dumb to understand it otherwise.
							var trueHeight = $('#crop-preview').attr('data-height');
							var trueWidth = $('#crop-preview').attr('data-width');
							trueX = selWidth/trueWidth;
							trueY = selHeight/trueHeight;
							previewUpdate(img, selection);
						},
						onSelectChange: previewUpdate,
					});
					$('#avatar-setting').on("hide",function(){
						selector.cancelSelection();
					});
					$('#avatar-setting').on("show",function(){
					});
					$('#crop-button').click(function(){
						var x1 = Math.round(selector.getSelection().x1/trueX);
						var x2 = Math.round(selector.getSelection().x2/trueX);
						var y1 = Math.round(selector.getSelection().y1/trueY);
						var y2 = Math.round(selector.getSelection().y2/trueY);
						var image = $('#crop-preview').find('img').attr('src');
						var uid = "<?php echo(getLoggedInUser())?>";
						var dataString = "op=crop&uid=" + uid + "&image=" + encodeURIComponent(image) + "&x1=" + x1 + "&x2=" + x2 + "&y1=" + y1 + "&y2=" + y2;
						$('#crop-button').after("<img class='loader' src='"+theRoot+"img/ajax-loader.gif'>");
						$.ajax({
							type: 'POST',
							url: theRoot+"avatar.php",
							data: dataString,
							success: function(data){
								selector.cancelSelection();
								var rand = Math.floor(Math.random()*123456789);
								$('.modal-av').html("<?php echo(getAvatar($pageuser->uid,250,1))?>"+rand+"'>");
								$('#av-save-button').removeClass("disabled");
								$('#av-save-button').addClass("btn-primary");
								$('.orig-modal').show();
								$('.crop-modal').remove();
								$('#upload-target').contents().empty();
								$('#image-file').val("");
							}
						});
						return false;
					});
				});
			});
			
			$('#edit-headbox').click(function(){
				if($('#edit-headbox').attr('data-edit') == "edit"){
					$('#edit-headbox').button('toggle');
					$('#edit-headbox').attr('data-edit',"save");
					$('.userpage-name').hide();
					$('.userpage-name').after("<input type='text' class='edit-userpage-name' value='"+name+"' maxlength='36'></input>");
					$('#alt-names').hide();
					var altform = "<input type='text' class='edit-alt-names' value='"+altnames+"' maxlength='140'></input>";
					if ($('.alt-names').html().indexOf("aka") == -1) altform = akainsert+altform;
					$('#alt-names').after(altform);
					$('.bio').hide();
					$('.bio').after("<textarea class='edit-bio' rows='3' maxlength='320'>"+bio+"</textarea>");
					$('#avcol').append("<h3 class='edit-av-note'>↑&nbsp;&nbsp;&nbsp;Click to Change&nbsp;&nbsp;&nbsp;↑</h3>");
				}
				else if($('#edit-headbox').attr('data-edit') == "save"){
					$('#edit-headbox').button('toggle');
					$('#edit-headbox').html("<i class='icon-white icon-refresh'></i>");
				 	name = $('.edit-userpage-name').val();
					if (!name) name = "<?php echo($pageuser->name) ?>";
					altnames = $('.edit-alt-names').val();
					bio = $('.edit-bio').val();
					var dataString = "uid=" + uid + "&name=" + encodeURIComponent(name) + "&altnames=" + encodeURIComponent(altnames) + "&bio=" + encodeURIComponent(bio) + "&mode=header";
					$.ajax({
						type: 'POST',
						url: theRoot+"useredit.php",
						data: dataString,
						success: function(data){
							$('#edit-headbox').attr('data-edit',"edit");
							$('#edit-headbox').html("<i class='icon-wrench icon-white'></i>");
							$('.edit-userpage-name').remove();
							$('.edit-alt-names').remove();
							$('.edit-alt-names-label').remove();
							$('.edit-bio').remove();
							$('.userpage-name').show();
							$('#alt-names').show();
							if (!altnames) $('.alt-names').html('<span id="alt-names"></span>');
							else if ($('.alt-names').html().indexOf("aka") == -1) $('.alt-names').prepend("aka ");
							$('.bio').show();
							$('.userpage-name').html(name);
							$('#alt-names').html(altnames);
							$('.bio').html(bio);
							$('.edit-av-note').remove();
							return false;
						}
					});
				}
			});
			
			$('#edit-basic-info').click(function(){
				if($('#edit-basic-info').attr('data-edit') == "edit"){
					$('.basic-info').find(".noshow").show();
					$('#edit-basic-info').button('toggle');
					$('#edit-basic-info').attr('data-edit',"save");
					var birthday = $('#birthday').find('.rcol').html();
					$('#birthday').find('.rcol').html("<input type='text' class='edit-basic-info-field' value='"+birthday+"'/><a role='button' tabindex='0' class='lock' title='Click to make Members Only' rel='tooltip'></a>");
					$('#birthday').find('.edit-basic-info-field').datepicker({
						changeMonth: true,
						changeYear: true,
						yearRange: "-100:-0",
						showAnim: 'slide',
						showOtherMonths: true,
						selectOtherMonths: true
					});
					<?php $basicinfocount = 0; foreach($basicinfo as $key => $val){
						$basicinfocount++; ?>
							var thing = $('#basic-info-<?php echo($basicinfocount)?>').find('.rcol').html();
							$('#basic-info-<?php echo($basicinfocount)?>').find('.rcol').html("<input type='text' class='edit-basic-info-field' value='"+thing+"'/><a role='button' tabindex='0' class='lock' title='Click to make Members Only' rel='tooltip'></a>");
					<?php } ?>
					$('.lock').tooltip();
					$('.lock').each(function(){
						if($(this).parent().attr('data-private') == "yes") {
							$(this).addClass('active');
							$(this).attr('data-original-title','Click to make Public');
						}
					});
					$('.lock').click(function(){
						if($(this).parent().attr('data-private') == "yes") {
							$(this).removeClass('active');
							$(this).attr('data-original-title','Click to make Members Only');
							$(this).parent().attr('data-private',"no");
						}
						else{
							$(this).addClass('active');
							$(this).attr('data-original-title','Click to make Public');
							$(this).parent().attr('data-private',"yes");
						}
					});
				}
				else if($('#edit-basic-info').attr('data-edit') == "save"){
					$('#edit-basic-info').button('toggle');
					$('#edit-basic-info').html("<i class='icon-white icon-refresh'></i>");
					var birthday = $('#birthday').find('.edit-basic-info-field').val();
					if ($('#birthday').find('.lock').hasClass('active')) birthday += "{{{∵}}}";
					var basicinfo = "";
					<?php $basicinfocount = 0; foreach($basicinfo as $key => $val){
						$basicinfocount++; ?>
							var thing = $('#basic-info-<?php echo($basicinfocount)?>').find('.edit-basic-info-field').val();
							if ($('#basic-info-<?php echo($basicinfocount)?>').find('.lock').hasClass('active')) thing += "{{{∵}}}";
							basicinfo += "<?php echo($key)?>,"+thing+"\n";
					<?php } ?>
					var dataString = "uid=" + uid + "&mode=basicinfo" + "&birthday=" + encodeURIComponent(birthday) + "&basicinfo=" + encodeURIComponent(basicinfo);
					$.ajax({
						type: 'POST',
						url: theRoot+"useredit.php",
						data: dataString,
						success: function(data){
							$('#edit-basic-info').attr('data-edit',"edit");
							$('#edit-basic-info').html("<i class='icon-wrench icon-white'></i>");
							$('#birthday').find('.rcol').html(birthday);
							if (birthday) $('#birthday').removeClass('noshow');
							else $('#birthday').addClass('noshow');
							<?php $basicinfocount = 0; foreach($basicinfo as $key => $val){
								$basicinfocount++; ?>
									var thing = $('#basic-info-<?php echo($basicinfocount)?>').find('.edit-basic-info-field').val();
									$('#basic-info-<?php echo($basicinfocount)?>').find('.rcol').html(thing);
									if (thing) $('#basic-info-<?php echo($basicinfocount)?>').removeClass('noshow');
									else $('#basic-info-<?php echo($basicinfocount)?>').addClass('noshow');
							<?php } ?>
							$('.basic-info').find(".noshow").hide();
							$('.setting').each(function(){
								if($(this).html().indexOf("{{{∵}}}") != -1){
									var before = $(this).html().substring(0,$(this).html().indexOf("{{{∵}}}"));
									var after = $(this).html().substring($(this).html().indexOf("{{{∵}}}")+7,$(this).html().length)
									$(this).html(before+after);
									$(this).attr("data-private","yes");
								}
							});
							return false;
						}
					});
				}
			});
			
			$('#edit-contact-info').click(function(){
				if($('#edit-contact-info').attr('data-edit') == "edit"){
					$('.contact-info').find(".noshow").show();
					$('#edit-contact-info').button('toggle');
					$('#edit-contact-info').attr('data-edit',"save");
					var email = $('#email').find('.rcol>a').html();
					$('#email').find('.rcol').html("<input type='text' class='edit-basic-info-field' value='"+email+"'/><a role='button' tabindex='0' class='lock' title='Click to make Members Only' rel='tooltip'></a>");
					var phone = $('#phone').find('.rcol>a').html();
					$('#phone').find('.rcol').html("<input type='text' class='edit-basic-info-field' value='"+phone+"'/><a role='button' tabindex='0' class='lock' title='Click to make Members Only' rel='tooltip'></a>");
					var website = $('#website').find('.rcol>a').html();
					$('#website').find('.rcol').html("<input type='text' class='edit-basic-info-field' value='"+website+"'/><a role='button' tabindex='0' class='lock' title='Click to make Members Only' rel='tooltip'></a>");
					<?php $contactinfocount = 0; foreach($externalsvcs as $key => $val){
						$contactinfocount++; ?>
							var thing;
							if ($('#contact-info-<?php echo($contactinfocount)?>').find('.rcol>a').html()) thing = $('#contact-info-<?php echo($contactinfocount)?>').find('.rcol>a').html();
							else thing = $('#contact-info-<?php echo($contactinfocount)?>').find('.rcol').html();
							if (thing.indexOf("<a href=") != -1) thing="";
							$('#contact-info-<?php echo($contactinfocount)?>').find('.rcol').html("<input type='text' class='edit-basic-info-field' value='"+thing+"'/><a role='button' tabindex='0' class='lock' title='Click to make Members Only' rel='tooltip'></a>");
					<?php } ?>
					$('.lock').tooltip();
					$('.lock').each(function(){
						if($(this).parent().attr('data-private') == "yes") {
							$(this).addClass('active');
							$(this).attr('data-original-title','Click to make Public');
						}
					});
					$('.lock').click(function(){
						if($(this).parent().attr('data-private') == "yes") {
							$(this).removeClass('active');
							$(this).attr('data-original-title','Click to make Members Only');
							$(this).parent().attr('data-private',"no");
						}
						else{
							$(this).addClass('active');
							$(this).attr('data-original-title','Click to make Public');
							$(this).parent().attr('data-private',"yes");
						}
					});
				}
				else if($('#edit-contact-info').attr('data-edit') == "save"){
					$('#edit-contact-info').button('toggle');
					$('#edit-contact-info').html("<i class='icon-white icon-refresh'></i>");
					var email = $('#email').find('.edit-basic-info-field').val();
					if (!email) email = "<?php echo($pageuser->email)?>";
					if ($('#email').find('.lock').hasClass('active')) email += "{{{∵}}}";
					var phone = $('#phone').find('.edit-basic-info-field').val();
					if ($('#phone').find('.lock').hasClass('active')) phone += "{{{∵}}}";
					var website = $('#website').find('.edit-basic-info-field').val();
					if (!website) website = "<?php echo($pageuser->website)?>";
					if ($('#website').find('.lock').hasClass('active')) website += "{{{∵}}}";
					var externalsvcs = "";
					<?php $contactinfocount = 0; foreach($externalsvcs as $key => $val){
						$contactinfocount++; ?>
							var thing = $('#contact-info-<?php echo($contactinfocount)?>').find('.edit-basic-info-field').val();
							if ($('#contact-info-<?php echo($contactinfocount)?>').find('.lock').hasClass('active')) thing += "{{{∵}}}";
							externalsvcs += "<?php echo($key)?>,"+thing+"\n";
					<?php } ?>
					var dataString = "uid=" + uid + "&mode=contactinfo" + "&email=" + encodeURIComponent(email) + "&phone=" + encodeURIComponent(phone) + "&website=" + encodeURIComponent(website) + "&externalsvcs=" + encodeURIComponent(externalsvcs);
					$.ajax({
						type: 'POST',
						url: theRoot+"useredit.php",
						data: dataString,
						success: function(data){
							$('#edit-contact-info').attr('data-edit',"edit");
							$('#edit-contact-info').html("<i class='icon-wrench icon-white'></i>");
							$('#email').find('.rcol').html("<a href='mailto:<?php echo($pageuser->email)?>'><?php echo($pageuser->email)?></a>");
							if (email != "<?php echo($pageuser->email)?>") {
								$('#email').after("<div class='alert alert-info'><button class='close' data-dismiss='alert'>×</button>Check your inbox at "+email+" to confirm your new email address.");
							}
							$('#phone').find('.rcol').html("<a href='tel:"+phone+"'>"+phone+"</a>");
							if (phone) $('#phone').removeClass('noshow');
							else $('#phone').addClass('noshow');
							$('#website').find('.rcol').html("<a href='"+website+"'>"+website+"</a>");
							if (website) $('#website').removeClass('noshow');
							else $('#website').addClass('noshow');
							<?php $contactinfocount = 0; foreach($externalsvcs as $key => $val){
								$contactinfocount++; ?>
									var thing = $('#contact-info-<?php echo($contactinfocount)?>').find('.edit-basic-info-field').val();
									<?php; if($val){ ?>
										if (thing){
											var link = "<?php echo($val) ?>";
											var before = link.substring(0,link.indexOf("$"));
											var after = link.substring(link.indexOf("$")+1,link.length);
											link = before + thing + after;
											thing = "<a href='"+link+"'>"+thing+"</a>";
										}
									<?php } ?>
									$('#contact-info-<?php echo($contactinfocount)?>').find('.rcol').html(thing);
									if (thing) $('#contact-info-<?php echo($contactinfocount)?>').removeClass('noshow');
									else $('#contact-info-<?php echo($contactinfocount)?>').addClass('noshow');
							<?php } ?>
							$('.contact-info').find(".noshow").hide();
							$('.setting').each(function(){
								if($(this).html().indexOf("{{{∵}}}") != -1){
									var before = $(this).html().substring(0,$(this).html().indexOf("{{{∵}}}"));
									var after = $(this).html().substring($(this).html().indexOf("{{{∵}}}")+7,$(this).html().length)
									$(this).html(before+after);
									$(this).attr("data-private","yes");
								}
							});
							$('.setting>a').each(function(){
								if($(this).html().indexOf("{{{∵}}}") != -1){
									var before = $(this).html().substring(0,$(this).html().indexOf("{{{∵}}}"));
									var after = $(this).html().substring($(this).html().indexOf("{{{∵}}}")+7,$(this).html().length)
									$(this).html(before+after);
									$(this).attr("data-private","yes");
								}
							});
							return false;
						}
					});
				}
			});
			
			$('#edit-location-box').click(function(){
				if($('#edit-location-box').attr('data-edit') == "edit"){
					$('#edit-location-box').button('toggle');
					$('#edit-location-box').attr('data-edit',"save");
					var zip = $('.zip').html();
					$('.ziplabel').show();
					$('.zip').after("<a role='button' tabindex='0' class='lock' title='Click to make Members Only' rel='tooltip'></a>");
					$('.map').attr("height","240px");
					$('.lock').tooltip();
					$('.lock').each(function(){
						if($(this).parent().attr('data-private') == "yes") {
							$(this).addClass('active');
							$(this).attr('data-original-title','Click to make Public');
						}
					});
					$('.lock').click(function(){
						if($(this).parent().attr('data-private') == "yes") {
							$(this).removeClass('active');
							$(this).attr('data-original-title','Click to make Members Only');
							$(this).parent().attr('data-private',"no");
						}
						else{
							$(this).addClass('active');
							$(this).attr('data-original-title','Click to make Public');
							$(this).parent().attr('data-private',"yes");
						}
					});
				}
				else if($('#edit-location-box').attr('data-edit') == "save"){
					$('#edit-location-box').button('toggle');
					$('#edit-location-box').html("<i class='icon-white icon-refresh'></i>");
					var location = $('.zip').val();
					if ($('.ziplabel').find('.lock').hasClass('active')) location += "{{{∵}}}";
					$('.ziplabel').find('.lock').remove();
					var dataString = "uid=" + uid + "&mode=location" + "&location=" + encodeURIComponent(location);
					$.ajax({
						type: 'POST',
						url: theRoot+"useredit.php",
						data: dataString,
						success: function(data){
							$('#edit-location-box').attr('data-edit',"edit");
							$('#edit-location-box').html("<i class='icon-wrench icon-white'></i>");
							$('.ziplabel').hide();
							$('.map').show();
							var map = '<iframe class="map" width="100%" height="250" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/?q='+location+'&amp;output=embed"></iframe>';
							if (!location) map = "<h3 class='no-location'>Click to set your location</h3>";
							$('.map').replaceWith(map);
							$('.no-location').replaceWith(map);
							$('.setting').each(function(){
								if($(this).html().indexOf("{{{∵}}}") != -1){
									var before = $(this).html().substring(0,$(this).html().indexOf("{{{∵}}}"));
									var after = $(this).html().substring($(this).html().indexOf("{{{∵}}}")+7,$(this).html().length)
									$(this).html(before+after);
									$(this).attr("data-private","yes");
								}
							});
							$('.zip').each(function(){
								if($(this).val().indexOf("{{{∵}}}") != -1){
									var before = $(this).val().substring(0,$(this).val().indexOf("{{{∵}}}"));
									var after = $(this).val().substring($(this).val().indexOf("{{{∵}}}")+7,$(this).val().length)
									$(this).val(before+after);
									$(this).attr("data-private","yes");
								}
							});
							return false;
						}
					});
				}
			});
		</script>
	<?php } ?>

<?php include('footer.php');
}?>