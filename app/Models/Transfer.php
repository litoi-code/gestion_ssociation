<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'amount',
        'date',
        'description',
    ];

    public function sender()
    {
        return $this->belongsTo(Member::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Member::class, 'receiver_id');
    }
}
