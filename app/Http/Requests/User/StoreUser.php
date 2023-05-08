<?php

namespace App\Http\Requests\User;

use App\Rules\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUser extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|max:255|unique:users,email,' . $this->get('id'),
            'mobile' => 'required|digits:10',
            'password' => [Rule::requiredIf(function () {
                return (request()->get('emailHidden') != request()->get('email'));
            }), 'confirmed', new StrongPassword],
        ];
    }

    public function messages()
    {
        return [
            'required' => ':Attribute is required',
            'password.required_without' => ':Attribute is required',
            'password_confirmation.required_without' => 'You must re-type the password',
        ];
    }

    public function attributes()
    {

        return [
            
        ];
    }
}
