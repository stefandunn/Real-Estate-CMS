@extends('admin.layout')

@section('content')
<div class="container">
	@include('admin.title-bar')
	<div class="x_panel">
		<a href="{{ action('Admin\PagesController@new') }}" class="btn btn-success no-margin pull-right">New Page</a>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="x_panel">
				@if( count( $pages ) == 0 )
				<p class="font-orange">No pages found @if( !empty($_GET['search']) ) using your search criteria @endif</p>
				@else
				<table class="table table-striped table-hover index-table" id="pages">
					<thead>
					    <tr>
							<th>Image</th>
							<th>Title</th>
							<th>Last Updated</th>
							<th class="text-right">Preview Link</th>
						</tr>
				  </thead>
				  <tbody id="pages-list" class="pagination-results" data-limit="{{ $limit }}" data-total="{{ $pages->total() }}">
						@foreach( $pages as $page )
							<tr class="page-item">
								<td>
									<a href="{{ action('Admin\PagesController@edit', [ 'page' => $page->id] ) }}">&nbsp;</a>
									{{ (!is_null($page->featureImage))? $page->featureImage->toTag('thumbnail', ['class' => 'thumbnail-image']) : '' }}
								</td>
								<td>
									<a href="{{ action('Admin\PagesController@edit', [ 'page' => $page->id] ) }}">&nbsp;</a>
									{{ $page->title }}
								</td>
								<td>
									<a href="{{ action('Admin\PagesController@edit', [ 'page' => $page->id] ) }}">&nbsp;</a>
									{!! date('g:ia j\<\s\u\p\>S\<\/\s\u\p\> F Y', strtotime($page->updated_at)) !!}
								</td>
								<td class="text-right">
									<span>
										@if( $page->status === 1 )
											<a href="{{ \URL::to('/') . '/' . $page->slug }}" class="btn btn-info" target="_blank">Visit</a>
										@else
											<a href="{{ \URL::to('/') . '/' . $page->slug . '?preview=true' }}" class="btn btn-info" target="_blank">Preview</a>
										@endif
									</span>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				{{ $pages->links() }}
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
