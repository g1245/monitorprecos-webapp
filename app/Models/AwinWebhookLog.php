<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AwinWebhookLog extends Model
{
    protected $fillable = [
        'payload',
        'source_ip',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
