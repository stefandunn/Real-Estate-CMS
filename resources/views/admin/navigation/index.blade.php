@extends('admin.layout')

@section('content')
<div class="container">
	@include('admin.title-bar')
	<div class="row">
		<div class="col-md-12">
			<form action="{{ action('Admin\NavigationController@save') }}" method="POST" class="form" id="nav-form">
				{{ csrf_field() }}
				<input type="hidden" name="navigation_data" id="nav-data"/>
				<div class="row">
					<div class="col-md-8">
						<div class="x_panel" id="nav-list-container">
							<h4>Main Navigation</h4>
							<ul id="header-nav-item-list" class="">
								@foreach (@$header_nav_items as $nav_item)
								<li class="nav-item" data-id="{{ $nav_item->id }}" data-label="{{ $nav_item->label }}" data-url="{{ $nav_item->url }}" data-new-window="{{ $nav_item->new_window }}" data-css-styling="{{ $nav_item->styling }}" data-css-class="{{ $nav_item->class }}">
									<span>{{ $nav_item->label }}<i class="fa fa-pencil nav-item-edit" title="Edit" aria-hidden="true"></i> <i class="fa fa-times nav-item-delete" title="Delete" aria-hidden="true"></i></span>
									<ul class="children">
										@foreach( $nav_item->children as $child_nav_item )
										<li class="nav-item" data-id="{{ $child_nav_item->id }}" data-label="{{ $child_nav_item->label }}" data-url="{{ $child_nav_item->url }}" data-new-window="{{ $child_nav_item->new_window }}" data-css-styling="{{ $child_nav_item->styling }}" data-css-class="{{ $child_nav_item->class }}">
											<span>{{ $child_nav_item->label }}<i class="fa fa-pencil nav-item-edit" title="Edit" aria-hidden="true"></i> <i class="fa fa-times nav-item-delete" title="Delete" aria-hidden="true"></i></span>
										</li>
										@endforeach
									    <li class="new-nav">
											<span><i class="fa fa-plus" aria-hidden="true"></i> New</span>
										</li>
									</ul>
								</li>
								@endforeach
								<li class="nav-item template" style="display: none;" data-id="" data-label="" data-url="" data-new-window="" data-css-styling="" data-css-class="">
									<span><i class="fa fa-pencil nav-item-edit" title="Edit" aria-hidden="true"></i> <i class="fa fa-times nav-item-delete" title="Delete" aria-hidden="true"></i></span>
									<ul class="children">
									    <li class="new-nav">
											<span><i class="fa fa-plus" aria-hidden="true"></i> New</span>
										</li>
									</ul>
								</li>
								<li class="new-nav">
									<span><i class="fa fa-plus" aria-hidden="true"></i> New</span>
								</li>
							</ul>
						</div>
					</div>
					<div class="col-md-4">
						<div class="x_panel">
							<h4>Footer Useful Links</h4>
							<ul id="footer-nav-item-list" class="">
								@foreach (@$footer_nav_items as $nav_item)
								<li class="nav-item" data-id="{{ $nav_item->id }}" data-label="{{ $nav_item->label }}" data-url="{{ $nav_item->url }}" data-new-window="{{ $nav_item->new_window }}" data-css-styling="{{ $nav_item->styling }}" data-css-class="{{ $nav_item->class }}">
									<span>{{ $nav_item->label }}<i class="fa fa-pencil nav-item-edit" title="Edit" aria-hidden="true"></i> <i class="fa fa-times nav-item-delete" title="Delete" aria-hidden="true"></i></span>
								</li>
								@endforeach
								<li class="new-nav">
									<span><i class="fa fa-plus" aria-hidden="true"></i> New</span>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="x_panel">
							<input type="submit" name="submit" value="Save Navigation" class="btn btn-success" />
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="container" id="new-nav-form" style="display: none;">
	<div class="row">
		<div class="col-md-3">
			<input type="text" id="label" placeholder="Label" class="form-control" />
		</div>
		<div class="col-md-7">
			<div style="position: relative;">
				<input type="text" id='url' placeholder="URL" class="form-control" />
				<div id='url-browser' title="Browse for page/property/media file">
					<i class="fa fa-search" aria-hidden="true"></i>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<select id="new-window" class="form-control">
				<option value="0">Same window</option>
				<option value="1">New window</option>
			</select>
		</div>
	</div>
	<div class="row" style="margin-top: 10px;">
		<div class="col-md-6">
			<input type="text" id="css-class" placeholder="CSS Class name" class="form-control" />
		</div>
		<div class="col-md-6">
			<input type="text" id="css-styling" placeholder="CSS Styling" class="form-control" />
		</div>
	</div>
	<div class="row" id="errors" style="margin-top: 10px; display: none; text-align: left; color: #e74c3c;">
		<div class="col-md-12">
			
		</div>
	</div>
	<div class="row" style="margin-top: 10px;">
		<div class="col-md-12 text-left">
			<span class="btn btn-success nav-form-create">Create</span>
			<span class="btn btn-danger nav-form-cancel">Cancel</span>
		</div>
	</div>
</div>
<div class="modal-wrapper" style="display: none; z-index: 10000;" id="url-browser-modal">
	<div class="modal">
		<span class='fa fa-times close-btn'></span>
		<h3>URL Browser</h3>
		<div style="background: #fafafa; padding: 10px; color: #333;">
			<div class="container">
				<div class="row">
					<div class="col-md-4 form-group">
						<select class="form-control" id="type">
							<option value="all">All</option>
							<option value='pages'>Pages</option>
							<option value='properties'>Properties</option>
							<option value='media'>Media File</option>
						</select>
					</div>
					<div class="col-md-8">
						<datalist id="lookup-data">
							@foreach ($url_browser_lookup as $type => $options)
								@foreach ($options as $option)
									<option value="{{ $option['value'] }}" data-url="{{ $option['data'] }}" data-type="{{ $type }}">
								@endforeach
							@endforeach
						</datalist>
						<input type="text" id="urls-selector" class="form-control" placeholder="Start typing title/name" list="lookup-data"/>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class='modal-bg' style="background-color: rgba(255, 255, 255, 0.4)"></div>
</div>
@endsection
