@extends('admin.layout')

@section('content')

<div class="container">
	@include('admin.title-bar')
	<div class="row">
		<div class="col-md-12">
				<form action="{{ ( $is_new )? action('Admin\UsersController@create') : action('Admin\UsersController@update', [ 'user' => $user->id ]) }}" method="POST" class="form">
					@if( !$is_new )
					{{ method_field('patch') }}
					@endif
					{{ csrf_field() }}

					<div class="row">
						<div class="col-md-12">
							<div class="x_panel">
								<div class="form-group">
									<label id='name'>Full name</label>
									<input class="form-control" type="text" name="user[name]" value="{{ old('user.name', $user->name) }}" maxlength="255" />
								</div>
								<div class="form-group">
									<label id='username'>Username</label>
									<input class="form-control" type="text" name="user[username]" value="{{ old('user.username', $user->username) }}" maxlength="20" />
								</div>
								<div class="form-group">
									<label id='email'>Email address</label>
									<input class="form-control" type="email" name="user[email]" value="{{ old('user.email', $user->email) }}" maxlength="255" />
								</div>
								<div class="form-group">
									<label id='password'>@if(!$is_new)Update Password (leave blank to keep current password) @else Password @endif</label>
									<input class="form-control" type="password" name="user[password]" />
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="x_panel">
								<input type="submit" value="{{ (!$is_new)? "Save" : "Create" }}" name="submit" class="btn btn-success no-margin" />
								@if( !$is_new )
								<a href="{{ action('Admin\UsersController@delete', [ 'user' => $user->id ]) }}" class="btn btn-danger">Delete</a>
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