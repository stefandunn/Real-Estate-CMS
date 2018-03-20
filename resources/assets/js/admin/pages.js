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