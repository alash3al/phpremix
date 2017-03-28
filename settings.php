<?php return [

	/**
	 * Whether to display error details or not
	 *
	 * This option relates to Slim Framework
	 */
	"displayErrorDetails"	=>	true,

	/**
	 * Array of application connections
	 *
	 * Each connection has name (key) and other properties ([k => v]),
	 * this option relates to 'Illuminate/Database/Capsule/Manager' (a laravel component) .
	 * 
	 * NOTE, to disable database, just set this option to [] or null .
	 */
	"capsule" => [

		// default connection
		"default" => [
		    'driver'    => 'mysql',
		    'host'      => 'localhost',
		    'database'  => 'test',
		    'username'  => 'root',
		    'password'  => '',
		    'charset'   => 'utf8',
		    'collation' => 'utf8_unicode_ci',
		    'prefix'    => '',
		],

	],
];
