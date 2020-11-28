<?php

namespace App\Http\Resources\WechatUser;

use App\Models\WeChatUser;
use Illuminate\Http\Resources\Json\JsonResource;

class WeChatUserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'nick_name'  => $this->nick_name,
            'avatar_url' => $this->avatar_url,
            'status'     => [
                'code'     => $this->status,
                'describe' => WeChatUser::STATUS_MAP[$this->status],
            ],
            'book_token'   => $this->book_token,
        ];
    }
}
