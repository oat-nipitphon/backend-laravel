<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfficeFile extends Model
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'office_files';

    protected $fillable = [
        'id',
        'profile_id',
        'post_id',
        'type_id',
        'file_data',
        'file_name',
        'file_path',
        'file_status',
        'created_at',
        'updated_at',
    ];

    public function office_file_type () : BelongsTo {
        return $this->belongsTo(OfficeFileType::class, 'type_id', 'id');
    }


}
