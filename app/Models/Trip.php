<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Aws\Sts\StsClient;
use Aws\S3\S3Client;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class Trip extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trip_post_master';

    protected $fillable = [
        'trip_type', 'vehical_manual_type', 'vehicle_type_id', 'pickup_date', 'pickup_time', 'pickup_location', 'trip_owner_name', 'trip_owner_mobile_no',
        'user_id', 'pickup_loc_lat', 'pickup_loc_lng', 'fare', 'commission_price', 'estimate_fare', 'status', 'posted_by_customer', 'is_live_trip', 'customer_id'
    ];
}