<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InterestDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'fund_id',
        'date',
        'share_percentage',
        'interest_amount',
    ];

    protected $casts = [
        'date' => 'datetime',
        'share_percentage' => 'float',
        'interest_amount' => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }
}

