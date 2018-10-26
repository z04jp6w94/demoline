<?php

use Aws\Credentials\CredentialProvider;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;

class s3 {

    private $_s3Client;
    private $_profileName;
    private $_path;
    private $_credentialProvider;
    private $_bucket;

    function __construct($region, $version, $path, $profileName, $bucket) {
        $this->_profileName = $profileName;
        $this->_path = $path;
        $this->_credentialProvider = CredentialProvider::ini($this->_profileName, $this->_path);
        $this->_credentialProvider = CredentialProvider::memoize($this->_credentialProvider);
        $this->_bucket = $bucket;

        $this->_s3Client = new S3Client([
            'region' => $region,
            'version' => $version,
            'credentials' => $this->_credentialProvider
        ]);
    }

    function __destruct() {
        
    }

    public function putObject($sourceFile, $keyFile) {
        /* Result Syntax of putObject
          [
          'ETag' => '<string>',
          'Expiration' => '<string>',
          'ObjectURL' => '<string>',
          'RequestCharged' => 'requester',
          'SSECustomerAlgorithm' => '<string>',
          'SSECustomerKeyMD5' => '<string>',
          'SSEKMSKeyId' => '<string>',
          'ServerSideEncryption' => 'AES256|aws:kms',
          'VersionId' => '<string>',
          ]
         */
        $result = $this->_s3Client->putObject([
            'Bucket' => $this->_bucket,
            'Key' => $keyFile,
            'SourceFile' => $sourceFile
        ]);
        return $result;
    }

    public function deleteObect($keyFile) {
        /* Result Syntax of deleteObect
          [
          'DeleteMarker' => true || false,
          'RequestCharged' => 'requester',
          'VersionId' => '<string>',
          ]
         */
        $result = $this->_s3Client->deleteObject([
            'Bucket' => $this->_bucket, // REQUIRED
            'Key' => $keyFile // REQUIRED
        ]);
        return $result;
    }

    public function deleteObjects($deleteObjects, $quiet = true) {
        /* Result Syntax of deleteObjects
          [
          'Deleted' => [
          [
          'DeleteMarker' => true || false,
          'DeleteMarkerVersionId' => '<string>',
          'Key' => '<string>',
          'VersionId' => '<string>',
          ],
          // ...
          ],
          'Errors' => [
          [
          'Code' => '<string>',
          'Key' => '<string>',
          'Message' => '<string>',
          'VersionId' => '<string>',
          ],
          // ...
          ],
          'RequestCharged' => 'requester',
          ]
         */
        $objects = array();
        $tObj = [];
        foreach ($deleteObjects as $value) {
            print_r($value);
            $tObj = ['Key' => $value];
            array_push($objects, $tObj);
        }

        $result = $this->_s3Client->deleteObjects([
            'Bucket' => $this->_bucket,
            'Delete' => [
                'Objects' => $objects,
                'Quiet' => $quiet,
            ],
        ]);
        return $result;
    }

}
