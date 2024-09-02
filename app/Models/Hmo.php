<?php

namespace App\Models;

use App\Enums\BatchPreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hmo extends Model
{
    use HasFactory;

    protected $casts = [
        'batch_preference' => BatchPreference::class,
    ];
}
