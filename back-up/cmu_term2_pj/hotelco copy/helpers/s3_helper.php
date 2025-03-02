<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

function getHotelImageUrl($hotelName, $imagePath, $expiry = "+10 minutes") {
    $s3Config = require 'config/s3.php';
    $bucketName = $s3Config['bucket'];

    try {
        $s3 = new S3Client([
            'region'      => $s3Config['region'],
            'version'     => 'latest',
            'credentials' => $s3Config['credentials'],
        ]);

        // Ensure we are using the correct S3 Key
        $fileKey = $imagePath; // Directly use the correct MySQL path

        // Generate Pre-Signed URL
        $cmd = $s3->getCommand('GetObject', [
            'Bucket' => $bucketName,
            'Key'    => $fileKey,
        ]);

        $request = $s3->createPresignedRequest($cmd, $expiry);
        return (string)$request->getUri();

    } catch (AwsException $e) {
        return "Error: " . $e->getMessage();
    }
}