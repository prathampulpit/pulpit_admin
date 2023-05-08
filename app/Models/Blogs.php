<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Aws\Sts\StsClient;
use Aws\S3\S3Client;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class Blogs extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blogs'; 
    
    protected $fillable = 
            [
                'id', 
                'user_id', 
                'title', 
                'description', 'meta_title', 'meta_author', 'meta_description','meta_keywords', 
                'created_at', 
                'updated_at', 
                'status',
                'images',
                'slug',
                'short_description'];
    
}