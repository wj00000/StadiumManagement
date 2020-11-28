<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use EloquentFilter\Filterable;

/**
 *  基类model
 * Class BaseModel
 *
 * @package App\Models
 */
class BaseModel extends Model
{

    use Filterable;
    /**
     * 不能被批量赋值的属性
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function __construct(array $attributes = [])
    {
        $this->perPage = (int)Request::get('per_page', config('app.pageSize'));
        parent::__construct($attributes);
    }

    /**
     * 审核状态
     */
    const STATUS_SAVED = 'B';           //保存
    const STATUS_AUDITED = 'P';           //审核

    /**
     * 启用状态
     */
    const ENABLED_ENABLE = 'Y';                 //启用
    const ENABLED_DISABLE = 'N';                 //禁用

    /**
     * 状态值映射
     */
    const STATUS_DESCRIBE_MAP = [
        self::STATUS_SAVED => '未审核',
        self::STATUS_AUDITED => '已审核',
    ];

    /**
     * 启用状态值映射
     */
    const ENABLED_DESCRIBE_MAP = [
        self::ENABLED_ENABLE => '启用',
        self::ENABLED_DISABLE => '禁用',
    ];


    public function getStatusDescribeAttribute()
    {
        return self::STATUS_DESCRIBE_MAP[$this->status] ?? null;
    }

    public function getStatusIsSavedAttribute()
    {
        return $this->status == self::STATUS_SAVED;
    }

    public function getStatusIsAuditedAttribute()
    {
        return $this->status == self::STATUS_AUDITED;
    }


    public function getEnabledDescribeAttribute()
    {
        return self::ENABLED_DESCRIBE_MAP[$this->enabled] ?? null;
    }

    public function getEnabledIsEnableAttribute()
    {
        return $this->enabled == self::ENABLED_ENABLE;
    }

    public function getEnabledIsDisableAttribute()
    {
        return $this->enabled == self::ENABLED_DISABLE;
    }

    /**
     * 创建者
     */
    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'creator_id');
    }

    /**
     * 更新者
     */
    public function updater()
    {
        return $this->hasOne(User::class, 'id', 'updator_id');
    }
}
