<?php require_once("data.php");

if (!$_POST["postcount"]){?>
	<div class="span7 stream">
	<?php if(getLoggedInUser()) showPostForm(); ?>
<?php
	$posts = getAllPosts();
	$postCount = sizeof($posts);
}
else {
	$postCount = $_POST["postcount"];
	require_once('functions.php');
}
	$postCount = streamPosts($postCount,setting("streampage"));
if ($postCount > 0) { ?>
	<div class="show-more" id="show-more-posts" data-postcount="<?php echo($postCount)?>">Load more posts</div>
	<?php } ?>
<?php if (!$_POST["postcount"]){?></div><?php } ?>
