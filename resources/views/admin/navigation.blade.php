<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
	<div class="menu_section">
		<ul class="nav side-menu">
			<li>
				<a href="{{ action('Admin\DashboardController@index') }}">
					<i class="fa fa-home"></i> Dashboard
				</a>
			</li>
		</ul>
		<h3 style="margin-top: 30px;">General</h3>
		<ul class="nav side-menu">
			<li>
				<a href="{{ action('Admin\MediaController@index') }}">
					<i class="fa fa-picture-o"></i> Media Library
				</a>
			</li>
			<li><a><i class="fa fa-desktop"></i> Pages <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
					<li><a href="{{ action('Admin\PagesController@index') }}">All</a></li>
					<li><a href="{{ action('Admin\PagesController@new') }}">Create new</a></li>
					@foreach( \App\Page::orderBy('title', 'asc')->get() as $key => $page )
					<li @if( $key == 0 ) style="border-top: 1px solid rgba(255, 255, 255, 0.1);" @endif><a href="{{ action('Admin\PagesController@edit', ['page' => $page->id ]) }}">{{ $page->title }}</a></li>
					@endforeach;
				</ul>
			</li>
			<li><a><i class="fa fa-newspaper-o"></i> Subscribers <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
					<li><a href="{{ action('Admin\SubscribersController@index') }}">List</a></li>
					<li><a href="{{ action('Admin\SubscribersController@export') }}">Export</a></li>
				</ul>
			</li>
			<li><a><i class="fa fa-sliders"></i> Settings <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
					<li><a href="{{ action('Admin\SettingsController@index') }}">Website Settings</a></li>
					<li><a href="{{ action('Admin\SEOController@index') }}">SEO</a></li>
					<li><a href="{{ action('Admin\NavigationController@index') }}">Navigation</a></li>
					<li><a href="{{ action('Admin\UsersController@index') }}">Users</a></li>
					<li><a href="{{ action('Admin\ActivityController@index') }}">Activity</a></li>
				</ul>
			</li>
		</ul>

		<h3 style="margin-top: 30px;">Properties</h3>
		<ul class="nav side-menu">
			<li><a><i class="fa fa-building-o"></i> Properties <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
					<li><a href="{{ action('Admin\PropertiesController@index') }}">View all</a></li>
					<li><a href="{{ action('Admin\PropertiesController@new') }}">Create new</a></li>
				</ul>
			</li>
			<li><a><i class="fa fa-list" aria-hidden="true"></i> Property Types <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
					<li><a href="{{ action('Admin\PropertyTypesController@index') }}">View all</a></li>
					<li><a href="{{ action('Admin\PropertyTypesController@new') }}">Create new</a></li>
				</ul>
			</li>
			<li><a><i class="fa fa-shopping-cart" aria-hidden="true"></i> Purchase Types <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
					<li><a href="{{ action('Admin\PurchaseTypesController@index') }}">View all</a></li>
					<li><a href="{{ action('Admin\PurchaseTypesController@new') }}">Create new</a></li>
				</ul>
			</li>
			<li><a><i class="fa fa-money" aria-hidden="true"></i> Pricing Types <span class="fa fa-chevron-down"></span></a>
				<ul class="nav child_menu">
					<li><a href="{{ action('Admin\PricingTypesController@index') }}">View all</a></li>
					<li><a href="{{ action('Admin\PricingTypesController@new') }}">Create new</a></li>
				</ul>
			</li>
		</ul>
	</div>

</div>
<!-- /sidebar menu -->