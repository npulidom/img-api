Image API Microservice
======================

Container service for image resize/compression with AWS S3 uploader.  
Uncompressed size: 229 MB.

## Image Libraries

- jpegoptim
- jpegtran
- pngquant

## Services

- `./resize` : Resize JPG or PNG images, optimize file and push it to AWS S3 bucket.
- `./s3Push` : Push any resource to AWS S3.

## Usage

Run Container [port **8080**]  
`docker run -p 8080:80 -d npulidom/img-api`

### PHP example
```php
<?php

	// set request body
	$body = json_encode([
		// encode image
		"contents" => base64_encode(file_get_contents("path/to/image.jpg")),
		// api config
		"config" => [
			// a filename
			"filename" => "MY_FILE_NAME.jpg",
			// resize options (each key will be appended to filename)
			"resize" => [
				// resize width to 500 px, height is auto-calculated (keep aspect ratio)
				"L" => ["w" => 500],
				// resize height to 500 px, width is auto-calculated (keep aspect ratio)
				"M" => ["h" => 100],
				// resize to 50% of current size
				"H" => ["p" => 50],
				// resize to 60% and then crop [width, height, x, y]
				"C" => ["p" => 60, "c" => [490, 220, 20, 20]],
				// blur and rotate 90 degrees
				"B" => ["b" => 60, "r" => 90]
			],
			// s3 options
			"s3" => [
				// required, your bucket name
				"bucketName" => "my-bucket",
				// required, a bucket path prefix, for ./ (root) leave empty
				"bucketBaseUri" => "backend/",
				// required, aws key
				"accessKey" => "MY_ACCESS_KEY",
				// required, aws secret
				"secretKey" => "MY_SECRET_KEY"
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
		CURLOPT_URL            => "http://imgapi/resize", // or ./s3Push
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

### Node Example
```javascript

// set request body
let data = {
	// encode image
	contents: Buffer.from(binary, 'binary').toString('base64'),
	// api config
	config: {
		// a filename
		filename: "MY_FILE_NAME.jpg",
		// resize options (each key will be appended to filename)
		resize: {
			// resize width to 500 px, height is auto-calculated (keep aspect ratio)
			L: { w: 500 },
			// resize height to 500 px, width is auto-calculated (keep aspect ratio)
			M: { h: 100 },
			// resize to 50% of current size
			H: { p: 50 },
			// resize to 60% and then crop [width, height, x, y]
			C: { p: 60, c: [490, 220, 20, 20] },
			// blur and rotate 90 degrees
			B: { b: 60, r: 90 }
		},
		//s3 options
		s3: {
			// required, your bucket name
			bucketName: "my-bucket",
			// required, a bucket path prefix, for ./ (root) leave empty
			bucketBaseUri: "backend/",
			// required, aws key
			accessKey: "MY_ACCESS_KEY",
			// required, aws secret
			secretKey: "MY_SECRET_KEY"
		}
	}
}

// request
try {

	let response = await axios({ method: "post", url: "http://imgapi/resize", data })

	console.log("response", response.data)
}
catch (e) { console.error("request error", e) }
```
