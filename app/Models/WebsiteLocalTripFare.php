<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Aws\Sts\StsClient;
use Aws\S3\S3Client;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class WebsiteLocalTripFare extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'website_local_trip_fare'; 
    
    protected $fillable = 
            ['id', 'vehicle_type_id', 'base_fare', 'km_range', 'per_km', 'gst', 'created_at', 'updated_at', 'status', 'from_km_range', 'to_km_range', 'advance_booking', 'description'];
    
}