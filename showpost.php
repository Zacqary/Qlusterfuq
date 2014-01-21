<?php
include('functions.php');
$p = $_GET["p"];
if(!postExists($p)) include('404.php');
else {
	$post = openPost($p);
	if(isDeleted($post)) include('404.php');
	else {
		$titletext = parseMarkdown(getPost($post));
		if (strpos($titletext,"{{{∵}}}")){
			$titletext = openEvent($p)->name;
		}
		else{
			if (strpos($titletext,"<p class='edit-timestamp'>")) $titletext = strstr($titletext,"<p class='edit-timestamp'>",true);
			if (strpos($titletext,"span class='image-share'>")) $titletext = strtr(strstr($titletext,"</span>"),array("</span>"=>"[Image] "));
			$titletext = strip_tags($titletext);
			if (strlen($titletext) > 75) $titletext = substr($titletext,0,75)."…";
		}
		$title = makeTitle($titletext);
		include('header.php');?>
			<div class="hidden-tablet hidden-desktop"><?php include('sidebar.php');?></div>
			<div class="row">
				<div class='span7 stream showpost'>
				<?php showPost($p); ?>
				</div>
			</div>
		<?php include('footer.php');
	}
}
?>