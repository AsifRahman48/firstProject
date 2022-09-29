<?php

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4',
	'author'                => 'Partex Star',
	//'subject'               => 'This Document will explain the whole universe.',
	//'keywords'              => 'PDF, Laravel, Package, Peace',
	//'creator'               => 'Laravel Pdf',
	'display_mode'          => 'fullpage',
	'tempDir'               => base_path('../temp/'),
	'custom_font_dir' 			=> storage_path('fonts/'),
	'custom_font_data' => [
		'bangla' => [
			'R'  => 'vrinda.ttf',    // regular font
			'B'  => 'vrindab.ttf',       // optional: bold font
			//'I'  => 'ExampleFont-Italic.ttf',     // optional: italic font
			//'BI' => 'ExampleFont-Bold-Italic.ttf' // optional: bold-italic font
			'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
			'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
		]
		// ...add as many as you want.
	]
];
