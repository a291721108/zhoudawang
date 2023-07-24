<?php

namespace App\Exceptions;

class ErrorCode
{
    /**
     * 错误数组
     * @var array|string[]
     */
    public  $errorCode = [
        'success'                 => "成功",
        'error'                   => "失败",


        'space_already_exists'      => "存储空间已存在",
    ];

    /**
     * 返回错误
     * @param $code
     * @return mixed|string
     */
    public function getErrorMsg($code)
    {
        $code == 'false' ?? $code = 'error';
        return $this->errorCode[$code];
    }


    /**
     * 返回成功
     * @param $code
     * @return mixed|string
     */
    public function getSuccessMsg($code)
    {
        $code == 'true' ?? $code = 'success';
        return $this->errorCode[$code];
    }

}
