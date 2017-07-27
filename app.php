<?php
/**
 * Phalcon Project Environment configuration file.
 * Requires PhalconPHP installed
 * @author Nicolas Pulido <nicolas.pulido@crazycake.cl>
 */

//include CrazyCake phalcon loader
require is_file(dirname(__DIR__)."/cc-phalcon/autoload.php") ?
				dirname(__DIR__)."/cc-phalcon/autoload.php"  : __DIR__."/core/cc-phalcon.phar";

class PhalconApp extends \CrazyCake\Phalcon\App
{
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
			"version" => "0.0.1",
			"key"     => false, //HTTP header API Key (basic security)
			//project properties
			"name"      => "img", //App name
			"namespace" => "imgapi", //App namespace (no usar underscore ni guiones)
			//crypto
			"cryptKey" => 'CC7IMAx*',
			//db
			"mysqlService" => false
		];
	}
}
