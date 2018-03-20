<!DOCTYPE html>
<html lang="en">

@include('admin.head')
@yield('styles')

<body class="nav-md" id="{{ strtolower( preg_replace( "/App\\\\Http\\\\Controllers\\\\Admin\\\\([A-Z]{1,})([a-z]*)Controller\@([a-z]+)/", '$1$2-$3', \Route::getCurrentRoute()->getActionName())) }}">

	{{-- NProgress events --}}
	<script>

	    /** Start progress */
	    NProgress.start();
    	/** NProgress bar done on window load */
		window.onload = function(){
			NProgress.done();
		};

    </script>


	@include('admin.flash-messages')
	<div class="container body">
		<div class="main_container">
			<div class="col-md-3 left_col">
				<div class="left_col scroll-view">
					<div class="navbar nav_title" style="border: 0; height: 0; min-height: 0;"></div>

					<div class="clearfix"></div>

					<!-- menu profile quick info -->
					<div class="profile clearfix">
						<div class="profile_pic">
							{{ \Html::image( "https://www.gravatar.com/avatar/" . md5( strtolower( trim(  \Auth::user()->email ) ) ) . "?d=" . urlencode( "https://cdnjs.cloudflare.com/ajax/libs/material-design-icons/3.0.1/image/ios/ic_tag_faces_36pt.imageset/ic_tag_faces_36pt_2x.png" ) . "&s=40", "", [ 'class' => 'img-circle profile_img' ]) }}
						</div>
						<div class="profile_info">
							<span>Welcome,</span>
							<h2>{{ \Auth::user()->name }}</h2>
						</div>
					</div>
					<!-- /menu profile quick info -->

					<br />

					<!-- sidebar menu -->
					@include('admin.navigation')
					<!-- /sidebar menu -->

					<!-- /menu footer buttons -->
					<div class="sidebar-footer hidden-small">
						<a href="{{ action('Admin\AccountController@logout') }}" data-toggle="tooltip" data-placement="top" title="Logout" class="fill">
							<span class="glyphicon glyphicon-off" aria-hidden="true"></span>
						</a>
					</div>
					<!-- /menu footer buttons -->
				</div>
			</div>

			<!-- top navigation -->
			@include("admin.top")
			<!-- /top navigation -->

			<!-- page content -->
			<div class="right_col" role="main">
				@yield('content')
			</div>
			<!-- /page content -->

			<!-- footer content -->
			<footer>
				<div class="pull-right">
					<small><a href="{{ URL::to('/') }}" target="_blank">{{ getSetting( 'title' ) }}</a> &bullet; {{ date( 'Y' ) }}</small>
				</div>
				<div class="clearfix"></div>
			</footer>
			<!-- /footer content -->
		</div>
	</div>

	@yield('scripts')
	@include('admin.end')

</body>
</html>
