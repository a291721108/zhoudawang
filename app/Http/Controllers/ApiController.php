<?php

namespace App\Http\Controllers;

use App\Api\Api;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ApiController extends Controller
{
    use Api;

    public function index(Request $request)
    {

        $users = User::all();
        return $this->success($users, config('errorcode.success'));
    }

    public function store(Request $request)
    {
        $dataArray = $request->input('data_array');
        $id = $dataArray['id'];
        $name = $dataArray['name'];

        //数据库插入
        $data = true;
        if ($data){
            return $this->created('成功');
        }
        //数据库插入
//        $data = true;
//        if ($data){
//            return $this->created('插入成功');
//        }
//        return $this->notFond('数据库添加失败');
    }

    public function show(Request $request)
    {
        $data = $request->all();
        $user = User::find($data['id']);
        return $this->success($user, '查找成功');
    }

    public function update(Request $request , $id)
    {
        // 根据 $id 更新用户数据
        $data = $id;
        if ($data == 1){
            return $this->success(null, 'OK');

        }
    }

    public function destroy(Request $request, $id)
    {
        // 根据 $id 删除用户

        // 生成成功响应
        return $this->success(null, '删除成功');
    }
}
