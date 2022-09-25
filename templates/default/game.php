<?php include  TEMPLATE_PATH . "/includes/header.php" ?>
<div class="container">
	<div class="game-container">
		<?php include  TEMPLATE_PATH . "/parts/ad-banner-728.php" ?>
		<div class="content-wrapper">
		<div class="row">
			<div class="col-md-9 game-content">
				<div class="game-iframe-container">
					<iframe class="game-iframe" id="game-area" src="<?php echo esc_url($game->url); ?>" width="<?php echo esc_int($game->width); ?>" height="<?php echo esc_int($game->height); ?>" scrolling="none" frameborder="0" allowfullscreen></iframe>
				</div>
				<div class="single-info-container">
					<h3 class="single-title"><?php echo htmlspecialchars( $game->title )?></h3>
					<p>Played <?php echo esc_int($game->views); ?> times.</p>
					<div class="action-btn">
						<div class="single-icon"><i class="fa fa-external-link-square" aria-hidden="true"></i><a href="<?php echo esc_url($game->url); ?>" target="_blank">Open in new window</a></div>
						<div class="single-icon"><i class="fa fa-expand" aria-hidden="true"></i><a href="#" onclick="open_fullscreen()">Fullscreen</a></div>
						<div class="single-icon"><i class="fa fa-facebook-square" aria-hidden="true"></i><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo DOMAIN . $_SERVER['REQUEST_URI']; ?>" target="_blank">Share game</a></div>
					</div>
				</div>
				<b>Description:</b>
				<div class="single-description">
					<?php echo nl2br( $game->description )?>
				</div>
				<br>
				<b>Instructions:</b>
				<div class="single-instructions">
					<?php echo nl2br( $game->instructions )?>
				</div>
				<br>
				<b>Categories:</b>
				<p class="cat-list"> 
					<?php if ( $game->category ) {
						$categories = commas_to_array($game->category);
						foreach ($categories as $cat) {
							$category = Category::getByName($cat); ?>
					<a href="<?php echo get_permalink('category', $category->slug) ?>" class="cat-link"><?php echo esc_string($category->name) ?></a>
					<?php
						}
						} ?>
				</p>
			</div>
			<div class="col-md-3">
				<?php include  TEMPLATE_PATH . "/parts/sidebar.php" ?>
			</div>
		</div>
	</div>
	<?php include  TEMPLATE_PATH . "/parts/ad-banner-728.php" ?>
	</div>
	<div class="bottom-container">
		<h3 class="item-title"><i class="fa fa-thumbs-up" aria-hidden="true"></i>SIMILAR GAMES</h3>
		<?php list_games_by_category($categories[0], 12) ?>
	</div>
</div>
<?php include  TEMPLATE_PATH . "/includes/footer.php" ?>