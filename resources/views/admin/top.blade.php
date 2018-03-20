<div class="top_nav">
	<div class="nav_menu">
		<nav>
			<div class="nav toggle">
				<a id="menu_toggle"><i class="fa fa-bars"></i></a>
			</div>

			<ul class="nav navbar-nav navbar-right">
				<li>
					<a href="{{ action('HomeController@index') }}" target="_blank">
						<span style="display: inline-block; vertical-align: middle;">View Site</span>
						<span style="display: inline-block; vertical-align: middle;"><i class="fa fa-external-link pull-right"></i></span>
					</a>
				</li>
				<li class="">
					<a href="#" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						{{ \Html::image( "https://www.gravatar.com/avatar/" . md5( strtolower( trim( \Auth::user()->email ) ) ) . "?d=" . urlencode( "https://cdnjs.cloudflare.com/ajax/libs/material-design-icons/3.0.1/image/ios/ic_tag_faces_36pt.imageset/ic_tag_faces_36pt_2x.png" ) . "&s=20") }}{{ \Auth::user()->name }}
						<span class=" fa fa-angle-down"></span>
					</a>
					<ul class="dropdown-menu dropdown-usermenu pull-right">
						<li><a href="{{ action('Admin\AccountController@settings') }}"> Account Settings</a></li>
						<li><a href="{{ action('Admin\AccountController@logout') }}"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
					</ul>
				</li>
			</ul>
		</nav>
	</div>
</div>