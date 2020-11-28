<?php

namespace App\Exceptions;

use App\Vo\ResultVo;
use HttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use PDOException;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable $exception
     * @return void

     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        /**
         * 以下异常不记录到日志
         */
        if (!($exception instanceof CustomException) &&                 //自定义
            !($exception instanceof NotFoundHttpException) &&           //路由不存在
            !($exception instanceof AuthenticationException) &&         //验证用户登录状态
            !($exception instanceof MethodNotAllowedHttpException) &&   //请求路由方法不存在
            !($exception instanceof QueryException)                     //一些SQL的问题
        ) {
            parent::report($exception);
        }
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $contentTypeHeader = explode(';', $request->header('content-type'));
        /** 捕抓自定义异常并处理返回 **/
        if ($request->expectsJson() || in_array('application/json', $contentTypeHeader) || in_array('multipart/form-data', $contentTypeHeader)) {
            $error    = $this->convertExceptionToResponse($exception);
            $debug    = config('app.debug');
            $response = [
                'error_msg'  => $debug && $exception->getMessage() ? $exception->getMessage() : '服务器有一点小问题。',
                'error_code' => 300002
            ];

            if ($debug) {
                $response['trace'] = $exception->getTrace();
            }

            /**
             * 自定义报错
             */
            if ($exception instanceof CustomException) {
                return ResultVo::fail($exception->getMessage(), [], $exception->getCode(), 200);
            }

            /**
             * 找不到模型数据，
             * 引起原因firstOrFail或findOrFail
             */
            if ($exception instanceof NotFoundHttpException && $exception->getPrevious() instanceof ModelNotFoundException) {
                return ResultVo::fail($debug ? $exception->getMessage() : '找不到数据，请刷新页面！', [], 500017, 200);
            }

            /**
             * 路由不存在
             * 例如请求地址 http://www.xxx.com/api/a/b
             */
            if ($exception instanceof NotFoundHttpException) {
                return ResultVo::fail($debug ? $exception->getMessage() : '找不到页面。', [], 500017, 200);
            }


            if ($exception instanceof UnauthorizedException) {
                return ResultVo::fail($debug ? $exception->getMessage() : '你没有权限对此操作。', [], 500017, 200);
            }


            /**
             * 验证用户登录状态
             */
            if ($exception instanceof AuthenticationException) {
                return ResultVo::fail($debug ? $exception->getMessage() : '未经授权。', [], 500017, 200);
            }

            /**
             * 请求路由方法不存在
             * 例如：请求为POST方法，但是路由没有设置POST方法
             */
            if ($exception instanceof MethodNotAllowedHttpException) {
                return ResultVo::fail($debug ? $exception->getMessage() : '请求路由方法不存在。', [], 500017, 200);
            }
            /**
             * 默认API一分钟60次
             */
            if ($exception instanceof HttpException) {
                return ResultVo::fail($debug ? $exception->getMessage() : '发送了太多请求。', [], 500017, 200);
            }
            /**
             * 一些SQL的问题
             * 例如：表名不对，字段不存在等
             */
            if ($exception instanceof QueryException) {
                return ResultVo::fail($debug ? $exception->getMessage() : '数据库查询错误。', [], 500017, 200);
            }
            /**
             * 数据库链接出错
             * 例如用户名密码错误等
             */
            if ($exception instanceof PDOException) {
                return ResultVo::fail($debug ? $exception->getMessage() : '数据库链接出错，请稍后尝试。', [], 500017, 200);
            }
            /**
             * 一些服务器运行异常
             * 例如.env没有设置key
             */
            if ($exception instanceof RuntimeException) {
                return ResultVo::fail($debug ? $exception->getMessage() : '网络出错，请稍后尝试。', [], 500017, 200);
            }
        }

        return parent::render($request, $exception);

    }
}
