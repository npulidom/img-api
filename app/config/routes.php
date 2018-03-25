<?php
/**
 * Phalcon App Routes files
 * @author Nicolas Pulido <nicolas.pulido@crazycake.cl>
 */

return function($app) {

	//welcome message
	$app->get('/', [new CoreController(), "welcome"]);

	//resize image
	$app->post("/resize", [new CoreController(), "resize"]);

	//push file
	$app->post("/s3push", [new CoreController(), "s3push"]);

	// ++ Test

	//resize image
	$app->get("/test/resize", [new TestController(), "resizeTest"]);

	$app->get("/test/logs", [new TestController(), "logs"]);

	//not found handler
	$app->notFound( function() use (&$app) {
		//$app->response->setStatusCode(404, "Not Found")->sendHeaders();
		$service = new WsCoreController();
		$service->serviceNotFound();
	});
};
