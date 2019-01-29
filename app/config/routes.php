<?php
/**
 * Phalcon App Routes files
 */

return function($app) {

	// welcome message
	$app->get('/', [new CoreController(), "welcome"]);

	// resize image
	$app->post("/resize", [new CoreController(), "resize"]);

	// push file
	$app->post("/s3push", [new CoreController(), "s3push"]);

	// ++ Tests

	$app->get("/test/resize", [new TestController(), "resizeTest"]);

	$app->get("/test/logs", [new TestController(), "logs"]);

	$app->get("/test/libraries", [new TestController(), "libraries"]);

	// not found handler
	$app->notFound(function() use (&$app) { (new CoreController())->serviceNotFound(); });
};
