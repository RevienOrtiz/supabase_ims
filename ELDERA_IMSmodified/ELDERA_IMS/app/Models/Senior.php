<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Senior extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'osca_id',
        'last_name',
        'first_name',
        'middle_name',
        'name_extension',
        'region',
        'province',
        'city',
        'barangay',
        'residence',
        'street',
        'date_of_birth',
        'birth_place',
        'marital_status',
        'sex',
        'contact_number',
        'email',
        'religion',
        'ethnic_origin',
        'language',
        'gsis_sss',
        'tin',
        'philhealth',
        'sc_association',
        'other_govt_id',
        'can_travel',
        'employment',
        'has_pension',
        'has_app_account',
        'status',
        'photo_path',
        // II. FAMILY COMPOSITION
        'spouse_last_name', 'spouse_first_name', 'spouse_middle_name', 'spouse_extension',
        'father_last_name', 'father_first_name', 'father_middle_name', 'father_extension',
        'mother_last_name', 'mother_first_name', 'mother_middle_name', 'mother_extension',
        // II-A. Children & Dependents
        'children',
        'dependent',
        // III. EDUCATION / HR PROFILE
        'education_level', 'skills', 'shared_skills', 'community_activities',
        // IV. DEPENDENCY PROFILE
        'living_condition_primary', 'living_with', 'household_condition',
        // V. ECONOMIC PROFILE
        'source_of_income', 'real_assets', 'personal_assets', 'monthly_income', 'problems_needs',
        // VI. HEALTH PROFILE
        'blood_type', 'physical_disability', 'health_problems', 'dental_concern',
        'visual_concern', 'hearing_condition', 'social_emotional', 'area_difficulty',
        'maintenance_medicines', 'scheduled_checkup', 'checkup_frequency',
        // PENSION FORM SPECIFIC FIELDS
        'permanent_income', 'income_amount', 'income_source', 'existing_illness', 
        'illness_specify', 'with_disability', 'disability_specify', 'certification'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'can_travel' => 'boolean',
        'has_pension' => 'boolean',
        'has_app_account' => 'boolean',
        // JSON fields
        'skills' => 'array',
        'community_activities' => 'array',
        'living_with' => 'array',
        'household_condition' => 'array',
        'source_of_income' => 'array',
        'real_assets' => 'array',
        'personal_assets' => 'array',
        'problems_needs' => 'array',
        'health_problems' => 'array',
        'dental_concern' => 'array',
        'visual_concern' => 'array',
        'hearing_condition' => 'array',
        'social_emotional' => 'array',
        'area_difficulty' => 'array',
        // Family composition arrays
        'children' => 'array',
        'dependent' => 'array',
    ];

    protected $appends = ['full_name', 'age'];

    // Relationships
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_participants')
                    ->withPivot(['registered_at', 'attended', 'attendance_notes'])
                    ->withTimestamps();
    }

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class, 'barangay', 'name');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        $lastName = $this->last_name ?? '';
        $firstName = $this->first_name ?? '';
        
        if (empty($lastName) && empty($firstName)) {
            return 'N/A';
        }
        
        $name = strtoupper($lastName . ', ' . $firstName);
        if ($this->middle_name) {
            $name .= ' ' . strtoupper(substr($this->middle_name, 0, 1)) . '.';
        }
        if ($this->name_extension) {
            $name .= ' ' . $this->name_extension;
        }
        return $name;
    }

    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }
        
        return $this->date_of_birth->age;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDeceased($query)
    {
        return $query->where('status', 'deceased');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAlive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByBarangay($query, $barangay)
    {
        return $query->where('barangay', $barangay);
    }

    public function scopeByGender($query, $gender)
    {
        return $query->where('sex', $gender);
    }

    public function scopeWithPension($query)
    {
        return $query->where('has_pension', true);
    }

    public function scopeByAgeRange($query, $minAge, $maxAge = null)
    {
        $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= ?', [$minAge]);
        if ($maxAge) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= ?', [$maxAge]);
        }
        return $query;
    }

    // Business Logic Methods
    public function isEligibleForPension(): bool
    {
        return ($this->age !== null && $this->age >= 60) && !$this->has_pension;
    }

    public function getMilestoneAge(): ?int
    {
        if ($this->age === null) {
            return null;
        }
        
        $milestones = [80, 85, 90, 95, 100];
        foreach ($milestones as $milestone) {
            if ($this->age >= $milestone) {
                return $milestone;
            }
        }
        return null;
    }

    public function generateOscaId(): string
    {
        $year = date('Y');
        $lastId = self::where('osca_id', 'like', $year . '-%')
                     ->orderBy('osca_id', 'desc')
                     ->first();
        
        if ($lastId) {
            $lastNumber = (int) substr($lastId->osca_id, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return $year . '-' . $newNumber;
    }

    // Clear dashboard cache when senior data changes
    protected static function booted()
    {
        static::created(function () {
            Cache::forget('dashboard_statistics');
        });

        static::updated(function () {
            Cache::forget('dashboard_statistics');
        });

        static::deleted(function () {
            Cache::forget('dashboard_statistics');
        });
    }
}

