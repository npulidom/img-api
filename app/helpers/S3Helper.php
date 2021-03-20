<?php
/**
 * S3Helper trait
 * Requires tpyo/amazon-s3-php-class
 */

trait S3Helper
{
	/**
	 * Amazon S3 URL
	 * @var String
	 */
	protected static $AMAZON_S3_URL = "https://{bucketName}.s3.amazonaws.com/";

	/**
	 * AWS S3 helper
	 * @var Object
	 */
	protected $s3;

	/**
	 * Bucket Region (default us-east-1)
	 * @var String
	 */
	protected $bucket_region;

	/**
	 * Bucket Name
	 * @var String
	 */
	protected $bucket_name;

	/**
	 * Bucket Base URI path
	 * @var String
	 */
	protected $bucket_base_uri;

	/**
	 * Init Helper
	 * @param Array $config - AWS S3 config
	 * @param + access_key - AWS Access Key
	 * @param + secret_key - AWS Secret Key
	 * @param + bucket_name - AWS Bucket Name
	 * @param + bucket_base_uri - AWS Bucket Base Uri
	 */
	protected function initS3Helper($config = [])
	{
		$config = array_merge([
			"accessKey"     => "",
			"secretKey"     => "",
			"bucketRegion"  => "",
			"bucketName"    => "",
			"bucketBaseUri" => ""
		], $config);

		$this->bucket_region   = $config["bucketRegion"];
		$this->bucket_name     = $config["bucketName"];
		$this->bucket_base_uri = $config["bucketBaseUri"];

		$this->s3 = new S3($config["accessKey"], $config["secretKey"]);

		//$this->logger->debug("S3Helper-> initS3Helper: ".json_encode($config));
	}

	/**
	 * Push files to S3
	 * @param  String $src_file - The main file path
	 * @param  Boolean $push_source- push source
	 * @return Array
	 */
	protected function s3PutFiles($src_file = "", $push_source = true)
	{
		if (empty($src_file))
			return false;

		$uploaded = [];
		$pinfo    = pathinfo($src_file);
		$src      = $pinfo["dirname"]."/";
		$subfiles = preg_grep('/^([^.])/', scandir($src));

		// set bucket URL
		$bucker_url = str_replace("{bucketName}", $this->bucket_name, self::$AMAZON_S3_URL);

		// append region?
		if (!empty($this->bucket_region)) $bucker_url = str_replace(".s3.", ".s3.".$this->bucket_region.".", $bucker_url);

		foreach ($subfiles as $f) {

			// match source
			if (strpos($f, $pinfo["filename"]) === false)
				continue;

			// keep source?
			if (!$push_source && $src.$f == $src_file)
				continue;

			$bucket_path = $this->bucket_base_uri.$f;

			// upload files to S3
			$this->s3Put($src.$f, $bucket_path);

			$uploaded[] = $bucker_url.$bucket_path;
		}

		return $uploaded;
	}

	 /**
	 * Push a object to AWS S3
	 * @param String $file - The file path
	 * @param String $dest_uri - The s3 destination path
	 * @param Boolean $private - Flag for private file
	 */
	protected function s3Put($file = "", $dest_uri = "", $private = false)
	{
		$private = $private ? S3::ACL_PRIVATE : S3::ACL_PUBLIC_READ;

		try {

			return S3::putObject(S3::inputFile($file, false), $this->bucket_name, $dest_uri, $private, [], ["Cache-Control" => "max-age=31536000"]);
		}
		catch (\S3Exception $e) {

			throw new Exception("S3Helper::s3Put -> resource [$file], exception: ".$e->getMessage());
		}
	}
}
