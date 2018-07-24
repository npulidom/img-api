<?php
/**
 * Test Controller
 * @author Nicolas Pulido <nicolas.pulido@crazycake.cl>
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
		ss(file_get_contents($this->logger->getPath()));
	}

	/**
	 * Logs action
	 */
	public function libraries()
	{
		// new optimizer
		$factory = new Optimizer(self::OPTIMIZER_OPTIONS);

		$jpegoptim = $factory->get('jpegoptim');
		$jpegtran  = $factory->get('jpegtran');

		ss($jpegoptim, $jpegtran);
	}

	/**
	 * API resize test
	 */
	public function resizeTest()
	{
		// save a test image
		$image    = file_get_contents("http://www.gstatic.com/webp/gallery/1.jpg");
		$filepath = self::UPLOAD_PATH."test.jpg";

		file_put_contents($filepath, $image);

		$config = [
			"filename" => "test.jpg",
			"resize" => [
				"L"  => ["p" => 100, "q" => 95],
				"M"  => ["p" => 50, "q" => 50],
				"S"  => ["w" => 100,"q" => 95],
				"C"  => ["p" => 60, "c" => [490, 220, 36, 20]]
			]
		];

		try {
			// resize images
			$response = Images::resize($filepath, $config["resize"]);
			// optimize images
			$this->_optimizer($response);
		}
		catch(\Exception | Exception $e) {

			$response = $e->getMessage();
			$this->logger->error("TestController::resizeTest -> An error ocurred: $response");
		}

		$this->jsonResponse(200, $response);
	}
}
