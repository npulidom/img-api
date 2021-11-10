<?php
/**
 * Test Controller
 */

use CrazyCake\Helpers\Images;

class TestController extends CoreController
{
	/**
	 * Logs action
	 */
	public function logs()
	{
		$file = STORAGE_PATH."logs/".date("d-m-Y").".log";

		if (!is_file($file)) die("No log file found.");

		ss(file_get_contents($file));
	}

	/**
	 * API resize test
	 */
	public function resizeTest()
	{
		// save a test image
		$image    = file_get_contents("http://www.gstatic.com/webp/gallery/1.jpg");
		$filename = "test-".time().".jpg";
		$filepath = self::UPLOAD_PATH.$filename;

		try   { file_put_contents($filepath, $image); }
		catch (\Exception | Exception $e) { ss("Test -> Image download exception", $e); }

		$config = [

			"filename" => $filename,
			"resize"   => [

				"L" => ["p" => 100, "q" => 95],
				"M" => ["p" => 50, "q" => 50],
				"S" => ["w" => 100,"q" => 95],
				"C" => ["p" => 60, "c" => [490, 220, 36, 20]]
			]
		];

		try {

			$response = Images::resize($filepath, $config["resize"]);
			// optimize
			$this->_optimizer($response);
		}
		catch (\Exception | Exception $e) {

			$response = $e->getMessage();
			$this->logger->error("TestController::resizeTest -> an error ocurred: $response");
		}

		// clean old files
		$this->_cleanFiles();

		$this->jsonResponse(200, $response);
	}
}
