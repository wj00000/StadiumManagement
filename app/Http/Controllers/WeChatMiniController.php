<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Models\WeChatUser;
use App\Vo\ResultVo;
use Illuminate\Http\Request;

class WeChatMiniController extends Controller
{
    public function bookLogin(Request $request)
    {
        $code = $request->get('code', '');
        if (!$code) {
            throw new CustomException(50065, '登录失败!');
        }
        $iv            = $request->get('iv');
        $encryptedData = $request->get('encryptedData');
        $app           = app('wechat.mini_program.book');

        $sessions = $app->auth->session($code);
        $sessions = $app->encryptor->decryptData($sessions['session_key'], $iv, $encryptedData);

        $wechatUser = WechatUser::where('book_openid', $sessions['openId'])->first();
        if ($wechatUser) {
            $wechatUser->update([
                'book_openid' => $sessions['openId'],
                'nick_name'   => $sessions['nickName'],
                'avatar_url'  => $sessions['avatarUrl'],
            ]);
        } else {
            WechatUser::create([
                'phone'          => '',
                'book_openid'    => $sessions['openId'],
                'avatar_url'     => $sessions['avatarUrl'],
                'nick_name'      => $sessions['nickName'],
                'gender'         => $sessions['gender'],
                'country'        => $sessions['country'],
                'province'       => $sessions['province'],
                'city'           => $sessions['city'],
                'status'         => 1,
                'unionid'        => '',
                'offical_openid' => '',
            ]);
        }
        return ResultVo::success('登陆成功!',[]);
    }
}
