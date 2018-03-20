@extends('admin.layout')

@section('content')
<div class="container">
	@include('admin.title-bar')
	<div class="x_panel">
		<a href="{{ action('Admin\SEOController@new') }}" class="btn btn-success no-margin pull-right">New Data</a>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="x_panel">
				@if( $seo_data->count() == 0 )
					<p>No SEO Data found @if( !empty($_GET['search']) ) using your search criteria @endif</p>
				@else
					<table class="table table-striped table-hover index-table" id="seo-data">
						<thead>
						    <tr>
								<th>#</th>
								<th>URL</th>
								<th>Last Updated</th>
								<th>Visit URL</th>
							</tr>
					  </thead>
					  <tbody id="seo-data-list" class="pagination-results" data-limit="{{ $limit }}" data-total="{{ $seo_data->total() }}">
							@foreach( $seo_data as $data )
								<tr class="data-item">
									<td>
										<a href="{{ action('Admin\SEOController@edit', [ 'data' => $data->id] ) }}">&nbsp;</a>
										{{ $data->id }}
									</td>
									<td>
										<a href="{{ action('Admin\SEOController@edit', [ 'data' => $data->id] ) }}">&nbsp;</a>
										{{ ( substr($data->url, 0, 1) == '/')? $data->url : '/' . $data->url }}
									</td>
									<td>
										<a href="{{ action('Admin\SEOController@edit', [ 'data' => $data->id] ) }}">&nbsp;</a>
										{!! date('g:ia j\<\s\u\p\>S\<\/\s\u\p\> F Y', strtotime($data->updated_at)) !!}
									</td>
									<td>
										<span>
											<a href="{{ \URL::to('/') . '/' . ltrim($data->url, '/') }}" target="_blank" class="btn btn-info">Go to page</a>
										</span>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{{ $seo_data->links() }}
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
