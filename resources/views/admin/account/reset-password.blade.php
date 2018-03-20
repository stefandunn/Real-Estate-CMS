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
					@if( \Session::has('reset_success') )
						<p class="text-success"><small>{{ \Session::get('reset_success') }}</small></p>
						<p class="text-center">
							<a href="{{ action('Admin\AccountController@login') }}" class="btn btn-default">Back to login</a>
						</p>
					@else
						<p><small>Enter your email address into the field below to receive an email to reset your password.</small></p>
						<form action="{{ action('Admin\AccountController@requestResetPassword') }}" method="POST">
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
							<div>
								<label for="email">Email address</label>
								<input type="email" id="email" name="email" class="form-control" placeholder="Email address" required value="{{ old('email') }}"/>
							</div>
							<div class="text-center">
								<input type="submit" id="login-button" class="btn btn-default submit" value="Reset" />
							<a style="display: block" href="{{ action('Admin\AccountController@login') }}">Return to login</a>
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
