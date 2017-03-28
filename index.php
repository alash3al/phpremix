<?php

	/**
	 * Load the composer autoload file
	 */
	$autoloader = require 'vendor/autoload.php';

	/**
	 * Initialize the Slim App
	 * 
	 * Initializing a Slim App and setting the default settings
	 */
	$app = new \Slim\App(["settings" => require(__DIR__ . "/settings.php")]);

	/**
	 * Our view handler
	 *
	 * @param  array 	$files
	 * @param  array    $args
	 *
	 * @return Object
	 */
	$app->getContainer()["view"] = function() use($app) {
		return new class($app) {
			private $scope;
			public function __construct($app) {
				$this->scope = $app;
			}
			public function render($files, array $args = []){
				$files = (array) $files;
				$app = $this->scope;
				$cb = (function() use($files, $args, $app) {
					ob_start();
					extract($args);
					foreach ( $files as $file ) {
						$orig = $file;
						$file = $file = __DIR__ . "/" . "views" . "/" . $file;
						if ( is_file($file) ) {
							require $file;
						} else if ( is_file($file = $file . ".php") ) {
							require $file;
						} else if ( is_file($file = $file . ".html") ) {
							require $file;
						} else {
							throw new Exception("View (" + $orig + ") not found !");
						}
					}
					return $app->getContainer()->response->getBody()->write(ob_get_clean());
				});
				return ($cb->bindTo($app->getContainer()))();
			}
		};
	};

	/**
	 * Our autoload handler
	 *
	 * @return Composer\Autoload\ClassLoader
	 */
	$app->getContainer()["autoloader"] = function() use($autoloader) {
		return $autoloader;
	};

	/**
	 * Prepare capsule "laravel database manager"
	 */
	use Illuminate\Database\Capsule\Manager as Capsule;
	{
		/**
		 * Initialize capsule
		 *
		 * @return Illuminate\Database\Capsule\Manager
		 */
		$capsule = new Illuminate\Database\Capsule\Manager;

		/**
		 * Set this instance as global
		 */
		$capsule->setAsGlobal();

		/**
		 * Makes capsule available via $app
		 *
		 * @return Illuminate\Database\Capsule\Manager
		 */
		$app->getContainer()["capsule"] = function() use($capsule) {
			return $capsule;
		};

		/**
		 * Adding connections to capsule
		 */
		foreach ( (array) $app->getContainer()->settings["capsule"] as $name => $conn ) {
			$capsule->addConnection($conn, $name);
		}
	}

	/**
	 * Loading routes
	 * 
	 * load all available routes in routes directory
	 */
	foreach ( glob(__DIR__ . "/" . "routes" . "/" . "*.php") as $route ) {
		require_once $route;
	}

	/**
	 * Dispatch !
	 * 
	 * Run the whole app now !
	 */
	$app->run();
