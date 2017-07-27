<?php
/**
 * Command Line Interface (CLI) Phalcon File.
 * PHP Settings must be set in php.ini (both files: Apache & CLI)
 * @author Nicolas Pulido <nicolas.pulido@crazycake.cl>
 */

//include App Loader
include dirname(dirname(__DIR__))."/app.php";

try {
	$app = new PhalconApp("cli");
	$app->start($argv);
}
catch (Exception $e) {
	echo $e->getMessage().PHP_EOL;
}
