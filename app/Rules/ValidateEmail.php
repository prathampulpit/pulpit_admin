<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User\User;

class ValidateEmail implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $emailExist = User::where(['email' => $value])
            ->orWhere(['secondary_email' => $value])
            ->orWhere(['work_email' => $value])->first();

        if ($emailExist)
            return false;
        else
            return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This email is already in use.';
    }
}
