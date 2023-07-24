<?php

namespace App\Http\Controllers\Common;

use App\Exceptions\ErrorCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{

    public function success($data = [])
    {
        return response()->json([
                'status' =>  true,
                'code'  =>  200,
                'message' =>  config('errorcode.code')[200],
                'data'  =>  $data,
            ]);
    }

    public function error($code, $data = [])
    {
        return response()->json([
                'status' =>  false,
                'code'  =>  $code,
                'message' =>  config('errorcode.code')[(int) $code],
                'data'  =>  $data,
            ]);
    }
}
