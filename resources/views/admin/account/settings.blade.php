@extends('admin.layout')

@section('content')

<div class="container">
	@include('admin.title-bar')
	<div class="row">
		<div class="col-md-12">
				<form action="{{ action('Admin\AccountController@updateSettings') }}" method="POST" class="form">
					{{ method_field('patch') }}
					{{ csrf_field() }}

					<div class="row">
						<div class="col-md-12">
							<div class="x_panel">
								<div class="form-group">
									<label id='name'>Full name</label>
									<input class="form-control" type="text" name="user[name]" value="{{ old('user.name', $user->name) }}" maxlength="255" />
								</div>
								<div class="form-group">
									<label id='username'>Username (Cannot change)</label>
									<input class="form-control" type="text" name="user[username]" value="{{ old('user.username', $user->username) }}" maxlength="20" readonly disabled />
								</div>
								<div class="form-group">
									<label id='email'>Email address</label>
									<input class="form-control" type="email" name="user[email]" value="{{ old('user.email', $user->email) }}" maxlength="255" />
								</div>
								<div class="form-group">
									<label id='password'>Update Password (leave blank to keep current password)</label>
									<input class="form-control" type="password" name="user[password]" />
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
</div>

@endsection