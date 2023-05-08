<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Aws\Sts\StsClient;
use Aws\S3\S3Client;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class VehicleBrandModels extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicle_brand_models';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'brand_id', 'status', 'vehicle_type_id'
    ];
}