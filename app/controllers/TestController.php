<?php
/**
 * Test Controller
 * @author Nicolas Pulido <nicolas.pulido@crazycake.cl>
 */

use CrazyCake\Helpers\Images;

class TestController extends CoreController
{
	/**
	 * Logs action
	 */
	public function logs()
	{
		sd(file_get_contents($this->logger->getPath()));
	}

	/**
	 * API resize test
	 */
	public function resizeTest()
	{
		//save a test image
		$image    = file_get_contents("http://www.gstatic.com/webp/gallery/1.jpg");
		$filepath = self::UPLOAD_PATH."test.jpg";

		file_put_contents($filepath, $image);

		$config = [
			"filename" => "test.jpg",
			"resize" => [
				"L"  => ["w" => 500],
				"M"  => ["p" => 50, "q" => 50],
				"S"  => ["w" => 100],
				"C"  => ["p" => 60, "c" => [490, 220, 36, 20]],
			]
		];

		try {
			//resize images
			$response = Images::resize($filepath, $config["resize"]);
			// optimize images
			$this->_optimizer($response);
		}
		catch(\Exception | Exception $e) {

			$response = $e->getMessage();
			$this->logger->error("WsCoreController::resizeTest -> An error ocurred: $response");
		}

		$this->jsonResponse(200, $response);
	}
}
