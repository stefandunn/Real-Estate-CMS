@extends('admin.layout')

@section('content')
<div class="container">
	@include('admin.title-bar')
	<div class="x_panel">
		<a href="{{ action('Admin\PricingTypesController@new') }}" class="btn btn-success no-margin pull-right">New Pricing Type</a>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="x_panel">
				@if( $pricing_types->count() == 0 )
					<p>No pricing type types found @if( !empty($_GET['search']) ) using your search criteria @endif</p>
				@else
					<table class="table table-striped table-hover index-table" id="properties">
						<thead>
						    <tr>
								<th>#</th>
								<th>Name</th>
								<th>Created At</th>
							</tr>
					  </thead>
					  <tbody id="properties-list" class="pagination-results" data-limit="{{ $limit }}" data-total="{{ $pricing_types->total() }}">
							@foreach( $pricing_types as $pricing_type )
								<tr class="pricing-type-item">
									<td>
										<a href="{{ action('Admin\PricingTypesController@edit', [ 'pricing_type' => $pricing_type->id] ) }}">&nbsp;</a>
										{{ $pricing_type->id }}
									</td>
									<td>
										<a href="{{ action('Admin\PricingTypesController@edit', [ 'pricing_type' => $pricing_type->id] ) }}">&nbsp;</a>
										{{ $pricing_type->name }}
									</td>
									<td>
										<a href="{{ action('Admin\PricingTypesController@edit', [ 'pricing_type' => $pricing_type->id] ) }}">&nbsp;</a>
										{!! date('g:ia j\<\s\u\p\>S\<\/\s\u\p\> F Y', strtotime($pricing_type->created_at)) !!}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{{ $pricing_types->links() }}
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
