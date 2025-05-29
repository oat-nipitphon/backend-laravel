<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;


class UserLogLogin extends Model
{

    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'user_log_logins';

    protected $fillable = [
        'id',
        'user_id',
        'status',
        'time_in',
        'time_out',
        'time_total_login',
        'created_at',
        'updated_at'
    ];

    public function users () : BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
