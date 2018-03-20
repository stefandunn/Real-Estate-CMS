@extends('admin.layout')

@section('content')

<div class="container">
	@include('admin.title-bar')
	<div class="row">
		<div class="col-md-12">
				<form action="{{ ( $is_new )? action('Admin\PropertyTypesController@create') : action('Admin\PropertyTypesController@update', [ 'property_type' => $property_type->id ]) }}" method="POST" class="form">
					@if( !$is_new )
					{{ method_field('patch') }}
					@endif
					{{ csrf_field() }}

					<div class="row">
						<div class="col-md-12">
							<div class="x_panel x_panel_no_row_effect">
								<div class="form-group">
									<label for='name'>Name</label>
									<input type="text" name="property_type[name]" required maxlength="255" class="form-control" id="name" value="{{ old('property_type.name', $property_type->name) }}" />
								</div>
								<div class="form-group">
									<label for="description">Description (optional)</label>
									<textarea class="wysiwyg form-control" rows="4" id="description" name="property_type[description]">{{ old('property_type.description', $property_type->description ) }}</textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="x_panel">
								<input type="submit" value="{{ (!$is_new)? "Save" : "Create" }}" name="submit" class="btn btn-success no-margin" />
								@if( !$is_new )
								<a href="{{ action('Admin\PropertyTypesController@delete', [ 'property_type' => $property_type->id ]) }}" class="btn btn-danger">Delete</a>
								@endif
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection