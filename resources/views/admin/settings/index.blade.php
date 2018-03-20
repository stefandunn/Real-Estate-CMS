@extends('admin.layout')

@section('content')
<div class="container">
	@include('admin.title-bar')
	<div class="row">
		<div class="col-md-12">
			<form action="{{ action('Admin\SettingsController@update') }}" method="POST" class="form">
				{{ csrf_field() }}
				<div class='row'>
					<div class="col-md-9">
						<div class='x_panel'>
							<div class="form-group">
								<label for="website-title">Site Title</label>
								<input id="website-title" type="text" name="settings[title]" maxlength="255" class="form-control" value="{{ old('settings.title', \App\Settings::getValue('title', 'Paul Simon Seaton Estate Agents')) }}" />
							</div>
							<div class="form-group">
								<label for='facebook-link'>Facebook URL</label>
								<input id='facebook-link' type="text" name="settings[facebook_link]" value="{{ old('settings.facebook_link', \App\Settings::getValue('facebook_link')) }}" class="form-control" />
							</div>
							<div class="form-group">
								<label for='twitter-link'>Twitter URL</label>
								<input id='twitter-link' type="text" name="settings[twitter_link]" value="{{ old('settings.twitter_link', \App\Settings::getValue('twitter_link')) }}" class="form-control" />
							</div>
							<div class="form-group">
								<label for='contact-email'>Contact Email-address</label>
								<input id='contact-email' type="email" name="settings[main_contact_email]" value="{{ old('settings.main_contact_email', \App\Settings::getValue('main_contact_email')) }}" class="form-control" />
							</div>
							<div class="form-group">
								<label for='contact-number'>Contact Phone</label>
								<input id='contact-number' type="text" name="settings[main_contact_number]" value="{{ old('settings.main_contact_number', \App\Settings::getValue('main_contact_number')) }}" class="form-control" />
							</div>
							<div class="form-group">
								<label for='ga-code'>Google Analytics Code</label>
								<textarea id="ga-code" name="settings[ga_code]" class="code form-control" placeholder="Copy and paste your GA tacking code here">{{ old('settings.ga_code', \App\Settings::getValue('ga_code')) }}</textarea>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class='x_panel'>
							<div class="form-group">
								<label>Logo Image</label>
								{!! fileSelector( 'logo', 'settings[logo_id]', old('settings.logo_id', \App\Settings::getValue('logo_id')) ) !!}
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="x_panel">
							<input type="submit" name="submit" value="Save settings" class="btn btn-success" />
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
