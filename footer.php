		</div><!--.main-->
		<div class='row footer'>
			<div class='span12'>
				<p>Powered by <a href="https://github.com/zacqary/qlusterfuq">Qlusterfuq</a></p>
				<?php if(pageExists("privacy")) {?><p><a href="<?php echo(theRoot())?>/privacy">Privacy Policy</a><? } ?>
				<p><a href="<?php echo theRoot()?>/feed.rss">RSS Feed</a></p>
			</div>
		</div>
	</div><!--.container-->
</body>
</html>
<?php $end_time = microtime(true);
$exec_time = $end_time - $start_time;?>
<!-- Script execution time: <?php echo $exec_time?> -->