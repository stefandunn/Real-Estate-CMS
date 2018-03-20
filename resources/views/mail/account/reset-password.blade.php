@extends('mail.layout')

@section('content')

	<h2>Password reset</h2>
	<p>You have requested that you would like to reset your password. To do so, click on the link below or copy and paste it into your favourite browser.</p>
	<a class="code-style" href="{{ $reset_url }}" target="_blank">{{ $reset_url }}</a>

@endsection