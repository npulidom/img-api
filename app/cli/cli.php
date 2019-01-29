<?php
/**
 * Command Line Interface (CLI) Phalcon File.
 */

include dirname(dirname(__DIR__))."/app.php";

try {

	(new PhalconApp("cli"))->start($argv);
}
catch (Exception $e) { echo $e->getMessage().PHP_EOL; }
