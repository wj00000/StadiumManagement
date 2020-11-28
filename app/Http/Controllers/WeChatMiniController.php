<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Resources\WechatUser\WeChatUserResource;
use App\Models\WeChatUser;
use App\Vo\ResultVo;
use GuzzleHttp\Client;
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
            $wechatUser = WechatUser::create([
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
        $token                    = $this->getToken($wechatUser);
        $wechatUser               = $wechatUser->toArray();
        $wechatUser['book_token'] = $token;
        return ResultVo::success('登陆成功!', $wechatUser);
    }

    public function getToken($weChatUser)
    {
        $http     = new Client();
        $response = $http->post(config('app.url') . '/oauth/token', [
            'form_params' => [
                'username'      => $weChatUser->book_openid,
                'password'      => '123456',
                'grant_type'    => 'password',
                'client_id'     => env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID', 2),
                'client_secret' => env('YYY9QamCBw49bMRHvHPe0vvtdn9JyLbWTebcprRP', 'YYY9QamCBw49bMRHvHPe0vvtdn9JyLbWTebcprRP'),
                'provider'      => 'users',
                'type'          => WeChatUser::LOGIN_TYPE_BOOK_WECHAT,
            ],
        ]);
        return json_decode((string)$response->getBody(), true);
    }
}
