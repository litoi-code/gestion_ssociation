<?php

// app/Models/Fund.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'total_interest'
    ];

    protected $casts = [
        'total_interest' => 'decimal:2'
    ];

    // Relationships
    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function interestDistributions()
    {
        return $this->hasMany(InterestDistribution::class);
    }

    public function addInterest($amount)
    {
        $this->total_interest += $amount;
        $this->save();
        
        return $this;
    }

    public function deductInterest($amount)
    {
        $this->total_interest -= $amount;
        $this->save();
        
        return $this;
    }

    public function getAvailableInterestAttribute()
    {
        return $this->total_interest;
    }
}


