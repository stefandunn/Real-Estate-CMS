@extends('admin.layout')

@section('content')
<div class="container">
	@include('admin.title-bar')
	<div class="x_panel">
		<a href="{{ action('Admin\PurchaseTypesController@new') }}" class="btn btn-success no-margin pull-right">New Purchase Type</a>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="x_panel">
				@if( $purchase_types->count() == 0 )
					<p>No purchase type types found @if( !empty($_GET['search']) ) using your search criteria @endif</p>
				@else
					<table class="table table-striped table-hover index-table" id="properties">
						<thead>
						    <tr>
								<th>#</th>
								<th>Name</th>
								<th>Created At</th>
							</tr>
					  </thead>
					  <tbody id="properties-list" class="pagination-results" data-limit="{{ $limit }}" data-total="{{ $purchase_types->total() }}">
							@foreach( $purchase_types as $purchase_type )
								<tr class="pricing-type-item">
									<td>
										<a href="{{ action('Admin\PurchaseTypesController@edit', [ 'purchase_type' => $purchase_type->id] ) }}">&nbsp;</a>
										{{ $purchase_type->id }}
									</td>
									<td>
										<a href="{{ action('Admin\PurchaseTypesController@edit', [ 'purchase_type' => $purchase_type->id] ) }}">&nbsp;</a>
										{{ $purchase_type->name }}
									</td>
									<td>
										<a href="{{ action('Admin\PurchaseTypesController@edit', [ 'purchase_type' => $purchase_type->id] ) }}">&nbsp;</a>
										{!! date('g:ia j\<\s\u\p\>S\<\/\s\u\p\> F Y', strtotime($purchase_type->created_at)) !!}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{{ $purchase_types->links() }}
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
