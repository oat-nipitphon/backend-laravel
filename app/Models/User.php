<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'status_id',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function user_status () : BelongsTo {
        return $this->belongsTo(UserStatus::class, 'status_id', 'id');
    }

    public function user_wallet () : HasOne {
        return $this->hasOne(UserWallet::class, 'user_id', 'id');
    }

    // get user profile start auth login
    public function user_profile () : HasOne {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    // report log login
    public function report_log_logins () : HasMany {
        return $this->hasMany(UserLogLogin::class, 'user_id', 'id');
    }

    // check status login now
    public function check_status_login () : HasOne {
        return $this->hasOne(UserLogLogin::class, 'user_id', 'id')->latestOfMany();
    }

}
