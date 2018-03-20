@extends('admin.layout')

@section('content')

<div class="container" id="media-file-edit">
	@include('admin.title-bar')
	<div class="row">
		<div class="col-md-6" id="media-wrapper">
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-body">
							<form action="{{ action('Admin\MediaController@update', [ 'media_file' => $media_file->id ]) }}" method="POST" class="form">
								{{ method_field('patch') }}
								{{ csrf_field() }}
								<div class="form-group">
									<label for="title">Title</label>
									<input id="title" class="form-control" type="text" name="media_file[title]" value="{{ $media_file->title }}" />
								</div>
								@if( strstr( $media_file->mime_type, 'image/' ) )
									<div class="form-group">
										<label for="alt">Alt <small style="font-weight: lighter;">(Text displayed when image can't be loaded. Also used for screen-readers)</small></label>
										<textarea id="alt" class="form-control" name="media_file[alt]">{{ $media_file->alt }}</textarea>
									</div>
								@endif
								<div class="form-group">
									<label for="caption">Caption</label>
									<textarea id="caption" class="form-control" name="media_file[caption]">{{ $media_file->caption }}</textarea>
								</div>
								<div class="form-group">
									<input type="submit" value="Save" name="submit" class="btn btn-success" />
									<a href="{{ action('Admin\MediaController@delete', [ 'media_file' => $media_file->id ]) }}" class="btn btn-danger">Delete</a>
									<a href="{{ action('Admin\MediaController@regenerateImages', [ 'media_file' => $media_file->id ]) }}" class="btn btn-warning">Regenerate Image Set</a>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12" id="media-information">
					<div class="panel panel-default">
						<div class="panel-body">
							<h4>Information</h4>
							<span>Original File URI</span>
							<p>{{ \URL::to('/') . $media_file->path }}</p>
							@if( !empty( $media_file->desktop_path ) )
								<span>Desktop URI</span>
								<p>{{ \URL::to('/') . $media_file->desktop_path }}</p>
							@endif
							@if( !empty( $media_file->tablet_path ) )
								<span>Tablet URI</span>
								<p>{{ \URL::to('/') . $media_file->tablet_path }}</p>
							@endif
							@if( !empty( $media_file->mobile_path ) )
								<span>Mobile URI</span>
								<p>{{ \URL::to('/') . $media_file->mobile_path }}</p>
							@endif
							@if( !empty( $media_file->mobile_path ) )
								<span>Tumbnail URI</span>
								<p>{{ \URL::to('/') . $media_file->thumbnail_path }}</p>
							@endif
							@if( strstr( $media_file->mime_type, 'image/' ) )
								<span>Dimensions</span>
								<p>{{ $media_file->natural_width }} x {{ $media_file->natural_height }}px</p>
							@endif
							<span>Mime Type</span>
							<p>{{ $media_file->mime_type }}</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6" id="media-preview">
			<div class="row">
				<div class="col-md-12">
					@if( strstr( $media_file->mime_type, 'image/' ) )
						{{ $media_file->toTag( 'desktop', [ 'style' => 'max-width: 100%; height: auto; max-height: 500px;', 'class' => 'bg-checker']) }}
					
					@elseif( strstr( $media_file->mime_type, 'video/' ) )
						<video controls="" style="width: 100%;">
							<source src="{{ $media_file->path }}" type="{{ $media_file->mime_type }}" />
						</video>
					
					@elseif( in_array( pathinfo($media_file->path)['extension'], [ 'doc', 'docx', 'xls', 'xlsx', 'pdf', 'ppt', 'pptx', 'pps', 'odt' ] ) )
						<iframe src="https://docs.google.com/viewer?url={{ \URL::to('/') . $media_file->path }}" frameborder="0"></iframe>
					
					@elseif( in_array( pathinfo($media_file->path)['extension'], [ 'html', 'css', 'js', 'css' ] ) )
						<pre>
		{{ file_get_contents( public_path() . $media_file->path )}}
						</pre>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@endsection