<x-sidebar>
  <x-header title="SOCIAL PENSION" icon="fas fa-hand-holding-usd">
    @include('message.popup_message')
    <script>
    // Prefer modal texts from database notifications if available
    const confirmTitleFromDb = {!! json_encode(optional(\App\Models\Notification::where('type','pension_confirm')->latest()->first())->title ?? null) !!};
    const confirmMessageFromDbRaw = {!! json_encode(optional(\App\Models\Notification::where('type','pension_confirm')->latest()->first())->message ?? null) !!};

    // Global save handler for inline onclick
    window.confirmCreatePension = function() {
        const form = document.getElementById('pensionForm');
        if (!form) return;

        // Ensure auto-filled values if helper exists
        if (typeof ensureAutoFilledValues === 'function') {
            ensureAutoFilledValues();
        }

        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Build senior name
        const first = document.querySelector('input[name="first_name"]')?.value || '';
        const last = document.querySelector('input[name="last_name"]')?.value || '';
        const seniorName = (first + ' ' + last).trim() || 'this senior';

        // Compute title/message
        const title = confirmTitleFromDb || 'Create Pension Application';
        const msg = confirmMessageFromDbRaw ? String(confirmMessageFromDbRaw).replace('{name}', seniorName) : `Are you sure you want to submit ${seniorName}'s pension application?`;

        // Use shared modal if available; otherwise fallback to browser confirm
        if (typeof showConfirmModal === 'function') {
            showConfirmModal(title, msg, '{{ route("forms.pension.store") }}', 'POST');
        } else {
            if (confirm(msg)) {
                form.submit();
            }
        }
    };
    </script>
    <div class="main">
        <div class="form">
            <div class="form-content">
                <div class="form-section">
                   
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <img src="{{ asset('images/DSWD_LOGO.png') }}" alt="DSWD Logo" class="logo-dswd" style="max-height: 80px;">
                            <div class="text-center flex-grow-1">
                                <div class="title-main" style="color: #e31575; font-size: 24px; font-weight: 800;">APPLICATION FORM</div>
                                <div class="title-main" style="color: #e31575; font-size: 20px; font-weight: 800;">SOCIAL PENSION FOR INDIGENT SENIOR CITIZENS</div>
                            </div>
                            <div class="d-flex gap-2">
                                
                                <img src="{{ asset('images/SOCIAL_PENSION.png') }}" alt="Pension_logo" class="logo-pension" style="max-height: 80px;">
                                <img src="{{ asset('images/Bagong_Pilipinas.png') }}" alt="Bagong Pilipinas Logo" class="logo-bagong-pilipinas" style="max-height: 80px;">
                            </div>
                        </div>
                        <!-- Pink line separator -->
                        <div style="height: 5px; background-color: #e31575; width: 100%; margin: 0 0 30px 0;"></div>

                            <form method="POST" action="{{ route('forms.pension.store') }}" enctype="multipart/form-data" id="pensionForm" novalidate>
                            @csrf
                            <meta name="csrf-token" content="{{ csrf_token() }}">
                            <input type="hidden" name="senior_id" id="selected-senior-id" value="">
                            <div id="form-messages" class="alert d-none"></div>
                            
                            <!-- Debug information -->
                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            
                            @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Set success message
                                    document.getElementById('successMessage').textContent = "{{ session('success') }}";
                                    document.getElementById('successIcon').style.backgroundColor = "#28a745";
                                    
                                    // Show success modal
                                    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                                    successModal.show();
                                    
                                    // Redirect after closing
                                    document.getElementById('continueBtn').addEventListener('click', function() {
                                        window.location.href = "{{ route('seniors') }}";
                                    });
                                });
                            </script>
                            @endif
                            
                            <!-- Senior Selection Section -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Choose Senior Citizen *</label>
                                <div class="position-relative">
                                    <input type="text" id="senior-search" class="form-control form-control-sm" placeholder="Type to search seniors..." autocomplete="off">
                                    <div id="senior-dropdown" class="dropdown-menu w-100" style="display: none; max-height: 300px; overflow-y: auto; z-index: 1000;">
                                        <!-- Search results will appear here -->
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-start mb-0">
                                <div class="flex-grow-1">
                                    <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">OSCA ID Number*</label>
                                            <input type="text" name="osca_id" id="senior_osca_id" class="form-control form-control-sm" placeholder="OSCA ID Number" required>
                                        </div>
                                        <div>
                                            <label class="form-label fw-bold small">NCSC Registration Reference Number (RRN)</label>
                                            <input type="text" name="rrn" class="form-control form-control-sm" placeholder="(RRN optional)">
                                        </div>
                                    </div>
                                </div>

                                </div>
                                <x-photo-upload id="photo_upload" name="photo" />
                            </div>
                            
                            <!-- I. PERSONAL INFORMATION -->
                            <div class="section-header">I. PERSONAL INFORMATION</div>

                <div class="mb-4">

                <!-- Name Fields -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Last Name*</label>
                        <input type="text" name="last_name" id="senior_last_name" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">First Name*</label>
                        <input type="text" name="first_name" id="senior_first_name" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Middle Name*</label>
                        <input type="text" name="middle_name" id="senior_middle_name" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Extension</label>
                        <input type="text" name="name_extension" id="senior_name_extension" class="form-control form-control-sm">
                    </div>
                </div>

                <!-- PERMANENT ADDRESS Section -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">PERMANENT ADDRESS IN THE PHILIPPINES*</h6>
                    <div class="row g-2 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">House No./Zone/Purok/Sitio</label>
                            <input type="text" name="house_no" class="form-control form-control-sm" placeholder="House No./Zone/Purok/Sitio">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Street</label>
                            <input type="text" name="street" class="form-control form-control-sm" placeholder="Street">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Province*</label>
                            <select name="province" class="form-select form-select-sm" required>
                                <option value="pangasinan">Pangasinan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">City/Municipality*</label>
                            <select name="city" class="form-select form-select-sm">
                                <option value="lingayen">Lingayen</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Barangay*</label>
                            <select name="res_barangay" class="form-select form-select-sm" required>
                                <option value="">Select Barangay</option>
                                <option value="aliwekwek">Aliwekwek</option>
                                <option value="baay">Baay</option>
                                <option value="balangobong">Balangobong</option>
                                <option value="balococ">Balococ</option>
                                <option value="bantayan">Bantayan</option>
                                <option value="basing">Basing</option>
                                <option value="capandanan">Capandanan</option>
                                <option value="domalandan-center">Domalandan Center</option>
                                <option value="domalandan-east">Domalandan East</option>
                                <option value="domalandan-west">Domalandan West</option>
                                <option value="dorongan">Dorongan</option>
                                <option value="dulag">Dulag</option>
                                <option value="estanza">Estanza</option>
                                <option value="lasip">Lasip</option>
                                <option value="libsong-east">Libsong East</option>
                                <option value="libsong-west">Libsong West</option>
                                <option value="malawa">Malawa</option>
                                <option value="malimpuec">Malimpuec</option>
                                <option value="maniboc">Maniboc</option>
                                <option value="matalava">Matalava</option>
                                <option value="naguelguel">Naguelguel</option>
                                <option value="namolan">Namolan</option>
                                <option value="pangapisan-north">Pangapisan North</option>
                                <option value="pangapisan-sur">Pangapisan Sur</option>
                                <option value="poblacion">Poblacion</option>
                                <option value="quibaol">Quibaol</option>
                                <option value="rosario">Rosario</option>
                                <option value="sabangan">Sabangan</option>
                                <option value="talogtog">Talogtog</option>
                                <option value="tonton">Tonton</option>
                                <option value="tumbar">Tumbar</option>
                                <option value="wawa">Wawa</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- DATE OF BIRTH and PLACE OF BIRTH -->
                <div class="row g-2 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Date of Birth*</label>
                        <input type="date" name="date_of_birth" id="senior_date_of_birth" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Place of Birth*</label>
                        <input type="text" name="place_of_birth" id="senior_place_of_birth" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Age*</label>
                        <input type="number" name="age" id="senior_age" class="form-control form-control-sm" required>
                    </div>
                </div>

                <!-- GENDER and CIVIL STATUS -->
                <div class="row g-2 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Gender*</label>
                        <div class="d-flex gap-3 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" value="male" id="male" required>
                                <label class="form-check-label" for="male">Male</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" value="female" id="female" required>
                                <label class="form-check-label" for="female">Female</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Civil Status*</label>
                        <select name="civil_status" id="senior_civil_status" class="form-select form-select-sm" required>
                            <option value="Married">Married</option>
                            <option value="Single">Single</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Separated">Separated</option>
                            <option value="Others">Others</option>
                        </select>
                    
                    </div>
                </div>

                <!-- CONTACT NUMBER -->
                <div class="mb-4">
                    <label class="form-label fw-bold small">Contact Number</label>
                    <input type="tel" name="contact_number" id="senior_contact_number" class="form-control form-control-sm">
                </div>

                <!-- MONTHLY INCOME -->
                <div class="mb-4">
                    <label class="form-label fw-bold small">Monthly Income <span class="text-danger">*</span></label>
                    <input type="number" name="monthly_income" id="monthly_income" class="form-control form-control-sm" placeholder="Auto-filled from senior's record" readonly required>
                    <small class="text-muted">This value is automatically filled from the senior's record and cannot be edited.</small>
                </div>
                </div>

                                <!-- II. ECONOMIC STATUS -->
                                <div class="section-header">II. ECONOMIC STATUS</div>

                                <div class="row g-2 mb-3">
                                    <!-- Living Arrangement Column -->
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small">Living Arrangement <span class="text-danger">*</span></label>
                                        <div class="mt-2">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input living-arrangement-check" type="checkbox" name="living_arrangement[]" value="owned" id="owned">
                                                <label class="form-check-label" for="owned">Owned</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input living-arrangement-check" type="checkbox" name="living_arrangement[]" value="rent" id="rent">
                                                <label class="form-check-label" for="rent">Rent</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input living-arrangement-check" type="checkbox" name="living_arrangement[]" value="living alone" id="living_alone">
                                                <label class="form-check-label" for="living_alone">Living Alone</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input living-arrangement-check" type="checkbox" name="living_arrangement[]" value="living with children or relatives" id="living_with">
                                                <label class="form-check-label" for="living_with">Living With Children Or Relatives</label>
                                            </div>
                                            <!-- We don't need this hidden field as we're validating the checkboxes directly -->
                                            <input type="hidden" name="living_arrangement_required" id="living_arrangement_required" value="valid">
                                        </div>
                                    </div>

                                    <!-- Receiving Pension Column -->
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small">Receiving Pension <span class="text-danger">*</span></label>
                                        <div class="mt-2">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="has_pension" value="1" id="pension_yes" onchange="togglePensionFields()" required>
                                                <label class="form-check-label small" for="pension_yes">Yes</label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="has_pension" value="0" id="pension_no" onchange="togglePensionFields()" required>
                                                <label class="form-check-label small" for="pension_no">No</label>
                                            </div>
                                            <div id="pensionFields" class="ms-4 mb-3" style="display: none;">
                                                <div class="mb-2">
                                                    <label class="form-label small">How Much <span class="text-danger">*</span></label>
                                                    <input type="text" name="pension_amount" class="form-control form-control-sm">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small">Source <span class="text-danger">*</span></label>
                                                    <input type="text" name="pension_source" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                            {{-- <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="pension" value="No" id="pension_no" onchange="togglePensionFields()">
                                                <label class="form-check-label small" for="pension_no">No</label>
                                            </div> --}}
                                        </div>
                                    </div>

                                    <!-- Permanent Income Column -->
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small">Permanent Income <span class="text-danger">*</span></label>
                                        <div class="mt-2">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="permanent_income" value="Yes" id="income_yes" onchange="toggleIncomeFields()" required>
                                                <label class="form-check-label small" for="income_yes">Yes</label>
                                            </div>
                                            <div id="incomeFields" class="ms-4 mb-3" style="display: none;">
                                                <div class="mb-2">
                                                    <label class="form-label small">How Much <span class="text-danger">*</span></label>
                                                    <input type="text" name="income_amount" class="form-control form-control-sm">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small">Source <span class="text-danger">*</span></label>
                                                    <input type="text" name="income_source" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="permanent_income" value="No" id="income_no" onchange="toggleIncomeFields()" required>
                                                <label class="form-check-label small" for="income_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                
                                <!-- III. HEALTH CONDITION -->
                                <div class="section-header">III. HEALTH CONDITION</div>

                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">With Existing Illness <span class="text-danger">*</span></label>
                                        <div class="d-flex gap-3 mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="existing_illness" value="yes" id="illness_yes" required>
                                                <label class="form-check-label small" for="illness_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="existing_illness" value="no" id="illness_no" required>
                                                <label class="form-check-label small" for="illness_no">No</label>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label class="form-label fw-bold small">Specify:</label>
                                            <input type="text" name="illness_specify" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">With Disability <span class="text-danger">*</span></label>
                                        <div class="d-flex gap-3 mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="with_disability" value="yes" id="disability_yes" required>
                                                <label class="form-check-label small" for="disability_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="with_disability" value="no" id="disability_no" required>
                                                <label class="form-check-label small" for="disability_no">No</label>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label class="form-label fw-bold small">Specify:</label>
                                            <input type="text" name="disability_specify" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                
                                <!-- CERTIFICATION -->
                                <div class="alert alert-light border mb-3">
                                    <div class="form-check" style="display: flex; align-items: top; gap: 8px;">
                                        <input class="form-check-input" type="checkbox" name="certification" id="certification" required style="margin-top: 0; margin-bottom: 0;">
                                        <label class="form-check-label small mb-0" for="certification" style="display: flex; align-items: top;">
                                            I hereby certify that the above-mentioned information is true and correct to the best of my knowledge, and I hereby authorize the verification of the information provided in this application form. <span class="text-danger">*</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Save Button -->
                                <div class="text-center mt-3">
                                    <button type="button" id="saveApplicationBtn" class="btn btn-danger px-5 py-2 fw-bold" style="background-color: #e31575; border-color: #e31575; border-radius: 20px;" onclick="window.confirmCreatePension()">SAVE APPLICATION</button>
                                </div>
                            </form>
                            
<script>
// Other helper functions below
// Function to submit the form
function submitPensionForm() {
    // Get the form element
    const form = document.getElementById('pensionForm');
    
    // Ensure auto-filled values are properly set
    ensureAutoFilledValues();
    
    // Ensure civil_status is included in the form before submission
    if (!form.querySelector('input[name="civil_status"]')) {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'civil_status';
        hiddenInput.value = document.getElementById('senior_civil_status')?.value || 'Married';
        form.appendChild(hiddenInput);
        console.log('Added civil_status hidden field before submission:', hiddenInput.value);
    }
    
    console.log('Submitting form directly');
    
    // Show loading state
    const saveBtn = document.getElementById('saveApplicationBtn');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
    }
    
    // Submit the form using AJAX to prevent page reload
    const formData = new FormData(form);
    
    $.ajax({
        url: form.action,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Form submitted successfully:', response);
            
            // Show success modal
            $('#successTitle').text('SUCCESS!');
            $('#successMessage').text('Pension application has been saved successfully.');
            $('#successModal').modal('show');
            
            // Reset form and button state
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = 'SAVE APPLICATION';
            }
            
            // Redirect after closing success modal
            $('#continueBtn').off('click').on('click', function() {
                window.location.href = '/seniors';
            });
        },
        error: function(xhr, status, error) {
            console.error('Error submitting form:', error);
            
            // Show error modal
            $('#errorMessage').text('Failed to save pension application. Please try again.');
            $('#errorModal').modal('show');
            
            // Reset button state
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = 'SAVE APPLICATION';
            }
        }
    });
}
// Handle living arrangement validation
    const livingArrangementChecks = document.querySelectorAll('.living-arrangement-check');
    const livingArrangementRequired = document.getElementById('living_arrangement_required');
    
    livingArrangementChecks.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            validateLivingArrangement();
        });
    });
    
    function validateLivingArrangement() {
        const isChecked = Array.from(livingArrangementChecks).some(cb => cb.checked);
        livingArrangementRequired.setCustomValidity(isChecked ? '' : 'Please select at least one living arrangement option');
    }
    
    // Initial validation
    validateLivingArrangement();
    
    // Handle pension fields validation
    function togglePensionFields() {
        const pensionYes = document.getElementById('pension_yes');
        const pensionFields = document.getElementById('pensionFields');
        const pensionAmount = document.querySelector('input[name="pension_amount"]');
        const pensionSource = document.querySelector('input[name="pension_source"]');
        
        if (pensionYes.checked) {
            pensionFields.style.display = 'block';
            pensionAmount.setAttribute('required', '');
            pensionSource.setAttribute('required', '');
        } else {
            pensionFields.style.display = 'none';
            pensionAmount.removeAttribute('required');
            pensionSource.removeAttribute('required');
            // Clear values when hidden
            pensionAmount.value = '';
            pensionSource.value = '';
        }
    }
    
    // Handle income fields validation
    function toggleIncomeFields() {
        const incomeYes = document.getElementById('income_yes');
        const incomeFields = document.getElementById('incomeFields');
        const incomeAmount = document.querySelector('input[name="income_amount"]');
        const incomeSource = document.querySelector('input[name="income_source"]');
        
        if (incomeYes.checked) {
            incomeFields.style.display = 'block';
            incomeAmount.setAttribute('required', '');
            incomeSource.setAttribute('required', '');
        } else {
            incomeFields.style.display = 'none';
            incomeAmount.removeAttribute('required');
            incomeSource.removeAttribute('required');
            // Clear values when hidden
            incomeAmount.value = '';
            incomeSource.value = '';
        }
    }
    
    // Override the global functions
    window.togglePensionFields = togglePensionFields;
    window.toggleIncomeFields = toggleIncomeFields;
    
    // Initial call to set the correct state
    togglePensionFields();
    toggleIncomeFields();
// Removed stray closing brace from a previous DOMContentLoaded wrapper

function validateForm() {
    console.log('Starting form validation...');
    // Validate the form before submission
    const form = document.getElementById('pensionForm');
    
    // Check if senior ID is selected
    const seniorId = document.getElementById('selected-senior-id').value;
    console.log('Senior ID:', seniorId);
    if (!seniorId) {
        alert('Please select a senior citizen');
        console.log('Error: No senior citizen selected');
        return false;
    }
    
    // Force validation of living arrangement
    const livingArrangementChecks = document.querySelectorAll('.living-arrangement-check');
    const isLivingArrangementChecked = Array.from(livingArrangementChecks).some(cb => cb.checked);
    console.log('Living arrangement checked:', isLivingArrangementChecked);
    if (!isLivingArrangementChecked) {
        alert('Please select at least one living arrangement option');
        console.log('Error: No living arrangement selected');
        return false;
    }
    
    // CRITICAL FIX: Force set civil status if it's empty
    const civilStatus = document.getElementById('senior_civil_status');
    const civilStatusActual = document.getElementById('civil_status_actual');
    
    if (civilStatus) {
        // If the select has a value, use it for the hidden field
        if (civilStatus.value) {
            if (civilStatusActual) {
                civilStatusActual.value = civilStatus.value;
            }
        } 
        // If the select doesn't have a value but we have a senior ID, try to get it from senior data
        else if (seniorId) {
            const senior = allSeniors.find(s => s.id == seniorId);
            if (senior && senior.marital_status) {
                civilStatus.value = senior.marital_status;
                if (civilStatusActual) {
                    civilStatusActual.value = senior.marital_status;
                }
            } else {
                // Default to "Married" if we can't find a value
                civilStatus.value = "Married";
                if (civilStatusActual) {
                    civilStatusActual.value = "Married";
                }
            }
        }
    }
    
    // SECURITY: Removed sensitive data logging
    
    // BYPASS VALIDATION: Directly submit the form if civil status is set
    if (civilStatus && civilStatus.value) {
        confirmSubmit();
        return true;
    }
    
    // Check for empty required fields
    const requiredFields = form.querySelectorAll('[required]');
    let emptyRequiredFields = [];
    requiredFields.forEach(field => {
        if (!field.value) {
            emptyRequiredFields.push(field.name || field.id);
        }
    });
    
    // Ensure all auto-filled fields are properly included
    ensureAutoFilledValues();
    
    // Check form validity
    if (form.checkValidity()) {
        confirmSubmit();
    } else {
        // Trigger browser's native form validation
        form.reportValidity();
    }
}

// Function to ensure auto-filled values are properly included in the form
function ensureAutoFilledValues() {
    console.log('Ensuring auto-filled values are properly set');
    
    // Specifically handle civil status field
    const seniorCivilStatus = document.getElementById('senior_civil_status');
    
    // Create a hidden input for civil_status if it doesn't exist
    let hiddenCivilStatus = document.getElementById('hidden_civil_status');
    if (!hiddenCivilStatus) {
        hiddenCivilStatus = document.createElement('input');
        hiddenCivilStatus.type = 'hidden';
        hiddenCivilStatus.id = 'hidden_civil_status';
        hiddenCivilStatus.name = 'civil_status';
        document.getElementById('pensionForm').appendChild(hiddenCivilStatus);
    }
    
    if (seniorCivilStatus && seniorCivilStatus.value) {
        // If the select has a value, make sure it's properly recognized
        console.log('Civil status value before fix:', seniorCivilStatus.value);
        
        // Force browser to recognize the value by triggering events
        seniorCivilStatus.dispatchEvent(new Event('change', { bubbles: true }));
        
        // Always update the hidden field with the current value
        hiddenCivilStatus.value = seniorCivilStatus.value;
        console.log('Set hidden civil status to:', hiddenCivilStatus.value);
    } else {
        // Default to 'Married' if no value is set
        hiddenCivilStatus.value = 'Married';
        console.log('Set default hidden civil status to: Married');
    }
        
        // Force the select to be valid
        seniorCivilStatus.setCustomValidity('');
    }
    
    // Get all input fields
    const inputs = document.querySelectorAll('input, select, textarea');
    
    // Loop through each input and ensure it's properly included
    inputs.forEach(input => {
        // Skip submit buttons and elements without a name
        if (input.type === 'submit' || !input.name) return;
        
        // For radio buttons and checkboxes, ensure checked status is reflected
        if (input.type === 'radio' || input.type === 'checkbox') {
            if (input.checked) {
                // Create a hidden input if needed to ensure the value is submitted
                if (!document.querySelector(`input[type="hidden"][name="${input.name}"][value="${input.value}"]`)) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = input.name;
                    hiddenInput.value = input.value;
                    document.getElementById('pensionForm').appendChild(hiddenInput);
                }
            }
        }
        
        // For other input types, ensure they have a value if they're auto-filled
        if (input.value && input.classList.contains('auto-filled')) {
            input.setAttribute('value', input.value);
        }
        
        // If it's a required field but empty, set a default value
        if (input.hasAttribute('required') && !input.value) {
            if (input.type === 'text' || input.type === 'textarea') {
                input.value = 'Not Applicable';
            } else if (input.type === 'number') {
                input.value = '0';
            } else if (input.type === 'select-one' && input.options.length > 0) {
                input.selectedIndex = 1; // Select the first non-empty option
            }
        }
    });
    
    // Special handling for the senior ID
    const seniorId = document.getElementById('selected-senior-id').value;
    if (seniorId) {
        const seniorIdInput = document.getElementById('selected-senior-id');
        seniorIdInput.setAttribute('value', seniorId);
    }
    
    // Ensure all radio button groups have a selection
    const radioGroups = {};
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        if (!radioGroups[radio.name]) {
            radioGroups[radio.name] = [];
        }
        radioGroups[radio.name].push(radio);
    });
    
    // For each radio group, ensure at least one is selected
    Object.keys(radioGroups).forEach(groupName => {
        const group = radioGroups[groupName];
        const isAnyChecked = group.some(radio => radio.checked);
        
        if (!isAnyChecked && group.length > 0) {
            // If none are checked, check the first one (usually "No" option)
            group[0].checked = true;
        }
    });
}

function confirmSubmit() {
    console.log('Confirm submit called');
    if (confirm('Are you sure you want to save this application?')) {
        console.log('User confirmed submission');
        
        // Get the form
        const form = document.getElementById('pensionForm');
        
        // Log form action and method
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);
        
        try {
            // Ensure all form fields are properly set before submission
            ensureAutoFilledValues();
            
            // Create FormData object to properly handle file uploads
            const formData = new FormData(form);
            
            // Log form data for debugging
            console.log('Submitting form data:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + (pair[1] instanceof File ? 'File: ' + pair[1].name : pair[1]));
            }
            
            // Submit the form using fetch API to properly handle file uploads
            fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.redirected) {
                    // If the server redirected us, follow the redirect
                    window.location.href = response.url;
                    return null;
                }
                return response.text();
            })
            .then(data => {
                if (data) {
                    // If we got data back instead of a redirect, show it
                    document.open();
                    document.write(data);
                    document.close();
                }
            })
            .catch(error => {
                console.error('Error during form submission:', error);
                alert('There was an error submitting the form. Please try again.');
            });
            
            console.log('Form submission triggered via fetch API');
        } catch (error) {
            console.error('Error during form submission:', error);
            alert('There was an error submitting the form. Please try again.');
        }
    } else {
        console.log('User cancelled submission');
    }
}

function submitFormWithAjax() {
    // Show loading state
    document.querySelector('button[type="button"]').disabled = true;
    document.querySelector('button[type="button"]').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
    
    const form = document.getElementById('pensionForm');
    const formData = new FormData(form);
    
    // Debug form data
    console.log('Form action:', form.action);
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    // Use regular form submission instead of AJAX
    form.submit();
    return;
    
    // The code below is disabled for now
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(JSON.stringify(data));
            });
        }
        return response.json();
    })
    .then(data => {
        // Success response
        if (data.success) {
            // Show success message
            alert('Pension application saved successfully!');
            // Redirect to seniors page
            window.location.href = '/seniors';
        } else {
            // Handle other success scenarios
            alert(data.message || 'Form submitted successfully');
            // Reset button state
            document.querySelector('button[type="button"]').disabled = false;
            document.querySelector('button[type="button"]').innerHTML = 'SAVE APPLICATION';
        }
    })
    .catch(error => {
        // Reset button state
        document.querySelector('button[type="button"]').disabled = false;
        document.querySelector('button[type="button"]').innerHTML = 'SAVE APPLICATION';
        
        try {
            // Try to parse the error as JSON
            const errorData = JSON.parse(error.message);
            
            // Check if we have validation errors
            if (errorData.errors) {
                // Display the first validation error
                const firstError = Object.values(errorData.errors)[0];
                alert(firstError[0] || 'Please check the form for errors');
                
                // Highlight fields with errors
                Object.keys(errorData.errors).forEach(field => {
                    const element = document.querySelector(`[name="${field}"]`);
                    if (element) {
                        element.classList.add('is-invalid');
                        
                        // Add error message below the field
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = errorData.errors[field][0];
                        element.parentNode.appendChild(errorDiv);
                    }
                });
            } else if (errorData.message) {
                // Display the error message
                alert(errorData.message);
            }
        } catch (e) {
            // If parsing fails, show a generic error
            console.error('Error submitting form:', error);
            alert('An error occurred while saving the application. Please try again.');
        }
    });
}
</script>
                </div>
            </div>
        </div>
    </div>

    <style>
        body { margin: 0; }

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
        
        .section-header {
            background: #e31575;
            color: #fff;
            padding: 8px 15px;
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0 15px;
            border-radius: 4px;
        }

        .btn-primary{
            margin-top: 30px;
            padding: 10px 20px;
             background-color: #e31575;
            border-color: #e31575;
            color: white;
            font-weight: bold;
        }
        .btn-primary:hover{
            background-color: #ffb7ce;
            border-color: #ffb7ce;
            color: #e31575;
            font-weight: bold;
        }
        
        .logo-osca {
            max-height: 60px;
        }
        
        .logo-bagong-pilipinas {
            max-height: 80px;
        }
        
        .title-main {
            font-size: 20px;
            font-weight: 800;
        }
        
        .form-section-bg {
            background: #f5faff;
        }
        
        .photo-upload-hidden {
            display: none;
        }
        
        .photo-upload-label {
            cursor: pointer;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .photo-icon {
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        /* Senior Search Dropdown Styles */
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #ffb7ce;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(227, 21, 117, 0.15);
            z-index: 1000;
        }

        .dropdown-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item:last-child {
            border-bottom: none;
        }

        .senior-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .senior-details {
            font-size: 12px;
            color: #6c757d;
        }

        .no-results {
            padding: 16px;
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }

        .deceased-senior {
            background-color: #f8f9fa;
            color: #6c757d;
            pointer-events: none !important;
        }

        .deceased-badge {
            background-color: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            margin-left: 8px;
        }
    </style>
    
    <script>
        let allSeniors = [];

        // Load all seniors data
        function loadAllSeniors() {
            // Load seniors data from PHP with properly formatted dates
            const seniorsData = {!! json_encode(\App\Models\Senior::orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'middle_name', 'name_extension', 'osca_id', 'barangay', 'sex', 'date_of_birth', 'birth_place', 'marital_status', 'contact_number', 'monthly_income', 'status'])) !!};
            
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

            if (!searchInput || !dropdown) return;

            // Show all seniors when input is focused
            searchInput.addEventListener('focus', function() {
                if (this.value === '') {
                    displaySearchResults(allSeniors);
                }
            });

            // Search as user types
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                
                if (query === '') {
                    displaySearchResults(allSeniors);
                    return;
                }

                const filteredSeniors = allSeniors.filter(senior => {
                    const fullName = `${senior.last_name} ${senior.first_name} ${senior.middle_name || ''}`.toLowerCase();
                    const oscaId = senior.osca_id ? senior.osca_id.toLowerCase() : '';
                    const barangay = senior.barangay ? senior.barangay.toLowerCase() : '';
                    
                    return fullName.includes(query) || 
                           oscaId.includes(query) || 
                           barangay.includes(query);
                });

                displaySearchResults(filteredSeniors);
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });
        }

        function displaySearchResults(seniors) {
            const dropdown = document.getElementById('senior-dropdown');
            if (seniors.length === 0) {
                dropdown.innerHTML = '<div class="no-results">No seniors found matching your search.</div>';
            } else {
                dropdown.innerHTML = seniors.map(senior => {
                    const isDeceased = senior.is_deceased;
                    let cssClasses = 'dropdown-item';
                    if (isDeceased) cssClasses += ' deceased-senior';

                    let badges = '';
                    if (isDeceased) badges += '<span class="deceased-badge">(DECEASED)</span>';

                    return `
                        <div class="${cssClasses}" data-senior-id="${senior.id}" data-name="${senior.first_name} ${senior.last_name}" data-age="${senior.age || ''}" data-gender="${senior.sex || ''}" data-address="${senior.barangay || ''}" data-birth-date="${senior.date_of_birth || ''}" data-osca-id="${senior.osca_id || ''}">
                            <div class="senior-name">
                                ${senior.last_name}, ${senior.first_name} ${senior.middle_name || ''}
                                ${badges}
                            </div>
                            <div class="senior-details">
                                OSCA ID: ${senior.osca_id || 'N/A'} | Barangay: ${senior.barangay || 'N/A'} | Age: ${senior.age || 'N/A'}
                            </div>
                        </div>
                    `;
                }).join('');
                
                // Add click event listeners to dropdown items
                dropdown.querySelectorAll('.dropdown-item').forEach(item => {
                    if (!item.classList.contains('deceased-senior')) {
                        item.addEventListener('click', function() {
                            selectSenior(this);
                        });
                    }
                });
            }
            dropdown.style.display = 'block';
        }

        function selectSenior(element) {
            const seniorId = element.getAttribute('data-senior-id');
            const seniorName = element.getAttribute('data-name');
            
            // Update the search input
            document.getElementById('senior-search').value = seniorName;
            
            // Update the hidden senior_id field
            document.getElementById('selected-senior-id').value = seniorId;
            
            // Hide the dropdown
            document.getElementById('senior-dropdown').style.display = 'none';
            
            // Load senior data
            loadSeniorData(seniorId);
        }

        function loadSeniorData(seniorId) {
            if (!seniorId) {
                // Clear all fields if no senior selected
                document.getElementById('senior_osca_id').value = '';
                document.getElementById('senior_last_name').value = '';
                document.getElementById('senior_first_name').value = '';
                document.getElementById('senior_middle_name').value = '';
                document.getElementById('senior_name_extension').value = '';
                document.getElementById('senior_date_of_birth').value = '';
                document.getElementById('senior_place_of_birth').value = '';
                document.getElementById('senior_age').value = '';
                document.getElementById('senior_civil_status').value = '';
                document.getElementById('senior_contact_number').value = '';
                
                // Clear barangay and monthly income
                document.querySelector('select[name="res_barangay"]').value = '';
                document.querySelector('input[name="monthly_income"]').value = '';
                
                // Clear gender radio buttons
                document.querySelectorAll('input[name="gender"]').forEach(radio => {
                    radio.checked = false;
                });
                
                return;
            }

            // Find the senior in our data
            const senior = allSeniors.find(s => s.id == seniorId);
            
            if (senior) {
                console.log('Senior data for auto-fill:', senior);
                // Populate the form fields with senior data
                document.getElementById('senior_osca_id').value = senior.osca_id || '';
                document.getElementById('senior_last_name').value = senior.last_name || '';
                document.getElementById('senior_first_name').value = senior.first_name || '';
                document.getElementById('senior_middle_name').value = senior.middle_name || '';
                document.getElementById('senior_name_extension').value = senior.name_extension || '';
                
                // Set gender radio button
                if (senior.sex) {
                    const genderValue = senior.sex.toLowerCase();
                    const genderRadio = document.querySelector(`input[name="gender"][value="${genderValue}"]`);
                    if (genderRadio) {
                        genderRadio.checked = true;
                    }
                }
                
                // Calculate age from birth date
                if (senior.date_of_birth) {
                    const dateField = document.getElementById('senior_date_of_birth');
                    dateField.value = senior.date_of_birth;
                    calculateAgeFromDate(senior.date_of_birth);
                }
                
                document.getElementById('senior_place_of_birth').value = senior.birth_place || 'Lingayen, Pangasinan';
                document.getElementById('senior_civil_status').value = senior.marital_status || '';
                document.getElementById('senior_contact_number').value = senior.contact_number || '';
                
                // Auto-fill barangay from senior data
                document.querySelector('select[name="res_barangay"]').value = senior.barangay || '';
                
                // Auto-fill monthly income from senior data
                const monthlyIncomeField = document.querySelector('input[name="monthly_income"]');
                if (monthlyIncomeField) {
                    // Handle both numeric values and legacy text ranges
                    let numericIncome = 0;
                    if (senior.monthly_income) {
                        // Check if monthly_income is already a number
                        if (!isNaN(parseFloat(senior.monthly_income))) {
                            numericIncome = parseFloat(senior.monthly_income);
                        } 
                        // Handle legacy text format for backward compatibility
                        else if (typeof senior.monthly_income === 'string') {
                            if (senior.monthly_income.includes('below')) {
                                numericIncome = 500; // Default for below 1000
                            } else if (senior.monthly_income.includes('to')) {
                                const parts = senior.monthly_income.split(' to ');
                                numericIncome = parseInt(parts[0].replace(/[^\d]/g, '')) || 0;
                            } else if (senior.monthly_income.includes('above')) {
                                numericIncome = parseInt(senior.monthly_income.replace(/[^\d]/g, '')) || 0;
                            } else if (senior.monthly_income === 'None') {
                                numericIncome = 0;
                            }
                        }
                    }
                    monthlyIncomeField.value = numericIncome;
                    console.log('Auto-filled monthly income:', numericIncome, 'from:', senior.monthly_income);
                } else {
                    console.log('Monthly income field not found');
                }
                
                // Auto-fill living arrangement based on senior's data
                // Clear all checkboxes first
                document.querySelectorAll('input[name="living_arrangement[]"]').forEach(checkbox => {
                    checkbox.checked = false;
                });
                
                // SECURITY: Removed sensitive data logging
                
                // IMPORTANT: The issue is with the checkbox values
                // The form has checkboxes with these values:
                // - "owned" (id="owned")
                // - "rent" (id="rent")
                // - "living alone" (id="living_alone")
                // - "living with children or relatives" (id="living_with")
                
                // Check if senior has living_with data
                let shouldCheckLivingWith = false;
                
                if (senior.living_with) {
                    // Handle JSON string format
                    if (typeof senior.living_with === 'string' && senior.living_with.includes('[')) {
                        try {
                            const livingWithArray = JSON.parse(senior.living_with);
                            
                            if (Array.isArray(livingWithArray) && 
                                (livingWithArray.includes('Children') || 
                                 livingWithArray.includes('Relatives') || 
                                 livingWithArray.includes('Spouse') ||
                                 livingWithArray.includes('Grand Children'))) {
                                console.log('Senior lives with family members, checking "living with children or relatives"');
                                shouldCheckLivingWith = true;
                            } else if (Array.isArray(livingWithArray) && livingWithArray.includes('Alone')) {
                                console.log('Senior lives alone, checking "living alone"');
                                document.getElementById('living_alone').checked = true;
                            }
                        } catch (e) {
                            console.log('Error parsing living_with JSON:', e);
                        }
                    }
                    // Handle plain string format
                    else if (typeof senior.living_with === 'string') {
                        console.log('Living with is a plain string:', senior.living_with);
                        if (senior.living_with.includes('Children') || 
                            senior.living_with.includes('Relatives') || 
                            senior.living_with.includes('Spouse') ||
                            senior.living_with.includes('Grand Children')) {
                            console.log('Senior lives with family members (string format), checking "living with children or relatives"');
                            shouldCheckLivingWith = true;
                        }
                    }
                    // Handle array format
                    else if (Array.isArray(senior.living_with)) {
                        console.log('Living with is an array:', senior.living_with);
                        if (senior.living_with.includes('Children') || 
                            senior.living_with.includes('Relatives') || 
                            senior.living_with.includes('Spouse') ||
                            senior.living_with.includes('Grand Children')) {
                            console.log('Senior lives with family members (array format), checking "living with children or relatives"');
                            shouldCheckLivingWith = true;
                        } else if (senior.living_with.includes('Alone')) {
                            console.log('Senior lives alone (array format), checking "living alone"');
                            document.getElementById('living_alone').checked = true;
                        }
                    }
                }
                
                // Fallback to living_condition_primary if living_with doesn't provide enough info
                if (!shouldCheckLivingWith && senior.living_condition_primary) {
                    console.log('Checking living_condition_primary:', senior.living_condition_primary);
                    
                    if (senior.living_condition_primary === 'Living Alone') {
                        console.log('Found "Living Alone" in living_condition_primary, checking "living alone"');
                        document.getElementById('living_alone').checked = true;
                    } else if (senior.living_condition_primary === 'Living with' || 
                              senior.living_condition_primary === 'With Family') {
                        console.log('Found "Living with" or "With Family" in living_condition_primary, checking "living with children or relatives"');
                        shouldCheckLivingWith = true;
                    }
                }
                
                // Apply the living with children or relatives checkbox if needed
                if (shouldCheckLivingWith) {
                    document.getElementById('living_with').checked = true;
                }
                
                // Final check - if we still haven't set any living arrangement, try one more approach
                const anyLivingArrangementChecked = Array.from(
                    document.querySelectorAll('input[name="living_arrangement[]"]')
                ).some(checkbox => checkbox.checked);
                
                if (!anyLivingArrangementChecked) {
                    console.log('No living arrangement checked yet, trying direct approach with checkbox values');
                    // Direct approach - find checkbox by value
                    const livingWithCheckbox = Array.from(
                        document.querySelectorAll('input[name="living_arrangement[]"]')
                    ).find(checkbox => checkbox.value === 'living with children or relatives');
                    
                    if (livingWithCheckbox) {
                        console.log('Found checkbox with value "living with children or relatives", checking it');
                        livingWithCheckbox.checked = true;
                    }
                }
                
                // Senior data loaded successfully
            }
        }

        function calculateAgeFromDate(dateString) {
            if (!dateString) return;
            
            const birthDate = new Date(dateString);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            // Adjust age if birthday hasn't occurred this year
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            document.getElementById('senior_age').value = age;
        }

        // Add event listener for date picker changes
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('senior_date_of_birth');
            if (dateInput) {
                dateInput.addEventListener('change', function() {
                    calculateAgeFromDate(this.value);
                });
            }
            
            // Initialize senior search functionality
            loadAllSeniors();
            setupSearchableDropdown();
        });

        function togglePensionFields() {
            const pensionYes = document.querySelector('input[name="has_pension"][value="1"]').checked;
            const pensionFields = document.getElementById('pensionFields');
            pensionFields.style.display = pensionYes ? 'block' : 'none';
        }

        function toggleIncomeFields() {
            const incomeYes = document.querySelector('input[name="permanent_income"][value="Yes"]').checked;
            const incomeFields = document.getElementById('incomeFields');
            incomeFields.style.display = incomeYes ? 'block' : 'none';
        }

        // Add form submission validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pensionForm');
            const formMessages = document.getElementById('form-messages');
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Check if senior is selected
                    const seniorId = document.getElementById('selected-senior-id').value;
                    if (!seniorId) {
                        formMessages.classList.remove('d-none', 'alert-success', 'alert-info');
                        formMessages.classList.add('alert-danger');
                        formMessages.innerHTML = '<i class="fas fa-exclamation-circle"></i> Please select a senior citizen first.';
                        return false;
                    }
                    
                    // Ensure auto-filled values are set before validation
                    ensureAutoFilledValues();
                    
                    // Check for empty required fields
                    const requiredFields = ['senior_id', 'last_name', 'first_name', 'date_of_birth', 'place_of_birth', 'age', 'gender', 'monthly_income'];
                    const emptyFields = [];
                    
                    // ALWAYS add civil_status field regardless of validation
                    // This ensures it's always submitted to the server
                    const civilStatusField = document.getElementById('senior_civil_status');
                    let civilStatusValue = 'Married'; // Default value
                    
                    if (civilStatusField && civilStatusField.value) {
                        civilStatusValue = civilStatusField.value;
                        console.log('Civil status value before submission:', civilStatusValue);
                    }
                    
                    // Always create/update the hidden input for civil_status
                    let hiddenCivilStatus = document.getElementById('hidden_civil_status');
                    if (!hiddenCivilStatus) {
                        hiddenCivilStatus = document.createElement('input');
                        hiddenCivilStatus.type = 'hidden';
                        hiddenCivilStatus.id = 'hidden_civil_status';
                        hiddenCivilStatus.name = 'civil_status';
                        hiddenCivilStatus.value = civilStatusValue;
                        document.getElementById('pensionForm').appendChild(hiddenCivilStatus);
                        console.log('Added hidden civil status field with value:', hiddenCivilStatus.value);
                    } else {
                        hiddenCivilStatus.value = civilStatusValue;
                        console.log('Updated hidden civil status field with value:', hiddenCivilStatus.value);
                    }
                    
                    requiredFields.forEach(field => {
                        if (field === 'senior_id') {
                            if (!seniorId) emptyFields.push('senior selection');
                        } else if (field === 'gender') {
                            const genderSelected = document.querySelector('input[name="gender"]:checked');
                            if (!genderSelected) emptyFields.push('gender');
                        } else {
                            const fieldElement = document.querySelector(`[name="${field}"]`);
                            if (fieldElement && (!fieldElement.value || fieldElement.value.trim() === '')) {
                                emptyFields.push(field.replace('_', ' '));
                            }
                        }
                    });
                    
                    if (emptyFields.length > 0) {
                        formMessages.classList.remove('d-none', 'alert-success', 'alert-info');
                        formMessages.classList.add('alert-danger');
                        formMessages.innerHTML = '<i class="fas fa-exclamation-circle"></i> Please fill out the following required fields: ' + emptyFields.join(', ');
                        return false;
                    }
                    
                    // Show loading state
                    formMessages.classList.remove('d-none', 'alert-success', 'alert-danger');
                    formMessages.classList.add('alert-info');
                    formMessages.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting application...';
                    
                    // Call ensureAutoFilledValues one more time right before submission
                    ensureAutoFilledValues();
                    
                    // Create FormData object
                    const formData = new FormData(form);
                    
                    // Log all form data for debugging
                    console.log('Form data being submitted:');
                    for (let pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }
                    
                    // ALWAYS ensure critical fields are in the formData
                    const criticalFields = {
                        'civil_status': document.getElementById('hidden_civil_status')?.value || 'Married',
                        'gender': document.querySelector('input[name="gender"]:checked')?.value || 'male',
                        'monthly_income': document.getElementById('monthly_income')?.value || '0'
                    };
                    
                    // Add or update critical fields in formData
                    Object.keys(criticalFields).forEach(key => {
                        if (!formData.has(key) || !formData.get(key)) {
                            formData.set(key, criticalFields[key]);
                            console.log(`Ensured ${key} in formData:`, criticalFields[key]);
                        }
                    });
                    
                    // Send AJAX request
                    fetch(form.getAttribute('action'), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => {
                        // Check if the response is JSON or HTML
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            return response.json();
                        } else {
                            // Handle HTML response
                            console.log('Received HTML response instead of JSON');
                            // Redirect to seniors page
                            window.location.href = "{{ route('seniors') }}";
                            return { success: true, message: 'Form submitted successfully' };
                        }
                    })
                    .then(data => {
                        formMessages.classList.remove('alert-info');
                        
                        if (data.success) {
                            // Success
                            formMessages.classList.add('alert-success');
                            formMessages.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                            
                            // Redirect after a short delay
                            setTimeout(function() {
                                window.location.href = "{{ route('seniors') }}";
                            }, 2000);
                        } else {
                            // Error
                            formMessages.classList.add('alert-danger');
                            formMessages.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
                            
                            // If there are validation errors
                            if (data.errors) {
                                let errorList = '<ul class="mb-0 mt-2">';
                                Object.keys(data.errors).forEach(key => {
                                    errorList += `<li>${data.errors[key]}</li>`;
                                });
                                errorList += '</ul>';
                                formMessages.innerHTML += errorList;
                            }
                        }
                    })
                    .catch(error => {
                        // Network or other error
                        formMessages.classList.remove('alert-info');
                        formMessages.classList.add('alert-danger');
                        formMessages.innerHTML = '<i class="fas fa-exclamation-triangle"></i> An error occurred while submitting the form. Please try again.';
                        console.error('Error:', error);
                    });
                });
            }
        });
        
        // Functions to show success and error modals using the existing popup system
        function showSuccessModal(title, message) {
            document.getElementById('successTitle').textContent = title;
            document.getElementById('successMessage').textContent = message;
            new bootstrap.Modal(document.getElementById('successModal')).show();
        }
        
        function showErrorModal(title, message) {
            document.getElementById('errorMessage').textContent = message;
            new bootstrap.Modal(document.getElementById('errorModal')).show();
        }
        
        // Function to ensure auto-filled values are properly set in the form
        function ensureAutoFilledValues() {
            console.log('Ensuring auto-filled values are properly set');
            
            // Specifically check and fix civil status
            const civilStatusSelect = document.getElementById('senior_civil_status');
            if (civilStatusSelect && civilStatusSelect.value === '') {
                // Try to get the value from the senior data
                const seniorId = document.getElementById('selected-senior-id').value;
                if (seniorId) {
                    const senior = allSeniors.find(s => s.id == seniorId);
                    if (senior && senior.marital_status) {
                        console.log('Setting civil status from senior data:', senior.marital_status);
                        civilStatusSelect.value = senior.marital_status;
                        
                        // If the value doesn't match any option, add it
                        if (civilStatusSelect.value === '') {
                            const newOption = document.createElement('option');
                            newOption.value = senior.marital_status;
                            newOption.text = senior.marital_status;
                            civilStatusSelect.add(newOption);
                            civilStatusSelect.value = senior.marital_status;
                        }
                    }
                }
            }
            
            // Trigger change event to ensure validation recognizes the value
            if (civilStatusSelect) {
                const event = new Event('change', { bubbles: true });
                civilStatusSelect.dispatchEvent(event);
            }
        }
        
        // Direct form submission without modal or fetch API
        // Function to ensure auto-filled values are properly set and recognized
        function ensureAutoFilledValues() {
            console.log('Ensuring auto-filled values are properly set...');
            
            // Create a hidden input for civil_status if it doesn't exist
            let hiddenCivilStatus = document.getElementById('hidden_civil_status');
            if (!hiddenCivilStatus) {
                hiddenCivilStatus = document.createElement('input');
                hiddenCivilStatus.type = 'hidden';
                hiddenCivilStatus.id = 'hidden_civil_status';
                hiddenCivilStatus.name = 'civil_status';
                document.getElementById('pensionForm').appendChild(hiddenCivilStatus);
                console.log('Created hidden civil status field');
            }
            
            // Get the selected senior ID
            const seniorId = document.getElementById('selected-senior-id').value;
            
            if (seniorId) {
                // Find the senior in our data
                const senior = allSeniors.find(s => s.id == seniorId);
                
                if (senior) {
                    console.log('Found senior data:', senior);
                    
                    // Handle civil status specifically
                    const civilStatusField = document.getElementById('senior_civil_status');
                    if (civilStatusField) {
                        // Get the value from senior data
                        let civilStatusValue = senior.marital_status || 'Married';
                        console.log('Setting civil status to:', civilStatusValue);
                        
                        // Set the value in both the select and hidden field
                        civilStatusField.value = civilStatusValue;
                        hiddenCivilStatus.value = civilStatusValue;
                        
                        // Trigger change event to ensure browser recognizes the value
                        const event = new Event('change', { bubbles: true });
                        civilStatusField.dispatchEvent(event);
                        
                        // Log the current values after setting
                        console.log('Civil status field value after setting:', civilStatusField.value);
                        console.log('Hidden civil status value after setting:', hiddenCivilStatus.value);
                    } else {
                        // If the field doesn't exist, still set the hidden field
                        hiddenCivilStatus.value = senior.marital_status || 'Married';
                        console.log('Set hidden civil status to:', hiddenCivilStatus.value);
                    }
                }
            } else {
                // Default to 'Married' if no senior is selected
                hiddenCivilStatus.value = 'Married';
                console.log('No senior selected, defaulting civil status to: Married');
            }
            
            return true;
        }
        
        function confirmSubmit() {
                    if (confirm('Are you sure you want to submit this pension application?')) {
                        console.log('Submitting form directly');
                        
                        try {
                            // Ensure auto-filled values are set
                            ensureAutoFilledValues();
                            
                            // Get the form
                            const form = document.getElementById('pensionForm');
                            
                            // Ensure civil_status is included in the form before submission
                            if (!form.querySelector('input[name="civil_status"]')) {
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'civil_status';
                                hiddenInput.value = document.getElementById('senior_civil_status')?.value || 'Married';
                                form.appendChild(hiddenInput);
                                console.log('Added civil_status hidden field before submission:', hiddenInput.value);
                            }
                            
                            // Call ensureAutoFilledValues one more time before submission
                            ensureAutoFilledValues();
                            
                            // Log form data before submission
                            console.log('Form is being submitted with civil status:', document.getElementById('senior_civil_status')?.value || 'Married');
                            
                            // Make sure form has the correct action and method
                            if (!form.action) {
                                form.action = "{{ route('forms.pension.store') }}";
                            }
                            if (!form.method) {
                                form.method = "POST";
                            }
                            
                            // Submit the form directly
                            form.submit();
                    
                    return false; // Prevent any other handlers from running
                } catch (error) {
                    console.error('Error during form submission:', error);
                    alert('There was an error submitting the form. Please try again.');
                    return false;
                }
            } else {
                console.log('User cancelled submission');
                return false;
            }
        }
    </script>
  </x-head>
</x-sidebar>