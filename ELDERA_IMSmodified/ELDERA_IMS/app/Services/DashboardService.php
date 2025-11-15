<?php

namespace App\Services;

use App\Models\Senior;
use App\Models\Application;
use App\Models\Event;
use App\Models\Barangay;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function getStatistics(): array
    {
        // Cache dashboard statistics for 2 minutes to improve performance
        return Cache::remember('dashboard_statistics', 120, function () {
            return [
                'seniors' => $this->getSeniorStatistics(),
                'applications' => $this->getApplicationStatistics(),
                'events' => $this->getEventStatistics(),
                'barangays' => $this->getBarangayStatistics(),
                'age_distribution' => $this->getAgeDistribution(),
            ];
        });
    }

    public function clearCache(): void
    {
        Cache::forget('dashboard_statistics');
    }

    public function getSeniorStatistics(): array
    {
        // Single optimized query to get all senior statistics at once
        $stats = Senior::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN sex = \'Male\' THEN 1 ELSE 0 END) as male,
                SUM(CASE WHEN sex = \'Female\' THEN 1 ELSE 0 END) as female,
                SUM(CASE WHEN has_pension = true THEN 1 ELSE 0 END) as with_pension,
                SUM(CASE WHEN has_pension = false THEN 1 ELSE 0 END) as without_pension,
                SUM(CASE WHEN status = \'active\' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = \'deceased\' THEN 1 ELSE 0 END) as deceased
            ')
            ->first();

        return [
            'total' => $stats->total ?? 0,
            'male' => $stats->male ?? 0,
            'female' => $stats->female ?? 0,
            'with_pension' => $stats->with_pension ?? 0,
            'without_pension' => $stats->without_pension ?? 0,
            'active' => $stats->active ?? 0,
            'deceased' => $stats->deceased ?? 0,
            'pension_rate' => $stats->total > 0 ? round(($stats->with_pension / $stats->total) * 100, 2) : 0,
        ];
    }

    public function getApplicationStatistics(): array
    {
        // Single optimized query to get all application statistics at once
        $stats = Application::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = \'pending\' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = \'received\' THEN 1 ELSE 0 END) as received,
                SUM(CASE WHEN status = \'approved\' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = \'rejected\' THEN 1 ELSE 0 END) as rejected
            ')
            ->first();

        return [
            'total' => $stats->total ?? 0,
            'pending' => $stats->pending ?? 0,
            'received' => $stats->received ?? 0,
            'approved' => $stats->approved ?? 0,
            'rejected' => $stats->rejected ?? 0,
            'completion_rate' => $stats->total > 0 ? round((($stats->approved) / $stats->total) * 100, 2) : 0,
        ];
    }

    public function getEventStatistics(): array
    {
        // Single optimized query to get all event statistics at once
        $stats = Event::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN event_date > NOW() THEN 1 ELSE 0 END) as upcoming,
                SUM(CASE WHEN event_date = CURRENT_DATE THEN 1 ELSE 0 END) as ongoing,
                SUM(CASE WHEN event_date < NOW() THEN 1 ELSE 0 END) as completed
            ')
            ->first();

        return [
            'total' => $stats->total ?? 0,
            'upcoming' => $stats->upcoming ?? 0,
            'ongoing' => $stats->ongoing ?? 0,
            'completed' => $stats->completed ?? 0,
        ];
    }

    public function getEventStatisticsByType(): array
    {
        // Get upcoming event statistics by type to match Events list
        $stats = Event::upcoming()
            ->selectRaw('
                event_type,
                COUNT(*) as count
            ')
            ->groupBy('event_type')
            ->get()
            ->pluck('count', 'event_type')
            ->toArray();

        return [
            'general' => $stats['general'] ?? 0,
            'pension' => $stats['pension'] ?? 0,
            'health' => $stats['health'] ?? 0,
            'id_claiming' => $stats['id_claiming'] ?? 0,
        ];
    }

    public function getBarangayStatistics(): array
    {
        // Cache barangay statistics for 5 minutes
        return Cache::remember('barangay_statistics', 300, function () {
            // Single optimized query to get all barangay statistics at once
            $statistics = Barangay::active()
                ->leftJoin('seniors', function($join) {
                    $join->on('barangays.name', '=', 'seniors.barangay')
                         ->where('seniors.status', 'active');
                })
                ->selectRaw('
                    barangays.name,
                    barangays.code,
                    COUNT(seniors.id) as total_seniors,
                    SUM(CASE WHEN seniors.sex = \'Male\' THEN 1 ELSE 0 END) as male_count,
                    SUM(CASE WHEN seniors.sex = \'Female\' THEN 1 ELSE 0 END) as female_count,
                    SUM(CASE WHEN seniors.has_pension = true THEN 1 ELSE 0 END) as with_pension_count,
                    SUM(CASE WHEN seniors.has_pension = false THEN 1 ELSE 0 END) as without_pension_count
                ')
                ->groupBy('barangays.id', 'barangays.name', 'barangays.code')
                ->orderBy('barangays.name')
                ->get();

            return [
                'total' => $statistics->count(),
                'barangays' => $statistics->toArray(),
            ];
        });
    }

    public function getAgeDistribution(): array
    {
        // Single optimized query to get all age data at once
        $ageData = Senior::selectRaw('
                CASE 
                    WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) BETWEEN 60 AND 65 THEN \'60-65\'
                    WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) BETWEEN 66 AND 70 THEN \'66-70\'
                    WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) BETWEEN 71 AND 75 THEN \'71-75\'
                    WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) BETWEEN 76 AND 80 THEN \'76-80\'
                    WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) BETWEEN 81 AND 85 THEN \'81-85\'
                    WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) BETWEEN 86 AND 90 THEN \'86-90\'
                    WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) >= 90 THEN \'90+\'
                END as age_group,
                sex,
                COUNT(*) as count
            ')
            ->whereRaw('EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) >= 60')
            ->groupBy('age_group', 'sex')
            ->get()
            ->groupBy('age_group');

        // Initialize age groups with zeros
        $ageGroups = [
            '60-65' => ['total' => 0, 'male' => 0, 'female' => 0],
            '66-70' => ['total' => 0, 'male' => 0, 'female' => 0],
            '71-75' => ['total' => 0, 'male' => 0, 'female' => 0],
            '76-80' => ['total' => 0, 'male' => 0, 'female' => 0],
            '81-85' => ['total' => 0, 'male' => 0, 'female' => 0],
            '86-90' => ['total' => 0, 'male' => 0, 'female' => 0],
            '90+' => ['total' => 0, 'male' => 0, 'female' => 0],
        ];

        // Populate the data
        foreach ($ageData as $ageGroup => $genders) {
            foreach ($genders as $gender) {
                $ageGroups[$ageGroup][$gender->sex === 'Male' ? 'male' : 'female'] = $gender->count;
                $ageGroups[$ageGroup]['total'] += $gender->count;
            }
        }

        return $ageGroups;
    }

    public function getGenderDistribution(): array
    {
        $male = Senior::active()->byGender('Male')->count();
        $female = Senior::active()->byGender('Female')->count();

        return [
            'male' => $male,
            'female' => $female,
            'total' => $male + $female,
        ];
    }

    public function getPensionDistribution(): array
    {
        $withPension = Senior::active()->withPension()->count();
        $withoutPension = Senior::active()->where('has_pension', false)->count();
        $total = $withPension + $withoutPension;

        return [
            'with_pension' => $withPension,
            'without_pension' => $withoutPension,
            'total' => $total,
        ];
    }

    public function getApplicationTrends(int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        $trends = Application::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, application_type, COUNT(*) as count')
            ->groupBy('date', 'application_type')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        $result = [];
        foreach ($trends as $date => $applications) {
            $result[$date] = [
                'senior_id' => $applications->where('application_type', 'senior_id')->sum('count'),
                'pension' => $applications->where('application_type', 'pension')->sum('count'),
                'benefits' => $applications->where('application_type', 'benefits')->sum('count'),
                'total' => $applications->sum('count'),
            ];
        }

        return $result;
    }

    public function getRecentApplications(int $limit = 10): array
    {
        return Application::with(['senior', 'submittedBy'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($application) {
                return [
                    'id' => $application->id,
                    'type' => $application->application_type,
                    'status' => $application->status,
                    'senior_name' => $application->senior ? $application->senior->full_name : 'N/A',
                    'submitted_by' => $application->submittedBy ? $application->submittedBy->name : 'System',
                    'submitted_at' => $application->submitted_at->format('M d, Y H:i'),
                ];
            })
            ->toArray();
    }

    public function getUpcomingEvents(int $limit = 5): array
    {
        return Event::upcoming()
            ->orderBy('event_date')
            ->orderBy('start_time')
            ->limit($limit)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'type' => $event->event_type,
                    'date' => $event->event_date->format('M d, Y'),
                    'time' => $event->start_time->format('g:i A'),
                    'location' => $event->location,
                    'participants' => $event->current_participants,
                    'max_participants' => $event->max_participants,
                ];
            })
            ->toArray();
    }

    public function getBarangayComparison(): array
    {
        $barangays = Barangay::active()
            ->withCount(['seniors' => function ($query) {
                $query->active();
            }])
            ->orderBy('seniors_count', 'desc')
            ->limit(10)
            ->get();

        return $barangays->map(function ($barangay) {
            $stats = $barangay->getStatistics();
            return [
                'name' => $barangay->name,
                'total_seniors' => $stats['total_seniors'],
                'male_count' => $stats['male_count'],
                'female_count' => $stats['female_count'],
                'with_pension_count' => $stats['with_pension_count'],
                'pension_rate' => $stats['total_seniors'] > 0 
                    ? round(($stats['with_pension_count'] / $stats['total_seniors']) * 100, 2) 
                    : 0,
            ];
        })->toArray();
    }

    public function getMonthlyStatistics(): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $seniorsThisMonth = Senior::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();

        $applicationsThisMonth = Application::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();

        $eventsThisMonth = Event::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();

        return [
            'seniors_added' => $seniorsThisMonth,
            'applications_submitted' => $applicationsThisMonth,
            'events_created' => $eventsThisMonth,
        ];
    }

    public function getStatisticsByBarangay(string $barangay): array
    {
        // Cache barangay-specific statistics for 1 minute
        return Cache::remember("dashboard_statistics_barangay_{$barangay}", 60, function () use ($barangay) {
            // Get barangay-specific statistics
            $seniorStats = $this->getSeniorStatisticsByBarangay($barangay);
            $applicationStats = $this->getApplicationStatisticsByBarangay($barangay);
            $ageDistribution = $this->getAgeDistributionByBarangay($barangay);
            $eventStats = $this->getEventStatistics();

            return [
                'barangays' => [
                    'total' => 1,
                    'selected' => $barangay,
                    'barangays' => [$barangay]
                ],
                'seniors' => $seniorStats,
                'applications' => $applicationStats,
                'age_distribution' => $ageDistribution,
                'events' => $eventStats,
            ];
        });
    }

    protected function getSeniorStatisticsByBarangay(string $barangay): array
    {
        // Convert barangay name to match seniors table format
        $seniorBarangayName = $this->convertBarangayNameForSeniors($barangay);
        
        $stats = Senior::where('barangay', $seniorBarangayName)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN sex = \'Male\' THEN 1 ELSE 0 END) as male,
                SUM(CASE WHEN sex = \'Female\' THEN 1 ELSE 0 END) as female,
                SUM(CASE WHEN has_pension = true THEN 1 ELSE 0 END) as with_pension,
                SUM(CASE WHEN has_pension = false THEN 1 ELSE 0 END) as without_pension
            ')
            ->first();

        return [
            'total' => $stats->total ?? 0,
            'male' => $stats->male ?? 0,
            'female' => $stats->female ?? 0,
            'with_pension' => $stats->with_pension ?? 0,
            'without_pension' => $stats->without_pension ?? 0,
        ];
    }

    /**
     * Convert barangay name from barangays table format to seniors table format
     */
    private function convertBarangayNameForSeniors(string $barangayName): string
    {
        $mapping = [
            'Domalandan East' => 'domalandan-east',
            'Domalandan West' => 'domalandan-west', 
            'Domalandan Center' => 'domalandan-center',
            'Libsong East' => 'libsong-east',
            'Libsong West' => 'libsong-west',
            'Pangapisan North' => 'pangapisan-north',
            'Pangapisan Sur' => 'pangapisan-sur',
        ];

        return $mapping[$barangayName] ?? strtolower($barangayName);
    }

    protected function getApplicationStatisticsByBarangay(string $barangay): array
    {
        $seniorBarangayName = $this->convertBarangayNameForSeniors($barangay);
        
        $stats = Application::whereHas('senior', function ($query) use ($seniorBarangayName) {
                $query->where('barangay', $seniorBarangayName);
            })
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = \'pending\' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = \'received\' THEN 1 ELSE 0 END) as received,
                SUM(CASE WHEN status = \'approved\' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = \'rejected\' THEN 1 ELSE 0 END) as rejected
            ')
            ->first();

        return [
            'total' => $stats->total ?? 0,
            'pending' => $stats->pending ?? 0,
            'received' => $stats->received ?? 0,
            'approved' => $stats->approved ?? 0,
            'rejected' => $stats->rejected ?? 0,
        ];
    }

    protected function getAgeDistributionByBarangay(string $barangay): array
    {
        $seniorBarangayName = $this->convertBarangayNameForSeniors($barangay);
        
        $distribution = Senior::where('barangay', $seniorBarangayName)
            ->selectRaw('
                CASE 
                    WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) BETWEEN 60 AND 65 THEN \'60-65\'
                    WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) BETWEEN 66 AND 70 THEN \'66-70\'
                    WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) BETWEEN 71 AND 75 THEN \'71-75\'
                    WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) BETWEEN 76 AND 80 THEN \'76-80\'
                    WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) BETWEEN 81 AND 85 THEN \'81-85\'
                    WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) BETWEEN 86 AND 90 THEN \'86-90\'
                    WHEN EXTRACT(YEAR FROM age(CURRENT_DATE, date_of_birth)) > 90 THEN \'90+\'
                END as age_group,
                sex,
                COUNT(*) as count
            ')
            ->groupBy('age_group', 'sex')
            ->get()
            ->groupBy('age_group');

        $result = [];
        $ageGroups = ['60-65', '66-70', '71-75', '76-80', '81-85', '86-90', '90+'];
        
        foreach ($ageGroups as $group) {
            $result[$group] = [
                'male' => $distribution->get($group, collect())->where('sex', 'Male')->sum('count'),
                'female' => $distribution->get($group, collect())->where('sex', 'Female')->sum('count'),
            ];
        }

        return $result;
    }
}

