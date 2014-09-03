<?php
include('functions.php');
$currentPage = "loginpage";
include('header.php');?>
<div class="row">
	<div class="span12 box" style="text-align: center">
		<h1>Log In</h1>
		<div class="body">
			<form action=''>
				<input type='text' id='login-email' name='email'/>
				<label class='up'>Email Address</label>
				<div id='login-body'>
					<input type='password' id='login-password' name='pass'/>
					<label class='up'>Password</label>
					<label class='up'><a role="button" tabindex="0" class='click' id='forgot-password'>Forgot password</a></label>
					<input type='checkbox' id='remember-me' name='remember'> <label id='remember-label' style='display:inline;position:relative;left:6px;top:1px' class='click up'>Remember Me</label>
				</div>
				<div class='alert alert-error' id='login-error'></div>
				<input type='button' class="btn btn-warning" id='cancel-button' value='Cancel' />
				<input type='button' class="btn btn-primary" id='reset-button' value='Send Reset Code' />
				<input type='submit' class="btn btn-primary" id='login-button' value='Log In'/>
			</form>
		</div>
	</div>
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
$('.loader').remove();
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
		if (data == "Success") window.location.href = "<?php echo(theRoot());?>";
		else {
			$('.loader').remove();
			$('#login-error').fadeIn(100).fadeOut(100).fadeIn(100);
			$('#login-error').html(data);
		}
	}
});
return false;
});
</script>

<?php include('footer.php');?>