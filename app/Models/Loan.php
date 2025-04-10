<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Loan extends Model
{
    protected $fillable = [
        'member_id',
        'fund_id',
        'amount',
        'interest_rate',
        'remaining_balance',
        'total_amount',
        'start_date'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    // Add accessors for current_total and current_interest
    protected $appends = ['current_total', 'current_interest'];

    // Make sure these values persist during the request
    protected $current_total_value = null;
    protected $current_interest_value = null;

    public function getCurrentTotalAttribute()
    {
        return $this->current_total_value ?? 0;
    }

    public function setCurrentTotalAttribute($value)
    {
        $this->current_total_value = $value;
    }

    public function getCurrentInterestAttribute()
    {
        return $this->current_interest_value ?? 0;
    }

    public function setCurrentInterestAttribute($value)
    {
        $this->current_interest_value = $value;
    }

    public function getStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }
}



