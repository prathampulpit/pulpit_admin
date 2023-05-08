<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Aws\Sts\StsClient;
use Aws\S3\S3Client;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class TripFare extends Authenticatable
{
    use Notifiable, HasApiTokens;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trip_fare';

    protected $fillable = [
        'city_id', 'vehicle_type_id', 'base_fare','minimum_fare','base_distance','base_distance_fare','base_time','base_time_fare','break_one_distance','break_one_distance_fare','break_one_time','break_one_time_fare','break_two_distance','break_two_distance_fare','break_two_time','break_two_time_fare','waiting_time','waiting_time_fare','price_surge','polygon_record_id'
    ];
}