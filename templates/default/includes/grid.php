<div class="col-md-2 col-sm-3 col-4 item-grid">
	<a href="<?php echo get_permalink('game', $game->slug) ?>">
	<div class="list-game">
		<div class="list-thumbnail"><img src="<?php echo esc_url($game->thumb_2) ?>" class="small-thumb" alt="<?php echo esc_string($game->title) ?>"></div>
		<div class="list-content">
			<div class="list-title"><?php echo esc_string($game->title); ?></div>
		</div>
	</div>
	</a>
</div>