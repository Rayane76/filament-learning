<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestImages extends Model
{
    use HasFactory;

    protected $fillable = ['images'];

    // Cast 'tags' as an array
    protected $casts = [
        'images' => 'array',
    ];
}
