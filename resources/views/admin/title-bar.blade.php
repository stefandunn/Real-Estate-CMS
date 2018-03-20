<div class="row">
	<div class="col-md-12">
		<div class="page-title">
			<div class="title_left" @if( isset( $hide_search ) ) style='width: 100%' @endif>
				<h3 id="page-title">{!! (isset($page_title))? $page_title : 'Missing Title' !!}</h3>
			</div>
			@if( !isset( $hide_search ) )
			<div class="title_right">
				<form class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search" method="GET">
					@foreach (array_diff_key($_GET, ['search' => '']) as $name => $value )
						<input type="hidden" name="{{ $name }}" value="{{ $value }}" />
					@endforeach
					<div class="input-group">
						<input type="text" class="form-control" name="search" placeholder="Search for..." value="{{ \Request::get('search') }}" />
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit"><span class="fa fa-search"></span></button>
						</span>
					</div>
				</form>
			</div>
			@endif
		</div>
	</div>
</div>