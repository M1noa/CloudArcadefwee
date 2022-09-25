<h3>Top Games</h3>
<div class="row">
	<div class="col-md-8">
		<table class="table">
			<thead>
				<tr>
					<th>#</th>
					<th>Title</th>
					<th>Played</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$index = 0;
				$data = get_game_list('popular', 10);
				$games = $data['results'];
				foreach ( $games as $game ) {
					$index++;
					?>
				<tr>
					<th scope="row"><?php echo esc_int($index); ?></th>
					<td>
						<?php echo esc_string($game->title); ?>
					</td>
					<td>
						<?php echo esc_int($game->views); ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			</table>
	</div>
	<div class="col-md-4">
		<div class="quote-box">
			<div id="quote"></div>
		</div>
	</div>
</div>