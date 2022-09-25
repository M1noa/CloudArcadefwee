<?php
	if(isset($_GET['status'])){
		$class = 'alert-success';
		$message = '';
		if($_GET['status'] == 'saved'){
			$message = 'Settings saved!';
		}
		echo '<div class="alert '.$class.' alert-dismissible fade show" role="alert">'.$message.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
	}
?>
<form id="form-settings" action="/admin/request.php" method="post">
	<input type="hidden" name="action" value="siteSettings">
	<input type="hidden" name="redirect" value="/admin/dashboard.php?viewpage=settings">
	<div class="form-group">
		<label for="title">Site title:</label>
		<input type="text" class="form-control" name="title" minlength="4" value="<?php echo esc_string(SITE_TITLE) ?>" required>
	</div>
	<div class="form-group">
		<label for="description">Site description:</label>
		<input type="text" class="form-control" name="description" minlength="4" value="<?php echo esc_string(SITE_DESCRIPTION) ?>" required>
	</div>
	<div class="form-group">
		<label for="meta_description">Meta description:</label>
		<input type="text" class="form-control" name="meta_description" minlength="4" value="<?php echo esc_string(META_DESCRIPTION) ?>" required>
	</div>
	<div class="form-group">
		<label for="theme">Theme:</label>
		<select class="form-control" name="theme" required>
			<?php
				$list = scandir('../templates');
				$folders = array_diff($list, array('.', '..'));
				foreach ($folders as $dir) {
					$selected = '';
					if($dir == THEME_NAME){
						$selected = 'selected';
					}
					echo '<option '.$selected.'>'.$dir.'</option>';
				}
			?>
	    </select>
	</div>
	<button type="submit" class="btn btn-primary">Save</button>
</form>
<br>
<form id="form-updatelogo" action="/admin/request.php" method="post" enctype="multipart/form-data">
	<div class="form-group">
		<input type="hidden" name="action" value="updateLogo">
		<input type="hidden" name="redirect" value="/admin/dashboard.php?viewpage=settings">
		<label for="logo">Site logo:</label><br>
		<img src="<?php echo DOMAIN . SITE_LOGO ?>" style="background-color: #aebfbc; padding: 10px"><br><br>
		<input type="file" name="logofile" accept=".png, .jpg, .jpeg"/><br><br>
		<button type="submit" class="btn btn-primary">Save</button>
	</div>
</form>

<br>
<form id="form-save-thumbs" action="/admin/request.php" method="post">
	<div class="form-group">
		<input type="hidden" name="action" value="set_save_thumbs">
		<input type="hidden" name="redirect" value="/admin/dashboard.php?viewpage=settings">
		<label>Save thumbnails:</label><br>
		<p>Save game thumbnails from fetch and remote games to local server. images also compressed and can reduce file size up to 80%.
		<br>Page will be loaded more quickly, but take more bandwidth on the server</p>
		<input type="checkbox" name="save_thumbs" value="1" <?php if( IMPORT_THUMB ){ echo 'checked'; } ?>><br><br>
		<button type="submit" class="btn btn-primary">Save</button>
	</div>
</form>