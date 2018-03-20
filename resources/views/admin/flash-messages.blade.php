@if(!empty(getFlashMessages( isset($errors)? $errors : null )))
<div id="flash-messages-wrapper" class="modal-wrapper">
	<div id="flash-messages" class="modal">
		<span class="fa fa-times close-btn"></span>
		<h3>@if(count(getFlashMessages())>1)
			Messages
			@else
			Message
			@endif
		</h3>
		@foreach(getFlashMessages() as $key => $message)
			<div class="{{ $key }} message">{{ $message }}</div>
		@endforeach
	</div>
	<div id="flash-messages-bg" class="modal-bg"></div>
</div>
@endif