<?php
/**
 * Phalcon App Routes files
 */

return function($app) {

	// index
	$app->get('/', [new CoreController(), "index"]);

	// resize image
	$app->post("/resize", [new CoreController(), "resize"]);

	// push file
	$app->post("/s3push", [new CoreController(), "s3push"]);

	// ++ Tests
	if (APP_ENV != "production") {

		$app->get("/test/resize", [new TestController(), "resizeTest"]);

		$app->get("/test/logs", [new TestController(), "logs"]);

		$app->get("/test/libraries", [new TestController(), "libraries"]);
	}

	// not found handler
	$app->notFound(function() use (&$app) { (new CoreController())->serviceNotFound(); });
};
