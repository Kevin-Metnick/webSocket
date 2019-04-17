<?php
namespace app\Http;

use app\model\User;

/**
 * 登录
 * @author kevin knmpby@163.com 19-4-17
 */
class Login
{
    /**
     * 用户登录
     */
    public function loginUp()
    {
       $user =User::getId(['id'=>1]);
       if ($user){
           $token = json_encode(['token'=>$user['token']]);
            return $token;
       }
    }


}