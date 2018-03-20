@extends('admin.layout')

@section('content')
<div class="container">
	@include('admin.title-bar')
	<div class="x_panel">
		<a href="{{ action('Admin\PropertiesController@new') }}" class="btn btn-success no-margin pull-right">New Property</a>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="x_panel">
				@if( $properties->count() == 0 )
					<p>No properties found @if( !empty($_GET['search']) ) using your search criteria @endif</p>
				@else
					<table class="table table-striped table-hover index-table" id="properties">
						<thead>
						    <tr>
								<th>Image</th>
								<th>Reference Code</th>
								<th>Name</th>
								<th>Price</th>
								<th>Type</th>
							</tr>
					  </thead>
					  <tbody id="properties-list" class="pagination-results" data-limit="{{ $limit }}" data-total="{{ $properties->total() }}">
							@foreach( $properties as $property )
								<tr class="property-item">
									<td >
										<a href="{{ action('Admin\PropertiesController@edit', [ 'property' => $property->id] ) }}">&nbsp;</a>
										{{ $property->featureImage->toTag('thumbnail', ['class' => 'thumbnail-image']) }}
									</td>
									<td>
										<a href="{{ action('Admin\PropertiesController@edit', [ 'property' => $property->id] ) }}">&nbsp;</a>
										{{ $property->reference_code }}
									</td>
									<td>
										<a href="{{ action('Admin\PropertiesController@edit', [ 'property' => $property->id] ) }}">&nbsp;</a>
										{{ $property->name }}
									</td>
									<td>
										<a href="{{ action('Admin\PropertiesController@edit', [ 'property' => $property->id] ) }}">&nbsp;</a>
										&pound;{{ number_format($property->price) . " ({$property->priceType->name})" }}
									</td>
									<td>
										<a href="{{ action('Admin\PropertiesController@edit', [ 'property' => $property->id] ) }}">&nbsp;</a>
										{{ $property->purchaseType->name }}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{{ $properties->links() }}
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
