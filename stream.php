<?php if (!$_POST["postcount"]){?>
	<div class="span7 stream">
	<?php if(getLoggedInUser()) showPostForm(); ?>
<?php
	$postCount = 1;
	while(1){ //Count how many posts there are
		if (!postExists($postCount)) break;
		$postCount++;
	}
	$postCount--;
}
else {
	$postCount = $_POST["postcount"];
	include('functions.php');
}
	$postCount = streamPosts($postCount,setting("streampage"));
if ($postCount > 0) { ?>
	<div class="show-more" id="show-more-posts" data-postcount="<?php echo($postCount)?>">Load more posts</div>
	<?php } ?>
<?php if (!$_POST["postcount"]){?></div><?php } ?>
