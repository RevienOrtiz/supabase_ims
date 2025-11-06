<x-sidebar>
  <x-header title="SENIOR ID" icon="fas fa-id-card">
    @include('message.popup_message')
    <div class="main">
        <div class="form">
            <div class="form-content">
               <div class="form-section">
                   
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <img src="{{ asset('images/DSWD_LOGO.png') }}" alt="DSWD Logo" class="logo-dswd" style="max-height: 80px;">
                            <div class="text-center flex-grow-1">
                                <div class="title-main" style="color: #e31575; font-size: 24px; font-weight: 800;">APPLICATION FORM</div>
                                <div class="title-main" style="color: #e31575; font-size: 20px; font-weight: 800;">SENIOR CITIZEN ID</div>
                            </div>
                            <div class="d-flex gap-2">
                                <img src="{{ asset('images/Bagong_Pilipinas.png') }}" alt="Bagong Pilipinas Logo" class="logo-bagong-pilipinas" style="max-height: 80px;">
                            </div>
                        </div>
                        <!-- Pink line separator -->
                        <div style="height: 5px; background-color: #e31575; width: 100%; margin: 0 0 -30px 0;"></div>
            </div>
            

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 20px;">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
                <form action="{{ route('forms.seniorID.store') }}" method="POST">
                    @csrf
                    
                    @if($errors->any())
                        <div class="alert alert-danger" style="color: red; margin-bottom: 1rem; padding: 0.5rem; background-color: #ffe6e6; border: 1px solid red; border-radius: 4px;">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    
                    
                    <div class="form-step active">
                        <div class="form-section-content">
                            <div class="mb-4">
                                <label class="input-label">Select Senior Citizen</label>
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <label class="form-label small">Choose Senior Citizen <span class="text-danger">*</span></label>
                                        <div class="searchable-dropdown">
                                            <input type="text" id="senior-search" class="form-control form-control-sm" placeholder="Type to search seniors..." autocomplete="off">
                                            <input type="hidden" name="senior_id" id="selected-senior-id" required>
                                            <div id="senior-dropdown" class="dropdown-menu" style="display: none; max-height: 300px; overflow-y: auto; width: 100%;">
                                                <!-- Search results will be populated here -->
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Type to search for senior citizens. Only existing senior citizens can apply for Senior ID cards.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label small">Address <span class="text-danger">*</span></label>
                                        <input type="text" name="address" id="senior_address" placeholder="Address" required class="form-control form-control-sm" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Gender <span class="text-danger">*</span></label>
                                        <input type="text" name="gender" id="senior_gender" required class="form-control form-control-sm" readonly>
                                    </div>
                                </div>
                                
                                <div class="row g-4 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label small">Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" id="senior_date_of_birth" name="date_of_birth" required class="form-control form-control-sm date-picker">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Age</label>
                                        <input type="number" id="senior_age" name="age" readonly class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Birth Place <span class="text-danger">*</span></label>
                                        <input type="text" name="birth_place" id="senior_birth_place" placeholder="Birth Place" required class="form-control form-control-sm">
                                    </div>
                                    </div>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label small">Occupation</label>
                                        <input type="text" name="occupation" placeholder="Occupation" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label small">Civil Status <span class="text-danger">*</span></label>
                                        <select name="civil_status" id="senior_civil_status" required class="form-select form-select-sm">
                                            <option value="">Select Civil Status</option>
                                            <option value="Single">Single</option>
                                            <option value="Married">Married</option>
                                            <option value="Widowed">Widowed</option>
                                            <option value="Divorced">Divorced</option>
                                            <option value="Separated">Separated</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Annual Income <span class="text-danger">*</span></label>
                                        <input type="number" name="annual_income" id="senior_annual_income" placeholder="Annual Income" required class="form-control form-control-sm">
                                    </div>
                                </div>
                                
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label small">Pension Source</label>
                                        <select name="pension_source" class="form-select form-select-sm">
                                            <option value="">Select Pension Source</option>
                                            <option value="SSS">SSS</option>
                                            <option value="GSIS">GSIS</option>
                                            <option value="Private">Private</option>
                                            <option value="None">None</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">C.T.C. Number</label>
                                        <input type="text" name="ctc_number" placeholder="Community Tax Certificate Number" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="input-label">Application Details</label>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label small">Date of Application</label>
                                        <input type="date" name="date_of_application" value="{{ date('Y-m-d') }}" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Place of Issuance <span class="text-danger">*</span></label>
                                        <input type="text" name="place_of_issuance" value="Municipality of Lingayen, Pangasinan" class="form-control form-control-sm" required>
                                    </div>
                                </div>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label small">Date Issued <span class="text-danger">*</span></label>
                                        <input type="date" name="date_of_issued" value="{{ date('Y-m-d') }}" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Date Received <span class="text-danger">*</span></label>
                                        <input type="date" name="date_of_received" value="{{ date('Y-m-d') }}" class="form-control form-control-sm" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="step-navigation">
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* ===== MAIN LAYOUT ===== */
        .main {
            margin-left: 250px;
            margin-top: 60px;
            height: calc(100vh - 60px);
            padding: 0;
            display: flex;
            flex-direction: column;
            background: #f5faff;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            border-radius: px;
        }
        
        .form {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .form-content {
            flex: 1;
            overflow-y: auto;
            padding: 0;
        }
        .form-section {
            border-radius: 0;
            padding: 24px;
            margin: 0;
        }
        
        /* ===== FORM HEADER ===== */
        .form-header {
            background: linear-gradient(135deg, #e31575 0%, #c01060 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            border-radius: 12px 12px 0 0;
            box-shadow: 0 2px 10px rgba(227, 21, 117, 0.2);
        }
        
        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        /* ===== FORM STEP STYLES ===== */
        .form-step {
            display: block;
            padding: 2.5rem;
        }
        
        .form-section-content {

        }
        
        /* ===== INPUT LABELS ===== */
        .input-label {
            font-size: 16px;
            font-weight: 700 !important;
            display: block !important;
            margin-bottom: 15px !important;
            color: #2c3e50 !important;
            letter-spacing: 0.3px !important;
        }
        
        .form-label.small {
            font-size: 13px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }
        
        /* ===== ENHANCED FORM CONTROLS WITH INNER SHADOW ===== */
        .form-control, .form-select, .form-control-sm, .form-select-sm {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #ffb7ce;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            margin-bottom: 12px;
            line-height: 1.5;
            background-color: #ffffff;
            transition: all 0.3s ease;
            /* Inner shadow effect */
            box-shadow: inset 0 2px 4px rgba(227, 21, 117, 0.1), inset 0 1px 2px rgba(227, 21, 117, 0.15);
        }

        .form-control:focus, .form-select:focus, .form-control-sm:focus, .form-select-sm:focus {
            outline: none;
            border-color: #e31575;
            background-color: #fefefe;
            /* Enhanced inner shadow on focus */
            box-shadow: inset 0 3px 6px rgba(227, 21, 117, 0.15), inset 0 2px 4px rgba(227, 21, 117, 0.2), 0 0 0 3px rgba(227, 21, 117, 0.12);
            transform: translateY(-1px);
        }

        .form-control:hover, .form-select:hover, .form-control-sm:hover, .form-select-sm:hover {
            border-color: #c01060;
            box-shadow: inset 0 2px 5px rgba(227, 21, 117, 0.12), inset 0 1px 3px rgba(227, 21, 117, 0.18);
        }

        /* Enhanced Select Dropdown Styling */
        .form-select, .form-select-sm {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23e31575' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 20px;
            padding-right: 45px;
            cursor: pointer;
        }

        .form-select:focus, .form-select-sm:focus {
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23c01060' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        }

        /* Textarea Styling */
        textarea.form-control {
            resize: vertical;
            min-height: 90px;
            font-family: inherit;
        }

        /* Radio and Checkbox Enhanced Styling */
        input[type="radio"], input[type="checkbox"] {
            margin-right: 10px;
            accent-color: #e31575;
            transform: scale(1.2);
            cursor: pointer;
        }

        .form-check-input {
            border: 2px solid #e31575;
            box-shadow: inset 0 1px 2px rgba(227, 21, 117, 0.1);
        }

        .form-check-input:checked {
            background-color: #e31575;
            border-color: #e31575;
            box-shadow: inset 0 1px 3px rgba(227, 21, 117, 0.2), 0 0 0 2px rgba(227, 21, 117, 0.1);
        }

        .form-check-input:focus {
            border-color: #c01060;
            box-shadow: inset 0 1px 3px rgba(227, 21, 117, 0.15), 0 0 0 3px rgba(227, 21, 117, 0.12);
        }

        /* Form Labels Enhancement */
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .form-label.fw-bold {
            font-weight: 700;
        }
        
        /* ===== STEP NAVIGATION ===== */
        .step-navigation {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem 2rem;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            margin-top: 2rem;
        }
        
        .step-navigation .btn {
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            border: none;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        
        .step-navigation .btn-primary {
            background-color: #e31575;
            border-color: #e31575;
            color: white;
            font-weight: bold;
        }
        
        .step-navigation .btn-primary:hover {
            background-color: #ffb7ce;
            border-color: #ffb7ce;
            color: #e31575;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }
        
        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 768px) {
            .main {
                margin: 0;
            }
            
            .form {
                margin: 10px;
                border-radius: 8px;
            }
            
            .form-header {
                padding: 1.5rem 1rem;
                border-radius: 8px 8px 0 0;
            }
            
            .form-title {
                font-size: 1.1rem;
            }
            
            .form-step {
                padding: 1.5rem 1rem;
            }
            
            .row.g-4 {
                --bs-gutter-x: 1rem;
            }
            
            .step-navigation {
                padding: 1rem;
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .step-navigation .btn {
                width: 100%;
                padding: 0.875rem 1rem;
                font-size: 0.85rem;
            }
        }

        /* Searchable Dropdown Styles */
        .searchable-dropdown {
            position: relative;
        }

        .searchable-dropdown .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            background: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 0;
            margin: 0;
        }

        .searchable-dropdown .dropdown-item {
            display: block;
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s;
        }

        .searchable-dropdown .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .searchable-dropdown .dropdown-item:last-child {
            border-bottom: none;
        }

        .searchable-dropdown .dropdown-item.selected {
            background-color: #e31575;
            color: white;
        }

        .searchable-dropdown .dropdown-item .senior-name {
            font-weight: 600;
            color: #333;
        }

        .searchable-dropdown .dropdown-item .senior-details {
            font-size: 0.85em;
            color: #666;
            margin-top: 2px;
        }

        .searchable-dropdown .dropdown-item.selected .senior-name,
        .searchable-dropdown .dropdown-item.selected .senior-details {
            color: white;
        }

        .no-results {
            padding: 12px;
            text-align: center;
            color: #666;
            font-style: italic;
        }

        /* Deceased Senior Styling */
        .deceased-senior {
            background-color: #f8f9fa !important;
            opacity: 0.7;
        }

        .deceased-senior:hover {
            background-color: #e9ecef !important;
        }

        .deceased-badge {
            color: #dc3545;
            font-weight: bold;
            font-size: 0.8em;
            margin-left: 8px;
        }

        /* Date Picker Styles */
        .date-picker {
            position: relative;
        }

        .date-picker::-webkit-calendar-picker-indicator {
            background: transparent;
            bottom: 0;
            color: transparent;
            cursor: pointer;
            height: auto;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            width: auto;
        }

        .date-picker {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23666'%3e%3cpath fill-rule='evenodd' d='M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z' clip-rule='evenodd'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 16px;
            padding-right: 35px;
        }

        .date-picker:focus {
            border-color: #e31575;
            box-shadow: 0 0 0 0.2rem rgba(227, 21, 117, 0.25);
        }

        .date-picker:hover {
            border-color: #e31575;
        }
    </style>

    <script>
        let allSeniors = [];
        let selectedSenior = null;

        // Auto-calculate age from date of birth
        function calculateAge() {
            const dateOfBirthInput = document.getElementById('senior_date_of_birth');
            const ageInput = document.getElementById('senior_age');
            
            if (dateOfBirthInput && ageInput && dateOfBirthInput.value) {
                const birthDate = new Date(dateOfBirthInput.value);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();
                
                // Adjust age if birthday hasn't occurred this year
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                
                ageInput.value = age;
                console.log('Age calculated:', age, 'for birth date:', dateOfBirthInput.value);
            }
        }

        // Load all seniors on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadAllSeniors();
            setupSearchableDropdown();
            
            // Add age calculation for date of birth
            const dateOfBirthInput = document.getElementById('senior_date_of_birth');
            if (dateOfBirthInput) {
                dateOfBirthInput.addEventListener('change', calculateAge);
                dateOfBirthInput.addEventListener('input', calculateAge); // Also listen for input events
                // Calculate age on page load if date is already filled
                if (dateOfBirthInput.value) {
                    calculateAge();
                }
            }
            
            // Add form submission debugging
            const form = document.querySelector('form[method="POST"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    console.log('Form submitting...');
                    
                    // Check if senior_id is selected
                    const seniorId = document.getElementById('selected-senior-id').value;
                    console.log('Senior ID selected:', seniorId);
                    
                    if (!seniorId) {
                        e.preventDefault();
                        alert('Please select a senior citizen first.');
                        return false;
                    }
                    
                    // Form validation complete
                    
                    // Check for empty required fields with detailed logging
                    const requiredFields = ['senior_id', 'address', 'gender', 'date_of_birth', 'birth_place', 'civil_status', 'annual_income', 'place_of_issuance', 'date_of_issued', 'date_of_received'];
                    const emptyFields = [];
                    const fieldDetails = {};
                    
                    requiredFields.forEach(field => {
                        const value = formData.get(field);
                        
                        if (!value || value.trim() === '') {
                            emptyFields.push(field);
                        }
                    });
                    
                    if (emptyFields.length > 0) {
                        e.preventDefault();
                        alert('Please fill out the following required fields: ' + emptyFields.join(', '));
                        return false;
                    }
                    
                    // Additional validation complete
                    
                    // Form submission proceeding
                });
            }
        });

        function loadAllSeniors() {
            // Load only essential data for form auto-fill (SECURITY: No sensitive data exposure)
            const seniorsData = {!! json_encode(\App\Models\Senior::orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'middle_name', 'osca_id', 'barangay', 'sex', 'date_of_birth', 'birth_place', 'marital_status', 'monthly_income', 'status'])->map(function($senior) {
                return [
                    'id' => $senior->id,
                    'first_name' => $senior->first_name,
                    'last_name' => $senior->last_name,
                    'middle_name' => $senior->middle_name,
                    'osca_id' => $senior->osca_id,
                    'barangay' => $senior->barangay,
                    'sex' => $senior->sex,
                    'date_of_birth' => $senior->date_of_birth,
                    'birth_place' => $senior->birth_place,
                    'marital_status' => $senior->marital_status,
                    'monthly_income' => $senior->monthly_income,
                    'status' => $senior->status
                ];
            })) !!};
            
            // Format dates for HTML date input and add status indicator
            allSeniors = seniorsData.map(senior => ({
                ...senior,
                date_of_birth: senior.date_of_birth ? senior.date_of_birth.split('T')[0] : null,
                is_deceased: senior.status === 'deceased'
            }));
            
            // Seniors loaded successfully
        }

        function setupSearchableDropdown() {
            const searchInput = document.getElementById('senior-search');
            const dropdown = document.getElementById('senior-dropdown');
            const selectedSeniorId = document.getElementById('selected-senior-id');

            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                
                if (query.length < 1) {
                    dropdown.style.display = 'none';
                    return;
                }

                // Filter seniors based on search query
                const filteredSeniors = allSeniors.filter(senior => {
                    const fullName = `${senior.last_name}, ${senior.first_name} ${senior.middle_name || ''}`.toLowerCase();
                    const oscaId = senior.osca_id ? senior.osca_id.toLowerCase() : '';
                    return fullName.includes(query) || oscaId.includes(query);
                });

                displaySearchResults(filteredSeniors);
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.searchable-dropdown')) {
                    dropdown.style.display = 'none';
                }
            });

            // Show dropdown when focusing on search input
            searchInput.addEventListener('focus', function() {
                if (this.value.length >= 1) {
                    dropdown.style.display = 'block';
                }
            });
        }

        function displaySearchResults(seniors) {
            const dropdown = document.getElementById('senior-dropdown');
            
            if (seniors.length === 0) {
                dropdown.innerHTML = '<div class="no-results">No seniors found matching your search.</div>';
            } else {
                dropdown.innerHTML = seniors.map(senior => `
                    <div class="dropdown-item ${senior.is_deceased ? 'deceased-senior' : ''}" data-senior-id="${senior.id}" data-name="${senior.first_name} ${senior.last_name}" data-age="${senior.age || ''}" data-gender="${senior.sex || ''}" data-address="${senior.barangay || ''}" data-birth-date="${senior.date_of_birth || ''}">
                        <div class="senior-name">
                            ${senior.last_name}, ${senior.first_name} ${senior.middle_name || ''}
                            ${senior.is_deceased ? '<span class="deceased-badge">(DECEASED)</span>' : ''}
                        </div>
                        <div class="senior-details">OSCA ID: ${senior.osca_id || 'N/A'} | Barangay: ${senior.barangay || 'N/A'}</div>
                    </div>
                `).join('');

                // Add click event listeners to dropdown items
                dropdown.querySelectorAll('.dropdown-item').forEach(item => {
                    item.addEventListener('click', function() {
                        selectSenior(this);
                    });
                });
            }
            
            dropdown.style.display = 'block';
        }

        function selectSenior(element) {
            const seniorId = element.getAttribute('data-senior-id');
            const seniorName = element.getAttribute('data-name');
            const isDeceased = element.classList.contains('deceased-senior');
            
            // Check if senior is deceased
            if (isDeceased) {
                if (!confirm('This senior is marked as DECEASED. Are you sure you want to proceed with the application?')) {
                    return; // User cancelled
                }
            }
            
            // Update the search input to show selected senior
            document.getElementById('senior-search').value = seniorName;
            document.getElementById('selected-senior-id').value = seniorId;
            
            // Hide dropdown
            document.getElementById('senior-dropdown').style.display = 'none';
            
            // Load senior data
            loadSeniorData(seniorId);
        }

        function loadSeniorData(seniorId) {
            
            if (!seniorId) {
                // Clear all fields if no senior selected
                document.getElementById('senior_address').value = '';
                document.getElementById('senior_gender').value = '';
                document.getElementById('senior_date_of_birth').value = '';
                document.getElementById('senior_age').value = '';
                document.getElementById('senior_birth_place').value = '';
                document.getElementById('senior_civil_status').value = '';
                document.getElementById('senior_annual_income').value = '';
                return;
            }

            // Find the senior in our data
            const senior = allSeniors.find(s => s.id == seniorId);
            
            if (senior) {
                // Populate the form fields with senior data
                document.getElementById('senior_address').value = senior.barangay || '';
                document.getElementById('senior_gender').value = senior.sex || '';
                
                // Calculate age from birth date
                if (senior.date_of_birth) {
                    const dateField = document.getElementById('senior_date_of_birth');
                    dateField.value = senior.date_of_birth;
                    calculateAgeFromDate(senior.date_of_birth);
                }
                
                document.getElementById('senior_birth_place').value = senior.birth_place || 'Lingayen, Pangasinan';
                
                // Auto-fill civil status from senior data
                document.getElementById('senior_civil_status').value = senior.marital_status || '';
                
                // Auto-calculate annual income from monthly income
                if (senior.monthly_income) {
                    // Convert to number and multiply by 12
                    const monthlyIncome = parseFloat(senior.monthly_income);
                    if (!isNaN(monthlyIncome)) {
                        const annualIncome = monthlyIncome * 12;
                        document.getElementById('senior_annual_income').value = annualIncome.toFixed(2);
                    } else {
                        document.getElementById('senior_annual_income').value = '';
                    }
                } else {
                    document.getElementById('senior_annual_income').value = '';
                }
                
                // Senior data loaded successfully
            }
        }

        // Add event listener for date picker changes
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('senior_date_of_birth');
            if (dateInput) {
                dateInput.addEventListener('change', function() {
                    if (this.value) {
                        calculateAgeFromDate(this.value);
                    }
                });
            }
        });

        function calculateAgeFromDate(birthDateString) {
            const birthDate = new Date(birthDateString);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            document.getElementById('senior_age').value = age;
        }
    </script>
    </x-header>
</x-sidebar>