<?php
/**
 * Phalcon App Routes files
 * @author Nicolas Pulido <nicolas.pulido@crazycake.cl>
 */

return function($app) {

	//welcome message
	$app->get('/', [new WsCoreController(), "welcome"]);

	//resize image
	$app->post("/resize", [new WsCoreController(), "resize"]);

	//push file
	$app->post("/s3push", [new WsCoreController(), "s3push"]);

	// ++ Test

	//resize image
	$app->get("/test/resize", [new WsTestController(), "resizeTest"]);

	$app->get("/test/logs", [new WsTestController(), "logs"]);

	//not found handler
	$app->notFound( function() use (&$app) {
		//$app->response->setStatusCode(404, "Not Found")->sendHeaders();
		$service = new WsCoreController();
		$service->serviceNotFound();
	});
};
