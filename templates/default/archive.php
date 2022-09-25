<?php include  TEMPLATE_PATH . "/includes/header.php" ?>
<div class="container">
	<div class="game-container">
		<?php include  TEMPLATE_PATH . "/parts/ad-banner-728.php" ?>
		<div class="content-wrapper">
			<h3 class="item-title"><?php echo strtoupper($archive_title); ?> GAMES</h3>
			<p><?php echo esc_int($total_games)?> game<?php echo ( $total_games != 1 ) ? 's' : '' ?> in total. Page <?php echo esc_int($cur_page)?> of <?php echo esc_int($total_page)?></p>
			<div class="game-container">
				<div class="row">
					<?php foreach ( $games as $game ) { ?>
					<?php include  TEMPLATE_PATH . "/includes/grid.php" ?>
					<?php } ?>
				</div>
			</div>
			<div class="pagination-wrapper">
				<nav aria-label="Page navigation example">
					<ul class="pagination justify-content-center">
						<?php
						$cur_page = 1;
						if(isset($_GET['page'])){
							$cur_page = esc_string($_GET['page']);
						}
						if($total_page){
							for($i = 0; $i<$total_page; $i++){
								$disabled = '';
								if($cur_page){
									if($cur_page == ($i+1)){
										$disabled = 'disabled';
									}
								}
								echo '<li class="page-item '.$disabled.'"><a class="page-link" href="'. get_permalink('category', $_GET['slug']) .'&page='.($i+1).'">'.($i+1).'</a></li>';
							}
						}
						?>
					</ul>
				</nav>
			</div>
		</div>
		<?php include  TEMPLATE_PATH . "/parts/ad-banner-728.php" ?>
	</div>
</div>
<?php include  TEMPLATE_PATH . "/includes/footer.php" ?>