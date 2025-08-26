<?php 

/*
    Typeform Module Configuration File

    Settings accessible as 'typeform.key' using Config::get()
*/
return [
	"links" => [
		// Terms of Service link - can be absolute or relative
		// Examples:
		// Absolute: "https://example.com/terms-and-conditions"
		// Relative: "/terms-and-conditions" or "terms-and-conditions"
		"tos" => "https://friendlypos.cl/terminos-y-condiciones/"
	],
	
	"ui" => [
		// Background image for left panel (desktop only)
		// Can be relative path from assets/img/ or absolute URL
		// Examples:
		// "blue-pos.jpeg" (looks in assets/img/)
		// "/path/to/image.jpg" (absolute path)
		// "https://example.com/image.jpg" (external URL)
		"background_image" => "blue-pos.jpeg",
		
		// Optional brand content for left panel
		"brand" => [
			"title" => "Bienvenido",
			"subtitle" => "Sistema de activación de boletas electrónicas"
		]
	]
];