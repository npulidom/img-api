<?php
/**
 * CoreController: Main API controller
 */

use CrazyCake\Core\WsCore;
use CrazyCake\Helpers\Images;

class CoreController extends WsCore
{
	use S3Helper;

	/**
	 * Upload directory
	 */
	const UPLOAD_PATH = STORAGE_PATH."uploads/";

	/**
	 * Mozjpeg binary location
	 */
	const MOZJPEG_BIN = "/usr/local/bin/cjpeg";

	/**
	 * API index
	 */
	public function index()
	{
		$payload = $this->config->name." API v.".$this->config->version;

		$this->jsonResponse(200, $payload);
	}

	/**
	 * API resize - resize image, optimize, and push to s3
	 */
	public function resize()
	{
		// get post body json data
		$data = $this->request->getJsonRawBody();

		if (!isset($data->contents) || !isset($data->config))
			$this->jsonResponse(400, "Missing contents & config json props.");

		if (empty($data->config->filename) || empty($data->config->resize))
			$this->jsonResponse(400, "Missing filename/resize property config.");

		if (strpos($data->config->filename, "/") !== false)
			$this->jsonResponse(400, "Filename can't contains slashes.");

		try {

			$filepath = self::UPLOAD_PATH.$data->config->filename;

			file_put_contents($filepath, base64_decode($data->contents));

			$this->logger->debug("CoreController::resize -> resizing image [".strlen($data->contents)."]: ".json_encode($data->config->resize));

			// resize images
			$resized = Images::resize($filepath, $data->config->resize);
			// optimize images
			$this->_optimizer($resized);
			// push files to s3
			$response = $this->_pushFiles($filepath, $data->config);

		}
		catch (\Exception | Exception $e) {

			$response = $e->getMessage();

			$this->logger->error("CoreController::resize -> An error ocurred: $response");
		}

		// clean old files
		$this->_cleanFiles();

		$this->logger->debug("CoreController::resize -> sending response [".strlen($data->contents)."]: ".json_encode($response, JSON_UNESCAPED_SLASHES));

		$this->jsonResponse(200, $response);
	}

	/**
	 * API S3Push - push any file to S3
	 */
	public function s3push()
	{
		// get post body json data
		$data = $this->request->getJsonRawBody();

		if (!isset($data->contents) || !isset($data->config))
			$this->jsonResponse(400, "Missing contents & config json props.");

		if (empty($data->config->filename))
			$this->jsonResponse(400, "Missing filename property config.");

		if (strpos($data->config->filename , "/") !== false)
			$this->jsonResponse(400, "Filename can't contains slashes.");

		try {

			$filepath = self::UPLOAD_PATH.$data->config->filename;

			file_put_contents($filepath, base64_decode($data->contents));

			$this->logger->debug("CoreController::s3push -> pushing image to S3 [".strlen($data->contents)."] ".$data->config->s3->bucketName);

			// push files to s3
			$response = $this->_pushFiles($filepath, $data->config);

		}
		catch (\Exception | Exception $e) {

			$response = $e->getMessage();

			$this->logger->error("CoreController::s3push -> An error ocurred: $response");
		}

		// clean old files
		$this->_cleanFiles();

		$this->logger->debug("CoreController::s3push -> sending response [".strlen($data->contents)."]: ".json_encode($response, JSON_UNESCAPED_SLASHES));

		$this->jsonResponse(200, $response);
	}

	/**
	 * Optimizer task
	 * @param Array $files - Input files
	 */
	protected function _optimizer($files = [])
	{
		if (empty($files)) return false;

		// optimize images
		foreach ($files as $f) {

			// mozjpeg optimization
			$binary = shell_exec(self::MOZJPEG_BIN." -optimize ".escapeshellarg($f));

			if (!empty($binary)) file_put_contents($f, $binary);
		}
	}

	/**
	 * Push files to AWS S3
	 * @param String $filepath - The main image filepath
	 * @param Array $config - S3 config
	 */
	protected function _pushFiles($filepath, $config = [])
	{
		$this->initS3Helper((array)$config->s3);

		return $this->s3PutFiles($filepath, $config->push_source ?? true);
	}

	/**
	 * Remove files
	 */
	protected function _cleanFiles()
	{
		try {

			$files = scandir(self::UPLOAD_PATH);

			clearstatcache();

			foreach ($files as $f) {

				if (in_array($f, ['.', '..'])) continue;

				$now      = new \DateTime();
				$date     = new \DateTime(date("Y-m-d H:i:s", filemtime(self::UPLOAD_PATH.$f)));
				$interval = $date->diff($now);

				if ($interval->i >= 10) {

					unlink(self::UPLOAD_PATH.$f);
					$this->logger->debug("CoreController::_cleanFiles -> file removed ".self::UPLOAD_PATH.$f);
				}
			}
		}
		catch (\Exception | Exception $e) {

			$this->logger->error("CoreController::_cleanFiles -> exception: ".$e->getMessage());
		}
	}
}
