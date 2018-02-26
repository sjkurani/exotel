<?php
// Include the SDK using the Composer autoloader
require './aws/aws-autoloader.php';
//$config = require 'config.php';

//Namespaces
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;

//[Configurations]
$bucket = "BUCKET_NAME";
$key = 'YOUR_KEY';
$secret = 'YOUR_SECRET';
$file_Path = 'files/abc.txt'; //Uploadable file path.

// Create an SDK class used to share configuration across clients.
$sharedConfig = [
    'region'  => 'us-west-2',
    'version' => 'latest'
];
$sdk = new Aws\Sdk($sharedConfig);

// Create an Amazon S3 client using the shared configuration data.
$client = new Aws\S3\S3Client([
    'version'     => $sharedConfig['version'],
    'region'      => $sharedConfig['region'],
    'credentials' => [
        'key'    => $key,
        'secret' => $secret
    ]
]);

//Create Bucket Dynamically.

/*  try {
    //Create Bucket.
    $result1 = $client->createBucket([
        'Bucket' => $bucket,
        'ACL' => 'public-read'
    ]);
    }

    catch (S3Exception $e) {
     echo $e->getMessage() . "\n";
    }*/

//Upload file abc.txt from /files folder without setting any headers.

$uploadable_file_name = md5(uniqid());

$uploader = new MultipartUploader($client, $file_Path, [
    'bucket' => $bucket,
    'key'    => $uploadable_file_name,
]);

try {
    $result = $uploader->upload();
    display_me("Upload complete with default http header and metadata: {$result['ObjectURL']}");
} catch (MultipartUploadException $e) {
    echo $e->getMessage() . "\n";
}

//Upload Same file abc.txt from /files folder without setting any headers.

try {
    //Put object in bucket.
    $result2 = $client->putObject([
        'Bucket'     => $bucket,
        'Key'        => $key,
        'SourceFile' => $file_Path,
        'ContentType'  => 'text/html',
        'ACL'          => 'public-read',
        'StorageClass' => 'REDUCED_REDUNDANCY',
        'Metadata'     => array(    
            'param1' => 'value 1',
            'param2' => 'value 2'
        )
    ]);
    display_me("Upload complete with extra http header and metadata: {$result2['ObjectURL']}");

} 
catch (S3Exception $e) {
    echo $e->getMessage() . "\n";
}


// Use the plain API (returns ONLY up to 1000 of your objects).
$result = $client->listObjects(array('Bucket' => $bucket));

foreach ($result['Contents'] as $object) {
    display_me($object);
}

function display_me($msg){
    echo "<pre>";
    print_r($msg);
    echo "</pre>";

}
/*
echo "<pre>";
print_r($result1);
print_r($result2);
print_r($result3);
echo "</pre>";
