<?php
include('functions.php');
$op = $_POST['op'];

if ($op == "process"){
	$name = $_POST['name'];
	$email = $_POST['email'];
	$intro = $_POST['intro'];
	if(emailToUid($email)){
		echo("That email address has already been registered.");
	}
	else{
		applyToJoin($name,$email,$intro);
		echo("Okay! Your application has been sent. Please check your email for a confirmation (it may be in your spam or junk folder). Once you're approved, you'll be emailed a password to log in with.");
	}
}
else{
	if (setting('open') == "yes") $t = 'Register';
	else $t = 'Apply to Join';
	$title = makeTitle($t);
	$currentPage = 'register';
	include('header.php'); ?>
	<div class='row reg-panel'>
		<div class='span12 box'>
			<h1><?php echo($t)?></h1>
			<div class='body'>
				<p><?php echo(setting('regdesc'))?></p>
				<form action=''>
					<input type='text' id='name' />
					<label class='up'>Display Name</label>
					<input type='text' id='email' />
					<label class='up'>Email Address</label>
					<textarea id='intro'></textarea>
					<label class='up'><?php echo(setting('regintro'))?></label>
					<input type='submit' id='submit' class='btn btn-large btn-danger' value='Submit'/>
					<p><div class='alert' id="warning"></div></p>
					<p><div class='alert alert-error' id="error"></div></p>
				</form>
			</div>
		</div>
	</div>
	<script type='text/javascript'>
		function checkEmail(email) {
			if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(email)){
				return (true);
			}
			return (false);
		}
		$('#error').hide();
		$('#warning').hide();
		$('#email').on('input',function(){
			var email = $('#email').val();
			if (email.indexOf("@aol") > -1){
				$('#warning').show();
				$('#warning').html("AOL has very aggressive anti-spam policies and some emails may not get through. Use a different email address if possible.");
			}
		});
		$('#submit').click(function(){
			var name = $('#name').val();
			if (name == "") {
				$('#error').show();
				$('#error').html("You need to enter a name.");
				return false;
			}
			var email = $('#email').val();
			if (email == "") {
				$('#error').show();
				$('#error').html("You need to enter an email address.");
				return false;
			}
			else if(!checkEmail(email)){
				$('#error').show();
				$('#error').html("That doesn't look like an email address.");
				return false;
			}
			var intro = $('#intro').val();
			if (intro == "") {
				$('#error').show();
				$('#error').html("You need to type something in the big box.");
				return false;
			}
			var dataString = "op=process&name="+encodeURIComponent(name)+"&email="+encodeURIComponent(email)+"&intro="+encodeURIComponent(intro);
			$('#error').hide();
			$('#submit').val('Please wait…');
			$('#submit').after('<p class="loader"><img src="<?php echo(theRoot())?>/img/ajax-loader.gif"></p>');
			$.ajax({
				type: 'POST',
				url: theRoot+'register.php',
				data: dataString,
				success: function(data){
					$('.loader').remove();
					$('#submit').val('Submit');
					if(data.indexOf("Okay!") != -1) {
						$('#submit').remove();
						$('#error').removeClass('alert-error').addClass('alert-info');
					}
					$('#error').html(data).show();
				}
			})
			return false;
		});
	</script>
	<?php include('footer.php');
}

function applyToJoin($name,$email,$intro){
	$tid = applyUser($name,$email, $intro);
	$admins = theAdmins();
	foreach ($admins as $key => $val){
			$user = new User($val);
			$subject = $name." has applied for membership on ".setting("sitename");
			ob_start(); //Turn on output buffering
			?>
		Hi <?php echo $user->name ?>,

		Someone applied to join <?php echo(setting("sitename"))?>:
		---
		Name: <?php echo($name."\n")?>
		Email: <?php echo($email."\n")?>
		Intro: <?php echo($intro."\n")?>
		---
		
		To approve this user, visit <?php echo(theRoot()) ?>/approve/<?php echo($tid) ?>
		
		<?
			//copy current buffer contents into $message variable and delete current output buffer
			$message = ob_get_clean();
			EmailUser($val, $subject, $message);
	}
	
	$confirmSubject = "Your ".setting("sitename")." membership is pending";
	ob_start(); //Turn on output buffering
	?>
	Hi <?php echo($name)?>,
	
	Thanks for applying to join <?php echo(setting("sitename"))?>! The administrators will review your application as soon as possible.
	
	If you don't receive your approval soon, please email <?php echo(setting("admincontact"))?> for assistance.
	
	If you didn't apply to join <?php echo(setting("sitename"))?>, please disregard this message.
	<?
	$confirmMessage = ob_get_clean();
	QFSendEmail($email, $name, $confirmSubject, $confirmMessage);
}

?>