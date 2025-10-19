<?php

namespace App\Http\Controllers;

use App\Models\Senior;
use App\Models\Barangay;
use App\Models\Application;
use App\Models\BenefitsApplication;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View as ViewFacade;

class SeniorController extends Controller
{
    /**
     * Generate a PDF report of seniors who can receive pensions
     */
    public function generatePensionReport(Request $request)
    {
        // Get seniors with pension from the database
        $pensionSeniors = Senior::select([
                'id', 'osca_id', 'first_name', 'last_name', 'middle_name', 
                'sex', 'barangay', 'date_of_birth', 'created_at'
            ])
            ->where('has_pension', 1)
            ->orderBy('last_name')
            ->get();
            
        // Generate the PDF view
        $html = ViewFacade::make('reports.pension_report', [
            'seniors' => $pensionSeniors,
            'date' => now()->format('F d, Y'),
            'total' => $pensionSeniors->count()
        ])->render();
        
        // Return the view for browser-based PDF generation
        return response()->view('reports.pension_report_wrapper', [
            'content' => $html,
            'title' => 'Seniors with Pension Report'
        ]);
    }
    
    /**
     * Clear relevant caches when data changes
     */
    private function clearRelevantCaches(): void
    {
        Cache::forget('benefits_applications_page_1');
        Cache::forget('pension_applications_page_1');
        Cache::forget('id_applications_page_1');
        Cache::forget('dashboard_statistics');
        Cache::forget('barangay_statistics');
        Cache::forget('active_barangays');
    }

    /**
     * Display a listing of seniors.
     */
    public function index(Request $request): View
    {
        // Optimize seniors query with selective eager loading
        $query = Senior::select([
            'id', 'osca_id', 'first_name', 'last_name', 'middle_name', 
            'sex', 'barangay', 'has_pension', 'has_app_account', 'status', 'created_at', 'date_of_birth'
        ]);

        // Filter by barangay if provided
        if ($request->filled('barangay')) {
            $query->byBarangay($request->barangay);
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by gender if provided
        if ($request->filled('gender')) {
            $query->byGender($request->gender);
        }

        // Filter by pension status if provided
        if ($request->filled('pension_status')) {
            if ($request->pension_status === 'with_pension') {
                $query->withPension();
            } elseif ($request->pension_status === 'without_pension') {
                $query->where('has_pension', false);
            }
        }

        // Search functionality with optimized query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('osca_id', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }

        // Handle sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['name', 'age', 'barangay', 'status', 'created_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }
        
        // Map frontend sort fields to database columns
        $sortMapping = [
            'name' => 'last_name',
            'age' => 'date_of_birth',
            'barangay' => 'barangay',
            'status' => 'status',
            'created_at' => 'created_at'
        ];
        
        $dbSortField = $sortMapping[$sortField] ?? 'created_at';
        $query->orderBy($dbSortField, $sortDirection);
        
        // Load all seniors without pagination to allow scrolling through all records
        $seniors = $query->get();
        $totalSeniors = $seniors->count();
        
        // Cache barangays as they don't change frequently
        $barangays = cache()->remember('active_barangays', 3600, function () {
            return Barangay::active()->orderBy('name')->get();
        });

        // Optimize applications queries with pagination and selective fields
        // Use smaller page sizes and cache results
        $benefitsApplications = cache()->remember('benefits_applications_page_1', 300, function () {
            return Application::select(['id', 'senior_id', 'status', 'submitted_at', 'created_at'])
                ->with(['senior:id,osca_id,first_name,last_name,middle_name,barangay,date_of_birth,sex'])
                ->where('application_type', 'benefits')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        });
            
        $pensionApplications = Application::select(['id', 'senior_id', 'status', 'submitted_at', 'created_at'])
            ->with(['senior:id,osca_id,first_name,last_name,middle_name,barangay,date_of_birth,sex', 
                    'pensionApplication:application_id,rrn,monthly_income,has_pension,pension_source,pension_amount'])
            ->where('application_type', 'pension')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $idApplications = cache()->remember('id_applications_page_1', 300, function () {
            return Application::select(['id', 'senior_id', 'status', 'submitted_at', 'created_at'])
                ->with(['senior:id,osca_id,first_name,last_name,middle_name,barangay,date_of_birth,sex'])
                ->where('application_type', 'senior_id')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        });

        return view('tables.seniors', compact('seniors', 'totalSeniors', 'barangays', 'benefitsApplications', 'pensionApplications', 'idApplications'));
    }
    /**
     * Display the specified senior.
     */
    public function show(string $id): View
    {
        $senior = Senior::with(['applications', 'documents', 'events'])
                        ->findOrFail($id);

        return view('seniors.view_senior', compact('senior'));
    }

    /**
     * Remove the specified senior from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $senior = Senior::findOrFail($id);
        
        // Soft delete the senior
        $senior->delete();

        return redirect()->route('seniors')
            ->with('success', 'Senior citizen record deleted successfully');
    }

    /**
     * Show the form for editing the specified senior.
     */
    public function edit(string $id): View
    {
        $senior = Senior::findOrFail($id);
        $barangays = Barangay::active()->orderBy('name')->get();

        // Force update all required fields with default values if they're missing
        $updateData = [];
        
        // Check each field and add default if missing
        if (is_null($senior->region) || $senior->region === '') {
            $updateData['region'] = 'Region I';
        }
        if (is_null($senior->province) || $senior->province === '') {
            $updateData['province'] = 'Pangasinan';
        }
        if (is_null($senior->city) || $senior->city === '') {
            $updateData['city'] = 'Lingayen';
        }
        if (is_null($senior->barangay) || $senior->barangay === '') {
            $updateData['barangay'] = 'aliwekwek'; // Use a valid barangay value
        }
        if (is_null($senior->residence) || $senior->residence === '') {
            $updateData['residence'] = 'Not specified';
        }
        if (is_null($senior->birth_place) || $senior->birth_place === '') {
            $updateData['birth_place'] = 'Not specified';
        }
        if (is_null($senior->marital_status) || $senior->marital_status === '') {
            $updateData['marital_status'] = 'Single';
        }
        if (is_null($senior->sex) || $senior->sex === '') {
            $updateData['sex'] = 'Male';
        }
        if (is_null($senior->contact_number) || $senior->contact_number === '') {
            $updateData['contact_number'] = '09000000000';
        }
        if (is_null($senior->email) || $senior->email === '') {
            $updateData['email'] = 'noemail@example.com';
        }
        if (is_null($senior->language) || $senior->language === '') {
            $updateData['language'] = 'Tagalog, English';
        }
        if (is_null($senior->status) || $senior->status === '') {
            $updateData['status'] = 'active';
        }
        if (is_null($senior->date_of_birth) || $senior->date_of_birth === '') {
            $updateData['date_of_birth'] = '1950-01-01';
        }
        
        // Always update the database with default values for missing fields
        if (!empty($updateData)) {
            try {
                $senior->update($updateData);
                $senior->refresh(); // Reload the updated data
                
                // Debug: Log what was updated
                Log::info('Updated senior with default values', [
                    'senior_id' => $senior->id,
                    'updated_fields' => $updateData,
                    'current_data' => [
                        'first_name' => $senior->first_name,
                        'last_name' => $senior->last_name,
                        'region' => $senior->region,
                        'province' => $senior->province,
                        'city' => $senior->city,
                        'barangay' => $senior->barangay,
                        'residence' => $senior->residence,
                        'birth_place' => $senior->birth_place,
                        'marital_status' => $senior->marital_status,
                        'sex' => $senior->sex,
                        'contact_number' => $senior->contact_number,
                        'email' => $senior->email,
                        'language' => $senior->language,
                        'status' => $senior->status,
                        'date_of_birth' => $senior->date_of_birth,
                    ]
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to update senior with default values', [
                    'senior_id' => $senior->id,
                    'error' => $e->getMessage(),
                    'update_data' => $updateData
                ]);
            }
        }

        return view('seniors.edit_comprehensive_profile', compact('senior', 'barangays'));
    }

    /**
     * Update the specified senior in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        try {
            $senior = Senior::findOrFail($id);
            
            // Debug: Log the received data
            Log::info('Senior update request received', [
                'senior_id' => $id,
                'method' => $request->method(),
                'osca_id_from_request' => $request->input('osca_id'),
                'osca_id_from_database' => $senior->osca_id,
                'certification' => $request->input('certification'),
                'received_data' => $request->all(),
                'current_senior_data' => [
                    'first_name' => $senior->first_name,
                    'last_name' => $senior->last_name,
                    'region' => $senior->region,
                    'province' => $senior->province,
                    'city' => $senior->city,
                    'barangay' => $senior->barangay,
                    'residence' => $senior->residence,
                    'birth_place' => $senior->birth_place,
                    'marital_status' => $senior->marital_status,
                    'sex' => $senior->sex,
                    'contact_number' => $senior->contact_number,
                    'email' => $senior->email,
                    'language' => $senior->language,
                    'status' => $senior->status,
                    'date_of_birth' => $senior->date_of_birth,
                ]
            ]);

            // Validate the request data
            $validatedData = $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'name_extension' => 'nullable|string|max:10',
            'region' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'residence' => 'required|string|max:255',
            'street' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'birth_place' => 'required|string|max:255',
            'marital_status' => 'required|string|in:Single,Married,Widowed,Separated,Others',
            'sex' => 'required|string|in:Male,Female',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'religion' => 'required|string|max:255',
            'ethnic_origin' => 'required|string|max:255',
            'language' => 'required|string|max:255',
            'osca_id' => 'required|string|max:50|unique:seniors,osca_id,' . $id,
            'gsis_sss' => 'nullable|string|max:50',
            'tin' => 'nullable|string|max:50',
            'philhealth' => 'nullable|string|max:50',
            'sc_association' => 'nullable|string|max:50',
            'other_govt_id' => 'nullable|string|max:50',
            'can_travel' => 'nullable|string|in:Yes,No',
            'employment' => 'nullable|string|max:255',
            'has_pension' => 'nullable|boolean',
            'status' => 'required|string|in:active,deceased',
            // II. FAMILY COMPOSITION
            'spouse_last_name' => 'nullable|string|max:255',
            'spouse_first_name' => 'nullable|string|max:255',
            'spouse_middle_name' => 'nullable|string|max:255',
            'spouse_extension' => 'nullable|string|max:10',
            'father_last_name' => 'nullable|string|max:255',
            'father_first_name' => 'nullable|string|max:255',
            'father_middle_name' => 'nullable|string|max:255',
            'father_extension' => 'nullable|string|max:10',
            'mother_last_name' => 'nullable|string|max:255',
            'mother_first_name' => 'nullable|string|max:255',
            'mother_middle_name' => 'nullable|string|max:255',
            'mother_extension' => 'nullable|string|max:10',
            // III. EDUCATION / HR PROFILE
            'education_level' => 'nullable|string|max:255',
            'shared_skills' => 'nullable|string',
            // IV. DEPENDENCY PROFILE
            'living_condition_primary' => 'nullable|string|max:255',
            'living_with' => 'nullable',
            'household_condition' => 'nullable',
            // V. ECONOMIC PROFILE
            'source_of_income' => 'nullable',
            'monthly_income' => 'nullable|numeric|min:0',
            // VI. HEALTH PROFILE
            'blood_type' => 'nullable|string|max:10',
            'physical_disability' => 'nullable|string|max:255',
            'health_problems' => 'nullable',
            'dental_concern' => 'nullable',
            'visual_concern' => 'nullable',
            'hearing_condition' => 'nullable',
            'social_emotional' => 'nullable',
            'area_difficulty' => 'nullable',
            'maintenance_medicines' => 'nullable|string',
            'scheduled_checkup' => 'nullable|string|max:255',
            'checkup_frequency' => 'nullable|string|max:255',
            // CERTIFICATION
            'certification' => 'required|accepted',
            // PHOTO
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Convert boolean fields properly
        $validatedData['can_travel'] = $request->input('can_travel') === 'Yes' ? true : false;
        $validatedData['has_pension'] = $request->input('has_pension') == '1' ? true : false;
        $validatedData['certification'] = $request->input('certification') === 'on' ? true : false;

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists (check both public and private storage)
            if ($senior->photo_path) {
                if (Storage::disk('public')->exists($senior->photo_path)) {
                    Storage::disk('public')->delete($senior->photo_path);
                } elseif (Storage::disk('private')->exists($senior->photo_path)) {
                    Storage::disk('private')->delete($senior->photo_path);
                }
            }
            
            // SECURITY: Store photo in private storage (not publicly accessible)
            $photoPath = $request->file('photo')->store('senior-photos', 'private');
            $validatedData['photo_path'] = $photoPath;
        }

            $senior->update($validatedData);
            Log::info('Senior updated successfully', ['id' => $id]);

            // Clear relevant caches
            $this->clearRelevantCaches();

            return redirect()->route('seniors')
                ->with('success', 'Senior citizen record updated successfully');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in senior update', [
                'senior_id' => $id,
                'errors' => $e->errors()
                // SECURITY: Don't log sensitive input data
            ]);
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Exception in senior update', [
                'senior_id' => $id,
                'error' => $e->getMessage()
                // SECURITY: Don't log sensitive request data or stack traces
            ]);
            
            // SECURITY: Don't expose system error details to users
            return redirect()->back()
                ->with('error', 'An error occurred while updating the senior record. Please try again.');
        }
    }

    /**
     * Store a newly created senior in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Log::info('Senior store method called', ['data' => $request->all()]);
        
        // Validate the request data
        $validatedData = $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'name_extension' => 'nullable|string|max:10',
            'region' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'residence' => 'required|string|max:255',
            'street' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'birth_place' => 'required|string|max:255',
            'marital_status' => 'required|string|in:Single,Married,Widowed,Separated,Others',
            'sex' => 'required|string|in:Male,Female',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'religion' => 'required|string|max:255',
            'ethnic_origin' => 'required|string|max:255',
            'language' => 'required|string|max:255',
            'osca_id' => 'required|string|max:50|unique:seniors,osca_id',
            'gsis_sss' => 'nullable|string|max:50',
            'tin' => 'nullable|string|max:50',
            'philhealth' => 'nullable|string|max:50',
            'sc_association' => 'nullable|string|max:50',
            'other_govt_id' => 'nullable|string|max:50',
            'can_travel' => 'nullable|string|in:Yes,No',
            'employment' => 'nullable|string|max:255',
            'has_pension' => 'nullable|string|in:Yes,No',
            'status' => 'required|string|in:active,deceased',
            // II. FAMILY COMPOSITION
            'spouse_last_name' => 'nullable|string|max:255',
            'spouse_first_name' => 'nullable|string|max:255',
            'spouse_middle_name' => 'nullable|string|max:255',
            'spouse_extension' => 'nullable|string|max:10',
            'father_last_name' => 'nullable|string|max:255',
            'father_first_name' => 'nullable|string|max:255',
            'father_middle_name' => 'nullable|string|max:255',
            'father_extension' => 'nullable|string|max:10',
            'mother_last_name' => 'nullable|string|max:255',
            'mother_first_name' => 'nullable|string|max:255',
            'mother_middle_name' => 'nullable|string|max:255',
            'mother_extension' => 'nullable|string|max:10',
            // III. EDUCATION / HR PROFILE
            'education_level' => 'nullable|string|max:255',
            'skills' => 'nullable',
            'shared_skills' => 'nullable|string',
            'community_activities' => 'nullable',
            // IV. DEPENDENCY PROFILE
            'living_condition_primary' => 'nullable|string|max:255',
            'living_with' => 'nullable',
            'household_condition' => 'nullable',
            // V. ECONOMIC PROFILE
            'source_of_income' => 'nullable',
            'real_assets' => 'nullable',
            'personal_assets' => 'nullable',
            'monthly_income' => 'nullable|numeric|min:0',
            'problems_needs' => 'nullable',
            // VI. HEALTH PROFILE
            'blood_type' => 'nullable|string|max:10',
            'physical_disability' => 'nullable|string|max:255',
            'health_problems' => 'nullable',
            'dental_concern' => 'nullable',
            'visual_concern' => 'nullable',
            'hearing_condition' => 'nullable',
            'social_emotional' => 'nullable',
            'area_difficulty' => 'nullable',
            'maintenance_medicines' => 'nullable|string',
            'scheduled_checkup' => 'nullable|string|max:255',
            'checkup_frequency' => 'nullable|string|max:255',
            'certification' => 'required|accepted',
            // SECURITY: Secure file upload validation
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Convert boolean fields properly
        $validatedData['can_travel'] = $request->input('can_travel') === 'Yes' ? true : false;
        $validatedData['has_pension'] = $request->input('has_pension') === 'Yes' ? true : false;
        $validatedData['certification'] = $request->input('certification') === 'on' ? true : false;

        $senior = Senior::create($validatedData);
        Log::info('Senior created successfully', ['id' => $senior->id, 'name' => $senior->first_name . ' ' . $senior->last_name]);

        // Clear relevant caches
        $this->clearRelevantCaches();

        return redirect()->route('seniors')
            ->with('success', 'Senior citizen record created successfully!');
    }

    /**
     * Show the form for editing a pension application.
     */
    public function editPension(string $id): View
    {
        $application = Application::with(['senior', 'pensionApplication'])
            ->findOrFail($id);

        $senior = $application->senior;
        
        // Debug: Check if pension application exists
        Log::info('Pension Edit - Application Debug', [
            'application_id' => $application->id,
            'application_type' => $application->application_type,
            'pension_application_exists' => $application->pensionApplication ? 'yes' : 'no',
            'pension_application_id' => $application->pensionApplication?->id ?? 'null'
        ]);
        
        // Debug: Log the senior data to see what fields have values
        Log::info('Pension Edit - Senior Data Debug', [
            'senior_id' => $senior->id,
            'permanent_income' => $senior->permanent_income,
            'existing_illness' => $senior->existing_illness,
            'with_disability' => $senior->with_disability,
            'illness_specify' => $senior->illness_specify,
            'disability_specify' => $senior->disability_specify,
            'living_with' => $senior->living_with,
            'has_pension' => $senior->has_pension,
            'pension_amount' => $senior->pension_amount ?? 'NULL',
            'pension_source' => $senior->pension_source ?? 'NULL'
        ]);
        
        return view('seniors.edit_pension_comprehensive', compact('application', 'senior'));
    }

    /**
     * Update a pension application.
     */
    public function updatePension(Request $request, string $id): RedirectResponse
    {
        try {
            Log::info('Pension Update - Request Data', $request->all());
            Log::info('Pension Update - All Request Keys', array_keys($request->all()));
            $application = Application::with(['senior', 'pensionApplication'])->findOrFail($id);
            
        // Validate pension-specific data
        $validatedData = $request->validate([
            'osca_id' => 'required|string|max:50',
            'rrn' => 'nullable|string|max:50',
                'region' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'barangay' => 'required|string|max:255',
                'residence' => 'required|string|max:255',
                'street' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'birth_place' => 'required|string|max:255',
            'age' => 'required|numeric|min:0',
            'sex' => 'required|string|in:Male,Female',
            'civil_status' => 'required|string|in:Single,Married,Widowed,Separated,Others',
                'contact_number' => 'required|string|max:20',
                'monthly_income' => 'required|numeric|min:0',
                'status' => 'required|string|in:pending,received,approved,rejected',
            // Pension-specific fields
            'permanent_income' => 'nullable|string',
            'income_amount' => 'nullable|string',
            'income_source' => 'nullable|string',
            'existing_illness' => 'nullable|string',
            'illness_specify' => 'nullable|string',
            'with_disability' => 'nullable|string',
            'disability_specify' => 'nullable|string',
            'living_arrangement' => 'nullable|array',
            'has_pension' => 'nullable|string',
            'pension_amount' => 'nullable|string',
            'pension_source' => 'nullable|string',
            'certification' => 'required|accepted'
            ]);

            // Update the senior information (excluding name fields which are read-only)
            $application->senior->update([
                'osca_id' => $validatedData['osca_id'],
                'region' => $validatedData['region'],
                'province' => $validatedData['province'],
                'city' => $validatedData['city'],
                'barangay' => $validatedData['barangay'],
                'residence' => $validatedData['residence'],
                'street' => $validatedData['street'],
                'date_of_birth' => $validatedData['date_of_birth'],
                'birth_place' => $validatedData['birth_place'],
                'sex' => $validatedData['sex'],
                'marital_status' => $validatedData['civil_status'],
                'contact_number' => $validatedData['contact_number'],
                // Health condition fields
                'existing_illness' => $validatedData['existing_illness'] ?? null,
                'illness_specify' => $validatedData['illness_specify'] ?? null,
                'with_disability' => $validatedData['with_disability'] ?? null,
                'disability_specify' => $validatedData['disability_specify'] ?? null,
                // Income fields
                'permanent_income' => $validatedData['permanent_income'] ?? null,
                'income_amount' => $validatedData['income_amount'] ?? null,
                'income_source' => $validatedData['income_source'] ?? null,
            ]);

            // Update the pension application
            $application->pensionApplication->update([
                'rrn' => $validatedData['rrn'] ?? null,
                'monthly_income' => $validatedData['monthly_income'],
                'has_pension' => $validatedData['has_pension'] == '1',
                'pension_amount' => $validatedData['pension_amount'] ?? 0,
                'pension_source' => $validatedData['pension_source'] ?? null,
                'living_arrangement' => $validatedData['living_arrangement'] ?? null,
                'certification' => $validatedData['certification'] == 'on' ? true : false,
            ]);

            // Update the application status
            $application->update([
                'status' => $validatedData['status']
            ]);

            // Clear relevant caches after updating ID application
            $this->clearRelevantCaches();

            return redirect()->route('seniors.pension')
                ->with('success', 'Pension application updated successfully! Status: ' . ucfirst(str_replace('_', ' ', $validatedData['status'])));
        } catch (\Exception $e) {
            Log::error('Pension Update Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'Unable to update pension application. Please check all required fields and try again.');
        }
    }


    /**
     * Show the form for editing an ID application.
     */
    public function editIdApplication(string $id): View
    {
        $application = Application::with(['senior', 'seniorIdApplication'])
            ->findOrFail($id);

        $senior = $application->senior;
        return view('seniors.edit_senior_id_comprehensive', compact('application', 'senior'));
    }

    /**
     * Show the form for viewing a benefits application.
     */
    public function viewBenefits(string $id): View
    {
        $application = Application::with(['senior'])
            ->findOrFail($id);

        return view('seniors.view_benefits_application_comprehensive', compact('application'));
    }

    /**
     * Show the form for viewing a pension application.
     */
    public function viewPension(string $id): View
    {
        $application = Application::with(['senior', 'pensionApplication'])
            ->findOrFail($id);

        return view('seniors.view_pension_application_comprehensive', compact('application'));
    }

    /**
     * Show the form for viewing a senior ID application.
     */
    public function viewSeniorId(string $id): View
    {
        $application = Application::with(['senior', 'seniorIdApplication'])
            ->findOrFail($id);

        return view('seniors.view_senior_id_application_comprehensive', compact('application'));
    }
    
    /**
     * Show the form for creating an app account for a senior.
     */
    public function createAppAccount(string $id)
    {
        $senior = Senior::findOrFail($id);
        
        if ($senior->has_app_account) {
            return redirect()->route('seniors')->with('error', 'Senior already has an app account.');
        }
        
        return view('seniors.create_app_account', compact('senior'));
    }
    
    /**
     * Store a newly created app account for a senior.
     */
    public function storeAppAccount(Request $request, string $id)
    {
        $senior = Senior::findOrFail($id);
        
        if ($senior->has_app_account) {
            return redirect()->route('seniors')->with('error', 'Senior already has an app account.');
        }
        
        $request->validate([
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&_])[A-Za-z\d@$!%*?&_]{8,}$/'
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);
        
        // Check if an app user with this OSCA ID already exists
        $existingAppUser = \App\Models\AppUser::where('osca_id', $senior->osca_id)->first();
        
        if ($existingAppUser) {
            // If app user exists, update the password
            $existingAppUser->password = bcrypt($request->password);
            $existingAppUser->save();
            
            // Mark senior as having an app account
            $senior->has_app_account = true;
            $senior->save();
            
            return redirect()->route('seniors')->with('success', 'Existing app account updated for senior.');
        } else {
            // Create new app user account
            $appUser = new \App\Models\AppUser();
            $appUser->osca_id = $senior->osca_id;
            $appUser->first_name = $senior->first_name;
            $appUser->last_name = $senior->last_name;
            $appUser->email = $senior->email ?? null;
            $appUser->password = bcrypt($request->password);
            $appUser->role = 'senior';
            $appUser->save();
            
            // Mark senior as having an app account
            $senior->has_app_account = true;
            $senior->save();
            
            return redirect()->route('seniors')->with('success', 'App account created successfully.');
        }
    }
    
    /**
     * Show the form for editing an app account for a senior.
     */
    public function editAppAccount(string $id)
    {
        $senior = Senior::findOrFail($id);
        
        if (!$senior->has_app_account) {
            return redirect()->route('seniors')->with('error', 'Senior does not have an app account yet.');
        }
        
        return view('seniors.edit_app_account', compact('senior'));
    }
    
    /**
     * Update the app account password for a senior.
     */
    public function updateAppAccount(Request $request, string $id)
    {
        $senior = Senior::findOrFail($id);
        
        // Check if senior has an app account
        if (!$senior->has_app_account) {
            return redirect()->route('seniors')->with('error', 'Senior does not have an app account yet.');
        }
        
        // Look for AppUser record (new system)
        $appUser = \App\Models\AppUser::where('osca_id', $senior->osca_id)->first();
        
        // Look for legacy User record (old system)
        $legacyUser = null;
        if ($senior->user_id) {
            $legacyUser = \App\Models\User::find($senior->user_id);
        }
        
        // Must have either AppUser or legacy User
        if (!$appUser && !$legacyUser) {
            return redirect()->route('seniors')->with('error', 'Senior does not have an app account yet.');
        }
        
        $request->validate([
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&_])[A-Za-z\d@$!%*?&_]{8,}$/'
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);
        
        // Update password based on which system is being used
        if ($appUser) {
            // New system: Update AppUser
            // Check if the new password is the same as the old one
            if (password_verify($request->password, $appUser->password)) {
                return back()->withErrors(['password' => 'The new password cannot be the same as your current password.'])->withInput();
            }
            
            $appUser->password = bcrypt($request->password);
            $appUser->save();
        } else {
            // Legacy system: Update User
            // Check if the new password is the same as the old one
            if (password_verify($request->password, $legacyUser->password)) {
                return back()->withErrors(['password' => 'The new password cannot be the same as your current password.'])->withInput();
            }
            
            $legacyUser->password = bcrypt($request->password);
            $legacyUser->save();
        }
        
        return redirect()->route('seniors')->with('success', 'App account password updated successfully.');
    }

    /**
     * Update an ID application.
     */
    public function updateIdApplication(Request $request, string $id): RedirectResponse
    {
        Log::info('updateIdApplication method called', ['id' => $id, 'data' => $request->all()]);
        try {
            $application = Application::with(['senior', 'seniorIdApplication'])->findOrFail($id);
            
            // Validate the data - ID application specific fields from form_seniorID.blade.php
            $validatedData = $request->validate([
            'address' => 'required|string|max:255',
                'gender' => 'required|string|max:255',
                'date_of_birth' => 'required|date',
                'age' => 'nullable|integer|min:0',
                'birth_place' => 'required|string|max:255',
                'occupation' => 'nullable|string|max:255',
                'civil_status' => 'required|string|in:Single,Married,Widowed,Divorced,Separated',
                'annual_income' => 'required|numeric|min:0',
                'pension_source' => 'nullable|string|max:255',
                'ctc_number' => 'nullable|string|max:255',
                'date_of_application' => 'nullable|date',
                'place_of_issuance' => 'required|string|max:255',
                'date_of_issued' => 'required|date',
                'date_of_received' => 'required|date',
                'status' => 'required|string|in:pending,received,approved,rejected',
            ]);

            // Update the senior information - ID application specific fields
            $application->senior->update([
                'barangay' => $validatedData['address'], // Map address to barangay
                'sex' => $validatedData['gender'], // Map gender to sex
                'date_of_birth' => $validatedData['date_of_birth'],
                'birth_place' => $validatedData['birth_place'],
                'employment' => $validatedData['occupation'], // Map occupation to employment
                'marital_status' => $validatedData['civil_status'], // Map civil_status to marital_status
                'pension_source' => $validatedData['pension_source'],
                'ctc_number' => $validatedData['ctc_number'],
            ]);

            // Update the senior ID application
            $application->seniorIdApplication->update([
                'civil_status' => $validatedData['civil_status'],
                'annual_income' => $validatedData['annual_income'],
                'pension_source' => $validatedData['pension_source'],
                'ctc_number' => $validatedData['ctc_number'],
                'date_of_application' => $validatedData['date_of_application'],
                'place_of_issuance' => $validatedData['place_of_issuance'],
                'date_of_issued' => $validatedData['date_of_issued'],
                'date_of_received' => $validatedData['date_of_received'],
            ]);

            // Update the application status
            $application->update([
                'status' => $validatedData['status']
            ]);

            // Clear caches so the ID applicants table reflects changes immediately
            $this->clearRelevantCaches();

            return redirect()->route('seniors.id-applications')
                ->with('success', 'Senior ID application updated successfully! Status: ' . ucfirst(str_replace('_', ' ', $validatedData['status'])));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Unable to update senior ID application. Please check all required fields and try again.');
        }
    }

    /**
     * Display benefits applications.
     */
    public function benefits(Request $request): View
    {
        // Get benefits applications
        $benefitsApplications = Application::where('application_type', 'benefits')
            ->whereHas('senior') // Just ensure senior exists
            ->whereHas('benefitsApplication') // Ensure benefits application data exists
            ->with([
                'senior' => function($query) {
                    $query->select('id', 'osca_id', 'first_name', 'last_name', 'middle_name', 'barangay', 'date_of_birth', 'sex', 'marital_status', 'contact_number', 'email');
                },
                'benefitsApplication' => function($query) {
                    $query->select('application_id', 'senior_id', 'milestone_age');
                }
            ])
            ->orderBy('submitted_at', 'desc')
            ->paginate(20);

        // Get pension applications (for compatibility)
        $pensionApplications = collect(); // Empty collection

        // Get ID applications (for compatibility)
        $idApplications = collect(); // Empty collection

        // Get barangays for filtering
        $barangays = Barangay::orderBy('name')->get();

        // Get seniors for compatibility with the main table view
        $seniors = collect(); // Empty collection since we're only showing benefits applications

        return view('seniors.benefits-wrapper', compact('seniors', 'benefitsApplications', 'pensionApplications', 'idApplications', 'barangays'));
    }

    /**
     * Display pension applications.
     */
    public function pension(Request $request): View
    {
        // Get pension applications with senior and pensionApplication relationships
        $pensionApplications = Application::where('application_type', 'pension')
            ->whereHas('senior') // Only show applications that have senior data
            ->with([
                'senior' => function($query) {
                    $query->select('id', 'osca_id', 'first_name', 'last_name', 'barangay', 'date_of_birth', 'sex');
                },
                'pensionApplication' => function($query) {
                    $query->select('application_id', 'rrn', 'monthly_income', 'has_pension', 'pension_source', 'pension_amount');
                }
            ])
            ->orderBy('submitted_at', 'desc')
            ->get();

        // Get barangays for filtering
        $barangays = Barangay::orderBy('name')->get();

        // Get seniors and other variables for compatibility with the main table view
        $seniors = collect(); // Empty collection since we're only showing pension applications
        $benefitsApplications = collect(); // Empty collection
        $idApplications = collect(); // Empty collection

        return view('seniors.pension-wrapper', compact('seniors', 'benefitsApplications', 'pensionApplications', 'idApplications', 'barangays'));
    }

    /**
     * Display ID applications.
     */
    public function idApplications(Request $request): View
    {
        // Get ID applications with senior and seniorIdApplication relationships
        $idApplications = Application::where('application_type', 'senior_id')
            ->whereHas('senior') // Only show applications that have senior data
            ->whereHas('seniorIdApplication') // Only show applications that have senior ID application data
            ->with([
                'senior' => function($query) {
                    $query->select('id', 'osca_id', 'first_name', 'last_name', 'barangay', 'date_of_birth', 'sex');
                },
                'seniorIdApplication' => function($query) {
                    $query->select('application_id', 'full_name', 'gender', 'date_of_birth', 'address');
                }
            ])
            ->orderBy('submitted_at', 'desc')
            ->paginate(20);

        // Get barangays for filtering
        $barangays = Barangay::orderBy('name')->get();

        // Get seniors and other variables for compatibility with the main table view
        $seniors = collect(); // Empty collection since we're only showing ID applications
        $benefitsApplications = collect(); // Empty collection
        $pensionApplications = collect(); // Empty collection

        return view('seniors.id-applications-wrapper', compact('seniors', 'benefitsApplications', 'pensionApplications', 'idApplications', 'barangays'));
    }

    /**
     * Delete a pension application.
     */
    public function deletePensionApplication(string $id): RedirectResponse
    {
        try {
            $application = Application::findOrFail($id);
            $application->delete();

            return redirect()->route('seniors.pension')
                ->with('success', 'Pension application deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('seniors.pension')
                ->with('error', 'Unable to delete pension application. Please try again.');
        }
    }

    /**
     * Delete a benefits application.
     */
    public function deleteBenefitsApplication(string $id): RedirectResponse
    {
        try {
            $application = Application::findOrFail($id);
            $application->delete();

            return redirect()->route('seniors.benefits')
                ->with('success', 'Benefits application deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('seniors.benefits')
                ->with('error', 'Unable to delete benefits application. Please try again.');
        }
    }

    /**
     * Delete an ID application.
     */
    public function deleteIdApplication(string $id): RedirectResponse
    {
        try {
            $application = Application::findOrFail($id);
            $application->delete();

            // Clear caches so the tables reflect the deletion immediately
            $this->clearRelevantCaches();

            return redirect()->route('seniors.id-applications')
                ->with('success', 'ID application deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Unable to delete ID application. Please try again.');
        }
    }

    /**
     * Show the form for editing a benefits application.
     */
    public function editBenefits(string $id): View
    {
        $application = Application::with(['senior', 'benefitsApplication'])
            ->whereHas('senior')
            // ->whereHas('benefitsApplication') // Commented out due to database restructuring
            ->findOrFail($id);

        $senior = $application->senior;
        
        // Ensure metadata is properly formatted for the view
        $metadata = $application->metadata ?? [];
        if (is_string($metadata)) {
            $metadata = json_decode($metadata, true) ?? [];
        }
        
        return view('seniors.edit_comprehensive_benefits', compact('application', 'senior', 'metadata'));
    }

    /**
     * Update a benefits application.
     */
    public function updateBenefits(Request $request, string $id): RedirectResponse
    {
        Log::info('updateBenefits method called', ['id' => $id, 'data' => $request->all()]);
        
        // Debug assessment data specifically
        Log::info('Assessment data received', [
            'findings_concerns' => $request->input('findings_concerns'),
            'initial_assessment' => $request->input('initial_assessment')
        ]);
        try {
            $application = Application::with(['senior'])
                ->whereHas('senior')
                // ->whereHas('benefitsApplication') // Commented out due to database restructuring
                ->findOrFail($id);
            
            // Refresh the application to ensure we have the latest metadata
            $application->refresh();
            
            // Debug: Log current senior data
            Log::info('Current senior data', ['senior' => $application->senior->toArray()]);
            
            // Validate milestone age is a valid milestone age
            $milestoneAge = (int) $request->milestone_age;
            $validMilestoneAges = [80, 85, 90, 95, 100];
            
            if (!in_array($milestoneAge, $validMilestoneAges)) {
                return redirect()->back()
                    ->with('error', 'Invalid milestone age. Please select a valid milestone age (80, 85, 90, 95, or 100).');
            }
            
            // Validate the data - benefits specific fields from form_existing_senior.blade.php
            $validatedData = $request->validate([
                'milestone_age' => 'required|integer|in:80,85,90,95,100',
                'osca_id' => 'nullable|string|max:255',
                'rrn' => 'nullable|string|max:255',
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'name_extension' => 'nullable|string|max:10',
                'date_of_birth' => 'nullable|date',
                'age' => 'nullable|integer|min:0',
                'res_house_number' => 'nullable|string|max:255',
                'res_street' => 'nullable|string|max:255',
                'res_barangay' => 'nullable|string|max:255',
                'res_city' => 'nullable|string|max:255',
                'res_province' => 'nullable|string|max:255',
                'res_zip' => 'nullable|string|max:10',
                'perm_house_number' => 'nullable|string|max:255',
                'perm_street' => 'nullable|string|max:255',
                'perm_barangay' => 'nullable|string|max:255',
                'perm_city' => 'nullable|string|max:255',
                'perm_province' => 'nullable|string|max:255',
                'perm_zip' => 'nullable|string|max:10',
                'sex' => 'nullable|string|in:Male,Female',
                'civil_status' => 'nullable|string',
                'civil_status_others' => 'nullable|string|max:255',
                'citizenship' => 'nullable|string|in:Filipino,Dual',
                'dual_citizenship_details' => 'nullable|string|max:255',
                'spouse_name' => 'nullable|string|max:255',
                'spouse_citizenship' => 'nullable|string|max:255',
                'children' => 'nullable|array',
                'authorized_reps' => 'nullable|array',
                'contact_number' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'primary_beneficiary' => 'nullable|string|max:255',
                'contingent_beneficiary' => 'nullable|string|max:255',
                'utilization' => 'nullable|array',
                'utilization_others' => 'nullable|string|max:255',
                'certification' => 'nullable|array',
                'status' => 'required|string|in:pending,received,approved,rejected',
                'findings_concerns' => 'nullable|string',
                'initial_assessment' => 'nullable|string|in:eligible,ineligible',
            ]);

            // Update the senior information - benefits specific fields
            $seniorUpdateData = [
                'osca_id' => $validatedData['osca_id'] ?? $application->senior->osca_id,
                'other_govt_id' => $validatedData['rrn'] ?? $application->senior->other_govt_id,
                'first_name' => $validatedData['first_name'] ?? $application->senior->first_name,
                'last_name' => $validatedData['last_name'] ?? $application->senior->last_name,
                'middle_name' => $validatedData['middle_name'] ?? $application->senior->middle_name,
                'name_extension' => $validatedData['name_extension'] ?? $application->senior->name_extension,
                'date_of_birth' => $validatedData['date_of_birth'] ?? $application->senior->date_of_birth,
                'residence' => $validatedData['res_house_number'] ?? $application->senior->residence,
                'street' => $validatedData['res_street'] ?? $application->senior->street,
                'barangay' => $validatedData['res_barangay'] ?? $application->senior->barangay,
                'city' => $validatedData['res_city'] ?? $application->senior->city,
                'province' => $validatedData['res_province'] ?? $application->senior->province,
                'sex' => $validatedData['sex'] ?? $application->senior->sex,
                'marital_status' => $validatedData['civil_status'] ?? $application->senior->marital_status,
                'contact_number' => $validatedData['contact_number'] ?? $application->senior->contact_number,
                'email' => $validatedData['email'] ?? $application->senior->email,
            ];
            
            // Debug: Log what we're updating
            Log::info('Updating senior with data', ['updateData' => $seniorUpdateData]);
            
            $application->senior->update($seniorUpdateData);
            
            // Debug: Log updated senior data
            Log::info('Updated senior data', ['senior' => $application->senior->fresh()->toArray()]);

            // Store additional form data in JSON fields or separate tables as needed
            // For now, we'll store the additional data in the application's metadata
            $additionalData = [
                'permanent_address' => [
                    'house_number' => $validatedData['perm_house_number'] ?? '',
                    'street' => $validatedData['perm_street'] ?? '',
                    'barangay' => $validatedData['perm_barangay'] ?? '',
                    'city' => $validatedData['perm_city'] ?? '',
                    'province' => $validatedData['perm_province'] ?? '',
                    'zip' => $validatedData['perm_zip'] ?? '',
                ],
                'spouse_information' => [
                    'name' => $validatedData['spouse_name'] ?? '',
                    'citizenship' => $validatedData['spouse_citizenship'] ?? '',
                ],
                'children' => $validatedData['children'] ?? [],
                'authorized_representatives' => $validatedData['authorized_reps'] ?? [],
                'beneficiaries' => [
                    'primary' => $validatedData['primary_beneficiary'] ?? '',
                    'contingent' => $validatedData['contingent_beneficiary'] ?? '',
                ],
                'utilization' => $validatedData['utilization'] ?? [],
                'utilization_others' => $validatedData['utilization_others'] ?? '',
                'certification' => $validatedData['certification'] ?? [],
                'citizenship_details' => [
                    'citizenship' => $validatedData['citizenship'] ?? '',
                    'dual_citizenship_details' => $validatedData['dual_citizenship_details'] ?? '',
                ],
                'civil_status_others' => $validatedData['civil_status_others'] ?? '',
                'assessment' => [
                    'findings_concerns' => $validatedData['findings_concerns'] ?? '',
                    'initial_assessment' => $validatedData['initial_assessment'] ?? '',
                ],
            ];

            // Update application with additional data - merge with existing metadata
            $existingMetadata = $application->metadata ?? [];
            
            // Ensure metadata is an array (handle both string and array cases)
            if (is_string($existingMetadata)) {
                $existingMetadata = json_decode($existingMetadata, true) ?? [];
            } elseif (!is_array($existingMetadata)) {
                $existingMetadata = [];
            }
            
            // Merge the data, preserving existing assessment data
            $mergedMetadata = array_merge($existingMetadata, $additionalData);
            
            Log::info('Updating application metadata', [
                'metadata_type' => gettype($application->metadata),
                'metadata_raw' => $application->metadata,
                'existing_metadata' => $existingMetadata,
                'new_data' => $additionalData,
                'merged_metadata' => $mergedMetadata
            ]);
            
            $application->update([
                'metadata' => $mergedMetadata
            ]);

            // Update the benefits application (new structure)
            Log::info('Updating benefits application', ['milestone_age' => $validatedData['milestone_age']]);
            
            // Find or create benefits application for this senior
            $benefitsApp = BenefitsApplication::where('senior_id', $application->senior_id)->first();
            if ($benefitsApp) {
                $benefitsApp->update([
                    'milestone_age' => $validatedData['milestone_age'],
                    'civil_status' => $validatedData['civil_status'] ?? null,
                    'civil_status_others' => $validatedData['civil_status_others'] ?? null,
                    'primary_beneficiary' => $validatedData['primary_beneficiary'] ?? null,
                    'contingent_beneficiary' => $validatedData['contingent_beneficiary'] ?? null,
                    'utilization' => $validatedData['utilization'] ?? null,
                    'utilization_others' => $validatedData['utilization_others'] ?? null,
                ]);
            } else {
                // Create benefits application if it doesn't exist
                $benefitsApp = BenefitsApplication::create([
                    'application_id' => $application->id,
                    'senior_id' => $application->senior_id,
                    'milestone_age' => $validatedData['milestone_age'],
                    'civil_status' => $validatedData['civil_status'] ?? null,
                    'civil_status_others' => $validatedData['civil_status_others'] ?? null,
                    'primary_beneficiary' => $validatedData['primary_beneficiary'] ?? null,
                    'contingent_beneficiary' => $validatedData['contingent_beneficiary'] ?? null,
                    'utilization' => $validatedData['utilization'] ?? null,
                    'utilization_others' => $validatedData['utilization_others'] ?? null,
                ]);
            }
                
            Log::info('Benefits application updated', [
                'senior_id' => $application->senior_id,
                'primary_beneficiary' => $validatedData['primary_beneficiary'] ?? null,
                'contingent_beneficiary' => $validatedData['contingent_beneficiary'] ?? null,
                'utilization' => $validatedData['utilization'] ?? null,
                'utilization_others' => $validatedData['utilization_others'] ?? null,
            ]);
            
            // Update the application status
            $application->update([
                'status' => $validatedData['status'],
                'notes' => 'Updated via edit form at ' . now()
            ]);

            return redirect()->route('seniors.benefits')
                    ->with('success', 'Benefits application updated successfully! Status: ' . ucfirst(str_replace('_', ' ', $validatedData['status'])));
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed in updateBenefits', ['errors' => $e->errors(), 'data' => $request->all()]);
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating benefits application', ['error' => $e->getMessage(), 'data' => $request->all()]);
            return redirect()->back()
                ->with('error', 'Unable to update benefits application. Please check all required fields and try again.')
                ->withInput();
        }
    }

    /**
     * Securely serve senior photos from private storage
     */
    public function servePhoto(string $id)
    {
        $senior = Senior::findOrFail($id);
        
        if (!$senior->photo_path) {
            abort(404, 'Photo not found');
        }
        
        // Check if file exists in private storage
        if (!Storage::disk('private')->exists($senior->photo_path)) {
            abort(404, 'Photo file not found');
        }
        
        // Return the file with proper headers
        $file = Storage::disk('private')->get($senior->photo_path);
        $mimeType = mime_content_type(Storage::disk('private')->path($senior->photo_path));
        
        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . basename($senior->photo_path) . '"');
    }
}
