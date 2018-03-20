@extends('admin.layout')

@section('content')
<div class="container">
	@include('admin.title-bar')
	<div class="row">
		<div class="col-md-12">
			<div class="x_panel">
				<p>You can filter your export by using the options below. Simply check the box next to the filter you'd like, and then fill in the corresponding fields. You can specify how some options are filtered, "Exact Match" will ensure that the filter searches for the exact same value provided, whereas "Contains Phrase" searches for matches that contain the value provided.</p>
				<p><small><i>Note: This exports to a CSV format, which can be imported into an application like Microsoft Excel, Libre Office Spreadsheets or Google Spreadsheets.</i></small></p>
			</div>
			<form action="{{ action('Admin\SubscribersController@export') }}" method="GET">
				<div class="x_panel">
					<h4 class="font-teal">Export Options</h4>
					<hr>
					<div class="form-group table filter-option">
						<div class="table-cell v-middle no-wrap padding-sm">
							<div class="fancy-checkbox">
					  			<input type="checkbox" name="" class="checkbox" @if( !empty($export_options['first_name']) ) checked @endif/>
					  			<label title="Enable/Disable this filter" ></label>
					  		</div>
						</div>
						<div class="table-cell v-middle no-wrap padding-sm @if( empty($export_options['first_name']) ) inactive @endif" style="min-width: 110px !important;">
							<label class="no-margin">First name</label>
						</div>
						<div class="table-cell v-middle no-wrap fill-width padding-sm @if( empty($export_options['first_name']) ) inactive @endif">
							<input type="text" value="{{ old( 'export.first_name', @$export_options['first_name']) }}" name="export[first_name]" class="form-control" @if( empty($export_options['first_name']) ) disabled @endif />
						</div>
						<div class="table-cell v-middle no-wrap padding-sm @if( empty($export_options['first_name']) ) inactive @endif">
							<select name="export[first_name_type]" @if( empty($export_options['first_name']) ) disabled @endif class="form-control" style="width: 150px !important">
								<option value="exact" @if( !empty($export_options['first_name_type']) && $export_options['first_name_type'] == 'exact') selected @endif>Exact Match</option>
								<option value="loose" @if( !empty($export_options['first_name_type']) && $export_options['first_name_type'] == 'loose') selected @endif>Contains Phrase</option>
							</select>
						</div>
					</div>
					<div class="form-group table filter-option">
						<div class="table-cell v-middle no-wrap padding-sm">
							<div class="fancy-checkbox">
					  			<input type="checkbox" name="" class="checkbox" @if( !empty($export_options['last_name']) ) checked @endif/>
					  			<label title="Enable/Disable this filter" ></label>
					  		</div>
						</div>
						<div class="table-cell v-middle no-wrap padding-sm @if( empty($export_options['last_name']) ) inactive @endif" style="min-width: 110px !important;">
							<label class="no-margin">Last name</label>
						</div>
						<div class="table-cell v-middle no-wrap fill-width padding-sm @if( empty($export_options['last_name']) ) inactive @endif">
							<input type="text" value="{{ old( 'export.last_name', @$export_options['last_name']) }}" name="export[last_name]" class="form-control" @if( empty($export_options['last_name']) ) disabled @endif />
						</div>
						<div class="table-cell v-middle no-wrap padding-sm @if( empty($export_options['last_name']) ) inactive @endif">
							<select name="export[last_name_type]" @if( empty($export_options['last_name']) ) disabled @endif class="form-control" style="width: 150px !important">
								<option value="exact" @if( !empty($export_options['last_name_type']) && $export_options['last_name_type'] == 'exact') selected @endif>Exact Match</option>
								<option value="loose" @if( !empty($export_options['last_name_type']) && $export_options['last_name_type'] == 'loose') selected @endif>Contains Phrase</option>
							</select>
						</div>
					</div>
					<div class="form-group table filter-option">
						<div class="table-cell v-middle no-wrap padding-sm">
							<div class="fancy-checkbox">
					  			<input type="checkbox" name="" class="checkbox" @if( !empty($export_options['email']) ) checked @endif/>
					  			<label title="Enable/Disable this filter" ></label>
					  		</div>
						</div>
						<div class="table-cell v-middle no-wrap padding-sm @if( empty($export_options['email']) ) inactive @endif" style="min-width: 110px !important;">
							<label class="no-margin">Email</label>
						</div>
						<div class="table-cell v-middle no-wrap fill-width padding-sm @if( empty($export_options['email']) ) inactive @endif">
							<input type="text" value="{{ old( 'export.email', @$export_options['email']) }}" name="export[email]" class="form-control" @if( empty($export_options['email']) ) disabled @endif />
						</div>
						<div class="table-cell v-middle no-wrap padding-sm @if( empty($export_options['email']) ) inactive @endif">
							<select name="export[email_type]" @if( empty($export_options['email']) ) disabled @endif class="form-control" style="width: 150px !important">
								<option value="exact" @if( !empty($export_options['email_type']) && $export_options['email_type'] == 'exact') selected @endif>Exact Match</option>
								<option value="loose" @if( !empty($export_options['email_type']) && $export_options['email_type'] == 'loose') selected @endif>Contains Phrase</option>
							</select>
						</div>
					</div>
					<div>
						<hr>
						<h5 class="font-orange">Subscribe Date Options</h5>
						<p>You can filter by when users subscribed, either from a start date or up to an end date or between dates (both start and end date will need to be specified).</p>
					</div>
					<div class="row" style="margin-left: -15px !important; margin-right: -15px !important;">
						<div class="col-md-6">
							<div class="form-group table filter-option">
								<div class="table-cell v-middle no-wrap padding-sm">
									<div class="fancy-checkbox">
							  			<input type="checkbox" name="" class="checkbox" @if( !empty($export_options['from_date']) ) checked @endif/>
							  			<label title="Enable/Disable this filter" ></label>
							  		</div>
								</div>
								<div class="table-cell v-middle no-wrap padding-sm  @if( empty($export_options['from_date']) ) inactive @endif ">
									<label class="no-margin">From Date</label>
								</div>
								<div class="table-cell v-middle no-wrap fill-width padding-sm  @if( empty($export_options['from_date']) ) inactive @endif ">
									<input type="text" value="{{ old( 'export.from_date', @$export_options['from_date']) }}" name="export[from_date]" class="form-control datepicker" @if( empty($export_options['from_date']) ) disabled @endif placeholder="yyyy-mm-dd" />
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group table filter-option">
								<div class="table-cell v-middle no-wrap padding-sm">
									<div class="fancy-checkbox">
							  			<input type="checkbox" name="" class="checkbox" @if( !empty($export_options['to_date']) ) checked @endif/>
							  			<label title="Enable/Disable this filter" ></label>
							  		</div>
								</div>
								<div class="table-cell v-middle no-wrap padding-sm  @if( empty($export_options['to_date']) ) inactive @endif ">
									<label class="no-margin">To Date</label>
								</div>
								<div class="table-cell v-middle no-wrap fill-width padding-sm  @if( empty($export_options['to_date']) ) inactive @endif ">
									<input type="text" value="{{ old( 'export.to_date', @$export_options['to_date']) }}" name="export[to_date]" class="form-control datepicker" @if( empty($export_options['to_date']) ) disabled @endif placeholder="yyyy-mm-dd" />
								</div>
							</div>
						</div>
					</div>
				</div>
				@if( !is_null($preview_results) )
				<div class="x_panel">
					<h4 class="font-teal">Preview Results</h4>
					@if( count($preview_results) == 0 )
						<p>No subscribers could be found using your criteria, try changing your criteria.</p>
					@else
						<div style="max-height: 600px; overflow: auto;">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>#</th>
										<th>First Name</th>
										<th>Last Name</th>
										<th>Email</th>
										<th>Subscribed Date</th>
									</tr>
								</thead>
								<tbody>
							@foreach ($preview_results as $subscriber)
									<tr>
										<td>{{ $subscriber->id }}</td>
										<td>{{ $subscriber->first_name }}</td>
										<td>{{ $subscriber->last_name }}</td>
										<td>{{ $subscriber->email }}</td>
										<td>{!! date('g:ia j\<\s\u\p\>S\<\/\s\u\p\> F Y', strtotime($subscriber->created_at)) !!}</td>
									</tr>
							@endforeach
								</tbody>
							</table>
						</div>
					@endif
				</div>
				@endif
				<div class="x_panel">
					<input type="submit" name="export_function" value="Preview Results" class="btn btn-info" @if(empty(\Request::query('export_function')) || \Request::query('export_function') != 'Preview Results') style="display: none;" @endif />
					<input type="submit" name="export_function" value="Export All" class="btn btn-success" />
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
