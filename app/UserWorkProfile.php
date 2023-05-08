<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Aws\Sts\StsClient;
use Aws\S3\S3Client;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class UserWorkProfile extends Authenticatable
{
    use Notifiable, HasApiTokens;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_work_profile';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'user_type_id', 'profile_id'
    ];
}