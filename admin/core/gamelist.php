<?php
	if(isset($_GET['status'])){
		$class = 'alert-success';
		$message = '';
		if($_GET['status'] == 'deleted'){
			$class = 'alert-warning';
			$message = 'Game deleted!';
		}
		echo '<div class="alert '.$class.' alert-dismissible fade show" role="alert">'.$message.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
	}
?>
<form class="form-inline my-2 my-lg-0 search-bar" action="/admin/dashboard.php">
	<div class="input-group">
		<input type="hidden" name="viewpage" value="gamelist" />
		<input type="hidden" name="action" value="search" />
		<input type="text" class="form-control rounded-left search" placeholder="Search game" name="key" minlength="2" required />
		<div class="input-group-append">
			<button type="submit" class="btn btn-secondary" type="button">
				Search
			</button>
		</div>
	</div>
</form>
<br>
<table class="table">
	<thead>
	<tr>
		<th>#</th>
		<th>Title</th>
		<th>Source</th>
		<th>URL</th>
		<th>Thumbnail</th>
		<th>Category</th>
		<th>Action</th>
	</tr>
</thead>
<tbody>
	<?php
	$index = 0;
	$cur_page = 1;
	if(isset($_GET['page'])){
		$cur_page = $_GET['page'];
	}
	$data;
	if(isset($_GET['action']) && $_GET['action'] == 'search'){
		$data = Game::searchGame($_GET['key'], 20, 20*($cur_page-1));
	} else {
		$data = get_game_list('new', 20, 20*($cur_page-1));
	}
	$games = $data['results'];
	$total_game = $data['totalRows'];
	$total_page = $data['totalPages'];
	foreach ( $games as $game ) {
		$index++;
		$categories = $game->category;
		?>
	<tr>
		<th scope="row"><?php echo esc_int($index); ?></th>
		<td>
			<?php echo esc_string($game->title) ?>
		</td>
		<td>
			<?php echo esc_string($game->source) ?>
		</td>
		<td><a href="<?php echo esc_url($game->url) ?>" target="_blank">Play</a></td>
		<td><img src="<?php echo esc_url($game->thumb_2) ?>" width="80px" height="auto"></td>
		<td><?php echo esc_string($categories)?></td>
		<td><a href="/admin/request.php?action=deleteGame&id=<?php echo esc_int($game->id) ?>&redirect=/admin/dashboard.php?viewpage=gamelist"> Remove </a> </td>
	</tr>
	<?php } ?>
</tbody>
</table>
<p><?php echo esc_int($total_game)?> Game<?php echo ( $total_game != 1 ) ? 's' : '' ?> in total.</p>
<div class="pagination-wrapper">
	<nav aria-label="Page navigation example">
		<ul class="pagination justify-content-center">
			<?php
			$cur_page = 1;
			if(isset($_GET['page'])){
				$cur_page = $_GET['page'];
			}
			if($total_page){
				for($i = 0; $i<$total_page; $i++){
					$disabled = '';
					if($cur_page){
						if($cur_page == ($i+1)){
							$disabled = 'disabled';
						}
					}
					echo '<li class="page-item '.$disabled.'"><a class="page-link" href="/admin/dashboard.php?viewpage=gamelist&page='.($i+1).'">'.($i+1).'</a></li>';
				}
			}
			?>
		</ul>
	</nav>
</div>