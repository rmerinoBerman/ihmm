<?php require_once realpath(dirname(__FILE__).'/../../../..').'/wp-load.php'; ?>
<div class="mainView">
	<div class="headerMenu">
		<div class="centerContent">
			<!-- <div class="menuButton">&#9776;</div> -->
			<div class="header-top">
				<div id="logo">
					<a class="logoLink">
						<img src="<?php echo PAGEDIR; ?>/images/graphics/ihmm-logo.png">
					</a>
				</div>
				<div class="social-media">
					<div class="hashtag">#IHMM30YEARS</div>
					<ul>
						<li><a href="/"><img src="<?php echo PAGEDIR; ?>/images/graphics/facebook.png" alt=""></a></li>
						<li><a href="/"><img src="<?php echo PAGEDIR; ?>/images/graphics/twitter.png" alt=""></a></li>
						<li><a href="/"><img src="<?php echo PAGEDIR; ?>/images/graphics/instagram.png" alt=""></a></li>
					</ul>
				</div>
			</div>
		</div>
		<div id="nav-bar">
			<div class="centerContent">
				<?php wp_nav_menu( array( 'theme_location' => 'header-menu' ) ); ?>
			</div>
		</div>
	</div>
</div>