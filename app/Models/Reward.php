<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reward extends Model
{


    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'rewards';

    protected $fillable = [
        'id',
        'name',
        'point',
        'amount',
        'status_id',
        'created_at',
        'updated_at'
    ];


    public function reward_status () : BelongsTo {
        return $this->belongsTo(RewardStatus::class, 'status_id', 'id');
    }


    public function reward_images () : HasMany {
        return $this->hasMany(RewardImage::class, 'reward_id', 'id');
    }

}
