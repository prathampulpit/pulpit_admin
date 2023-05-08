<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Aws\Sts\StsClient;
use Aws\S3\S3Client;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class Vehicles extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicles';

    protected $fillable = [
        'user_id',
        'vehicle_number',
        'brand_id',
        'model_id',
        'vehicle_type_id',
        'fuel_type_id',
        'city',
        'state',
        'registration_year',
        'fuel_type_id',
        'colour_id',
        'owner_name',
        'rc_front_url',
        'rc_front_url_status',
        'rc_back_url',
        'rc_back_url_status',
        'insurance_doc_url',
        'insurance_doc_url_status',
        'insurance_exp_date',
        'permit_doc_url',
        'permit_doc_url_status',
        'permit_exp_date',
        'fitness_doc_url',
        'fitness_doc_url_status',
        'fitness_exp_date',
        'puc_doc_url',
        'puc_doc_url_status',
        'puc_exp_date',
        'agreement_doc_url',
        'agreement_doc_url_status',
        'status',
        'completion_steps',
        'all_document_verify',
        'all_document_verify'
    ];

}