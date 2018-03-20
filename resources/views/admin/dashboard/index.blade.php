@extends('admin.layout')

@section('content')

<div class="container">
	@include('admin.title-bar')
	<div class="row">
		<div class="col-md-12">
			<div class="x_panel">
				<p>Welcome to the dashboard for Paul Simon Seaton website. You can mange the website's content from this backend system. To explore, use the navigation menu on the left hand side of the screen.</p>
				<p>You can modify your profile by clicking on your profile name at the top of the screen and clicking, "account settings".</p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-7">
			<div class="panel">
				<div class="panel-heading green">
					<h3 class="panel-title">Statistics</h3>
				</div>
				<div class="panel-body">
					<div class="row tile_count">
						<div class="col-md-3 col-xs-6 tile_stats_count text-center">
							<a href="{{ action('Admin\PropertiesController@index') }}" title="{{ 'Total properties: ' . $properties }}">
								<span class="count_top"><i class="fa fa-building-o"></i> Properties</span>
								<div class="count green">{{ $properties }}</div>
							</a>
						</div>
						<div class="col-md-3 col-xs-6 tile_stats_count text-center">
							<a href="{{ action('Admin\MediaController@index') }}" title="{{ 'Total media files: ' . $media_files }}">
								<span class="count_top"><i class="fa fa-file-image-o"></i> Media Files</span>
								<div class="count green">{{ $media_files }}</div>
							</a>
						</div>
						<div class="col-md-3 col-xs-6 tile_stats_count text-center">
							<a href="#" title="{{ 'Total document downloads: ' . $downloads }}">
								<span class="count_top"><i class="fa fa-download"></i> Downloads</span>
								<div class="count green">{{ $downloads }}</div>
							</a>
						</div>
						<div class="col-md-3 col-xs-6 tile_stats_count text-center">
							<a href="{{ action('Admin\SubscribersController@index') }}" title="{{ 'Total Newsletter Subscribers: ' . $subscribers }}">
								<span class="count_top"><i class="fa fa-download"></i> Subscribers</span>
								<div class="count green">{{ $subscribers }}</div>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="panel">
				<div class="panel-heading red">
					<h3 class="panel-title">Quick Actions</h3>
				</div>
				<div class="panel-body">
					<ul class="reset-list">
						<li class="inline-block col-small-padding col-md-6 col-xs-6" style="margin-bottom: 10px;">
							<a class="block">
								<a href="{{ action('Admin\PropertiesController@index') }}" style="display: block; overflow: hidden; text-overflow: ellipsis;" class="btn btn-default"><i class="fa fa-building-o"></i> New Property</a>
							</a>
						</li>
						<li class="inline-block col-small-padding col-md-6 col-xs-6" style="margin-bottom: 10px;">
							<a class="block">
								<a href="{{ action('Admin\MediaController@index') }}" style="display: block; overflow: hidden; text-overflow: ellipsis;" class="btn btn-default"><i class="fa fa-picture-o"></i> New Media File</a>
							</a>
						</li>
						<li class="inline-block col-small-padding col-md-6 col-xs-6">
							<a class="block">
								<a href="{{ action('Admin\SettingsController@index') }}" style="display: block; overflow: hidden; text-overflow: ellipsis;" class="btn btn-default"><i class="fa fa-cog"></i> Adjust Settings</a>
							</a>
						</li>
						<li class="inline-block col-small-padding col-md-6 col-xs-6">
							<a class="block">
								<a href="{{ action('Admin\SubscribersController@index') }}" style="display: block; overflow: hidden; text-overflow: ellipsis;" class="btn btn-default"><i class="fa fa-users"></i> View Subscribers</h4>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-5">
			<div class="panel">
				<div class="panel-heading dark">
					<h3 class="panel-title">
						<a href="{{ action('Admin\ActivityController@index') }}">Recent Activity</a>
					</h3>
				</div>
				<div class="panel-body">
					<div class="x_content">
						<div class="dashboard-widget-content">
							@if( $activity_logs->count() > 0 )
								<ul class="list-unstyled timeline widget">
									@foreach( $activity_logs as $activity_log )
									<li>
										<div class="block">
											<div class="block_content">
												<h4 class="title">
													{{ ucwords($activity_log->action) . ": " . $activity_log->model }}
												</h3>
												<div class="byline">
													<span>{{ $activity_log->created_at }}</span>@if(isset($activity_log->user->name)) by <a>{{ $activity_log->user->name }}</a> @endif
												</div>
												@if( !preg_match("/((?:log(?:in|out))|(?:reset\spassword)|(?:password\sreset))/", $activity_log->action) )
													<div class="row">
														<div class="col-md-8 col-sm-12">
															<p class="excerpt">
														{{ ( count( $activity_log->changesToArray() ) > 1 || count( $activity_log->changesToArray() ) == 0 )? count( $activity_log->changesToArray() ) . " changes were made" : count( $activity_log->changesToArray() ) . " change was made" }}
															</p>
														</div>
														<div class="col-md-4 col-sm-12">
															<span class="btn btn-default btn-sm pull-right" onclick="showModal('Original Data', $(this).next('table').clone().show(), {'max-width' : 900, 'overflow' : 'auto', 'max-height': 700});">See changes</span>
				                                            <table class="table bg-white" style="display: none; margin-bottom: 0;">
				                                                <thead>
				                                                    <tr>
				                                                        <th class="font-blue">Attribute</th>
				                                                        <th class="font-blue">Value</th>
				                                                    </tr>
				                                                </thead>
				                                                <tbody>
				                                                @foreach( $activity_log->changesToArray() as $attr => $value )
				                                                    <tr>
				                                                        <td>{{ $attr }}</td>
				                                                        <td>{!! $value !!}</td>
				                                                    </tr>
				                                                @endforeach
				                                                </tbody>
				                                            </table>
														</div>
													</div>
												@endif
											</div>
										</div>
									</li>
									@endforeach
								</ul>
								<a href="{{ action('Admin\ActivityController@index') }}" class="btn btn-default pull-right">View all</a>
							@else
							<p style="text-align: center;">There's been no <b>"cativity"</b> recently, so kitty's getting bored..
							<div style="margin: 10px -21px 0 -21px; text-align: center;">
								<img src="{{ URL::to('/') . '/images/admin/no-cativity.gif' }}" style="width: 100%; height: auto;" alt=""><br/>
								<small style="font-size: 0.7rem; color: #ddd">Credit: <a href="https://dribbble.com/shots/2415798-Kitty-Wiggle-Pt-2" target="_blank" style="color: #ddd">https://dribbble.com/shots/2415798-Kitty-Wiggle-Pt-2</a></small>
							</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection