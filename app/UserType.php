<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Aws\Sts\StsClient;
use Aws\S3\S3Client;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class UserType extends Authenticatable
{
    use Notifiable, HasApiTokens;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','status'
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function getImageUsingStsToken($image){
        
        $user = Auth::user();
        $sts_token = $user['sts_token'];
        //$sts_arr

        $result = unserialize($sts_token);

        $options = [
            'region' => env('S3_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' =>  [
                'key'    => $result['Credentials']['AccessKeyId'],
                'secret' => $result['Credentials']['SecretAccessKey'],
                'token'  => $result['Credentials']['SessionToken']
            ]
        ];
        
        $s3Client = new S3Client($options);
        //Get a command to GetObject
        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => env('AWS_BUCKET'),
            'Key'    => $image
        ]);
        
        //The period of availability
        $request = $s3Client->createPresignedRequest($cmd, '+10 minutes');
        $signedUrl = (string)$request->getUri();
        return $signedUrl;
    }
}