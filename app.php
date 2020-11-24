<?php
/**
 * Phalcon Project Environment configuration file.
 */

require "core/cc-phalcon.phar";

class App extends \CrazyCake\Phalcon\App
{
	/**
	 * Required app configuration
	 */
	protected function config()
	{
		return [
			"loader" => ["helpers"],
			"key"    => false,
			// project properties
			"name"      => "imgapi",
			"namespace" => "imgapi"
		];
	}
}
