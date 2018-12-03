<?php
/**
 * Phalcon Project Environment configuration file.
 * @author Nicolas Pulido <nicolas.pulido@crazycake.tech>
 */

require "core/cc-phalcon.phar";

class PhalconApp extends \CrazyCake\Phalcon\App
{
	protected static $PROJECT_PATH = __DIR__."/";

	/**
	 * Required app configuration
	 */
	protected function config()
	{
		return [
			// project Path
			"version" => "0.0.1",
			"loader"  => ["helpers"],
			"core"    => [],
			"key"     => false,
			// project properties
			"name"      => "img",    // app name
			"namespace" => "imgapi", // app namespace (no usar underscore ni guiones)
			// crypto
			"cryptKey" => "CC3ImAx*"
		];
	}
}
