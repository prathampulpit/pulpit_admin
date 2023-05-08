<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class ReferralMasters extends Authenticatable
{
    use Notifiable, HasApiTokens;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'referral_masters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'referral_bonus','max_referral_bonus','created_at','updated_at'
    ];
}