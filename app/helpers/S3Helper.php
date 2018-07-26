<?php
/**
 * S3Helper trait.
 * Requires tpyo/amazon-s3-php-class
 * @author Nicolas Pulido <nicolas.pulido@crazycake.cl>
 */

trait S3Helper
{
	/**
	 * Amazon S3 URL
	 * @var string
	 */
	protected static $AMAZON_S3_URL = "https://s3.amazonaws.com/";

	/**
	 * AWS S3 helper
	 * @var object
	 */
	protected $s3;

	/**
	 * Bucket Name
	 * @var string
	 */
	protected $bucket_name;

	/**
	 * Bucket Base URI path
	 * Ends with /
	 * @var string
	 */
	protected $bucket_base_uri;

	/**
	 * Init Helper
	 * @param array $config - AWS S3 config
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
			"bucketName"    => "",
			"bucketBaseUri" => ""
		], $config);

		$this->bucket_name     = $config["bucketName"];
		$this->bucket_base_uri = $config["bucketBaseUri"];

		$this->s3 = new S3($config["accessKey"], $config["secretKey"]);
	}

	/**
	 * Push files to S3
	 * @param  string $filepath - The main file path
	 * @return array
	 */
	protected function s3PutFiles($filepath = "")
	{
		if (empty($filepath))
			return false;

		$uploaded = [];
		$pinfo    = pathinfo($filepath);
		$src      = $pinfo["dirname"]."/";
		$subfiles = preg_grep('/^([^.])/', scandir($src));
		//ss("pinfo:", $pinfo);

		$bucker_url = self::$AMAZON_S3_URL.$this->bucket_name."/";

		foreach ($subfiles as $f) {

			if (strpos($f, $pinfo["filename"]) === false)
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
	 * @param string $file - The file path
	 * @param string $dest_uri - The s3 filepath uri
	 * @param boolean $private - Flag for private file
	 */
	protected function s3Put($file = "", $dest_uri = "", $private = false)
	{
		$private = $private ? S3::ACL_PRIVATE : S3::ACL_PUBLIC_READ;

		try {
			return S3::putObject(S3::inputFile($file, false), $this->bucket_name, $dest_uri, $private);
		}
		catch (\S3Exception $e) {
			throw new Exception("S3Helper::put -> resource [$file], exception: ".$e->getMessage());
		}
	}

	/**
	 * Get an object
	 * @param string $dest_uri - The s3 filepath uri
	 * @param boolean $parse_body - Return only the binary content
	 * @return object
	 */
	protected function s3Get($dest_uri = "", $parse_body = false)
	{
		try {
			$object = S3::getObject($this->bucket_name, $dest_uri);
		}
		catch (\S3Exception $e) {
			throw new Exception("S3Helper::get -> resource [$file], exception: ".$e->getMessage());
		}

		if ($object && $parse_body)
			$object = $object->body;

		return $object;
	}

	/**
	 * Deletes an object from storage
	 * @param string $file - The filename
	 * @return boolean
	 */
	protected function s3Delete($file = "")
	{
		try {
			return S3::deleteObject($this->bucket_name, $file);
		}
		catch (\S3Exception $e) {
			throw new Exception("S3Helper::delete -> resource [$file], exception: ".$e->getMessage());
		}
	}

	/**
	  * Copies an object from bucket
	  * @param string $file - The origin filename
	  * @param string $bucket_dest_uri - The bucket uri destination
	  * @param string $save_name - The bucket file save name
	  * @return boolean
	  */
	protected function s3Copy($file = "", $bucket_dest_uri = null, $save_name = "file")
	{
		try {

			if (empty($bucket_dest_uri))
				$bucket_dest_uri = $this->bucket_name;

			return S3::copyObject($this->bucket_name, $file, $bucket_dest_uri, $save_name);
		}
		catch (\S3Exception $e) {
			throw new Exception("S3Helper::copy -> resource [$file], exception: ".$e->getMessage());
		}
	}
}
