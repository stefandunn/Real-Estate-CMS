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
					<h3>Paul Simon Seaton</h3>
					@if( \Session::has('do_reset_success') )
						<p class="font-teal"><small>{{ \Session::get('do_reset_success') }}</small></p>
						<p><a href="{{ action('Admin\AccountController@login') }}" class="btn btn-default">Back to login</a></p>
					@elseif( \Session::has('do_reset_error') )
						<p class="font-red"><small>{{ \Session::get('do_reset_error') }}</small></p>
						<p><a href="{{ action('Admin\AccountController@login') }}" class="btn btn-default">Back to login</a></p>
					@else
					<form action="{{ action('Admin\AccountController@doLogin') }}" method="POST">
						{{ csrf_field() }}
						@if( isset($_GET['request_uri']) )
							<input type="hidden" name="redirect_uri" value={{ $_GET['request_uri'] }} />
						@endif
						<div>
							<label for="">Username</label>
							<input type="text" name="username" class="form-control" placeholder="Username" required value="{{ old('username') }}"/>
						</div>
						<div>
							<label for="">Password:</label>
							<input type="password" name="password" class="form-control" placeholder="Password" required/>
						</div>
						@if( \Session::has('failed_login') )
							<p class="font-red"><small>{{ \Session::get('failed_login') }}</small></p>
						@endif
						<div class="text-center">
							<input type="submit" id="login-button" class="btn btn-default submit" value="Log in" />
							<small><a style="display: block" href="{{ action('Admin\AccountController@resetPassword') }}">Forgot Password?</a></small>
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
