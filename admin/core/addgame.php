<ul class="nav nav-tabs">
	<li class="nav-item">
		<a class="nav-link active" data-toggle="tab" href="#addgame">Upload Game</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#fetch">Fetch Games</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#remote">Remote add</a>
	</li>
</ul>
<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane tab-container active" id="addgame">
		<?php
			if(isset($_GET['status'])){
				if($_GET['status'] == 'added'){
					echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Game added!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
				} elseif($_GET['status'] == 'ready'){
					echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">Game already exist!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
				} elseif($_GET['status'] == 'error'){
					$error = json_decode($_GET['error-data']);
					echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul>';
					foreach ($error as $value) {
						echo '<li>'.$value.'</li>';
					}
					echo '</ul><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
				}
			}
		?>
		<form id="form-uploadgame" action="upload.php" enctype="multipart/form-data" method="post">
			<input type="hidden" name="source" value="self"/>
			<input type="hidden" name="tags" value=""/>
			<div class="form-group">
				<label for="title">Game title:</label>
				<input type="text" class="form-control" name="title" value="" required/>
			</div>
			<?php
				if(CUSTOM_SLUG){ ?>
				<div class="form-group">
					<label for="slug">Category slug:</label>
					<input type="text" class="form-control" name="slug" placeholder="adventure-game" value="" minlength="3" maxlength="15" required>
				</div>
				<?php }
			?>
			<div class="form-group">
				<label for="description">Description:</label>
				<textarea class="form-control" name="description" rows="3" required/></textarea>
			</div>
			<div class="form-group">
				<label for="instructions">Instructions:</label>
				<textarea class="form-control" name="instructions" rows="3"></textarea>
			</div>
			<label for="gamefile">Game file (.zip):</label>
			<ul>
				<li>Must contain index.html on root</li>
				<li>Must contain "thumb_1.jpg" (512x384px) on root</li>
				<li>Must contain "thumb_2.jpg"(512x512px) on root</li>
			</ul>
			<div class="input-group mb-3">
				<div class="custom-file">
					<input type="file" name="gamefile" class="custom-file-input" id="input_gamefile" accept=".zip">
					<label class="custom-file-label" for="input_gamefile">Choose file</label>
				</div>
			</div>
			<div class="form-group">
				<label for="width">Game width:</label>
				<input type="number" class="form-control" name="width" value="1280" required/>
			</div>
			<div class="form-group">
				<label for="height">Game height:</label>
				<input type="number" class="form-control" name="height" value="720" required/>
			</div>
			<div class="form-group">
				<label for="category">Category:</label>
				<select multiple class="form-control" name="category[]" required/>
					<?php
						$results = array();
						$data = Category::getList();
						$categories = $data['results'];
						foreach ($categories as $cat) {
							echo '<option>'.ucfirst($cat->name).'</option>';
						}
					?>
				</select>
			</div>
			<button type="submit" class="btn btn-primary">Upload game</button>
		</form>
	</div>
	<div class="tab-pane tab-container fade" id="fetch">
		<form id="form-fetch">
			<div class="form-group">
				<label>Collection</label> 
				<select name="Collection" class="form-control">
					<option value="all">All games</option>
					<option value="exclusive">Exclusive games</option>
					<option selected="selected" value="best">Best new games</option>
					<option value="featured">Hot games</option>
				</select>
			</div>
			<div class="form-group">
				<label>Category</label> 
				<select name="Category" class="form-control">
					<option selected="selected" value="All">All</option>
					<option value=".IO">.IO</option>
					<option value="2 Player">2 Player</option>
					<option value="3D">3D</option>
					<option value="Action">Action</option>
					<option value="Adventure">Adventure</option>
					<option value="Arcade">Arcade</option>
					<option value="Baby">Baby</option>
					<option value="Bejeweled">Bejeweled</option>
					<option value="Boys">Boys</option>
					<option value="Clicker">Clicker</option>
					<option value="Cooking">Cooking</option>
					<option value="Farming">Farming</option>
					<option value="Girls">Girls</option>
					<option value="Hypercasual">Hypercasual</option>
					<option value="Multiplayer">Multiplayer</option>
					<option value="Puzzle">Puzzle</option>
					<option value="Racing">Racing</option>
					<option value="Shooting">Shooting</option>
					<option value="Soccer">Soccer</option>
					<option value="Social">Social</option>
					<option value="Sports">Sports</option>
					<option value="Stickman">Stickman</option>
				</select>
			</div>
			<div class="form-group">
				<label>Item</label> 
				<select name="Limit" class="form-control">
					<option selected="selected" value="10">10</option>
					<option value="20">20</option>
					<option value="30">30</option>
					<option value="40">40</option>
				</select>
			</div>
			<div style="display:none;" class="form-group">
				<label>Offset</label> 
				<select name="Offset" class="form-control">
					<option selected="selected" value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
				</select>
			</div>
			<input type="submit" class="btn btn-primary" value="Fetch games" id="fetch">
		</form>
		<br>
		<div class="fetch-loading" style="display: none;">
			<h3>Fecthing games ...</h3>
		</div>
		<div class="fetch-list" style="display: none;">
			<table class="table">
				<thead>
					<tr>
						<th>#</th>
						<th>Title</th>
						<th>URL</th>
						<th>Thumbnail</th>
						<th>Category</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id="gameList">
				</tbody>
			</table>
			<button class="btn btn-primary" id="add-all">Add all</button>
		</div>
	</div>
	<div class="tab-pane tab-container fade" id="remote">
		<form id="form-remote">
			<div class="form-group">
				<label for="title">Game title:</label>
				<input type="text" class="form-control" name="title" value="" required />
			</div>
			<?php
				if(CUSTOM_SLUG){ ?>
				<div class="form-group">
					<label for="slug">Category slug:</label>
					<input type="text" class="form-control" name="slug" placeholder="adventure-game" value="" minlength="3" maxlength="15" required>
				</div>
				<?php }
			?>
			<div class="form-group">
				<label for="description">Description:</label>
				<textarea class="form-control" name="description" rows="3" required /></textarea>
			</div>
			<div class="form-group">
				<label for="instructions">Instructions:</label>
				<textarea class="form-control" name="instructions" rows="3"></textarea>
			</div>
			<div class="form-group">
				<label for="thumb_1">Thumbnail 512x384:</label>
				<input type="text" class="form-control" name="thumb_1" placeholder="https://example.com/yourgames/thumb_1.jpg" value="" required />
			</div>
			<div class="form-group">
				<label for="thumb_2">Thumbnail 512x512:</label>
				<input type="text" class="form-control" name="thumb_2" placeholder="https://example.com/yourgames/thumb_2.jpg" value="" required />
			</div>
			<div class="form-group">
				<label for="url">Game URL:</label>
				<input type="text" class="form-control" name="url" value="" placeholder="https://example.com/yourgames/index.html" required />
			</div>
			<div class="form-group">
				<label for="width">Game width:</label>
				<input type="number" class="form-control" name="width" value="1280" required />
			</div>
			<div class="form-group">
				<label for="height">Game height:</label>
				<input type="number" class="form-control" name="height" value="720" required />
			</div>
			<div class="form-group">
				<label for="category">Category:</label>
				<select multiple class="form-control" name="category" required />
					<?php
						$results = array();
						$data = Category::getList();
						$categories = $data['results'];
						foreach ($categories as $cat) {
							echo '<option>'.ucfirst($cat->name).'</option>';
						}
					?>
				</select>
			</div>
			<button type="submit" class="btn btn-primary">Add game</button>
		</form>
	</div>
</div>