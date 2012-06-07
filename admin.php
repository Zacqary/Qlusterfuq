<?php
include('functions.php');
if(!isAdmin(getLoggedInUser())) include('404.php');
else{
	$title = makeTitle("Admin Panel");
	$currentPage = 'admin';
	include('header.php'); ?>
	<div class='row admin-panel'>
		<div class='span12 box'>
			<h1>Admin Panel</h1>
				<p>This doesn't do anything yet.</p>
		</div>
	</div>
	<?php include('footer.php');
}
?>