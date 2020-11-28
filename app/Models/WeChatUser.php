<?php

namespace App\Models;

use App\Traits\UserTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class WeChatUser extends Authenticatable
{
    use HasApiTokens, Notifiable, UserTrait;

    protected $table = 'wechat_users';

    protected $guarded = [];

    const STATUS_DISABLE = 0;
    const STATUS_ENABLE = 1;

    const STATUS_MAP = [
        self::STATUS_ENABLE  => '启用',
        self::STATUS_DISABLE => '禁用',
    ];

    const LOGIN_TYPE_BOOK_WECHAT = 1;

    const LOGIN_TYPE_MAP = [
        self::LOGIN_TYPE_BOOK_WECHAT => '书城微信登录'
    ];

    /**
     * 微信昵称转码还原
     * @param $nickName
     * @return string|boolean
     */
    public function getNickNameAttribute($nickName)
    {
        return base64_decode($nickName);
    }

    /**
     * 微信昵称转码
     * @param $nickName
     */
    public function setNickNameAttribute($nickName)
    {
        $this->attributes['nick_name'] = base64_encode($nickName);
    }

    /**
     * 微信昵称转码还原
     * @return string|boolean
     */
    public function getBookTokenAttribute()
    {
        return null;
    }

}
