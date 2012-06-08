<?php
include("functions.php");
$title = makeTitle("Member List");
$currentPage = "members";
include("header.php");?>
<div class='span12 box'>
	<?php
	$disp = 0;
	$rawusers = theUsers();
	foreach ($rawusers as $key => $val){
		$thisu = new User($val);
		$users[strtolower($thisu->name)] = $val;
		
	}
	ksort($users);
	foreach ($users as $key => $val){
		$thisu = new User($val);
		if($disp == 0) { ?><div class='row member-row'><?php } $disp++; ?>
			<a href='<?php echo(theRoot()."/".$thisu->url)?>' class='member-box'><div class='span5'>
				<div class='span2' id='avcol'>
					<?php echo(getAvatar($thisu->uid,250));?>
				</div>
				<div class='span3' id='namecol'>
					<h2><?php echo($thisu->name);?></h2>
					<p class='alt-names'><?php if($thisu->altnames) echo('aka'); ?> <span id='alt-names'><?php echo($thisu->altnames)?></span></p>
					<p class='bio'><?php if($thisu->bio) {
						$bio = $thisu->bio;
						if (strlen($bio) > 140) $bio = substr($bio,0,139)."…";
						echo($bio);
						}?></p>
				</div>
			</div>
		<?php if($disp == 2) { ?></div><?php $disp = 0; }
	}
	
	?>
</div>

<?php include("footer.php")?>