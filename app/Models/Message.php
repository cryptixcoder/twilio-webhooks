<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'body',
        'twilio_sid',
        'type',
        'length',
        'segments',
        'price',
        'received_at',
        'sent_at',
        'failed_at',
        'failed_reason',
    ];

    public function media()
    {
        return $this->hasMany(Media::class);
    }
}
