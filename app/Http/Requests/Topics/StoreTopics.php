<?php

namespace App\Http\Requests\Topics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTopics extends FormRequest
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
                'type' => 'required',
                'name_en' => 'required',
                'name_sw' => 'required',
            ];
        } else {
            return [
                'name_en' => 'required',
                'name_sw' => 'required',
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
            'name_en' => 'Topic English',
            'name_sw' => 'Topic Swahili',
        ];
    }
}
