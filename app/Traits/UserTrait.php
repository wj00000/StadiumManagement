<?php


namespace App\Traits;

use App\Models\WeChatUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use League\OAuth2\Server\Exception\OAuthServerException;

trait UserTrait
{
    public function findForPassport($username)
    {
        $loginType = Request::get('type', WeChatUser::LOGIN_TYPE_BOOK_WECHAT);
        $user      = $this->where('book_openid', $username)->first();

        if (!$user) {
            throw new OAuthServerException('用户不存在！', 6, 'invalid_credentials', 401);
        }
        if ($loginType == WeChatUser::LOGIN_TYPE_BOOK_WECHAT && $user->status == WeChatUser::STATUS_DISABLE) {
            throw new OAuthServerException('用户已被禁止登陆！', 6, 'invalid_credentials', 401);
        }
        $user->loginType = $loginType;
        return $user;
    }

    public function validateForPassportPasswordGrant($password)
    {
        switch ($this->loginType) {
            case WeChatUser::LOGIN_TYPE_BOOK_WECHAT:
                break;
            default:
                return FALSE;
        }
        return TRUE;
    }


}
