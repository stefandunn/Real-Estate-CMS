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