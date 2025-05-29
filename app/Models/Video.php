<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{


    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'videos';

    protected $fillable = [
        'id',
        'profile_id',
        'post_id',
        'video_data',
        'video_name',
        'video_path',
        'video_status',
        'created_at',
        'updated_at'
    ];

    public function video_type () : BelongsTo {
        return $this->belongsTo(Video::class, 'type_id', 'id');
    }

}
