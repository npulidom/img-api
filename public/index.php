<?php
/**
 * Index Phalcon File
 */

include dirname(__DIR__)."/app.php";

try {

	(new PhalconApp("api"))->start();
}
catch (Exception $e) { echo $e->getMessage(); }
