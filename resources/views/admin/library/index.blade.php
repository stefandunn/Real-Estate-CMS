@extends( (isset($_GET['modal']))? 'admin.layout-modal' : 'admin.layout')

@section('content')

<div class="container @if( isset($_GET['modal']) )modal-mode @endif" data-image-uri="{{ \URL::to('/') }}" id="library-container" @if( isset($_GET['ref_id']) )data-field-id="{{$_GET['ref_id']}}" @endif @if( isset($_GET['initiator']) )data-initiator="{{$_GET['initiator']}}" @endif>
	@include('admin.title-bar')
	<div class="row">
		<div class="col-md-12">
			<div class="x_panel">
				<form action="{{ action('Admin\MediaController@upload') }}" class="dropzone bg-warning" id="upload-area" data-max-size="{{ \Config::get('pss.max_upload_size', 4096)/1000 }}">
					{{ csrf_field() }}
				</form>
			</div>
		</div>
	</div>
	<div class="row" id="upload-error-alert">
		<div class="col-md-12">
			<p class="alert alert-danger"></p>
		</div>
	</div>
	<div class="row" id="upload-complete-alert">
		<div class="col-md-12">
			<p class="alert alert-success">All files have been uploaded successfully</p>
		</div>
	</div>

	<div class="row" id="view-type">
		<div class="col-md-12">
			<div class="x_panel">
				<div class="row">
					<div class="col-md-9">
						@if( empty($_GET['modal']) )
							<form method="GET" class="form">
								@foreach(array_diff_key($_GET, ['mime_type' => '']) as $name => $value)
									<input type="hidden" name="{{ $name }}" value="{{ $value }}" />
								@endforeach
								<div class="form-group no-margin">
									<div>
										<div class="inline-block v-middle">
											<span class="no-margin inline-block" style="width: 100px;">File Type&nbsp;</span>
										</div>
										<div class="inline-block v-middle">
											<select id="file-type" name="mime_type" class="form-control" onchange="$(this).parents('form').submit()" style="width: auto">
												<option value="*" @if( !isset($_GET['mime_type']) || $_GET['mime_type'] == '*' ) selected @endif>All</option>
												<option value="image" @if( isset($_GET['mime_type']) && $_GET['mime_type'] == 'image' ) selected @endif>Image</option>
												<option value="video" @if( isset($_GET['mime_type']) && $_GET['mime_type'] == 'video' ) selected @endif>Video</option>
												<option value="application" @if( isset($_GET['mime_type']) && $_GET['mime_type'] == 'application' ) selected @endif>Document</option>
											</select>
										</div>
									</div>
								</div>
							</form>
						@endif
					</div>
					<div class="col-md-3" style="text-align: right;">
						<span class="inline-block v-middle">View:</span>
						<span class="inline-block v-middle">
							<a href="{{ action('Admin\MediaController@index', array_merge($_GET, [ 'view' => 'list' ] )) }}" class="btn {{ ($view == 'list')? 'btn-success' : 'btn-default' }}">List</a>
						</span>
						<span class="inline-block v-middle">
							<a href="{{ action('Admin\MediaController@index', array_merge($_GET, [ 'view' => 'grid' ] )) }}" class="btn {{ ($view == 'grid')? 'btn-success' : 'btn-default' }}">Grid</a>
						</span>
					</div>
				</div>
				@if( empty($_GET['modal']) )
					<div class="row">
						<div class="col-md-12" id="bulk-actions" style="display: none; text-align: left; margin-top: 10px">
							<span style="width: 100px;" class="inline-block">Action</span>
							<select id="bulk-action" class="form-control" style="display: inline-block; width: auto;" data-token="{{ csrf_token() }}">
								<option id="">Select</option>
								<option id='regenerate' data-action-url="{{ action('Admin\MediaController@bulkRegenerateImages') }}">Regenerate Images</option>
								<option id='delete' data-action-url="{{ action('Admin\MediaController@bulkDelete') }}">Delete</option>
							</select>
							<div id="perform-action" class="btn btn-default no-margin">Go</div>
						</div>
					</div>
				@endif
			</div>
		</div>
	</div>
	@if( $media_files->count() > 0 )
		@if( $view == 'list' )
			<div class="row">
				<div class="col-md-12">
					<div class="x_panel">
						<table class="table table-striped table-hover index-table" id="media-files">
							<thead>
							    <tr>
							    	@if( empty($_GET['modal']) )
							    	<th>Select</th>
							    	@endif
									<th>Thumbnail</th>
									<th>Title</th>
									<th>Caption</th>
								</tr>
						  </thead>
						  <tbody id="media-file-list" class="pagination-results" data-limit="{{ $limit }}" data-total="{{ $media_files->total() }}">
							  <tr class="media-file-item-template" style="display: none;">
							  		@if( empty($_GET['modal']) )
									<td style="vertical-align: middle !important; text-align: center;">
										<div class="fancy-checkbox">
								  			<input type="checkbox" name="" class="checkbox"/>
								  			<label class="" title="Select this checkbox to apply an action to it" ></label>
								  		</div>
									</td>
									@endif
									<td class="media-file-thumb">
										<a href="">&nbsp;</a>
										<img src="" alt="" />
									</td>
									<td class="media-file-title">
										<a href="">&nbsp;</a>
									</td>
									<td class="media-file-caption">
										<a href="">&nbsp;</a>
									</td>
								</tr>
								@foreach( $media_files as $media_file )
									<tr class="media-file-item"
										data-id="{{ $media_file->id }}" 
										data-desktop-size="{{ $media_file->desktop_path }}"
										data-tablet-size="{{ $media_file->tablet_path }}"
										data-mobile-size="{{ $media_file->mobile_path }}"
										data-thumbnail-size="{{ $media_file->thumbnail_path }}"
										data-original-size="{{ $media_file->path }}" 
										data-forced-thumbnail-url="{{ $media_file->getThumbnailPath() }}"
										data-title="{{ $media_file->title }}"
										data-alt="{{ $media_file->alt }}" >
										@if( empty($_GET['modal']) )
										<td style="vertical-align: middle !important; text-align: center;">
								  			<div class="fancy-checkbox">
									  			<input type="checkbox" name="" class="checkbox" />
									  			<label class="" title="Select this checkbox to apply an action to it" ></label>
									  		</div>
										</td>
										@endif
										<td class="media-file-thumb">
											<a href="{{ action('Admin\MediaController@edit', [ 'media_file' => $media_file->id] ) }}">&nbsp;</a>
											{{ $media_file->toTag('thumbnail', ['class' => 'thumbnail-image bg-checker']) }}
										</td>
										<td class="media-file-title">
											<a href="{{ action('Admin\MediaController@edit', [ 'media_file' => $media_file->id] ) }}">&nbsp;</a>
											{{ $media_file->title }}
										</td>
										<td class="media-file-caption">
											<a href="{{ action('Admin\MediaController@edit', [ 'media_file' => $media_file->id] ) }}">&nbsp;</a>
											{!! $media_file->caption !!}
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		@else
			<div class="row" id="media-files">
				<div class="col-md-12">
					<div class="x_panel">
						<div class="row" id="media-file-list" class="pagination-results" data-limit="{{ $limit }}" data-total="{{ $media_files->total() }}">
							<div class="col-md-3 media-file-item-template">
								@if( empty($_GET['modal']) )
								<div class="checkbox-container">
							  		<div class="fancy-checkbox">
							  			<input type="checkbox" name="" class="checkbox"/>
							  			<label class="" title="Select this checkbox to apply an action to it" ></label>
								  	</div>
								</div>
								@endif
								<div class="media-file-thumb">
									<a href="">&nbsp;</a>
									<img src="" alt="" />
								</div>
								<div class="media-file-title">
									<a href="">&nbsp;</a>
								</div>
								<div class="media-file-caption">
									<a href="">&nbsp;</a>
								</div>
							</div>
							@foreach( $media_files as $media_file )
								<div class="col-md-3 media-file-item"
										data-id="{{ $media_file->id }}" 
										data-desktop-size="{{ $media_file->desktop_path }}"
										data-tablet-size="{{ $media_file->tablet_path }}"
										data-mobile-size="{{ $media_file->mobile_path }}"
										data-thumbnail-size="{{ $media_file->thumbnail_path }}"
										data-original-size="{{ $media_file->path }}"
										data-title="{{ $media_file->title }}"
										data-forced-thumbnail-url="{{ $media_file->getThumbnailPath() }}"
										data-alt="{{ $media_file->alt }}" >
									@if( empty($_GET['modal']) )
									<div class="checkbox-container">
								  		<div class="fancy-checkbox">
								  			<input type="checkbox" name="" class="checkbox"/>
								  			<label class="" title="Select this checkbox to apply an action to it" ></label>
									  	</div>
									</div>
									@endif
									<div class="media-file-thumb">
										<a href="{{ action('Admin\MediaController@edit', [ 'media_file' => $media_file->id] ) }}">&nbsp;</a>
										{{ $media_file->toTag('thumbnail', ['class' => 'thumbnail-image', 'style' => 'background-color: rgba(0, 0, 0, 0.3)']) }}
									</div>
									<div class="media-file-title">
										<a href="{{ action('Admin\MediaController@edit', [ 'media_file' => $media_file->id] ) }}">&nbsp;</a>
										{{ $media_file->title }}
									</div>
									<div class="media-file-caption">
										<a href="{{ action('Admin\MediaController@edit', [ 'media_file' => $media_file->id] ) }}">&nbsp;</a>
										{{ $media_file->caption }}
									</div>
								</div>
							@endforeach
						</div>
					</div>
				</div>
			</div>

		@endif

		<div class="row">
			<div class="col-md-12" id="pagination-wrapper" data-view="{{ $view }}">
				{{ $media_files->appends(['view' => $view])->links() }}
			</div>
		</div>
	@else
		<div class="row">
			<div class="col-md-12">
				<p class="text-center">No media files found @if( !empty($_GET['mime_type']) || !empty($_GET['search']) ) using your search criteria @endif</p>
			</div>
		</div>
	@endif
</div>

@endsection