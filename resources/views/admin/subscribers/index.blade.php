@extends('admin.layout')

@section('content')
<div class="container">
	@include('admin.title-bar')
	<div class="x_panel">
		<a href="{{ action('Admin\SubscribersController@export') }}" class="btn btn-success pull-right">Export</a>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="x_panel">
				@if( $subscribers->count() == 0 )
					<p>No subscribers found @if( !empty($_GET['search']) ) using your search criteria @endif</p>
				@else
					<table class="table table-striped table-hover index-table" id="subscribers">
						<thead>
						    <tr>
								<th>#</th>
								<th>First Name</th>
								<th>Last Name</th>
								<th>Email</th>
								<th>Subscribed Date</th>
							</tr>
					  </thead>
					  <tbody id="subscribers-list" class="pagination-results" data-limit="{{ $limit }}" data-total="{{ $subscribers->total() }}">
							@foreach( $subscribers as $subscriber )
								<tr class="property-item">
									<td>{{ $subscriber->id }}</td>
									<td>{{ $subscriber->first_name }}</td>
									<td>{{ $subscriber->last_name }}</td>
									<td>{{ $subscriber->email }}</td>
									<td>{!! date('g:ia j\<\s\u\p\>S\<\/\s\u\p\> F Y', strtotime($subscriber->created_at)) !!}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{{ $subscribers->links() }}
				@endif
			</div>
		</div>
	</div>
	<div class="x_panel">
		<a href="{{ action('Admin\SubscribersController@export') }}" class="btn btn-success pull-right">Export</a>
	</div>
</div>
@endsection
