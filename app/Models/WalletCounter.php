<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WalletCounter extends Model
{


    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'wallet_counters';

    protected $fillable = [
        'id',
        'wallet_id',
        'reward_id',
        'point',
        'status',
        'detail',
        'created_at',
        'updated_at'
    ];

    public function wallets () : HasMany {
        return $this->hasMany(UserWallet::class, 'wallet_id', 'id');
    }

    public function rewards () : HasMany {
        return $this->hasMany(Reward::class, 'reward_id', 'id');
    }




}
