Image API Microservice
=======================

Container service for image resize/compression with AWS S3 uploader.  
Uncompressed size: 202 Mb.

## Services

- `./resize` : Resize JPG images (PNG soon), optimize file and push it to AWS S3 bucket.
- `./s3Push` : Push any resource to AWS S3.

## Usage

Run Container [port **8080**]  
`docker run -p 8080:80 -d npulidom/img-api`

### PHP example
```php
<?php

	// set request body
	$body = json_encode([
		"contents" => base64_encode(file_get_contents("path/to/image.jpg")),
		"config"   => [
			"filename" => "MY_FILE_NAME.jpg",
			"s3" => [
				"bucketName"    => "my-bucket",
				"bucketBaseUri" => "backend/",
				"accessKey"     => "MY_ACCESS_KEY",
				"secretKey"     => "MY_SECRET_KEY"
			]
		]
	]);

	// set request headers
	$headers = [
		"Content-Type: application/json",
		"Content-Length: ".strlen($body),
	];

	// curl options
	$options = [
		CURLOPT_URL            => http://imgapi/resize,
		CURLOPT_PORT           => 80,
		CURLOPT_POST           => 1,
		CURLOPT_POSTFIELDS     => $body,
		CURLOPT_HTTPHEADER     => $headers,
		CURLOPT_RETURNTRANSFER => true
	];

	// curl request
	$ch = curl_init();
	curl_setopt_array($ch, $options);
	$result = curl_exec($ch);
	curl_close($ch);

	// print result
	print_r(json_decode($result, true));
?>
```
