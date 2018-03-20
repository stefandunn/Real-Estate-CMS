<li class="nav-item @if(!isset($nav_item)) template @endif block" style="display: none;">
	<div class="handle"><i class="fa fa-arrows-v" aria-hidden="true"></i></div>
	<div class="details">
		<div class="row">
			<div class="col-md-2 nav-item-label">
				{{ @$nav_item->label }}
			</div>
		</div>
	</div>
	<div class="edit-form">
		<div class="row">
			<div class="col-md-2">
				<input type="text" name="nav[][label]" placeholder="Label" maxlength="20" class="form-control" value="{{ @$nav_item->label }}" />
			</div>
			<div class="col-md-6">
				<input type="text" name="nav[][url]" placeholder="URL" maxlength="255" class="form-control" value="{{ @$nav_item->url }}" />
			</div>
			<div class="col-md-2">
				<select name="nav[][new_window]" required class="form-control">
					<option value="0" @if( isset($nav_item->new_window) && $nav_item->new_window === 0) selected @endif>Same Window</option>
					<option value="1" @if( isset($nav_item->new_window) && $nav_item->new_window === 1) selected @endif>New Window</option>
				</select>
			</div>
			<div class="col-md-2">
				<select name="nav[][useful_link]" required class="form-control">
					<option value="0" @if( isset($nav_item->useful_link) && $nav_item->useful_link === 0) selected @endif>Header</option>
					<option value="1" @if( isset($nav_item->useful_link) && $nav_item->useful_link === 1) selected @endif>Useful Link</option>
				</select>
			</div>
		</div>
		<div class="row" style="margin-top: 10px;">
			<div class="col-md-6">
				<input type="text" name="nav[][styling]" placeholder="CSS styling" maxlength="255" class="form-control" value="{{ @$nav_item->styling }}" />
			</div>
			<div class="col-md-5">
				<input type="text" name="nav[][class]" placeholder="CSS class" maxlength="255" class="form-control" value="{{ @$nav_item->class }}" />
			</div>
			<div class="col-md-1">
				<span class="save-new-item btn btn-success pull-right">Save</span>
			</div>
		</div>
	</div>
	<div class="nestable-list nav-children">
		
	</div>
</li>