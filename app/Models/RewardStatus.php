<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;


class RewardStatus extends Model
{

    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'reward_status';

    protected $fillable = [
        'id',
        'name',
        'code',
        'icon',
        'created_at',
        'updated_at'
    ];
}
