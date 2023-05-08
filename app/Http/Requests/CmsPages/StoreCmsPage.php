<?php

namespace App\Http\Requests\CmsPages;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCmsPage extends FormRequest
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
                'page_name' => 'required',
                'content' => 'required',
            ];
        } else {
            return [
                'page_name' => 'required',
                'content' => 'required',
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
