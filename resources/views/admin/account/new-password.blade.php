<!DOCTYPE html>
<html lang="en">

@include('admin.head')

<body class="login">
	<div>
		<a class="hiddenanchor" id="signup"></a>
		<a class="hiddenanchor" id="signin"></a>

		<div class="login_wrapper">
			<div class="animate form login_form">
				<section class="login_content">
					<h2>Paul Simon Seaton</h2>
					@if( \Session::has('do_reset_error') )
						<p class="text-error"><small>{{ \Session::get('do_reset_error') }}</small></p>
						<a href="{{ action('Admin\AccountController@login') }}" class="btn btn-default">Back to login</a>
					@else
						<p class="text-info">Resetting password for <b>{{ $user->username }}</b>.</p>
						<p><small>Please type a new password in below and click "Reset password" to change your password</small></p>
						<form action="{{ action('Admin\AccountController@doReset') }}" method="POST">
							{{ csrf_field() }}
							<div class="text-center">
								@if( count( $errors ) )
									<ul class="text-danger list-unstyled">
										@foreach( $errors->all() as $error )
											<li><small>{{ $error }}</small></li>
										@endforeach
									</ul>
								@endif
							</div>
							<input type="hidden" name="reset_token" value="{{ \Request::route('token') }}" />
							<div>
								<label for="">New Password</label>
								<input type="password" name="password" class="form-control" placeholder="New Password" required value="{{ old('password') }}"/>
							</div>
							<div>
								<label for="">Repeat New Password:</label>
								<input type="password" name="password_confirm" class="form-control" placeholder="Confirm New Password" required value="{{ old('password_confirm') }}"/>
							</div>
							<div class="text-center">
								<input type="submit" id="login-button" class="btn btn-default submit" value="Reset Password" />
							</div>
						</form>
					@endif
				</section>
			</div>
		</div>
	</div>

	@include('admin.end')

</body>
</html>
