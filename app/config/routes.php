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
	}

	// not found handler
	$app->notFound(fn() => (new CoreController())->serviceNotFound());
};
