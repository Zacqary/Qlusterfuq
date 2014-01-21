<?php 
$user = sessionAuth()?>
<!DOCTYPE html>
<html lang="<?php echo(setting("meta-language"))?>">
<head>
	<meta charset="UTF-8">
	<meta name="description" content="<?php echo(setting("meta-description"))?>">
	<meta name="keywords" content="<?php echo(setting("meta-keywords"))?>">
	<meta name="language" content="<?php echo(setting("meta-language"))?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title><?php echo($title);echo(siteName());echo($subtitle)?></title>
	<link rel="icon" type="image/gif" href="<?php echo(theRoot());?>/favicon.gif">
	<link rel="stylesheet" href="<?php echo(theRoot());?>/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo(theRoot());?>/css/bootstrap-responsive.min.css">
	<link rel="stylesheet" href="<?php echo(theRoot());?>/css/style.css">
	<link rel="stylesheet" href="<?php echo(theRoot());?>/css/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="<?php echo(theRoot());?>/css/imgareaselect-default.css" />
	<link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo(theRoot());?>/feed.rss" />
	<link href='http://fonts.googleapis.com/css?family=Lato:700,700italic,400,400italic' rel='stylesheet' type='text/css'>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
	<script src="<?php echo(theRoot());?>/js/jquery-ui.min.js" type="text/javascript"></script>
  	<script type="text/javascript" src="<?php echo(theRoot());?>/js/jquery.imgareaselect.pack.js"></script>
<script type="text/javascript" src="<?php echo(theRoot());?>/js/jquery.ba-hashchange.min.js"></script>
	<script src="<?php echo(theRoot());?>/js/bootstrap.min.js"></script>
	<script type="text/javascript">var theRoot="<?php echo(theRoot());?>/"</script>
	<script src="<?php echo(theRoot());?>/js/jquery.truncator.js" type="text/javascript"></script>
	<script src="<?php echo(theRoot());?>/js/outerHTML-2.1.0-min.js" type="text/javascript"></script>
	<script src="<?php echo(theRoot());?>/js/textpandable.js" type="text/javascript"></script>
	<script src="<?php echo(theRoot());?>/js/postystuff.js" type="text/javascript"></script>
	<script src="<?php echo(theRoot());?>/js/epiceditor.js"></script>
</head>
<body>
	<div class="navbar">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="<?php echo(theRoot())?>"><?php echo(siteName()) ?></a>
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				</a>
				<div class='nav-collapse hidden-desktop'>
					<ul class="nav">
						<li <?php if($currentPage == "index") echo("class='active'") ?>><a href='<?php echo(theRoot())?>'>Home</a></li>
						<li <?php if($currentPage == "members") echo("class='active'") ?>><a href='<?php echo(theRoot())?>/members'>Member List</a></li>
						<?php if (isAdmin(getLoggedInUser())) { ?>
							<li <?php if($currentPage == "admin") echo("class='active'") ?>><a href='<?php echo(theRoot())?>/admin'>Admin Panel</a></li>
						<?php } ?>
						<li><a href='mailto:<?php echo(setting('admincontact'))?>'>Contact Support</a></li>
					</ul>
				</div>
				<ul class='nav pull-left visible-desktop'>
					<li <?php if($currentPage == "index") echo("class='active'") ?>><a href='<?php echo(theRoot())?>'>Home</a></li>
					<li <?php if($currentPage == "members") echo("class='active'") ?>><a href='<?php echo(theRoot())?>/members'>Member List</a></li>
					<?php if (isAdmin(getLoggedInUser())) { ?>
						<li <?php if($currentPage == "admin") echo("class='active'") ?>><a href='<?php echo(theRoot())?>/admin'>Admin Panel</a></li>
					<?php } ?>
					<li><a href='mailto:<?php echo(setting('admincontact'))?>'>Contact Support</a></li>
				</ul>
				<div class='hidden-phone hidden-tablet'>
					<ul class='nav pull-right'>
						<?php if(getLoggedInUser()) { ?>
						<li class='dropdown'>
							<a role="button" tabindex="0" class='dropdown-toggle click' id='messages-dropdown' data-toggle='dropdown'>
								<i class='icon-conversation'></i>
								<b class='caret'></b>
							</a>
							<ul class='dropdown-menu dropdown-notifications'>
								<?php if(userSetting($user->uid,"notifications")) listNotifications($user->uid); 
								else {?><li class='muted' style='text-align:center'>No notifications</li><?php } ?>
							</ul>
						</li>
						<li class='dropdown'>
							<a role="button" tabindex="0" class='dropdown-toggle click' data-toggle='dropdown'>
								<span class='navatar'><?php echo(getAvatar($user->uid,32));?></span>
								<b class='caret'></b>
							</a>
							<ul class='dropdown-menu'>
								<li <?php if($currentPage == $user->name) echo("class='active'") ?>><a href="<?php echo(theRoot())?>/<?php echo($user->url)?>"><h4><?php echo($user->name)?></h4>View my profile page</a></li>
								<li class='divider'></li>
								<li><a href="<?php echo(theRoot())?>/<?php echo($user->url)?>#control-panel">Settings</a></li>
								<li><a role="button" tabindex="0" href="<?php echo(theRoot())?>/logout.php">Log out</a></li>
							</ul>
						</li>
						<?php } else {?>
						<li <?php if($currentPage == "register") echo("class='active'") ?>><a href="<?php echo(theRoot())?>/register">Register</a></li>
						<li><!--<a role="button" tabindex="0" class='click' data-toggle="modal" href="#login-modal">Log In</a>-->
							<a href="<?php echo(theRoot())?>/loginpage.php">Log In</a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
		<div class="container">
			<div class="row main">
				<?php if($_SESSION['alert']) { ?>
					<div class='alert top-alert'>
						<button class="close" data-dismiss="alert">×</button>
						<?php echo($_SESSION['alert']);
						unset($_SESSION['alert'])?>
					</div>
				<?php } ?>
				<?php if( (!getLoggedInUser()) && $currentPage != "loginpage"){ ?>
					<!--<div class="modal fade" id="login-modal">
				    		<div class="modal-header">
						  	  <button class="close" data-dismiss="modal">×</button>
							    <h3 id='login-header'>Log In</h3>
						    </div>
							<form action=''>
						    <div class="modal-body">
						    	<input type='text' id='login-email' name='email'/>
								<label class='up'>Email Address</label>
								<div id='login-body'>
									<input type='password' id='login-password' name='pass'/>
									<label class='up'>Password</label>
									<label class='up'><a role="button" tabindex="0" class='click' id='forgot-password'>Forgot password</a></label>
									<input type='checkbox' id='remember-me' name='remember'> <label id='remember-label' style='display:inline;position:relative;left:6px;top:1px' class='click up'>Remember Me</label>
								</div>
								<div class='alert alert-error' id='login-error'></div>
						    </div>
						    <div class="modal-footer">
								<input type='button' class="btn" data-dismiss="modal" value='Close' />
								<input type='button' class="btn btn-warning" id='cancel-button' value='Cancel' />
								<input type='button' class="btn btn-primary" id='reset-button' value='Send Reset Code' />
								<input type='submit' class="btn btn-primary" id='login-button' value='Log In'/>
						    </div>
						</form>
				    </div>
					<script type='text/javascript'>
						$('#login-modal').modal({
							keyboard:false,
							show: false
						})
						$('#login-error').hide();
						$('#reset-button').hide();
						$('#cancel-button').hide();
						$('#remember-label').click(function(){
							if($('#remember-me').is(':checked')) $('#remember-me').attr('checked',false);
							else $('#remember-me').attr('checked',true);
						});
						$('#forgot-password').click(function(){
							$('#reset-button').show();
							$('#login-button').hide();
							$('#cancel-button').show();
							$('#login-body').hide();
							$('#login-header').html('Reset Password');
						});
						$('#cancel-button').click(function(){
							$('#reset-button').hide();
							$('#login-button').show();
							$('#cancel-button').hide();
							$('#login-body').show();
							$('#login-header').html('Log In');
						});
						$('#reset-button').click(function(){
							var email = $('#login-email').val();
							var dataString = "email=" + email;
							if (!email) {
								$('#login-error').fadeIn(100).fadeOut(100).fadeIn(100);
								$('#login-error').html("You should probably type in an email address.");
								return false;
							}
							$('#reset-button').after("<img class='loader' src='<?php echo(theRoot())?>/img/ajax-loader.gif'>");
							$.ajax({
								type: 'POST',
								url: theRoot+'reset.php',
								data: dataString,
								success: function(data){
	 								$('#login-error').removeClass('alert-error');
									$('.loader').remove();
									$('#login-error').fadeIn(100).fadeOut(100).fadeIn(100);
									$('#login-error').html(data);
								}
							});
							return false;
						});
						$('#login-button').click(function(){
							var email = $('#login-email').val();
							var pass = $('#login-password').val();
							var remember = false;
							if ($('#remember-me').is(':checked')) remember = "yes";
							var dataString = "email=" + email + "&pass=" + pass +"&remember=" + remember;
							if (!email) {
								$('#login-error').fadeIn(100).fadeOut(100).fadeIn(100);
								$('#login-error').html("You should probably type in an email address.");
								return false;
							}
							if (!pass) {
								$('#login-error').fadeIn(100).fadeOut(100).fadeIn(100);
								$('#login-error').html("You should probably type in a password.");
								return false;
							}
							$('#login-button').after("<img class='loader' src='<?php echo(theRoot())?>/img/ajax-loader.gif'>");
							$.ajax({
								type: 'POST',
								url: theRoot+'login.php',
								data: dataString,
								success: function(data){
									if (data == "Success") location.reload();
									else {
										$('.loader').remove();
										$('#login-error').fadeIn(100).fadeOut(100).fadeIn(100);
										$('#login-error').html(data);
									}
								}
							});
							return false;
						});
					</script>-->
				<?php } ?>