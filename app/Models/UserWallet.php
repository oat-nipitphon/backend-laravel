<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserWallet extends Model
{


    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'user_wallets';

    protected $fillable = [
        'id',
        'user_id',
        'point',
        'status',
        'created_at',
        'updated_at'
    ];

    public function user () : HasMany {
        return $this->hasMany(User::class, 'user_id', 'id');
    }

    public function wallet_counters () : BelongsTo {
        return $this->belongsTo(WalletCounter::class, 'wallet_id', 'id');
    }

}
