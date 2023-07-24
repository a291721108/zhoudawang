<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use OSS\Core\OssException;
use OSS\OssClient;
use OSS\Core\OssUtil;

class Uploading extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uploading:run {type?} {object?} {filePath?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试推送功能';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        //获取参数
        $input      = $this->arguments();
        $type       = $input['type'];
        $object     = $input['object'];
        $filePath   = $input['filePath'];

        $OssClient = $this->ossClient = new OssClient(
            config('alioss.AccessKeyId'),
            config('alioss.AccessKeySecret'),
            config('alioss.ENDPOINT'),
        );

        // 普通上传
        if ($type === '1'){
            self::uploadFile($OssClient,$object,$filePath);
        }

        // 分片上传
        if ($type === '2'){
            self::completeMultipartUpload($OssClient,$object,$filePath);
        }

    }

    public function uploadFile($OssClient,$object,$filePath){
        try{


            $OssClient->uploadFile(config('alioss.BucketName'), $object, storage_path($filePath));

            echo "成功";
        } catch(OssException $e) {

            Log::error(__FUNCTION__ . ": FAILED\n" . $e->getMessage() . "\n");
        }
    }

    public function completeMultipartUpload($OssClient,$object,$filePath){

        $initOptions = array(
            OssClient::OSS_HEADERS  => array(
                // 指定该Object被下载时的网页缓存行为。
                // 'Cache-Control' => 'no-cache',
                // 指定该Object被下载时的名称。
                // 'Content-Disposition' => 'attachment;filename=oss_download.jpg',
                // 指定该Object被下载时的内容编码格式。
                // 'Content-Encoding' => 'utf-8',
                // 指定过期时间，单位为毫秒。
                // 'Expires' => 150,
                // 指定初始化分片上传时是否覆盖同名Object。此处设置为true，表示禁止覆盖同名Object。
                //'x-oss-forbid-overwrite' => 'true',
                // 指定上传该Object的每个part时使用的服务器端加密方式。
                // 'x-oss-server-side-encryption'=> 'KMS',
                // 指定Object的加密算法。
                // 'x-oss-server-side-data-encryption'=>'SM4',
                // 指定KMS托管的用户主密钥。
                //'x-oss-server-side-encryption-key-id' => '9468da86-3509-4f8d-a61e-6eab1eac****',
                // 指定Object的存储类型。
                // 'x-oss-storage-class' => 'Standard',
                // 指定Object的对象标签，可同时设置多个标签。
                // 'x-oss-tagging' => 'TagA=A&TagB=B',
            ),
        );

        /**
         *  步骤1：初始化一个分片上传事件，并获取uploadId。
         */
        try{

            //返回uploadId。uploadId是分片上传事件的唯一标识，您可以根据uploadId发起相关的操作，如取消分片上传、查询分片上传等。
            $uploadId = $OssClient->initiateMultipartUpload(config('alioss.BucketName'), $object, $initOptions);
            print("initiateMultipartUpload OK" . "\n");
        } catch(OssException $e) {
            printf($e->getMessage() . "\n");
            return;
        }

        /*
         * 步骤2：上传分片。
         */
        $partSize = 10 * 1024 * 1024;
        $uploadFileSize = sprintf('%u',filesize($filePath));
        $pieces = $OssClient->generateMultiuploadParts($uploadFileSize, $partSize);
        $responseUploadPart = array();
        $uploadPosition = 0;
        $isCheckMd5 = true;
        foreach ($pieces as $i => $piece) {
            $fromPos = $uploadPosition + (integer)$piece[$OssClient::OSS_SEEK_TO];
            $toPos = (integer)$piece[$OssClient::OSS_LENGTH] + $fromPos - 1;
            $upOptions = array(
                // 上传文件。
                $OssClient::OSS_FILE_UPLOAD => $filePath,
                // 设置分片号。
                $OssClient::OSS_PART_NUM => ($i + 1),
                // 指定分片上传起始位置。
                $OssClient::OSS_SEEK_TO => $fromPos,
                // 指定文件长度。
                $OssClient::OSS_LENGTH => $toPos - $fromPos + 1,
                // 是否开启MD5校验，true为开启。
                $OssClient::OSS_CHECK_MD5 => $isCheckMd5,
            );

            // 开启MD5校验。
            if ($isCheckMd5) {
                $contentMd5 = OssUtil::getMd5SumForFile($filePath, $fromPos, $toPos);
                $upOptions[$OssClient::OSS_CONTENT_MD5] = $contentMd5;
            }
            try {
                // 上传分片。
                $responseUploadPart[] = $OssClient->uploadPart(config('alioss.BucketName'), $object, $uploadId, $upOptions);
                printf("initiateMultipartUpload, uploadPart - part#{$i} OK\n");
            } catch(OssException $e) {
                printf("initiateMultipartUpload, uploadPart - part#{$i} FAILED\n");
                printf($e->getMessage() . "\n");
                return;
            }

        }
        // $uploadParts是由每个分片的ETag和分片号（PartNumber）组成的数组。
        $uploadParts = array();
        foreach ($responseUploadPart as $i => $eTag) {
            $uploadParts[] = array(
                'PartNumber' => ($i + 1),
                'ETag' => $eTag,
            );
        }
        /**
         * 步骤3：完成上传。
         */
        $comOptions['headers'] = array(
            // 指定完成分片上传时是否覆盖同名Object。此处设置为true，表示禁止覆盖同名Object。
            // 'x-oss-forbid-overwrite' => 'true',
            // 如果指定了x-oss-complete-all:yes，则OSS会列举当前uploadId已上传的所有Part，然后按照PartNumber的序号排序并执行CompleteMultipartUpload操作。
            // 'x-oss-complete-all'=> 'yes'
        );

        try {
            // 执行completeMultipartUpload操作时，需要提供所有有效的$uploadParts。OSS收到提交的$uploadParts后，会逐一验证每个分片的有效性。当所有的数据分片验证通过后，OSS将把这些分片组合成一个完整的文件。
            $OssClient->completeMultipartUpload(config('alioss.BucketName'), $object, $uploadId, $uploadParts,$comOptions);
            printf( "Complete Multipart Upload OK\n");
        }  catch(OssException $e) {
            printf("Complete Multipart Upload FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
    }
}
