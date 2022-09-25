<?php
	if(isset($_GET['status'])){
		$class = 'alert-success';
		$message = '';
		if($_GET['status'] == 'added'){
			$message = 'New category added!';
		} elseif($_GET['status'] == 'exist'){
			$class = 'alert-warning';
			$message = 'Category already exist!';
		} elseif($_GET['status'] == 'deleted'){
			$class = 'alert-warning';
			$message = 'Category deleted!';
		}
		echo '<div class="alert '.$class.' alert-dismissible fade show" role="alert">'.$message.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
	}
?>
<div class="row">
	<div class="col-8">
		<ul class="list-group category-list">
			<?php
			$results = array();
			$data = Category::getList();
			$categories = $data['results'];
			foreach ($categories as $cat) {
				echo '<li class="list-group-item d-flex align-items-center">';
				echo esc_string($cat->name);
				$count = Category::getCategoryCount($cat->id);
				if($count > 0){
					echo '<span class="badge badge-primary badge-pill">';
					echo esc_int($count);
					echo '</span>';
				}
				echo '<button type="button" class="close remove-category text-danger" aria-label="Close" id="'.esc_int($cat->id).'"><span aria-hidden="true">&times;</span></button>';
				echo '</li>';
			}
			?>
		</ul>
		</div>
	<div class="col-4">
		<form id="form-newcategory" action="request.php" method="post">
			<input type="hidden" name="action" value="newCategory">
			<input type="hidden" name="redirect" value="/admin/dashboard.php?viewpage=categories">
			<div class="form-group">
				<label for="category">Add new category:</label>
				<input type="text" class="form-control" name="name" placeholder="Name" value="" minlength="2" maxlength="15" required>
			</div>
			<?php
				if(CUSTOM_SLUG){ ?>
				<div class="form-group">
					<label for="slug">Category slug:</label>
					<input type="text" class="form-control" name="slug" placeholder="adventure-game" value="" minlength="3" maxlength="15" required>
				</div>
				<?php }
			?>
			<button type="submit" class="btn btn-primary">Add</button>
		</form>
	</div>
</div>