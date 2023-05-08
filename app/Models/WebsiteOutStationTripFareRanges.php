<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Aws\Sts\StsClient;
use Aws\S3\S3Client;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class WebsiteOutStationTripFareRanges extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'website_outstation_trip_fare_ranges'; 
    
    protected $fillable = 
            ['id', 'website_outstation_trip_fare_id', 'created_at', 'updated_at', 'status', 'km_per_day', 'prices'];
    
}