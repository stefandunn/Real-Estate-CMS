$(document).ready(function(){

	// Error flag
	var error_occurred = false;

	// Find the list of items
	var media_files = $("#media-file-list");

	var total_files = media_files.attr('data-total');
	var page_limit = media_files.attr('data-limit');

	var pagination_button = $("ul.pagination li a:first").parent()
	var pagination_wrapper = $( "#pagination-wrapper" );

	var modal_mode = $("#library-container").hasClass('modal-mode');
	var public_folder_uri = $("#library-container").attr('data-image-uri');
	var initiator = $("#library-container").attr('data-initiator');

	// Get a template
	var media_file_template = media_files.find(".media-file-item-template").remove().clone().removeClass('media-file-item-template').addClass('media-file-item').hide();

	// Initiate the dropzone
	$.getScript( "https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/min/dropzone.min.js", function(){

		var dropzone_options = {
			paramName: "file", // The name that will be used to transfer the file
			maxFilesize: $( "form#upload-area" ).attr('data-max-size'), // MB
			createImageThumbnails: true,
			maxFiles: 4,
			dictDefaultMessage: 'Drop / select your files here to upload them (maximum 4 files).',
			paramName: 'uploaded_file',
			init: function(){
				this.on( "error", function( File, errorMessage ){
					error_occurred = true;
					if( errorMessage.uploaded_file !== undefined )
						$( "#upload-error-alert" ).show().find("p").text(errorMessage.uploaded_file).delay(10000).fadeOut();
					else
						$( "#upload-error-alert" ).show().find("p").text(errorMessage).delay(10000).fadeOut();
					this.removeAllFiles();
				});
				this.on( "success", function( File, data ){
					var new_media = media_file_template.clone();
					new_media.find("img").attr('src', data.thumbnail_url).attr('alt', data.media_file.alt).addClass('thumbnail-image');
					new_media.find("a").attr('href', data.edit_url);
					new_media.find(".media-file-title > a").after(data.media_file.title);
					new_media.find(".media-file-caption > a").after(data.media_file.caption);
					new_media.attr('data-desktop-size', data.media_file.desktop_path);
					new_media.attr('data-tablet-size', data.media_file.tablet_path);
					new_media.attr('data-mobile-size', data.media_file.mobile_path);
					new_media.attr('data-original-size', data.media_file.path);
					new_media.attr('data-forced-thumbnail-url', data.media_file.thumbnail_path);
					new_media.attr('data-title', data.media_file.title);
					new_media.attr('data-id', data.media_file.id);
					new_media.attr('data-alt', '');

					// If list is longer than the pagination limit, remove the end one and append new one
					if( $("#media-file-list").find( ".media-file-item" ).length > page_limit-1 )
						$("#media-file-list").find( ".media-file-item:last" ).remove();
					
					// Prepend new one
					media_files.prepend( new_media.show() );

					// If in grid view, set to show as inline-block not block;
					if( new_media.hasClass('col-md-3') ) new_media.css( 'display', 'inline-block' );

					// Add one to total files
					total_files++;

					// If need new page on paginator, add it
					var new_no_pages = Math.ceil( total_files/page_limit);
					if( $( "ul.pagination" ).length > 0 && new_no_pages > ($( "ul.pagination li" ).length - 2) )
					{
						// Clone an existing li
						var new_page = pagination_button.clone();
						// Change URL and text of li element
						new_page.find("a").attr('href', new_page.find("a").attr('href').replace(/([^?]+\?page\=)(\d)/, '$1' + new_no_pages ) ).text(new_no_pages);
						// Add new page
						$("ul.pagination li").eq($("ul.pagination li").length-2).after(new_page);
					}
					else
					{
						// No paginator, but we need one
						if( $( "ul.pagination" ).length == 0 && new_no_pages > 1 )
						{
							var pagination_template = $( "<ul class=\"pagination\"><li class=\"disabled\"><span>«</span></li><li class=\"active\"><span>1</span></li><li><a href=\"http://pss.dev/dashboard/media?view=" + pagination_wrapper.attr('data-view') + "&amp;page=2\">2</a></li><li><a href=\"http://pss.dev/dashboard/media?view=" + pagination_wrapper.attr('data-view') + "&amp;page=2\" rel=\"next\">»</a></li></ul>" );
							pagination_wrapper.append(pagination_template);
						}
					}

				});
				this.on( "queuecomplete", function(){
					if( !error_occurred )
					{
						$("#upload-complete-alert").fadeIn().delay(10000).fadeOut();
						this.removeAllFiles();
					}
				});

				this.on( "sendingmultiple", function(){
					error_occurred = false;
					$("#upload-complete-alert, #upload-error-alert").fadeOut();
				})
			},
		};

		// Settings for the dropzone
		Dropzone.options.uploadArea = dropzone_options;

		// Initiate if from modal
		if( !$("#upload-area").hasClass("dz-clickable") )
			$("#upload-area").dropzone(dropzone_options);
	});

	/**
	* Handler bulk-action methods
	*/
	var bulk_action_panel = $("#bulk-actions");
	$(document).on("click", ".media-file-item .fancy-checkbox", function(){
		
		// Count the number of selected checkboxes
		var checkbox_count = $(".media-file-item .fancy-checkbox input:checked").length;

		if( checkbox_count > 0)
			bulk_action_panel.slideDown();
		else
			bulk_action_panel.slideUp();


		// On bulk action "Go" button pressed
		bulk_action_panel.on("click", "#perform-action", function(){

			// If not proccess, then process
			if( $(this).text() == 'Go')
			{
				$(this).text("Processing...");

				var checked_checkboxes = $(".media-file-item .fancy-checkbox input:checked");

				if( checked_checkboxes.length == 0 )
					showModal( "Bulk Action", "No media files were selected");
				else
				{
					// Setup array of media file IDs
					var media_file_ids = [];

					// Loop through the checked ones and get their ID
					checked_checkboxes.each(function(){
						media_file_ids.push( $(this).parents(".media-file-item").attr('data-id') );
					});

					// Get option selected (the action)
					var option_selected = $(this).prev("select").find("option:selected");
					var csrf_token = option_selected.parent().attr('data-token');

					// Make post request
					$.post( option_selected.attr('data-action-url'), {
						'_token': csrf_token,
						'medias_file_ids': JSON.stringify(media_file_ids),
					}, function(response){

						// Reset action button
						$("#perform-action").text("Go");

						// If deleted, remove files that were deleted
						if( option_selected.attr('id') == 'delete' )
						{
							// Loop through the items deleted
							response.completed_deletes.forEach(function(id){
								$(".media-file-item[data-id='" + id + "']").slideUp('fast', function(){
									$(this).remove();
								});
							});
						}

						// If success
						if( response.success !== undefined ) {

							if( option_selected.attr('id') != 'delete' ) {
								// Show success modal
								var modal = showModal("Success", "Successfully ran action, reloading page...").find(".close-btn").hide();

								// Reload page after 3 seconds
								setTimeout(function(){
									location.reload();
								}, 3000);
							}
						}

						// If failed
						if( response.error !== undefined )
						{
							// Show modal for failure
							showModal("Something went wrong!", response.messages);
						}

					}, 'json');
				}
			}

		});
	})

	/**
	* Handle modal mode
	*/
	if( modal_mode ) {

		// Get list of media items
		var media_file_items = media_files.find(".media-file-item");

		// When clicked on
		$(document).on("click", ".media-file-item", function(e){

			e.preventDefault();

			// Get item in question
			var this_item = $(this);

			// If the initiator is tinymce, continue to select size
			if( initiator == 'tinymce' )
			{
				// Create a new modal instance
				var modal_template = $("<div class='modal-wrapper'><div class='modal'><h3>Title</h3><span class='fa fa-times close-btn'></span></div><div class='modal-bg'></div></div>");

				// Adjust modal appearance
				modal_template.find("h3").text("Which size?").after("<span class='option size-desktop'>Desktop</span><span class='option size-tablet'>Tablet</span><span class='option size-mobile'>Mobile</span><span class='option size-thumbnail'>Thumbnail</span><span class='option size-original'>Original</span>");

				// Remove unavailable sizes
				if( this_item.attr('data-desktop-size') == '' )
					modal_template.find(".size-desktop").remove();

				if( this_item.attr('data-tablet-size') == '' )
					modal_template.find(".size-tablet").remove();

				if( this_item.attr('data-mobile-size') == '' )
					modal_template.find(".size-mobile").remove();

				if( this_item.attr('data-thumbnail-size') == '' )
					modal_template.find(".size-thumbnail").remove();

				if( this_item.attr('data-original-size') == '' )
					modal_template.find(".size-original").remove();

				// When size selected, return URL
				modal_template.on("click", ".option", function(e){

					// Get the size to return
					var size_selected = $(this).attr('class').replace(/(option\ssize\-)(\w+)/, '$2');

					// Call pareent window function to inject into WYSIWYG
					var data_to_parse = {
						url: public_folder_uri + this_item.attr('data-' + size_selected + '-size'),
						alt: this_item.attr('data-alt'),
						title: this_item.attr('data-title'),
						id: this_item.attr('data-id'),
						thumbnail_url: public_folder_uri + this_item.attr('data-forced-thumbnail-url')
					};

					window.top.mediaPopupCallback($("#library-container").attr('data-field-id'), JSON.stringify(data_to_parse));

				});
			}
			if( initiator == 'file_selector' )
			{
				var data_to_parse = {
					alt: this_item.attr('data-alt'),
					title: this_item.attr('data-title'),
					id: this_item.attr('data-id'),
					thumbnail_url: public_folder_uri + this_item.attr('data-forced-thumbnail-url')
				};

				window.top.mediaPopupCallback($("#library-container").attr('data-field-id'), JSON.stringify(data_to_parse));
			}

			// Append to page
			$("body").append(modal_template);
		});
	}

});