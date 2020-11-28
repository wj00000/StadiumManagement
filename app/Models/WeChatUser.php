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

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 1;

    const STATUS_MAP = [
        self::STATUS_ENABLE  => '启用',
        self::STATUS_DISABLE => '禁用',
    ];

    const LOGIN_TYPE_BOOK_WECHAT = 1;

    const LOGIN_TYPE_MAP = [
        self::LOGIN_TYPE_BOOK_WECHAT => '书城微信登录'
    ];
}
