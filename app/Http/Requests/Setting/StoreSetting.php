<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSetting extends FormRequest
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
                'digest_spike' => 'required',
                'graphs_months_interval' => 'required',
                'minimum_funds_for_add_card' => 'required',
                'total_otp_attempt' => 'required',
                'otp_attempt_min_time' => 'required',
            ];
        } else {
            return [
                'digest_spike' => 'required',
                'graphs_months_interval' => 'required',
                'minimum_funds_for_add_card' => 'required',
                'total_otp_attempt' => 'required',
                'otp_attempt_min_time' => 'required',
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
