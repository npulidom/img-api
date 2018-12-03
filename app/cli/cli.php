<?php
/**
 * Command Line Interface (CLI) Phalcon File.
 * @author Nicolas Pulido <nicolas.pulido@crazycake.tech>
 */

include dirname(dirname(__DIR__))."/app.php";

try {

	(new PhalconApp("cli"))->start($argv);
}
catch (Exception $e) { echo $e->getMessage().PHP_EOL; }
