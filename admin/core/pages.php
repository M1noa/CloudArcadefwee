<ul class="nav nav-tabs">
	<li class="nav-item">
		<a class="nav-link active" data-toggle="tab" href="#pagelist">Pages</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#addpage">Add page</a>
	</li>
</ul>
<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane tab-container active" id="pagelist">
		<table class="table">
			<thead>
			<tr>
				<th>#</th>
				<th>Title</th>
				<th>Created</th>
				<th>Slug</th>
				<th>URL</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$results = array();
			$data = Page::getList();
			$results['pages'] = $data['results'];
			$results['totalRows'] = $data['totalRows'];
			include("part/list-page.php" );
			?>
		</tbody>
		</table>
		<p><?php echo esc_int($results['totalRows'])?> page<?php echo ( $results['totalRows'] != 1 ) ? 's' : '' ?> in total.</p>
	</div>
	<div class="tab-pane tab-container fade" id="addpage">
		<?php
		$results = array();
		$results['page'] = new Page;
		include("part/add-page.php" );
		?>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="edit-page" tabindex="-1" role="dialog" aria-labelledby="edit-page-modal-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title" id="edit-page-label">Edit Page</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
	  <div class="modal-body">
		<form id="form-editpage">
			<input type="hidden" id="edit-id" name="id" value=""/>
			<input type="hidden" id="edit-createdDate" name="createdDate" value=""/>
			<div class="form-group">
				<label for="title">Page Title:</label>
				<input type="text" class="form-control" id="edit-title" name="title" placeholder="Name of the page" required autofocus minlength="3" maxlength="255" value=""/>
			</div>
			<div class="form-group">
				<label for="slug">Page Slug:</label>
				<input type="text" class="form-control" id="edit-slug" name="slug" placeholder="Page url ex: this-is-sample-page" required minlength="3" autofocus maxlength="255" value=""/>
			</div>
			<div class="form-group">
				<label for="content">Content:</label>
				<textarea class="form-control" name="content" id="edit-content" rows="12" placeholder="The HTML content of the page" required minlength="3" maxlength="100000"></textarea>
			</div>
			<input type="submit" class="btn btn-primary" value="Save changes" />
			<input type="button" class="btn btn-secondary" data-dismiss="modal" value="Close" />
		</form>
	  </div>
	</div>
  </div>
</div>