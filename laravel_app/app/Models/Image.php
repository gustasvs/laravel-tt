<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $table = 'images';

    protected $fillable = [
        'filename',
        'path',
        'apraksts',
        'likes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
