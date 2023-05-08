<?php

namespace App\Http\Requests\AddMoneyLabels;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAddMoneyLabels extends FormRequest
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
                'display_name_sw' => 'required',
            ];
        } else {
            return [
                'display_name' => 'required',
                'display_name_sw' => 'required',
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
