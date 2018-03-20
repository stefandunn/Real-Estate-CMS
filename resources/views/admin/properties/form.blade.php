@extends('admin.layout')

@section('content')

<div class="container">
	@include('admin.title-bar')
	<div class="row">
		<div class="col-md-12">
			<form action="{{ ( $is_new )? action('Admin\PropertiesController@create') : action('Admin\PropertiesController@update', [ 'property' => $property->id ]) }}" method="POST" class="form">
				@if( !$is_new )
				{{ method_field('patch') }}
				@endif
				{{ csrf_field() }}

				<div class="row">
					<div class="col-md-9">
						<div class="x_panel x_panel_no_row_effect">
							<div class="row">
								<div class="col-md-9">
									<div class="form-group">
										<label for="name">Name</label>
										<input id="name" class="form-control" type="text" name="property[name]" value="{{ old('property.name', $property->name ) }}" required />
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for="reference-code">Reference Code</label>
										<input id="reference-code" class="form-control" type="text" name="property[reference_code]" value="{{ old('property.reference_code', $property->reference_code) }}" style="text-transform: uppercase;" maxlength="6" />
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="description">Full Description</label>
										<textarea id="description" class="form-control wysiwyg" name="property[description]" rows="10">{{ old('property.description', $property->description) }}</textarea>
									</div>
									<div class="form-group">
										<label for="overview">Overview</label>
										<textarea id="overview" class="form-control wysiwyg" name="property[overview]" rows="6">{{ old('property.overview', $property->overview) }}</textarea>
									</div>
									<div class="form-group">
										<label for="snippet">Snippet</label>
										<textarea id="snippet" class="form-control wysiwyg" name="property[snippet]">{{ old('property.snippet', $property->snippet) }}</textarea>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="address-1">Address Line 1</label>
												<input type="text" class="form-control" name="property[address_line_1]" id="address-1" value="{{ old('property.address_line_1', $property->address_line_1) }}" />
											</div>
											<div class="form-group">
												<label for="address-2">Address Line 2</label>
												<input type="text" class="form-control" name="property[address_line_2]" id="address-2" value="{{ old('property.address_line_2', $property->address_line_2) }}" />
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="town">Town/City</label>
												<input type="text" class="form-control" name="property[town]" id="town" value="{{ old('property.town', $property->town) }}" />
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="postcode">Postcode</label>
												<input type="text" class="form-control" name="property[postcode]" id="postcode" value="{{ old('property.postcode', $property->postcode) }}" />
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="short_address">Short Address</label>
												<textarea class="form-control" name="property[short_address]" id="short_address">{{ old('property.short_address', $property->short_address) }}</textarea>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="latitude">Latitude</label>
												<input type="text" class="form-control" name="property[latitude]" id="latitude" value="{{ old('property.latitude', $property->latitude) }}" />
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="longitude">Longitude</label>
												<input type="text" class="form-control" name="property[longitude]" id="longitude" value="{{ old('property.longitude', $property->longitude) }}" />
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Square Footage (Ft<sup>2</sup>)</label>
												<input type="text" class="form-control" name="property[square_footage]" value="{{ old('property.square_footage', $property->square_footage) }}" />
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<label for='property-type'>Property type</label>
										<select id="property-type" name="property[property_type_id]" class="form-control">
											<option value="" disabled @if( empty( $property->property_type_id ) ) selected @endif>Select form list</option>
											@foreach (\App\PropertyType::orderBy( 'name', 'ASC' )->get() as $type )
											<option value='{{ $type->id }}' @if( $type->id == old('property.property_type_id', $property->property_type_id) ) selected @endif>{{ $type->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for='purchase-type'>Purchase type</label>
										<select id="purchase-type" name="property[purchase_type_id]" class="form-control">
											<option value="" disabled @if( empty( $property->purchase_type_id ) ) selected @endif>Select form list</option>
											@foreach (\App\PurchaseType::orderBy( 'name', 'ASC' )->get() as $type )
											<option value='{{ $type->id }}' @if( $type->id == old('property.purchase_type_id', $property->purchase_type_id) ) selected @endif>{{ $type->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for='price'>Price (in £)</label>
										<input id="price" type="text" name="property[price]" maxlength="11" value="{{ old('property.price', $property->price) }}" class="form-control"/>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label for='pricing-type'>Pricing Type</label>
										<select id="pricing-type" name="property[pricing_type_id]" class="form-control">
											<option value="" disabled @if( empty( $property->pricing_type_id ) ) selected @endif>Select form list</option>
											@foreach (\App\PricingType::orderBy( 'name', 'ASC' )->get() as $type )
											<option value='{{ $type->id }}' @if( $type->id == old('property.pricing_type_id', $property->pricing_type_id) ) selected @endif>{{ $type->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="x_panel">
							<div class="form-group">
								<label for="feature-image">Feature Image</label>
								{!! fileSelector('feature-image', 'property[feature_image_id]', old('property.feature_image_id', $property->feature_image_id)) !!}
							</div>
							<div class="form-group">
								<label for='contact-email'>Contact Email</label>
								<input id="contact-email" type="email" name="property[contact_email]" class="form-control" value="{{ old('property.contact_email', $property->contact_email) }}" />
							</div>
							<div class="form-group">
								<label for='contact-phone'>Contact Phone Number</label>
								<input id="contact-phone" type="text" name="property[contact_number]" class="form-control" value="{{ old('property.contact_number', $property->contact_number) }}" />
							</div>
							<div class="form-group">
								<label>Tags (Seperate with commas)</label>
								<input id="tags" type="text" name="property[tags]" value="{{ old('property.tags', $property->tags) }}" class="form-control" data-role="tagsinput" />
								<small>You can remove the last tag by pressing the backspace key, or remove other tags by clicking on the tag's cross.</small>
							</div>
							@if( !$is_new )
							<div class="form-group">
								<a href="{{ action('Admin\PropertiesController@files', ['property' => $property->id]) }}" class="btn btn-info">Property Files ({{ $property->files->count() }})</a>
							</div>
							@endif
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="x_panel">
							<input type="submit" value="{{ (!$is_new)? "Save" : "Create" }}" name="submit" class="btn btn-success no-margin" />
							@if( !$is_new )
							<a href="{{ action('Admin\PropertiesController@delete', [ 'property' => $property->id ]) }}" class="btn btn-danger">Delete</a>
							<a href="{{ action('Admin\PropertiesController@generateReport', [ 'property' => $property->id ]) }}" class="btn btn-info">Generate Report</a>
							@endif
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection