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