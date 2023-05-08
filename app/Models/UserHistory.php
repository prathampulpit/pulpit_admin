<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class UserHistory extends Model
{
    use Notifiable, HasApiTokens;

    protected $table = 'user_history';
}
