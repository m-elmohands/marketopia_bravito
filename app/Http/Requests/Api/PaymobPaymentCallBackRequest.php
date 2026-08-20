<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class PaymobPaymentCallBackRequest extends FormRequest
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
            'obj.amount_cents' => 'required|integer',
            'obj.created_at' => 'required|string',
            'obj.currency' => 'required|string',
            'obj.error_occured' => 'required|boolean',
            'obj.has_parent_transaction' => 'required|boolean',
            'obj.integration_id' => 'required|integer',
            'obj.is_3d_secure' => 'required|boolean',
            'obj.is_auth' => 'required|boolean',
            'obj.is_capture' => 'required|boolean',
            'obj.is_refunded' => 'required|boolean',
            'obj.is_standalone_payment' => 'required|boolean',
            'obj.is_voided' => 'required|boolean',
            'obj.order.id' => 'required|integer',
            'obj.owner' => 'required|integer',
            'obj.data.txn_response_code' => 'nullable|string',
            'obj.data.message' => 'required|string',
            'obj.pending' => 'required|boolean',
            'obj.success' => 'required|boolean',
            'obj.source_data.pan' => 'nullable|string',
            'obj.source_data.type' => 'required|string',
            'obj.source_data.sub_type' => 'required|string',
            'hmac' => 'required|string',
        ];
    }
}
