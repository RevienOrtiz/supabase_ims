<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barangay extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function seniors(): HasMany
    {
        return $this->hasMany(Senior::class, 'barangay', 'name');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // Accessors
    public function getSeniorCountAttribute(): int
    {
        return $this->seniors()->count();
    }

    public function getActiveSeniorCountAttribute(): int
    {
        return $this->seniors()->active()->count();
    }

    // Business Logic Methods
    public function getStatistics(): array
    {
        // Single optimized query to get all statistics at once
        $stats = $this->seniors()
            ->where('status', 'active')
            ->selectRaw('
                COUNT(*) as total_seniors,
                SUM(CASE WHEN sex = \'Male\' THEN 1 ELSE 0 END) as male_count,
                SUM(CASE WHEN sex = \'Female\' THEN 1 ELSE 0 END) as female_count,
                SUM(CASE WHEN has_pension = true THEN 1 ELSE 0 END) as with_pension_count,
                SUM(CASE WHEN has_pension = false THEN 1 ELSE 0 END) as without_pension_count,
                SUM(CASE WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) BETWEEN 60 AND 70 THEN 1 ELSE 0 END) as age_60_70,
                SUM(CASE WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) BETWEEN 71 AND 80 THEN 1 ELSE 0 END) as age_71_80,
                SUM(CASE WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) BETWEEN 81 AND 90 THEN 1 ELSE 0 END) as age_81_90,
                SUM(CASE WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) > 90 THEN 1 ELSE 0 END) as age_90_plus
            ')
            ->first();
        
        return [
            'total_seniors' => $stats->total_seniors ?? 0,
            'male_count' => $stats->male_count ?? 0,
            'female_count' => $stats->female_count ?? 0,
            'with_pension_count' => $stats->with_pension_count ?? 0,
            'without_pension_count' => $stats->without_pension_count ?? 0,
            'age_60_70' => $stats->age_60_70 ?? 0,
            'age_71_80' => $stats->age_71_80 ?? 0,
            'age_81_90' => $stats->age_81_90 ?? 0,
            'age_90_plus' => $stats->age_90_plus ?? 0,
        ];
    }

    public function getPopulationDensity(): float
    {
        // This would need actual area data to calculate properly
        // For now, return a placeholder
        return $this->senior_count / 100; // Assuming 100 sq km per barangay
    }

    public function getPensionEligibilityRate(): float
    {
        $total = $this->seniors()->active()->count();
        if ($total === 0) {
            return 0;
        }
        
        $eligible = $this->seniors()->active()->where('has_pension', false)->count();
        return ($eligible / $total) * 100;
    }
}





















