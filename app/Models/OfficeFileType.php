<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;


class OfficeFileType extends Model
{

    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'office_file_types';

    protected $fillable = [
        'id',
        'name',
        'code',
        'icon'
    ];
}
