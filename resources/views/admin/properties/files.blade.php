@extends('admin.layout')

@section('content')

<div class="container">
	@include('admin.title-bar')
	<div class="row">
		<div class="col-md-12">
			<form action="{{ action('Admin\PropertiesController@updateFiles', ['property' => $property->id]) }}" method="POST" class="form">
				{{ csrf_field() }}
				<?php $file_index = 0; /* Unique ID that increments for each file selector shown */ ?>

				{{-- File selector template to use --}}
				<div class="col-md-3" style="margin-bottom: 10px;" id='file-template'>
					{!! fileSelector( "file-selector-X", "documents[]", null, 'XXX', true ) !!}
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="x_panel">
							<h4 class="no-margin">Images <span class="btn btn-success pull-right" id="add-image"><i class="fa fa-plus" aria-hidden="true"></i> Add</span></h4>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="x_panel" style="padding: 10px 0 0 0;">
							<div class="row" id="property-images" style="margin-left: -5px !important; margin-right: -5px !important;">
								<?php $i = 0; ?>
								@foreach( $property->images as $image )
								<div class="col-md-3" style="margin-bottom: 10px;">
									{!! fileSelector( "file-selector-{$file_index}", "images[]", old( "images.{$i}", $image->id), 'image', true ) !!}
								</div>
								<?php $i++; $file_index++; ?>
								@endforeach
								@foreach( array_slice((is_null(\Request::get('images')))? [] : \Request::get('images'), $i) as $image_id )
									<div class="col-md-3" style="margin-bottom: 10px;">
										{!! fileSelector( "file-selector-{$file_index}", "images[]", old( "images.{$i}", $image_id), 'image', true ) !!}
									</div>
									<?php $i++; $file_index++; ?>
								@endforeach
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="x_panel">
							<h4 class="no-margin">Documents/Files <span class="btn btn-success pull-right" id="add-document"><i class="fa fa-plus" aria-hidden="true"></i> Add</span></h4>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="x_panel" style="padding: 10px 0 0 0;">
							<div class="row" id="property-documents" style="margin-left: -5px !important; margin-right: -5px !important;">
								<?php $d = 0; ?>
								@foreach( $property->documents as $document )
								<div class="col-md-3" style="margin-bottom: 10px;">
									{!! fileSelector( "file-selector-{$file_index}", "documents[]", old( "documents.{$d}", $document->id), '', true ) !!}
								</div>
								<?php $d++; $file_index++; ?>
								@endforeach
								@foreach( array_slice((is_null(\Request::get('documents')))? [] : \Request::get('documents'), $d) as $document_id )
									<div class="col-md-3" style="margin-bottom: 10px;">
										{!! fileSelector( "file-selector-{$file_index}", "documents[]", old( "documents.{$d}", $document_id), '', true ) !!}
									</div>
									<?php $d++; $file_index++; ?>
								@endforeach
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="x_panel">
							<input type="submit" value="Save" name="submit" class="btn btn-success no-margin" />
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection