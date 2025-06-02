<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserProfile extends Model
{


    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'user_profiles';

    protected $fillable = [
        'id',
        'user_id',
        'title_name',
        'first_name',
        'last_name',
        'nick_name',
        'birth_day',
        'created_at',
        'updated_at'
    ];

    public function user () : HasOne {
        return $this->hasOne(User::class, 'user_id', 'id');
    }

    public function users () : BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function profile_image () : HasOne {
        return $this->hasOne(UserProfileImage::class, 'profile_id', 'id');
    }

    public function profile_contacts () : HasMany {
        return $this->hasMany(UserProfileContact::class, 'profile_id', 'id');
    }

    public function profile_pops () : HasMany {
        return $this->hasMany(UserProfilePop::class, 'profile_id', 'id');
    }

    public function profile_followers () : HasMany {
        return $this->hasMany(UserProfileFollowers::class, 'profile_id', 'id');
    }

    public function posts () : HasMany {
        return $this->hasMany(Post::class);
    }


}
