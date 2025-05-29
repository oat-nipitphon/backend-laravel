<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class PostPop extends Model
{

    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'post_pops';

    protected $fillable = [
        'id',
        'post_id',
        'profile_id_pop',
        'status',
        'created_at',
        'updated_at'
    ];
}
