
	<div class='span3 sidebar'>
		<div class='row box'>
			<?php if(getLoggedInUser()){?>
			<div class='span1 avatar48'>
				<?php echo(getAvatar($user->uid,48)); ?>
			</div>
			<a href="<?php echo(theRoot()."/".$user->url);?>"><div class='span2 username'>
				<h4><?php echo($user->name);?></h4>
				<p>View my profile page</p>
			</div></a>
			<div class='hidden-desktop hidden-tablet' style="margin-left: 72px; display:block; clear:both">
				<a href="<?php echo(theRoot())?>/<?php echo($user->url)?>#control-panel"><i class="icon-cog"></i> Settings</a> ·
				<a role="button" tabindex="0" href="<?php echo(theRoot())?>/logout.php"><i class="icon-off"></i> Log out</a>
			</div>
			<?php } else {?>
			<div class='mebox-nolog'>
			<h4>Welcome to <?php echo(siteName())?></h4>
			<p><!--<a role="button" tabindex="0" id="login" class='click' data-toggle="modal" href="#login-modal" >--><a href="<?php echo(theRoot())?>/loginpage.php">Log In</a> · <a id="register" href="<?php echo(theRoot())?>/register">Apply to Join</a></p>
			</div>
			<?php } ?>
		</div>
		<?php if($currentPage == "index") { ?>
			<div class='row box'>
			<?php upcomingEvents();?>
			</div>
		<?php } ?>
	</div>