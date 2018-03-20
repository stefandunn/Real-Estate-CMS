@extends('admin.layout')

@section('content')

<div class="container">
	@include('admin.title-bar')
	<div class="row">
		<div class="col-md-12">
				<form action="{{ ( $is_new )? action('Admin\SEOController@create') : action('Admin\SEOController@update', [ 'seo_data' => $seo_data->id ]) }}" method="POST" class="form">
					@if( !$is_new )
					{{ method_field('patch') }}
					@endif
					{{ csrf_field() }}

					<div class="row">
						<div class="col-md-5">
							<div class="x_panel">
								<h4 class="font-blue">General SEO Data</h4>

								<div class="form-group">
									<label for="url">URL</label>
									<input type="text" name="seo_data[url]" id="url" class="form-control" maxlength="255" value="{{ old('seo_data.url', (empty($seo_data->url))? \Request::query('url') : $seo_data->url) }}" />
								</div>

								<div class="form-group">
									<label for="title">Title</label>
									<input type="text" name="seo_data[title]" id="title" class="form-control" maxlength="255" value="{{ old('seo_data.title', (empty($seo_data->title))? getSetting('title') : $seo_data->title) }}" />
								</div>

								<div class="form-group">
									<label for="description">Meta description</label>
									<textarea name="seo_data[description]" id="description" class="form-control" maxlength="255">{{ old('seo_data.description', $seo_data->description) }}</textarea>
								</div>

								<div class="form-group">
									<label for="index">Search engine indexing</label>
									<select class="form-control" name="seo_data[no_index]" id="index">
										<option value="0" @if( old('seo_data.no_index', $seo_data->no_index) == 0 ) selected @endif>Normal</option>
										<option value="1" @if( old('seo_data.no_index', $seo_data->no_index) == 1 ) selected @endif>Do not index</option>
									</select>
								</div>

								<div class="form-group">
									<label for="tracking-code">Additional Tracking Code</label>
									<textarea name="seo_data[tracking_code]" id="tracking-code-textarea" class="code form-control" rows="10">{{ old('seo_data.tracking_code', $seo_data->tracking_code) }}</textarea>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="x_panel">
								<h4 class="font-blue">Open Graph Data <a href="http://ogp.me/" target="_blank" title="More details can be found here."><i class="fa fa-external-link" aria-hidden="true"></i></a></h4>

								<div class="form-group">
									<label for="og_url">Open Graph URL</label>
									<input type="text" name="seo_data[og_url]" id="og_url" class="form-control" maxlength="255" value="{{ old('seo_data.og_url', (empty($seo_data->og_url))? URL::to('/') . '/'. ltrim(\Request::query('url'), '/') : $seo_data->og_url) }}" />
								</div>

								<div class="form-group">
									<label for="og-title">Open Graph Title</label>
									<input type="text" name="seo_data[og_title]" id="og-title" class="form-control" maxlength="255" value="{{ old('seo_data.og_title', $seo_data->og_title) }}" />
								</div>

								<div class="form-group">
									<label for="og-description">Open Graph Description</label>
									<textarea name="seo_data[og_description]" id="og-description" class="form-control" maxlength="255">{{ old('seo_data.og_description', $seo_data->og_description) }}</textarea>
								</div>

								<div class="form-group">
									<label for="og-type">Open Graph Type</label>
									<select class="form-control" name="seo_data[og_type]" id="og-type">
										<option value="article" @if( old('seo_data.og_type', $seo_data->og_type) == "article" ) selected @endif>Web Page / Article</option>
										<option value="place" @if( old('seo_data.og_type', $seo_data->og_type) == "place" ) selected @endif>Property</option>
									</select>
								</div>

								<div class="form-group">
									<label for="og-image">Open Graph Title</label>
									{!! fileSelector('og-image', 'seo_data[og_image_id]', old('seo_data.og_image_id', $seo_data->og_image_id)) !!}
								</div>

								<div class="form-group">
									<label for="og-site-name">Open Graph Site Name</label>
									<input type="text" name="seo_data[og_site_name]" id="og-site-name" class="form-control" maxlength="255" value="{{ old('seo_data.og_site_name', (empty($seo_data->og_site_name))? 'Paul Simon Seaton' : $seo_data->og_site_name ) }}" />
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="x_panel">
								<h4 class="font-blue">Twitter Card Data <a href="https://dev.twitter.com/cards/getting-started" target="_blank" title="More details can be found here."><i class="fa fa-external-link" aria-hidden="true"></i></a></h4>

								<div class="form-group">
									<label for="twitter-site-name">Twitter Site Name (@username)</label>
									<input type="text" name="seo_data[twitter_site]" id="twitter-site-name" class="form-control" maxlength="255" value="{{ old('seo_data.twitter_site', (empty($seo_data->twitter_site))? '@PSSCommercial' : $seo_data->twitter_site ) }}" />
								</div>

								<div class="form-group">
									<label for="twitter-creator">Twitter Creator (@username)</label>
									<input type="text" name="seo_data[twitter_creator]" id="twitter-creator" class="form-control" maxlength="255" value="{{ old('seo_data.twitter_creator', (empty($seo_data->twitter_creator))? '@PSSCommercial' : $seo_data->twitter_creator ) }}" />
								</div>

								<div class="form-group">
									<label for="og-twitter_image_id">Twitter Card Image</label>
									{!! fileSelector('og-image', 'seo_data[twitter_image_id]', old('seo_data.twitter_image_id', $seo_data->twitter_image_id)) !!}
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="x_panel">
								<input type="submit" value="{{ (!$is_new)? "Save" : "Create" }}" name="submit" class="btn btn-success no-margin" />
								@if( !$is_new )
								<a href="{{ action('Admin\SEOController@delete', [ 'seo_data' => $seo_data->id ]) }}" class="btn btn-danger">Delete</a>
								@endif
								<a href="{{ ( getPageDetailsByURL('status', 1, $seo_data->url) === 1 )? \URL::to('/') . '/' . ltrim($seo_data->url, '/') : \URL::to('/') . '/' . ltrim($seo_data->url, '/') . '?preview=true' }}" target="_blank" class="btn btn-info">Go to page</a>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection