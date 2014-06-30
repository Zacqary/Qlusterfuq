<?php
require_once('functions.php');
$p = $_GET['u'];
$currentPage = $p;
$page = openPage($p);
$title = makeTitle(getPageTitle($page));
$body = parseMarkdown(getPageBody($page));
include('header.php');?>
<div class='span10 box box-page'>
	<h1><?php echo(getPageTitle($page))?></h1>
	<?php echo($body)?>
</div>
<?php include('footer.php');?>
