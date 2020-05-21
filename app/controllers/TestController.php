<?php
/**
 * Test Controller
 */

use \ImageOptimizer\OptimizerFactory as Optimizer;

use CrazyCake\Helpers\Images;


class TestController extends CoreController
{
	/**
	 * Logs action
	 */
	public function logs()
	{
		if (APP_ENV == "production") die("Not Found");

		ss(file_get_contents($this->logger->getPath()));
	}

	/**
	 * Logs action
	 */
	public function libraries()
	{
		if (APP_ENV == "production") die("Not Found");

		// new optimizer
		$factory = new Optimizer(self::OPTIMIZER_OPTIONS);

		$jpegoptim = $factory->get("jpegoptim");
		$jpegtran  = $factory->get("jpegtran");

		ss($jpegoptim, $jpegtran);
	}

	/**
	 * API resize test
	 */
	public function resizeTest()
	{
		if (APP_ENV == "production") die("Not Found");

		// save a test image
		$image    = file_get_contents("http://www.gstatic.com/webp/gallery/1.jpg");
		$filepath = self::UPLOAD_PATH."test.jpg";

		try   { file_put_contents($filepath, $image); }
		catch (\Exception | Exception $e) { ss("Test -> Image download exception", $e); }

		$config = [

			"filename" => "test.jpg",
			"resize"   => [

				"L"  => ["p" => 100, "q" => 95],
				"M"  => ["p" => 50, "q" => 50],
				"S"  => ["w" => 100,"q" => 95],
				"C"  => ["p" => 60, "c" => [490, 220, 36, 20]]
			]
		];

		try {

			$response = Images::resize($filepath, $config["resize"]);

			$this->_optimizer($response);
		}
		catch (\Exception | Exception $e) {

			$response = $e->getMessage();
			$this->logger->error("TestController::resizeTest -> an error ocurred: $response");
		}

		$this->jsonResponse(200, $response);
	}
}
