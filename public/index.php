<?php
/**
 * Index Phalcon File
 * @author Nicolas Pulido <nicolas.pulido@crazycake.cl>
 */

//include App Loader
include dirname(__DIR__)."/app.php";

try {
	$app = new PhalconApp("api");
	$app->start();
}
catch (Exception $e) {
	echo $e->getMessage();
}
