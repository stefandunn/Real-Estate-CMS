<!DOCTYPE html>
<html lang="en">

@include('admin.head')
@yield('styles')

<body class="nav-md" id="{{ strtolower( preg_replace( "/App\\\\Http\\\\Controllers\\\\Admin\\\\([A-Z]{1})([a-z]{1,})Controller\@([a-z]+)/", '$1$2-$3', \Route::getCurrentRoute()->getActionName())) }}">
	@include('admin.flash-messages')
	<div class="container body">
		<div class="main_container">

			<!-- page content -->
			<div class="right_col" role="main" style="margin-left: 0;">
				@yield('content')
			</div>
			<!-- /page content -->
		</div>
	</div>

	@yield('scripts')
	@include('admin.end')

</body>
</html>
