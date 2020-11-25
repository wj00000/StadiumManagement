<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebController extends Controller
{
//检查标签
    public function checkSignature()
    {
        //先获取到这三个参数
        $signature = $_GET['signature'];
        $nonce = $_GET['nonce'];
        $timestamp = $_GET['timestamp'];

        //把这三个参数存到一个数组里面
        $tmpArr = array($timestamp,$nonce,'WVq4zq8C4eczdTPc8cteMS88yn5GsCzS');
        //进行字典排序
        sort($tmpArr);
        //把数组中的元素合并成字符串，impode()函数是用来将一个数组合并成字符串的
        $tmpStr = implode($tmpArr);
        //sha1加密，调用sha1函数
        $tmpStr = sha1($tmpStr);
        //判断加密后的字符串是否和signature相等
        if($tmpStr == $signature)
        {
            return true;
        }
        return false;
    }

    public function init()
    {
        //如果相等，验证成功就返回echostr
        if ($this->checkSignature()) {
            //返回echostr
            $echostr = $_GET['echostr'];
            if ($echostr) {
                echo $echostr;
                exit;
            }
        }
    }
}
