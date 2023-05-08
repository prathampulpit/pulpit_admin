<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Factory;

class LoginRequest extends FormRequest
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
            'username' => ['required', 'email'],
            'password' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'required' => ':Attribute is required',
            'username.required' => 'Email is required',
            'email' => 'Invalid email address'
        ];
    }


    /**
     * Get the needed authorization credentials from the request.
     *
     * @return array
     */
    public function getCredentials()
    {
        // The form field for providing username or password
        // have name of "username", however, in order to support
        // logging users in with both (username and email)
        // we have to check if user has entered one or another
        $username = $this->get('username');

        if ($this->isEmail($username)) {
            return [
                'email' => $username,
                'password' => $this->get('password')
            ];
        }

        return $this->only('username', 'password');
    }

    /**
     * Validate if provided parameter is valid email.
     *
     * @param $param
     * @return bool
     */
    private function isEmail($param)
    {
        $factory = $this->container->make(Factory::class);
        
        return !$factory->make(
            ['username' => $param],
            ['username' => 'email']
        )->fails();
    }
}
