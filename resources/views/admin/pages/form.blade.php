@extends('admin.layout')

@section('content')

<div class="container">
	@include('admin.title-bar')
	<div class="row">
		<div class="col-md-12">
				<form action="{{ ( $is_new )? action('Admin\PagesController@create') : action('Admin\PagesController@update', [ 'page' => $page->id ]) }}" method="POST" class="form">
					@if( !$is_new )
					{{ method_field('patch') }}
					@endif
					{{ csrf_field() }}

					<div class="row">
						<div class="col-md-9">
							<div class="x_panel">
								<div class="form-group">
									<label for="title">Title</label>
									<input type="text" id="title" name="page[title]" maxlength="255" data-slug-generator="1" class="form-control" required value="{{ old('page.title', $page->title) }}" />
								</div>

								<div class="form-group">
									<label for="slug" class="block">
										<span>Slug</span>
										<div class="table form-control">
											<div style="display: table-column;"></div>
											<div style="display: table-column; width: 100%;"></div>
											<div style="display: table-row; font-weight: normal;">
												<span class="table-cell v-middle nowrap" id="parent-slug"></span>
												<input type="text" id="slug" maxlength="255" name="temp_slug" data-slug-placement="1" class="table-cell v-middle" style="border: none; background: none; -webkit-appearance: none; appearance: none; width: 100%;" required value="{{ old('temp_slug') }}"/>
												<input type="hidden" name="page[slug]" value="{{ old('page.slug', $page->slug) }}" />
											</div>
										</div>
								</div>

								<div class="form-group">
									<label for="content">Content</label>
									<textarea class="wysiwyg form-control" id="content" name="page[content]" rows="20">{{ old('page.content', $page->content) }}</textarea>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="x_panel">
								<div class="form-group">
									<label for="feature-image">Feature Image</label>
									{!! fileSelector('feature-image', 'page[feature_image_id]', old('page.feature_image_id', $page->feature_image_id)) !!}
								</div>
								<div class="form-group">
									<label for="parent">Parent Page</label>
									<select id="parent" name="page[parent_id]" class="form-control">
										<option selected value="">None</option>
										@foreach (\App\Page::where(['parent_id' => null])->where([['id', '!=', $page->id]])->get() as $parent_page)
										<option data-slug="{{ $parent_page->slug }}" value="{{ $parent_page->id }}" @if($parent_page->id == old('page.parent_id', $page->parent_id)) selected @endif>{{ $parent_page->title }}</option>
										@endforeach
									</select>
								</div>
								<div class="form-group">
									<label for="parent">Status</label>
									<select id="parent" name="page[status]" class="form-control">
										<option value="1" @if(1 === old('page.status', $page->status)) selected @endif>Live</option>
										<option value="0" @if(0 === old('page.status', $page->status)) selected @endif>Draft</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="x_panel">
								<input type="submit" value="{{ (!$is_new)? "Save" : "Create" }}" name="submit" class="btn btn-success no-margin" />
								@if( !$is_new )
									<a href="{{ action('Admin\PagesController@delete', [ 'page' => $page->id ]) }}" class="btn btn-danger">Delete</a>
									@if( $page->status === 1 )
										<a href="{{ \URL::to('/') . '/' . $page->slug }}" class="btn btn-info" target="_blank">Visit</a>
									@else
										<a href="{{ \URL::to('/') . '/' . $page->slug . '?preview=true' }}" class="btn btn-info" target="_blank">Preview</a>
									@endif
								@endif
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection