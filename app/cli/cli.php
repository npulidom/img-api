<?php
/**
 * Command Line Interface (CLI) Phalcon File.
 */

include dirname(dirname(__DIR__))."/app.php";

(new App("cli"))->start($argv);
