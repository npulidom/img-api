<?php
/**
 * Phalcon Project Environment configuration file.
 * @author Nicolas Pulido <nicolas.pulido@crazycake.cl>
 */

//include CrazyCake phalcon loader
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
			//Project Path
			"version" => "0.0.1",
			"loader"  => ["helpers"],
			"core"    => [],
			"key"     => false, //HTTP header API Key (basic security)
			//project properties
			"name"      => "img",    //App name
			"namespace" => "imgapi", //App namespace (no usar underscore ni guiones)
			//crypto
			"cryptKey" => "CC3ImAx*"
		];
	}
}
