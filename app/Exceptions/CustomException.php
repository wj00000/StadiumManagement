<?php

namespace App\Exceptions;

use Exception;

/**
 * 自定义报错
 * 使有方法 throw new InvalidRequestException(500,'参数错误');
 *
 * Class CustomException
 * @package App\Exceptions
 *
 * @author 王勤锋 <qinfeng_wang@fabriciechina.com>
 * @copyright 2019/11/22 11:59 上午
 */
class CustomException extends Exception
{
    
    public $code = 500;
    public $message = 0;
    public $data = [];
    public $httpCode = 200;
    
    public function __construct($code = 500, $message = null, $data = [], $httpCode = 200)
    {
        parent::__construct('The given data was invalid.');
        
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
        $this->httpCode = $httpCode;
    }
    
    
   
}
