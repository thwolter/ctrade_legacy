<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PositionUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => 'required|min:0.001',
            'transaction' => 'required|in:buy,sell',
            'id' => 'required|exists:positions,id'
        ];
    }
}