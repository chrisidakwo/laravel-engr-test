<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Batch extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function hmo(): BelongsTo
    {
        return $this->belongsTo(Hmo::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'batch_orders', 'batch_id', 'order_id');
    }
}
