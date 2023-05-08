<?php

namespace App\Http\Requests\BubbleTextMessages;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBubbleTextMessage extends FormRequest
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
                'bubble_text_en' => 'required',
                'bubble_text_sw' => 'required',
                'user_id' => 'required',
            ];
        } else {
            return [
                'bubble_text_en' => 'required',
                'bubble_text_sw' => 'required',
                'user_id' => 'required',
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
