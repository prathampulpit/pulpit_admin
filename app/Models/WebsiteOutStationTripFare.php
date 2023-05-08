<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Aws\Sts\StsClient;
use Aws\S3\S3Client;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class WebsiteOutStationTripFare extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'website_outstation_trip_fare'; 
    
    protected $fillable = 
            ['id', 'vehicle_type_id','per_km', 'gst','advance_booking','driver_allowances', 'description', 'created_at', 'updated_at', 'status'];
    
}