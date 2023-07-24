<?php

namespace App\services;

use OSS\OssClient;
use OSS\Core\OssException;

class Oss
{
    private $ossClient;
    private $bucketName;

    /**
    *   私有化
     */
    public function __construct()
    {
        $this->bucketName = config('alioss.BucketName');

        $this->ossClient = new OssClient(
            config('alioss.AccessKeyId'),
            config('alioss.AccessKeySecret'),
            config('alioss.ENDPOINT')
        );
    }

    /**
    * 列出所有存储空间
     */
    public function listBuckets()
    {
        try {
            $bucketListInfo = $this->ossClient->listBuckets();
            $bucketList = $bucketListInfo->getBucketList();

            $buckets = [];
            foreach ($bucketList as $bucket) {
                $buckets[] = [
                    'getLocation' => $bucket->getLocation(),
                    'getName' => $bucket->getName(),
                    'getCreatedate' => $bucket->getCreatedate(),
                ];
            }

            return $buckets;
        } catch (OssException $e) {
            // 处理异常情况
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 存储空间添加
     */
    public function createBucket($bucket)
    {
        try {
            // 设置Bucket的存储类型为低频访问类型，默认是标准类型。
            $options = array(
                OssClient::OSS_STORAGE => OssClient::OSS_STORAGE_IA
            );

            if ($this->ossClient->doesBucketExist($bucket)) {
                return 'space_already_exists';

            }
            // 设置Bucket的读写权限为公共读，默认是私有读写。
            $this->ossClient->createBucket($bucket, OssClient::OSS_ACL_TYPE_PUBLIC_READ, $options);

            return true;
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return false;
        }
    }

    /**
     * 删除存储空间
     */
    public function deleteBucket($bucket)
    {
        try{
            $this->ossClient->deleteBucket($bucket);
            return true;
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return false;
        }
    }
}
