<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Common\BaseController;
use App\Log\CustomErrorLog;
use App\Models\User;
use App\services\Oss;
use Illuminate\Http\Request;


use OSS\Core\OssException;
use OSS\OssClient;

class UserController extends BaseController
{

    /**
     * 私有初始化
     */
    private $aliyunOssService;
    private $errorLog;


    public function __construct(Oss $aliyunOssService)
    {
        $this->aliyunOssService = $aliyunOssService;

        // 指定错误日志文件路径
        $logFilePath = storage_path('logs/user_errors.log');

        // 实例化自定义错误日志类
        $this->errorLog = new CustomErrorLog($logFilePath);
    }

    public function index()
    {
        $data = [
            'id'=>1,
            'name'  =>'周一飞',
        ];
        return $this->error(400);
        return $this->success($data);

    }

    public function store(Request $request)
    {
        // 创建新用户
        echo "创建新用户".$request->id;
    }

    public function show($id,$name)
    {
        // 记录错误日志
        $errorMessage = '错误日志记录.';
        $this->errorLog->error($errorMessage, ['data' => ['id'=>1,'user'=>2]]);
        dd('213');
        die();
        if ($id == 1){
            $users = User::find($id)->get()->toArray();

            return $this->success('home',200,$users);
        }else{
            return $this->error('error');
        }

    }

    public function update(Request $request, $id)
    {
        // 更新特定用户
        echo "更新特定用户".$id;
    }

    public function destroy($id)
    {
        // 删除特定用户
        echo "删除特定用户".$id;
    }

    /**
     * 上传
     */
    public function uploading()
    {

        $object = "test.txt";
        $filePath = storage_path('1.txt');

        try{

            $this->OssClient->uploadFile($this->BucketName, $object, $filePath);
            return $this->success('success');
        } catch(OssException $e) {

            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
    }

    /**
     * 下载
     */
    public function download()
    {

        $object = "test.txt";
        $localfile = storage_path('test.txt');;
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $localfile
        );

        try{

            $this->OssClient->getObject($this->BucketName, $object, $options);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
    }



    public function listBuckets(Request $request)
    {
        //创建存储空间
        $createBucket = $this->aliyunOssService->createBucket($request->bucket);

        //列出存储空间
//        $buckets = $this->aliyunOssService->listBuckets();
        //删除存储空间
//        $deleteBucket = $this->aliyunOssService->deleteBucket($request->bucket);



        if ($createBucket == 'success'){
            return $this->success('success',200, $createBucket);
        }else{
            return $this->error($createBucket);

        }

    }
}

