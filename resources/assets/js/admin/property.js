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