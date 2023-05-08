<?php

namespace App\Http\Requests\Auth;

use App\Rules\GoogleRecaptcha;
use App\Rules\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidateEmail;

class RegisterUser extends FormRequest
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
            'email' =>
            [
                'required',
                new ValidateEmail,
                'max:255'
            ],
            'password' => ['required without:id', 'confirmed', new StrongPassword],
            'mobile' => 'required',
            'country' => 'required',
            'agreecheck' => 'required',
            'g-recaptcha-response' => [new GoogleRecaptcha]

        ];
    }
    public function messages()
    {
        return [
            'password.required_without' => 'Password should not be empty',
            'password_confirmation.required_without' => 'You must re-type the password',
            'agreecheck.required' => 'Please agree on the terms and conditions.',
            'mobile.required' => 'Phone Number is required.',

        ];
    }
}
