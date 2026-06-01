<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'customer_name',
        'email',
        'phone',
        'category_id',
        'rating',
        'message',
        'status',
        'response',
        'internal_notes',
        'archived',
    ];

    protected $casts = [
        'rating' => 'integer',
        'archived' => 'boolean',
    ];
}
