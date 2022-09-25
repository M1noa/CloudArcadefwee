<?php

function list_categories(){
	$categories = get_all_categories();
	echo '<ul class="links list-categories">';
	foreach ($categories as $item) {
		echo '<a href="'. get_permalink('archive', $item->slug) .'"><li>'. esc_string($item->name) .'</li></a>';
	}
	echo '</ul>';
}
function list_games($type, $amount){
	echo '<div class="row">';
	$data = get_game_list($type, $amount);
	$games = $data['results'];
	foreach ( $games as $game ) { ?>
	<div class="col-4 list-tile">
		<a href="<?php echo get_permalink('game', $game->slug) ?>">
			<div class="list-game">
				<div class="list-thumbnail"><img src="<?php echo esc_url($game->thumb_2) ?>" class="small-thumb" alt="<?php echo esc_string($game->title) ?>"></div>
			</div>
		</a>
	</div>
	<?php }
	echo '</div>';
}
function list_games_by_category($cat, $amount){
	echo '<div class="grid-layout grid-wrapper">';
	$data = get_game_list_category($cat, $amount);
	$games = $data['results'];
	foreach ( $games as $game ) { ?>
		<?php include  TEMPLATE_PATH . "/includes/grid.php" ?>
	<?php }
	echo '</div>';
}

?>