<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class PaymobCallBackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            Response::api($validator->errors()->first(), 400, false, 400)
        );
    }

    public function rules(): array
    {
        return [
            'obj.id' => 'required|integer',
            'obj.data.txn_response_code' => 'nullable|string',
            'obj.data.message' => 'required|string',
            'obj.pending' => 'required|boolean',
            'obj.success' => 'required|boolean',
            'obj.source_data.type' => 'required|string',
            'obj.source_data.sub_type' => 'required|string',
            'obj.order.shipping_data.order_id' => 'required|integer',
            'hmac' => 'required|string',
        ];
    }
}
