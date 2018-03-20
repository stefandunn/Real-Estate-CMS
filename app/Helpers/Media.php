<?php

/**
* Converts an ID of a media file to an image tag
*/
function mediaIDToTag($id, $size='desktop', $attributes=[]){
	return \App\MediaFile::tagFromID( $id, $size, $attributes );
}

/**
* Serves a HTML input type to select a file
*/
function fileSelector($id, $field_name, $media_file_id, $mime_type='', $show_delete_btn = false){

	// Find media file
	$media_file = \App\MediaFile::find($media_file_id);

	// Get media file properties
	$media_file_preview = ( !is_null($media_file) )? ( ($media_file->thumbnail_path != null )? $media_file->thumbnail_path : $media_file->path ) : null;
	$media_file_title = ( !is_null($media_file) )? $media_file->title : null;
	$media_file_id = ( !is_null($media_file) )? $media_file_id : null;

	$to_return = "";
	$to_return .= "<div class='file-selector-wrapper'>";
	if( $show_delete_btn )
		$to_return .= "<span class='delete-btn'><i class='fa fa-times' aria-hidden='true'></i></span>";
	$to_return .= "	<div class='file-selector-preview' data-preview-url='{$media_file_preview}' data-id='{$media_file_id}' data-title='{$media_file_title}'>";
	$to_return .= ( !is_null( $media_file ) )? $media_file->toTag('thumbnail', ['class' => 'thumbnail-image']) : '<img class="thumbnail-image" style="display: none;" />';
	$to_return .= "<span class='file-title'>{$media_file_title}</span></div>\n";
	$to_return .= "	<label " . ( ( empty($mime_type) )? "" : "data-mime='" . $mime_type . "'") . " class='file-selector-label btn btn-" . ( ( is_null( $media_file ) )? "info" : "warning" ) . "'>" . ( ( is_null( $media_file ) )? "Select file" : "Change File" ) . "</label>";
	$to_return .= "	<input type='hidden' name='{$field_name}' id='{$id}' value='{$media_file_id}' />";
	$to_return .= "</div>";
	return $to_return;
}