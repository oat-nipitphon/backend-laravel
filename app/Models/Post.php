<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{


    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'posts';

    protected $fillable = [
        'id',
        'type_id',
        'profile_id',
        'title',
        'content',
        'refer',
        'status', // 'active', 'store', 'block', 'disable', 'deletetion'
        'created_at',
        'updated_at'
    ];

    public function user_profiles () : BelongsTo {
        return $this->belongsTo(UserProfile::class, 'profile_id', 'id');
    }

    public function post_type () : BelongsTo {
        return $this->belongsTo(PostType::class, 'type_id', 'id');
    }

    public function post_images () : HasMany {
        return $this->hasMany(PostImage::class, 'post_id', 'id');
    }

    public function post_pops () : HasMany {
        return $this->hasMany(PostPop::class, 'post_id', 'id');
    }

    public function post_comments () : HasMany {
        return $this->hasMany(PostComment::class, 'post_id', 'id');
    }

    public function post_office_files () : HasMany {
        return $this->hasMany(OfficeFile::class, 'post_id', 'id');
    }

    public function post_videos () : HasMany {
        return $this->hasMany(Video::class, 'post_id', 'id');
    }


}
