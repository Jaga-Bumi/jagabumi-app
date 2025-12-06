<?php

namespace App\Services;

use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;

class FilebaseService
{
    protected $client;
    protected $bucket;

    public function __construct()
    {
        $this->client = new S3Client([
          'version' => 'latest',
          'region'  => config('filesystems.disks.filebase.region'),
          'endpoint' => config('filesystems.disks.filebase.endpoint'),
          'use_path_style_endpoint' => true,
          'credentials' => [
              'key'    => config('filesystems.disks.filebase.key'),
              'secret' => config('filesystems.disks.filebase.secret'),
          ],
        ]);

        $this->bucket = config('filesystems.disks.filebase.bucket');
    }

    // Upload file/json dan kembalikan CID IPFS
    public function uploadToIpfs($content, $filename, $contentType)
    {
        try {
            $result = $this->client->putObject([
                'Bucket' => $this->bucket,
                'Key'    => $filename,
                'Body'   => $content,
                'ContentType' => $contentType,
                'ACL'    => 'public-read',
                'Metadata'    => [
                    'project' => 'JagaBumi', 
                ],
            ]);

            $cid = $this->extractCidFromHeaders($result['@metadata']['headers'] ?? []);

            if (!$cid) {
                Log::warning("CID missing in initial response for $filename. Retrying via HeadObject...");
                sleep(1); 
                $cid = $this->fetchCidViaHeadObject($filename);
            }

            if (!$cid) {
                throw new \Exception("Filebase processed the file but did not return a CID.");
            }

            return $cid;

        } catch (AwsException $e) {
            Log::error("Filebase Upload Error: " . $e->getMessage());
            throw new \Exception("Gagal upload ke IPFS: " . $e->getMessage());
        }
    }

    protected function fetchCidViaHeadObject($filename)
    {
        try {
            $result = $this->client->headObject([
                'Bucket' => $this->bucket,
                'Key'    => $filename,
            ]);

            return $this->extractCidFromHeaders($result['@metadata']['headers'] ?? []);
        } catch (AwsException $e) {
            Log::error("HeadObject failed for $filename: " . $e->getMessage());
            return null;
        }
    }

    protected function extractCidFromHeaders(array $headers)
    {
        return $headers['x-amz-meta-cid'] 
            ?? $headers['X-Amz-Meta-Cid'] 
            ?? null;
    }
}