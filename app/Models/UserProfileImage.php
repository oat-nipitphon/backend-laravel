<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserProfileImage extends Model
{


    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'user_profile_images';

    protected $fillable = [
        'id',
        'profile_id',
        'image_data',
        'image_status',
        'created_at',
        'updated_at'
    ];
}
