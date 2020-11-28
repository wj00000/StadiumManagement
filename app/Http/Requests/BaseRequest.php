<?php

namespace App\Http\Requests;

use App\Helpers\CommonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    use CommonResponse;
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function expectsJson()
    {
        return true;
    }

    public function wantsJson()
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {
        throw (new HttpResponseException($this->failed([
            'error_msg'     => $validator->errors()->first(),
            'all_error_msg' => $validator->errors()->toArray(),
            'error_code'    => 300001,
        ])));
    }
}
