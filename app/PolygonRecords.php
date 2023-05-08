<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Aws\Sts\StsClient;
use Aws\S3\S3Client;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class PolygonRecords extends Authenticatable
{
    use Notifiable, HasApiTokens;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'polygon_records';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'coordinates','status','created_at','updated_at','service','area_name'
    ];
}