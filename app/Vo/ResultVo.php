<?php


namespace App\Vo;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;


class ResultVo
{

    public static function success($message = '成功。', $data = [], $code = 200, $httpCode = 200)
    {
        $json = [
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ];
        return response()->json($json, $httpCode);
    }


    public static function fail($message = '失败。', $data = [], $code = 500, $httpCode = 200)
    {
        $json = [
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ];
        return response()->json($json, $httpCode);
    }

    /**
     * 返回JSON
     *
     * @param      $result
     * @param null $message
     * @param int $code
     * @param int $httpCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function response($result, $message = null, $code = 200, $httpCode = 200)
    {

        $json = [
            'code'    => $code,
            'message' => $message,
            'data'    => [],
        ];

        if (is_numeric($result)) {
            $json['code'] = $result;
        } elseif (is_array($result)) {
            $json['data'] = $result;
        } elseif (is_object($result) && $result instanceof AnonymousResourceCollection && $result->resource instanceof LengthAwarePaginator) {
            $json['data'] = $result;
            $json['meta'] = [
                'total'        => $result->total(),
                'current_page' => $result->currentPage(),
                'last_page'    => $result->lastPage(),
                'per_page'     => $result->perPage(),
            ];
        } elseif (is_object($result)) {
            $json['data'] = $result;
        }

        return response()->json($json, $httpCode);

    }
}
