<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostStore extends Model
{
    use HasFactory, HasApiTokens;
    protected $table = 'post_stores';

    protected $fillable = [
        'id',
        'post_id',
        'status', // true , false
        'created_at',
        'updated_at'
    ];

    public function posts () : BelongsTo {
        return $this->belongsTo(Post::class);
    }

}
