@extends('admin.layout')

@section('content')
<div class="container">
	@include('admin.title-bar')
	<div class="x_panel">
		<a href="{{ action('Admin\UsersController@new') }}" class="btn btn-success pull-right">New User</a>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="x_panel">
				@if( $users->count() == 0 )
					<p>No users found @if( !empty($_GET['search']) ) using your search criteria @endif</p>
				@else
					<table class="table table-striped table-hover index-table" id="users">
						<thead>
						    <tr>
								<th>#</th>
								<th>Name</th>
								<th>Username</th>
								<th>Email</th>
							</tr>
					  </thead>
					  <tbody id="users-list" class="pagination-results" data-limit="{{ $limit }}" data-total="{{ $users->total() }}">
							@foreach( $users as $user )
								<tr class="property-item">
									<td>
									<a href="{{ action('Admin\UsersController@edit', ['user' => $user->id ]) }}">&nbsp;</a>
									{{ $user->id }}
									</td>
									<td>
									<a href="{{ action('Admin\UsersController@edit', ['user' => $user->id ]) }}">&nbsp;</a>
									{{ $user->name }}
									</td>
									<td>
									<a href="{{ action('Admin\UsersController@edit', ['user' => $user->id ]) }}">&nbsp;</a>
									{{ $user->username }}
									</td>
									<td>
									<a href="{{ action('Admin\UsersController@edit', ['user' => $user->id ]) }}">&nbsp;</a>
									{{ $user->email }}
									</td>
									<td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{{ $users->links() }}
				@endif
			</div>
		</div>
	</div>
	<div class="x_panel">
		<a href="{{ action('Admin\UsersController@new') }}" class="btn btn-success pull-right">New User</a>
	</div>
</div>
@endsection
