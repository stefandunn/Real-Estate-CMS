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