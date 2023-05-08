<?php

namespace App\Http\Requests\WithdrawMoneyLabels;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWithdrawMoneyLabels extends FormRequest
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
        if ($this->id == "") {

            return [
                'display_name' => 'required',
            ];
        } else {
            return [
                'display_name' => 'required',
            ];
        }
    }

    public function messages()
    {
        return [
            'required' => ':Attribute is required',
        ];
    }

    public function attributes()
    {

        return [

        ];
    }
}
