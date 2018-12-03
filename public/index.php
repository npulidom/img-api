<?php
/**
 * Index Phalcon File
 * @author Nicolas Pulido <nicolas.pulido@crazycake.tech>
 */

include dirname(__DIR__)."/app.php";

try {

	(new PhalconApp("api"))->start();
}
catch (Exception $e) { echo $e->getMessage(); }
