<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class PostType extends Model
{


    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'post_types';

    protected $fillable = [
        'id',
        'name',
        'icon'
    ];
}
