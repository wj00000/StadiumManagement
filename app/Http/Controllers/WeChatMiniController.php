<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Models\WeChatUser;
use Illuminate\Http\Request;

class WeChatMiniController extends Controller
{
    public function login(Request $request)
    {
        $code = $request->get('code', '');
        if (!$code) {
            throw new CustomException(50065, '登录失败!');
        }
        $iv            = $request->get('iv');
        $encryptedData = $request->get('encryptedData');
        $app           = app('wechat.mini_program.book');

        $sessions = $app->auth->session($code);
        if (!isset($sessions['unionid'])) {
            $sessions['unionid'] = $app->encryptor->decryptData($sessions['session_key'], $iv, $encryptedData)['unionId'];
        }
        if (!isset($sessions['openid']) || !isset($sessions['unionid'])) {
            throw new CustomException(50066, '登录失败!');
        }
        $wechatUser = WechatUser::where('unionid', $sessions['unionid'])->first();
        if (!$wechatUser) {
            $wechatUser->update([
                'book_openid' => $sessions['opendid'],
                'nick_name'   => $sessions['nick_name'],
                'avatar_url'  => $sessions['avatar_url'],
            ]);
        } else {
            WechatUser::create([
                'nick_name'      => $sessions['nick_name'],
                'avatar_url'     => $sessions['avatar_url'],
                'phone'          => '',
                'gender'         => $sessions['gender'],
                'status'         => 1,
                'unionid'        => $sessions['unionid'],
                'offical_openid' => $sessions['offical_openid'],
                'book_openid'    => $sessions['openid'],
                'country'        => $sessions['country'],
                'province'       => $sessions['province'],
                'city'           => $sessions['city'],
            ]);
        }
    }
}
