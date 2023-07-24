<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redis;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function mysql(Request $request){
        echo "123";die();

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $users = User::find($request->id)->get()->toArray();
        dd($users);
    }

    public function redis(){

        Redis::set('name', '周一飞');
        $values = Redis::get('name');

        dd($values);
    }

}
