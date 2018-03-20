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
$(document).ready(function(){

	// On navigation page
	if($("#navigation-index").length > 0)
	{
		var nav_hidden_data = $("#nav-data");

		// Get a clone of the templsate to use
		var nav_list = $("#nav-item-list");
		var nav_item_template = $(".nav-item.template").remove().clone().show();

		// Set up modal form
		var modal_form = modal_template.clone();
		var form = $("#new-nav-form").remove().clone().show();
		modal_form.find("h3").text("Navigation Item Properties").after("<div class='message'></div>");
		modal_form.find(".message").append(form.clone());
		modal_form.find(".modal").css('max-width', '900px');

		var active_popup;

		// When new button clicked
		$("#navigation-index").on("click", ".new-nav", function(){
			
			// Get parent list (ul) new nav item
			var this_btn_instance = $(this);

			// Show the edit modal
			active_popup = showEditModal(this_btn_instance);
		});


		// When delete button clicked
		$("#navigation-index").on("click", ".nav-item-delete", function(){
			$(this).parents("li").remove();
			setupSortableLists();
		});

		// When edit button is clicked
		$("#navigation-index").on("click", ".nav-item-edit", function(){
			active_popup = showEditModal($(this).parents("li"), false);
			setupSortableLists();
		});

		/**
		* Shows the edit/create popup modal
		*/
		function showEditModal(item = null, is_new = true){
			
			// Show modal
			var new_modal = modal_form.clone();
			$("body").append(new_modal);

			// Prepoulate the modal values if item has them
			new_modal.find("#label").val(item.attr('data-label'));
			new_modal.find("#url").val(item.attr('data-url'));
			new_modal.find("#css-styling").val(item.attr('data-css-styling'));
			new_modal.find("#css-class").val(item.attr('data-css-class'));
			new_modal.find("#new-window option[value='" + item.attr('data-new-window') + "']").prop("selected", true);

			// If not new, change "create" to "update"
			if( !is_new )
				new_modal.find(".nav-form-create").text("Update");

			// Add modal events
			new_modal.on("click", ".nav-form-create", function(e){

				// Get form values
				var label = new_modal.find("#label").val();
				var url = new_modal.find("#url").val();
				var css_styling = new_modal.find("#css-styling").val();
				var css_class = new_modal.find("#css-class").val();
				var new_window  = new_modal.find("#new-window option:selected").val();

				// Set modal errors blank again
				new_modal.find("#errors").html('').hide();

				/* Validate */
				var errors = [];

				// Ensure label and URL is populated
				if( ( label == '' || label == undefined || label == null ) || ( url == '' || url == undefined || url == null ) )
					errors.push("We require that you provide a label and url");

				// Check valid URL
				url_regex = /^((https?:\/\/)?([\w\d-_]+)\.([\w\d-_\.]+)\/?\??([^#\n\r]*)?#?([^\n\r]*)|((?:\/?\w)+)|(?:\/))/;
				if( ( url == '' || url == undefined || url == null ) || !( url_regex.test(url) || url == '#' ) )
					errors.push("Please provide a valid URL");

				// If errors
				if( errors.length > 0)
					errors.forEach(function(error){
						new_modal.find("#errors").show().append("<li>"+error+"</li>");
					});
				else
				{
					// Clone nav item template and populate
					var new_item = nav_item_template.clone().removeClass('template');
					
					// Set label
					new_item.find("> span i:first").before("<span class='link-preview'>"+label+"</span>");

					// Add link preview css class if given
					new_item.find(".link-preview").addClass(css_class);

					// Add styling if exists to previe
					if( css_styling !== '' && css_styling !== undefined && css_styling.length > 0)
						new_item.append("<style>"+css_styling+"</style>");
					// Set data
					new_item.attr('data-label', label).attr('data-url', url).attr('title', 'Goes to: '+url).attr('data-css-styling', css_styling).attr('data-css-class', css_class).attr('data-new-window', new_window);
					

					// If is new, add before the "new item" li element that was clicked
					if( is_new )
						// Add to list
						item.before(new_item);

					// Otherwisde replace the one we are editing
					else
						item.replaceWith(new_item);

					// Hide modal
					new_modal.fadeOut('fast', function(){ $(this).remove(); });
					// Setup sortable
					setupSortableLists();

				}

			});

			new_modal.on("click", ".nav-form-cancel", function(){
				// Hide modal
				new_modal.fadeOut('fast', function(){ $(this).remove(); });
			});

			return new_modal;
		}

		/**
		* Setsa up the sortable lists for the nav menus
		*/
		function setupSortableLists(){

			var horizontal_attrs = {
				axis: 'x',
				items: "> li.nav-item",
				forcePlaceholderSize: true,
				helper: "clone"
			};

			var vertical_attrs = {
				axis: 'y',
				items: "> li.nav-item",
				forcePlaceholderSize: true,
				helper: "clone",
				start: function(event, ui){
					var max_width = 0;
					ui.item.parent().find("li").each(function(index, element){
						if( $(element).outerWidth() > max_width )
							max_width = $(element).outerWidth();
					});
					ui.placeholder.css('width', max_width);
				}
			};


			// Make header menu sortable
			if( $("#header-nav-item-list > li").length > 2 )
				$("#header-nav-item-list").sortable(horizontal_attrs);

			// Make header schildren lists sortable
			$("#header-nav-item-list ul.children").each(function(index, el){
				if( $(el).find(">li").length > 2 )
					$(el).sortable(vertical_attrs);
			});

			if( $("#footer-nav-item-list > li").length > 2 )
				// Make footer/useful-links menu sortable
				$("#footer-nav-item-list").sortable(vertical_attrs);

		}
		// Do on page load also
		setupSortableLists();

		/**
		* Updates the hidden field containing serialised data of the menus
		*/
		$("#nav-form").on("submit", function(e){

			// Serialise header
			header_data = [];
			$("#header-nav-item-list > li.nav-item").each(function(index, element){
				// Create porperties object
				var item_props = {
					'label' : $(element).attr('data-label'),
					'url' : $(element).attr('data-url'),
					'css_styling' : $(element).attr('data-css-styling'),
					'css_class' : $(element).attr('data-css-class'),
					'new_window' : $(element).attr('data-new-window'),
					'sort_order' : index,
					'children' : [],
				}

				// If this item has any children add them.
				if( $(element).find("ul.children > li.nav-item").length > 0 )
				{
					// COnstruct properties for child
					$(element).find("ul.children > li.nav-item").each(function(index2, element2){

						// Add to parent's property, "children"
						item_props.children.push({
							'label' : $(element2).attr('data-label'),
							'url' : $(element2).attr('data-url'),
							'css_styling' : $(element2).attr('data-css-styling'),
							'css_class' : $(element2).attr('data-css-class'),
							'new_window' : $(element2).attr('data-new-window'),
							'sort_order' : index2,
						});
					});
				}

				// Add to main data array
				header_data.push(item_props);
			});

			// Serialise footer
			footer_data = [];
			$("#footer-nav-item-list > li.nav-item").each(function(index, element){
				// Create porperties object
				footer_data.push({
					'label' : $(element).attr('data-label'),
					'url' : $(element).attr('data-url'),
					'css_styling' : $(element).attr('data-css-styling'),
					'css_class' : $(element).attr('data-css-class'),
					'new_window' : $(element).attr('data-new-window'),
					'sort_order' : index,
				});
			});

			
			// Store in hidden field
			nav_hidden_data.val(JSON.stringify({
				'header' : header_data,
				'footer' : footer_data,
			}));

		});

		// Get URL browser lookup data
		var url_browser_modal = $("#url-browser-modal").remove().clone();
		var url_selector = url_browser_modal.find("#urls-selector");
		var lookup_button = $("#url-browser");
		var lookup_data = url_browser_modal.find("#lookup-data");
		var type_selector = url_browser_modal.find("select#type");

		// On Close, clear the URL selector
		url_browser_modal.on("click", ".close-btn", function(){
			url_selector.val('');
		});

		// Disable selector options that are not available
		type_selector.find("option:not(:first)").each(function(index, element){
			if( lookup_data.find("option[data-type='" + $(element).val() + "']").length == 0 )
				$(element).remove();
		});

		// Deal with browse URL button
		$(document).on("click", "#url-browser", function(){

			// Show modal
			$("body").append(url_browser_modal.fadeIn());
			
		});

		// When the type selector changes
		$(document).on("change", "#url-browser-modal select#type", function(){
			sortData();
		});

		// When option selected
		$(document).on("input", "#urls-selector", function(e){
			var found_option = lookup_data.find("option[value='"+ url_selector.val() + "']");
			
			if( found_option.length > 0 ){
				active_popup.find("input#url").val(found_option.attr('data-url'));
				$(this).val('');
				$(this).parents(".modal-wrapper").find(".close-btn").trigger("click");
			}
		});

		// Filters the lookup data
		function sortData(){

			// Get type to fetch
			var type = type_selector.find("option:selected").val();
			lookup_data.find("option").prop('disabled', false);

			if(type == 'media'){
				lookup_data.find("option[data-type!='media']").prop('disabled', true);
			}
			if(type == 'pages'){
				lookup_data.find("option[data-type!='pages']").prop('disabled', true);
			}
			if(type == 'properties'){
				lookup_data.find("option[data-type!='properties']").prop('disabled', true);
			}

			url_selector.val('');
		}
	}
})
$(document).ready(function(){

	var page_form = $("body[id^='pages-new'], body[id^='pages-edit']" );
	// If on page form
	if( page_form.length > 0 ){

		var slug = page_form.find("form input[name='page[slug]']");
		var parent_slug = page_form.find("form span#parent-slug");
		var slug_edit = page_form.find("form input#slug");

		// On parent select, update the parent slug field
		page_form.on("change keyup keydown paste", "select#parent, input#slug", function(){
			fixSlugs();
		});

		// Run on document ready
		fixSlugsOnReady();

		function fixSlugs(){

			// Get slug of selected parent
			var this_slug= page_form.find("form select#parent option:selected").attr('data-slug');

			// If not defined
			this_slug = ( this_slug !== undefined )? this_slug + "/" : "";

			// Set the parent slug to this value (append /)
			parent_slug.text(this_slug);

			// Set the hidden actua slug field to the parent slufg plus the provided page slug
			slug.val( this_slug + slug_edit.val() );

			console.log(slug.val());
		}

		function fixSlugsOnReady(){

			// Get slug of selected parent
			var this_slug= page_form.find("form select#parent option:selected").attr('data-slug');

			// If not defined
			this_slug = ( this_slug !== undefined )? this_slug + "/" : "";

			// Set the parent slug to this value (append /)
			parent_slug.text(this_slug);

			slug_edit.val(slug.val().replace(this_slug, ""));
		}

	}

});
$(document).ready(function(){

	// If on property page..
	if( $("body[id^='properties-']").length > 0 )
	{
		// Setup tags plugin
		$("#tags").tagsInput({
   			'height':'80px',
   			'width': '100%',
   			'defaultText': '',
		});

		// Make sure reference code is all caps
		$("#reference-code").on("keyup change paste", function(){
			$(this).val( $(this).val().toUpperCase() );
		});


		// Files' form
		if( $("body").attr('id') == 'properties-files' )
		{
			// Get a copy of the file selector template
			var file_selector_template = $("#file-template").remove().clone().removeAttr('id');

			// Files containers
			var images_container = $("#property-images");
			var documents_container = $("#property-documents");

			// New image button
			var new_image_btn = $("#add-image");
			var new_document_btn = $("#add-document");

			// On new image click
			new_image_btn.on("click", function(){
				newFileSelector(images_container);
			});

			// On Delete btn
			$("body").on('click', '.file-selector-wrapper .delete-btn', function(){

				if( confirm( "Are you sure you'd like to remove this file?" )) {
					$(this).parents('.col-md-3').remove();
				}

			});

			// On new document click
			new_document_btn.on("click", function(){
				newFileSelector(documents_container);
			});

			// Create new file selector
			function newFileSelector(container){

				// New instance of template
				var new_file_selector = file_selector_template.clone();

				// Adjust height to match others
				new_file_selector.find(".file-selector-wrapper").css('height', getTallestSelectorHeight(images_container));

				// Change attributes of selector to prevent ID conflicts
				new_file_selector.find("input").attr('id', 'file-selector-' + ( parseInt($(".file-selector-wrapper").length) + 1));

				// If image container, set mime_type to image
				if( container.attr('id') == 'property-images' ) {
					new_file_selector.find(".file-selector-label").attr('data-mime', 'image');
					new_file_selector.find("input").attr('name', 'images[]');
				}

				// Else, leave as anything
				else {
					new_file_selector.find(".file-selector-label").attr('data-mime', '');
					new_file_selector.find("input").attr('name', 'documents[]');
				}

				// Append to container
				container.append(new_file_selector);

				// 
				new_file_selector.find(".file-selector-label").trigger("click");
			}

			// Get tallest height
			function getTallestSelectorHeight(container){

				// Set height low!
				var height = 0;

				// Loop through existing file selectors and see if their height is taller, if so, then override the value of height
				container.find(".file-selector-wrapper").each(function(){
					if($(this).innerHeight() > height )
						height = $(this).innerHeight() + 4; // 4px added to compensate for border thickness
				});

				return height;
			}

		}
	}
});
var modal_template = $("<div class='modal-wrapper'><div class='modal'><h3>Title</h3><span class='fa fa-times close-btn'></span></div><div class='modal-bg'></div></div>");

$(document).ready(function(){

    /**
    * Animate counters
    */
    $(".tile_count div.count").each(function(element){
        $(this).attr('count',0).animate({
            count: $(this).text()
        }, {
            duration: 1000,
            easing: 'swing',
            step: function (now) {
                $(this).text(Math.ceil(now));
            }
        });
    });

    /**
    * Handle code editors
    */
    var editors = [];
    $("textarea.code").each(function(){
        initialiseCodeEditor( $(this) );
    });
    function initialiseCodeEditor( this_el )
    {

        // Get syntax language
        var language = ( this_el.attr( 'data-language' ) !== undefined && this_el.attr( 'data-language' ).length > 0 )? this_el.attr( 'data-language' ) : 'html';

        // Should we show errors
        var show_errors = ( this_el.attr( 'data-show-errors' ) !== undefined && this_el.attr( 'data-show-errors' ).length > 0 )? this_el.attr( 'data-show-errors' ) : 'false';
        show_errors = ( show_errors == 'true' )? true : false;

        // Hide the textarea
        this_el.hide();

        // Append editor after
        this_el.after("<div class='code-container'></div>");

        // New editor instance
        var editor = ace.edit( this_el.siblings(".code-container").get(0) );

        editor.setTheme( 'ace/theme/github' );
        editor.getSession().setMode( "ace/mode/" + language );
        editor.getSession().setTabSize( 4 );
        editor.getSession().setUseSoftTabs( true );
        editor.getSession().setUseWrapMode( false );
        editor.getSession().setUseWorker( show_errors );

        editor.getSession().setValue( this_el.val() );
        editor.getSession().on( 'change', function()
        {
          this_el.val( editor.getSession().getValue() );
        });

        editor.setShowPrintMargin(false);


        editors.push( editor );
    }

    /**
    * Handle slug generators
    */
    $(document).on("keyup paste change", "input[data-slug-generator]", function(){

        var this_el = $(this);
        var placement_el = $("input[data-slug-placement='" + $(this).attr('data-slug-generator') + "']");

        // If placement found, do slug generation
        if( placement_el.length > 0 )
            placement_el.val( this_el.val().toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '').replace(/\-\-+/g, '-').replace(/^-+/, '').replace(/-+$/, '') ).trigger("change");

    });
    
    // If lost focus on slug, ensure it's in okay format
    $(document).on("blur", "input[data-slug-placement]", function(){
        $(this).val( $(this).val().toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '').replace(/\-\-+/g, '-').replace(/^-+/, '').replace(/-+$/, '') ).trigger("change");
    });

    /**
    * Handle delete button confirmation
    */
    $( "a:contains('Delete')" ).on( "click", function(e){
        var delete_link = $(this);
        var delete_link_url = delete_link.attr('href');


        if( !delete_link.parent().hasClass("confirm-delete-wrapper") )
        {

            var delete_confirm_wrapper = $("<div class='confirm-delete-wrapper inline-block'></div>");
            var delete_confirm_popup = $("<div class='confirm-delete-popup'></div>");
            var yes_link = delete_link.clone().text("Yes").attr('href', '#').removeClass('btn-danger').addClass('btn-success delete-confirm').on("click", function(event){
                event.preventDefault();
                if( delete_link.is("a" ) )
                    window.location = delete_link_url;
                else
                    delete_link.trigger(e);
            });
            var no_link = delete_link.clone().text("No").attr('href', '#').addClass('delete-confirm').on("click", function(ev){
                ev.preventDefault();
                delete_link.parent().find(".confirm-delete-popup").animate({
                    'opacity' : 0,
                    'margin-top' : -10,
                }, 300, function(){
                    $(this).remove();
                    delete_link.unwrap().addClass('btn-danger').removeClass('btn-info').text("Delete");
                });
            });
            delete_link.attr('href', '#');
            delete_link.text("Are you sure?").removeClass("btn-danger").addClass("btn-info");
            delete_link.wrap(delete_confirm_wrapper);
            delete_confirm_popup.append(yes_link).append(no_link);
            delete_link.after(delete_confirm_popup);
            delete_confirm_popup.css({
                'opacity' : 0,
                'margin-top' : -10,
                'margin-left' : (0 - (delete_confirm_popup.width()/2))
            }).animate({
                'opacity' : 1,
                'margin-top' : 0,
            }, 300);

            e.preventDefault();
        } else {
            e.preventDefault();
        }

    });

    /**
    * Handle modal close button
    */
    $(document).on("click", ".modal .fa-times.close-btn, .modal-bg", function(){
        $(this).parents(".modal-wrapper").fadeOut('fast', function(){
            $(this).remove();
        });
    })

    /**
    * Handle flash messages modal
    */
    if($("#flash-messages-wrapper").length > 0){
        var flash_message_wrapper = $("#flash-messages-wrapper");

        // Set new timeout to auto-close the alert box
        var flash_message_timeout = setTimeout(function(){
            flash_message_wrapper.fadeOut('fast', function(){
                flash_message_wrapper.remove()
            });
        }, 1000000);

        // If box clicked, hide it also and clear timeout.
        flash_message_wrapper.on("click", ">div, *", function(e){
            // Clear timeout
            clearTimeout( flash_message_timeout );
            flash_message_wrapper.fadeOut('fast', function(){
                flash_message_wrapper.remove();
            });
        });
    }

    /**
    * Handle WYSIWYG Editors
    */
    tinymce.PluginManager.load('powerpaste', '/admin-theme/helpers/powerpaste.min.js');
    tinymce.init({
        selector: ".wysiwyg",
        content_css: "/css/style.css",
        plugins: [
            "advlist autolink lists link image charmap print preview anchor autosave",
            "searchreplace visualblocks code fullscreen textcolor colorpicker importcss",
            "media table contextmenu powerpaste imagetools",
        ],
        powerpaste_html_import: prompt,
        powerpaste_word_import: prompt,
        powerpaste_allow_local_images: true,
        paste_data_images: true,
        convert_urls: false,
        importcss_append: true,
        menu: {
            file: {title: 'File', items: 'newdocument'},
            edit: {title: 'Edit', items: 'undo redo | cut copy paste pastetext | selectall | searchreplace'},
            insert: {title: 'Insert', items: 'link image media | template hr'},
            view: {title: 'View', items: 'visualaid fullscreen'},
            format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
            table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'},
            tools: {title: 'Tools', items: 'spellchecker code'}
        },
        toolbar: "undo redo | styleselect | bold italic underline strikethrough subscript superscript forecolor backcolor removeformat | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | fullscreen",
        imagetools_cors_hosts: ['www.tinymce.com', 'codepen.io'],
        file_browser_callback: mediaPopup
    });

    /**
    * Bootstrap datepickers
    */
    $("input.datepicker").datepicker({
        format: 'yyyy-mm-dd'
    });

    /**
    * File selectors
    */
    $(document).on("click", ".file-selector-label", function(){
        
        // Find input field for data to be parsed to
        var input_field = $(this).next("input");

        // Get mime-type
        mime_type = ( $(this).attr('data-mime') === 'undefined' )? '' : $(this).attr('data-mime');

        // Open media file modal
        mediaPopup(input_field.attr('id'), input_field.val(), mime_type, window, 'file_selector');
    });

    /**
    * Invokes a media popup selector (for TinyMCE or File selector)
    */
    function mediaPopup(field, val, type, win, initiator='tinymce') {

        // Mime types accepted based on type we're browsing for.
        switch(type){
            case 'image':
                mimes_accepted = "image";
                break;
            case 'media':
                mimes_accepted = "video";
                break;
            default:
                mimes_accepted = "*";
                break;
        }

        // Get URL for the library URL;
        var media_library_url = $("meta[name='library-url']").attr('content');

        // Create a temporary hidden field to store data and append to body
        var temp_input = $("<textarea style='display:none' id='tmp-" + Math.random().toString(36).substring(4) + "'>" + val + "</textarea>");

        // Set the initiator
        temp_input.attr('data-initiator', initiator);

        // Append to body
        $("body").append(temp_input);

        // Create modal and adjust styling
        var new_modal = modal_template.clone().css( {
            'z-index': parseInt( $(".mce-floatpanel").css('z-index') )+1,
        });
        new_modal.addClass('media-modal').find(".modal-bg").css('background-color', 'rgba(255, 255, 255, 0.7');
        new_modal.find(".modal").css('max-width', '100%');

        // Create iframe to library and adjust properties
        library_iframe = $("<iframe></iframe>");
        library_iframe.attr('frameborder', 0).attr('src', media_library_url + "?view=list&modal=true&include_callback=true&initiator=" + initiator + "&mime_type=" + mimes_accepted + "&ref_id=" + temp_input.attr('id')).css({
            'width' : '100%',
            'height' : parseInt($(window).innerHeight()) - 100,
            'min-height' : 400,
        });

        // Deal with resizing of window
        $(window).on("resize", function(){
            library_iframe.css({
                'height' : parseInt($(window).innerHeight()) - 100,
            });
        })

        // Append iframe to modal
        new_modal.find(".modal").append(library_iframe);

        new_modal.find(".close-btn").off("click").on("click", function(){
            new_modal.fadeOut('fast', function(){
                $(this).remove();
                temp_input.remove();
            })
        });

        $body.append(new_modal);

        // When input value changes
        temp_input.on("change", function(){

            // Get JSON data
            var parsed_data = JSON.parse($(this).val());

            // Get field to update
            var field_updating = $(win.document.getElementById(field));

            // Set value according to initiator
            if( $(this).attr('data-initiator') == 'tinymce' )
            {
                field_updating.val(parsed_data.url);
                field_updating.parents(".mce-floatpanel").find("label:contains(Image description)").next("input").val(parsed_data.alt);
                temp_input.remove();
            }
            if( $(this).attr('data-initiator') == 'file_selector' )
            {
                field_updating.val( parsed_data.id );
                field_updating.prev("label").removeClass("btn-info").addClass("btn-warning").text("Change file");
                field_updating.parent().find(".file-selector-preview").attr('data-preview-url', parsed_data.thumbnail_url).attr('data-id', parsed_data.id).attr('data-title', parsed_data.alt).find("img").attr('src', parsed_data.thumbnail_url).show();
                field_updating.parent().find(".file-selector-preview > .file-title").text(parsed_data.title);
            }
        });
    }

    /**
    * Fancy checkbox
    */
    $(document).on("click", ".fancy-checkbox > label", function(){
        $(this).prev("input").prop( "checked", function( i, val ) {
          return !val;
        });
    });
});

/**
* Easy modal display
* Accepts a title and array/string of messages
*/
function showModal( title, messages = [], styling={}) {

    // Clone modal template
    var new_modal = modal_template.clone();

    // If styling given
    if( typeof styling == 'object' )
        new_modal.find(".modal").css( styling );

    // Add title
    new_modal.find("h3").text(title);

    // Add messages
    if( (typeof messages == 'string' && messages.length > 0) || typeof messages == 'object' )
    {
        if( typeof messages == 'string' )
            new_modal.find(".modal").append($("<div class='message'>" + messages + "</div>"));
        else
            new_modal.find(".modal").find('h3').after(messages);
    }
    else if( typeof messages == 'object' && messages.length > 0 ) {
        messages.forEach(function (message){
            new_modal.find(".modal").append($("<div class='message'>" + message + "</div>"));
        });
    }

    // Append to body
    $("body").append(new_modal);

    // Return the new modal
    return new_modal;
}

/**
/* Callback form media popup
* Must be outside $(document).ready to be read by iframe
*/
function mediaPopupCallback(field_id, value){
    $("#"+field_id).val(value).trigger("change");
    $(".modal-wrapper.media-modal").find(".close-btn").trigger("click");
}
$(document).ready(function(){

	// When on subscriptione export page
	if( $("#subscribers-export").length > 0 )
	{
		var preview_btn = $("input[type='submit'][value='Preview Results']");
		var export_btn = $("input[type='submit'][value='Export All']");
		var number_checkboxes_checked = $(".filter-option .fancy-checkbox > input:checked").length;


		$(".fancy-checkbox > label").on("click", function(){
			var checkbox = $(this).prev("input");
			var filter_option = $(this).parents(".filter-option");

			// If checkbox is going from unchecked to checked (will return false when checking)
			if( !checkbox.is(":checked") )
			{
				// Remove inactive status
				filter_option.find(".table-cell:not(:first)").removeClass('inactive');

				// Enable any input,select
				filter_option.find("input").prop('disabled', false);
				filter_option.find("select").prop('disabled', false);

				number_checkboxes_checked++;
			}
			else
			{
				// Reset inactive status, and remove any input data
				filter_option.find(".table-cell:not(:first)").addClass('inactive');
				filter_option.find("input").val('').prop('disabled', true);
				filter_option.find("select").prop('disabled', true);

				number_checkboxes_checked--;
			}

			setButtonLabels();
		});

		setButtonLabels();

		function setButtonLabels(){
			// If no checkboxes checked, then hide preview btn
			if( number_checkboxes_checked == 0 ){
				preview_btn.hide();
				export_btn.val('Export All');
			}
			else
			{
				preview_btn.show();
				export_btn.val('Export Filtered');
			}
		}
	}
});
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$current_url = window.location.href.split('#')[0].split('?')[0],
$body = $('body'),
$menu_toggle = $('#menu_toggle'),
$sidebat_menu = $('#sidebar-menu'),
$sidebar_footer = $('.sidebar-footer'),
$left_col = $('.left_col'),
$right_col = $('.right_col'),
$nav_menu = $('.nav_menu'),
$footer = $('footer');

$(document).ready(function() {
    // Sidebar
    var setContentHeight = function () {
        // reset height
        $right_col.css('min-height', $(window).height());

        var bodyHeight = $body.outerHeight(),
            footerHeight = $body.hasClass('footer_fixed') ? -10 : $footer.height(),
            leftColHeight = $left_col.eq(1).height() + $sidebar_footer.height(),
            contentHeight = bodyHeight < leftColHeight ? leftColHeight : bodyHeight;

        // normalize content
        contentHeight -= $nav_menu.height() + footerHeight;

        $right_col.css('min-height', contentHeight);
    };

    $sidebat_menu.find('a').on('click', function(ev) {
        var $li = $(this).parent();

        if ($li.is('.active')) {
            $li.removeClass('active active-sm');
            $('ul:first', $li).slideUp(function() {
                setContentHeight();
            });
        } else {
            // prevent closing menu if we are on child menu
            if (!$li.parent().is('.child_menu')) {
                $sidebat_menu.find('li').removeClass('active active-sm');
                $sidebat_menu.find('li ul').slideUp();
            }
            
            $li.addClass('active');

            $('ul:first', $li).slideDown(function() {
                setContentHeight();
            });
        }
    });

    // toggle small or large menu
    $menu_toggle.on('click', function() {
        if ($body.hasClass('nav-md')) {
            $sidebat_menu.find('li.active ul').hide();
            $sidebat_menu.find('li.active').addClass('active-sm').removeClass('active');
        } else {
            $sidebat_menu.find('li.active-sm ul').show();
            $sidebat_menu.find('li.active-sm').addClass('active').removeClass('active-sm');
        }

        $body.toggleClass('nav-md nav-sm');

        setContentHeight();
    });

    // check active menu
    $sidebat_menu.find('a[href="' + $current_url + '"]').parent('li').addClass('current-page');

    $sidebat_menu.find('a').filter(function () {
        return this.href == $current_url;
    }).parent('li').addClass('current-page').parents('ul').slideDown(function() {
        setContentHeight();
    }).parent().addClass('active');

    // recompute content when resizing
    $(window).smartresize(function(){  
        setContentHeight();
    });

    setContentHeight();

    // fixed sidebar
    if ($.fn.mCustomScrollbar) {
        $('.menu_fixed').mCustomScrollbar({
            autoHideScrollbar: true,
            theme: 'minimal',
            mouseWheel:{ preventDefault: true }
        });
    }
    // /Sidebar

    // Progressbar
    if ($(".progress .progress-bar")[0])
        $('.progress .progress-bar').progressbar();
    // /Progressbar


    // Table
    var checkState = '';
    $('table input').on('ifChecked', function () {
        checkState = '';
        $(this).parent().parent().parent().addClass('selected');
        countChecked();
    });
    $('table input').on('ifUnchecked', function () {
        checkState = '';
        $(this).parent().parent().parent().removeClass('selected');
        countChecked();
    });
    // /Table


    // Accordion
    $(".expand").on("click", function () {
        $(this).next().slideToggle(200);
        $expand = $(this).find(">:first-child");

        if ($expand.text() == "+") {
            $expand.text("-");
        } else {
            $expand.text("+");
        }
    });
    // /Accordion
});
//# sourceMappingURL=custom-theme.js.map
