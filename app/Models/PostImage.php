<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class PostImage extends Model
{


    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'post_images';

    protected $fillable = [
        'id',
        'post_id',
        'image_data',
        'created_at',
        'updated_at'
    ];
}
