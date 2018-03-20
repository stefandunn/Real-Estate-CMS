@extends('admin.layout')

@section('content')
<div class="container">
	@include('admin.title-bar')
	<div class="x_panel">
		<a href="{{ action('Admin\PropertyTypesController@new') }}" class="btn btn-success no-margin pull-right">New Property Type</a>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="x_panel">
				@if( $property_types->count() == 0 )
					<p>No property types found @if( !empty($_GET['search']) ) using your search criteria @endif</p>
				@else
					<table class="table table-striped table-hover index-table" id="properties">
						<thead>
						    <tr>
								<th>#</th>
								<th>Name</th>
								<th>Created At</th>
							</tr>
					  </thead>
					  <tbody id="properties-list" class="pagination-results" data-limit="{{ $limit }}" data-total="{{ $property_types->total() }}">
							@foreach( $property_types as $property_type )
								<tr class="property-type-item">
									<td>
										<a href="{{ action('Admin\PropertyTypesController@edit', [ 'property_type' => $property_type->id] ) }}">&nbsp;</a>
										{{ $property_type->id }}
									</td>
									<td>
										<a href="{{ action('Admin\PropertyTypesController@edit', [ 'property_type' => $property_type->id] ) }}">&nbsp;</a>
										{{ $property_type->name }}
									</td>
									<td>
										<a href="{{ action('Admin\PropertyTypesController@edit', [ 'property_type' => $property_type->id] ) }}">&nbsp;</a>
										{!! date('g:ia j\<\s\u\p\>S\<\/\s\u\p\> F Y', strtotime($property_type->created_at)) !!}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{{ $property_types->links() }}
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
