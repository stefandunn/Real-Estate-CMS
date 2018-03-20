<?php

return [

	// Maximum upload file size allowed
	'max_upload_size' => 10000, // In Kilobytes

	// MAximum dimensions to resize images to per device
	'max_resize_dimensions' => [
		'desktop' 	=> [1440, 1440],
		'tablet'	=> [960, 960],
		'mobile'	=> [640, 640],
		'thumbnail'	=> [128, 128],
	],

	// Quality to save images (0-100, 100 being the best)
	'image_save_quality' => 65,

	// TinyPNG Developer API Keys
	'tinify_api_key' => '-Xz2u0IsPgjMV7Ddexad-ojYOv6hcfSR',

];