@extends('admin.layout')

@section('content')

<div class="container">
	@include('admin.title-bar')
	<div class="row">
		<div class="col-md-12">
				<form action="{{ ( $is_new )? action('Admin\PurchaseTypesController@create') : action('Admin\PurchaseTypesController@update', [ 'purchase_type' => $purchase_type->id ]) }}" method="POST" class="form">
					@if( !$is_new )
					{{ method_field('patch') }}
					@endif
					{{ csrf_field() }}

					<div class="row">
						<div class="col-md-12">
							<div class="x_panel x_panel_no_row_effect">
								<div class="form-group">
									<label for='name'>Name</label>
									<input type="text" name="purchase_type[name]" required maxlength="255" class="form-control" id="name" value="{{ old('purchase_type.name', $purchase_type->name) }}" />
								</div>
								<div class="form-group">
									<label for="description">Description (optional)</label>
									<textarea class="wysiwyg form-control" rows="4" id="description" name="purchase_type[description]">{{ old('purchase_type.description', $purchase_type->description ) }}</textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="x_panel">
								<input type="submit" value="{{ (!$is_new)? "Save" : "Create" }}" name="submit" class="btn btn-success no-margin" />
								@if( !$is_new )
								<a href="{{ action('Admin\PurchaseTypesController@delete', [ 'purchase_type' => $purchase_type->id ]) }}" class="btn btn-danger">Delete</a>
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