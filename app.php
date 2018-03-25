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
			"path"    => __DIR__."/",
			"loader"  => ["helpers"],
			"core"    => [],
			"version" => "0.1.0",
			"key"     => false, //HTTP header API Key (basic security)
			//project properties
			"name"      => "img",    //App name
			"namespace" => "imgapi", //App namespace (no usar underscore ni guiones)
			//crypto
			"cryptKey" => 'CC7IMAx*',
			//db
			"mysqlService" => false
		];
	}
}
