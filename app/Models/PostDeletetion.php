<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class PostDeletetion extends Model
{
    use HasFactory, HasApiTokens;
        protected $table = 'post_deletetions';

    protected $fillable = [
        'id',
        'post_id',
        'delete_at',
        'status', // true , false
        'created_at',
        'updated_at'
    ];
}
