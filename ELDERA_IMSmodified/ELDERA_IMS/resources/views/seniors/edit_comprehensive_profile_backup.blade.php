@php
// Helper function to extract "Others" value from array
function extractOthersValue($array, $knownValues = []) {
    if (!is_array($array)) return '';
    
    // Common predefined values that should not be considered as "Others"
    $defaultKnownValues = [
        'Others', 'None', 'N/A', 'Not Applicable', 'No', 'Yes',
        // Health Problems
        'Hypertension', 'Arthritis / Gout', 'Coronary Heart Disease', 'Diabetes', 
        'Chronic Kidney Disease', 'Alzheimer\'s / Dementia', 'Chronic Obstructive Pulmonary Disease',
        // Dental Concern
        'Needs Dental Care',
        // Visual Concern
        'Eye impairment', 'Needs eye care',
        // Hearing
        'Aural impairment',
        // Social/Emotional
        'Feeling neglect / rejection', 'Feeling helplessness / worthlessness', 
        'Feeling loneliness / isolate', 'Lack leisure / recreational activities', 'Lack SC friendly environment',
        // Area/Difficulty
        'High Cost of medicines', 'Lack of medicines', 'Lack of medical attention',
        // Living With
        'Alone', 'Spouse', 'Children', 'Grandchildren', 'Relatives', 'Non-relatives',
        // Household Condition
        'No privacy', 'Overcrowded in home', 'Informal Settler', 'No permanent house', 
        'High cost of rent', 'Longing for independent living quiet atmosphere'
    ];
    
    $allKnownValues = array_merge($defaultKnownValues, $knownValues);
    
    foreach ($array as $value) {
        if (is_string($value) && trim($value) !== '') {
            $isKnown = false;
            foreach ($allKnownValues as $known) {
                if (strcasecmp(trim($value), trim($known)) === 0) {
                    $isKnown = true;
                    break;
                }
            }
            if (!$isKnown) {
                return trim($value);
            }
        }
    }
    return '';
}
@endphp

<x-sidebar>
  <x-header title="EDIT SENIOR CITIZEN INFORMATION" icon="fas fa-user-edit">
    @include('message.popup_message')
    <div class="main">
        <div class="form">
            <div class="form-header">
                <h2 class="form-title">EDIT SENIOR CITIZEN INFORMATION</h2>
                <div class="progress-container">
                    <div class="progress-steps">
                        <div class="step-indicator active" data-step="1">1</div>
                        <div class="step-indicator" data-step="2">2</div>
                        <div class="step-indicator" data-step="3">3</div>
                        <div class="step-indicator" data-step="4">4</div>
                        <div class="step-indicator" data-step="5">5</div>
                        <div class="step-indicator" data-step="6">6</div>
                        <div class="step-indicator" data-step="7">7</div>
                    </div>
                </div>
            </div>
            
            <div class="form-content">
                <form method="POST" action="{{ route('edit_senior.update', $senior->id) }}" enctype="multipart/form-data" id="editSeniorForm">
                    @csrf
                    @method('PUT')
                    <!-- Hidden certification field to satisfy backend 'accepted' validation -->
                    <input type="hidden" name="certification" id="certificationField" value="accepted">

                    @if ($errors->any())
                        <div class="alert alert-danger mt-3">
                            <strong>There were problems with your submission:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Step 1: Identifying Information -->
                    <div class="form-step active" id="step1">
                        <div class="form-section-content">
                            <!-- OCR Scan Panel - Minimal (Only on Step 1) -->
                            <div class="ocr-minimal-panel mb-4">
                                <div class="ocr-minimal-header">
                                    <i class="fas fa-wand-magic-sparkles"></i>
                                    <span>Quick Scan</span>
                                    <small class="ocr-hint">Auto-fill form from document</small>
                                </div>
                                <div class="ocr-minimal-content">
                                    <div class="ocr-file-input-wrapper">
                                        <input type="file" class="ocr-file-input" id="ocrFileUpload" name="ocr_document[]" accept=".jpg,.jpeg,.png,.pdf" multiple>
                                        <label for="ocrFileUpload" class="ocr-file-label">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <span id="ocrFileText">Choose files or drag here</span>
                                        </label>
                                    </div>
                                    <button type="button" id="processOcrBtn" class="ocr-scan-btn" onclick="handleOcrScan()" disabled>
                                        <i class="fas fa-magic"></i>
                                        <span>Scan</span>
                                    </button>
                                </div>
                                <div id="ocrProgressContainer" class="ocr-progress-minimal d-none">
                                    <div class="ocr-progress-bar-minimal">
                                        <div id="ocrProgressBar" class="ocr-progress-fill-minimal"></div>
                                    </div>
                                    <span id="ocrProgressText" class="ocr-progress-text-minimal">0%</span>
                                </div>
                                <div id="ocrStatus" class="ocr-status-minimal"></div>
                                <div id="ocrResultsContainer" class="ocr-results-minimal d-none">
                                    <i class="fas fa-check-circle"></i>
                                    <span id="ocrResultsSummary">Done!</span>
                                </div>
                                <div id="selectedFilesContainer" class="ocr-files-list d-none">
                                    <div id="selectedFilesList"></div>
                                </div>
                            </div>

                            <div class="mb-5">
                                <label class="form-label fw-bold mb-3" style="font-size: 16px; color: #2c3e50; font-weight: 700; letter-spacing: 0.3px;">1. Name of Senior Citizen</label>
                                <div class="row g-4">
                                    <div class="col-md-3">
                                        <label class="form-label small">Last Name <span style="color: red;">*</span></label>
                                        <input type="text" name="last_name" class="form-control form-control-sm" placeholder="Last Name" value="{{ old('last_name') ?: $senior->last_name }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">First Name <span style="color: red;">*</span></label>
                                        <input type="text" name="first_name" class="form-control form-control-sm" placeholder="First Name" value="{{ old('first_name') ?: $senior->first_name }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Middle Name</label>
                                        <input type="text" name="middle_name" class="form-control form-control-sm" placeholder="Middle Name" value="{{ old('middle_name', $senior->middle_name) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Name Extension</label>
                                        <select name="name_extension" class="form-select form-select-sm">
                                            <option value="">Extension</option>
                                            <option value="Jr." {{ old('name_extension', $senior->name_extension) == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                                            <option value="Sr." {{ old('name_extension', $senior->name_extension) == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                                            <option value="II" {{ old('name_extension', $senior->name_extension) == 'II' ? 'selected' : '' }}>II</option>
                                            <option value="III" {{ old('name_extension', $senior->name_extension) == 'III' ? 'selected' : '' }}>III</option>
                                            <option value="IV" {{ old('name_extension', $senior->name_extension) == 'IV' ? 'selected' : '' }}>IV</option>
                                            <option value="V" {{ old('name_extension', $senior->name_extension) == 'V' ? 'selected' : '' }}>V</option>
                                            <option value="VI" {{ old('name_extension', $senior->name_extension) == 'VI' ? 'selected' : '' }}>VI</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="typing-instructions">
                                    How do you type Ññ? <a href="#" onclick="showTypingInstructions()" class="typing-link">Click here!</a>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold mb-3" style="font-size: 16px; color: #2c3e50; font-weight: 700; letter-spacing: 0.3px;">2. Address</label>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-3">
                                        <label class="form-label small">Region <span style="color: red;">*</span></label>
                                        <select name="region" class="form-select form-select-sm" required>
                                           <option value="Region I" {{ old('region', $senior->region) == 'Region I' ? 'selected' : '' }}>Region I - Ilocos Region</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Province <span style="color: red;">*</span></label>
                                        <select name="province" class="form-select form-select-sm" required>
                                            <option value="Pangasinan" {{ old('province', $senior->province) == 'Pangasinan' ? 'selected' : '' }}>Pangasinan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">City <span style="color: red;">*</span></label>
                                        <select name="city" class="form-select form-select-sm" required>
                                            <option value="Lingayen" {{ old('city', $senior->city) == 'Lingayen' ? 'selected' : '' }}>Lingayen</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Barangay <span style="color: red;">*</span></label>
                                        <select name="barangay" class="form-select form-select-sm" required>
                                            <option value="">Select Barangay</option>
                                            <option value="aliwekwek" {{ old('barangay', $senior->barangay) == 'aliwekwek' ? 'selected' : '' }}>Aliwekwek</option>
                                            <option value="baay" {{ old('barangay', $senior->barangay) == 'baay' ? 'selected' : '' }}>Baay</option>
                                            <option value="balangobong" {{ old('barangay', $senior->barangay) == 'balangobong' ? 'selected' : '' }}>Balangobong</option>
                                            <option value="balococ" {{ old('barangay', $senior->barangay) == 'balococ' ? 'selected' : '' }}>Balococ</option>
                                            <option value="bantayan" {{ old('barangay', $senior->barangay) == 'bantayan' ? 'selected' : '' }}>Bantayan</option>
                                            <option value="basing" {{ old('barangay', $senior->barangay) == 'basing' ? 'selected' : '' }}>Basing</option>
                                            <option value="capandanan" {{ old('barangay', $senior->barangay) == 'capandanan' ? 'selected' : '' }}>Capandanan</option>
                                            <option value="domalandan-center" {{ old('barangay', $senior->barangay) == 'domalandan-center' ? 'selected' : '' }}>Domalandan Center</option>
                                            <option value="domalandan-east" {{ old('barangay', $senior->barangay) == 'domalandan-east' ? 'selected' : '' }}>Domalandan East</option>
                                            <option value="domalandan-west" {{ old('barangay', $senior->barangay) == 'domalandan-west' ? 'selected' : '' }}>Domalandan West</option>
                                            <option value="dorongan" {{ old('barangay', $senior->barangay) == 'dorongan' ? 'selected' : '' }}>Dorongan</option>
                                            <option value="dulag" {{ old('barangay', $senior->barangay) == 'dulag' ? 'selected' : '' }}>Dulag</option>
                                            <option value="estanza" {{ old('barangay', $senior->barangay) == 'estanza' ? 'selected' : '' }}>Estanza</option>
                                            <option value="lasip" {{ old('barangay', $senior->barangay) == 'lasip' ? 'selected' : '' }}>Lasip</option>
                                            <option value="libsong-east" {{ old('barangay', $senior->barangay) == 'libsong-east' ? 'selected' : '' }}>Libsong East</option>
                                            <option value="libsong-west" {{ old('barangay', $senior->barangay) == 'libsong-west' ? 'selected' : '' }}>Libsong West</option>
                                            <option value="malawa" {{ old('barangay', $senior->barangay) == 'malawa' ? 'selected' : '' }}>Malawa</option>
                                            <option value="malimpuec" {{ old('barangay', $senior->barangay) == 'malimpuec' ? 'selected' : '' }}>Malimpuec</option>
                                            <option value="maniboc" {{ old('barangay', $senior->barangay) == 'maniboc' ? 'selected' : '' }}>Maniboc</option>
                                            <option value="matalava" {{ old('barangay', $senior->barangay) == 'matalava' ? 'selected' : '' }}>Matalava</option>
                                            <option value="naguelguel" {{ old('barangay', $senior->barangay) == 'naguelguel' ? 'selected' : '' }}>Naguelguel</option>
                                            <option value="namolan" {{ old('barangay', $senior->barangay) == 'namolan' ? 'selected' : '' }}>Namolan</option>
                                            <option value="pangapisan-north" {{ old('barangay', $senior->barangay) == 'pangapisan-north' ? 'selected' : '' }}>Pangapisan North</option>
                                            <option value="pangapisan-sur" {{ old('barangay', $senior->barangay) == 'pangapisan-sur' ? 'selected' : '' }}>Pangapisan Sur</option>
                                            <option value="poblacion" {{ old('barangay', $senior->barangay) == 'poblacion' ? 'selected' : '' }}>Poblacion</option>
                                            <option value="quibaol" {{ old('barangay', $senior->barangay) == 'quibaol' ? 'selected' : '' }}>Quibaol</option>
                                            <option value="rosario" {{ old('barangay', $senior->barangay) == 'rosario' ? 'selected' : '' }}>Rosario</option>
                                            <option value="sabangan" {{ old('barangay', $senior->barangay) == 'sabangan' ? 'selected' : '' }}>Sabangan</option>
                                            <option value="talogtog" {{ old('barangay', $senior->barangay) == 'talogtog' ? 'selected' : '' }}>Talogtog</option>
                                            <option value="tonton" {{ old('barangay', $senior->barangay) == 'tonton' ? 'selected' : '' }}>Tonton</option>
                                            <option value="tumbar" {{ old('barangay', $senior->barangay) == 'tumbar' ? 'selected' : '' }}>Tumbar</option>
                                            <option value="wawa" {{ old('barangay', $senior->barangay) == 'wawa' ? 'selected' : '' }}>Wawa</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small">House No./Zone/Purok/Sitio</label>
                                        <input type="text" name="residence" class="form-control form-control-sm" placeholder="House No./Zone/Purok/Sitio" value="{{ old('residence', $senior->residence) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Street</label>
                                        <input type="text" name="street" class="form-control form-control-sm" placeholder="Street" value="{{ old('street', $senior->street) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                               <div class="col-md-3">
                                     <label class="form-label fw-bold">3. Date of Birth <span style="color: red;">*</span>  <small class="text-muted" style="font-size: 11px;">Must be 60 years or older</small></label>
                                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control form-control-sm" value="{{ old('date_of_birth') ?: ($senior->date_of_birth ? \Carbon\Carbon::parse($senior->date_of_birth)->format('Y-m-d') : '') }}" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label small">4. Place of Birth <span style="color: red;">*</span></label>
                                    <input type="text" name="birth_place" class="form-control form-control-sm" placeholder="Place of Birth" value="{{ old('birth_place') ?: $senior->birth_place }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">5. Marital Status <span style="color: red;">*</span></label>
                                    <select name="marital_status" class="form-select form-select-sm" required>
                                        <option value="">Select Marital Status</option>
                                        <option value="Single" {{ (old('marital_status') ?: $senior->marital_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                        <option value="Married" {{ (old('marital_status') ?: $senior->marital_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                        <option value="Widowed" {{ (old('marital_status') ?: $senior->marital_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="Separated" {{ (old('marital_status') ?: $senior->marital_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                                        <option value="Others" {{ (old('marital_status') ?: $senior->marital_status) == 'Others' ? 'selected' : '' }}>Others</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">6. Gender <span style="color: red;">*</span></label>
                                    <select name="sex" class="form-select form-select-sm" required>
                                        <option value="">Select</option>
                                        <option value="Male" {{ (old('sex') ?: $senior->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ (old('sex') ?: $senior->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">7. Contact Number <span style="color: red;">*</span></label>
                                    <input type="tel" name="contact_number" class="form-control form-control-sm" placeholder="Contact Number" value="{{ old('contact_number') ?: $senior->contact_number }}" required onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label small">8. Email Address</label>
                                    <input type="email" name="email" class="form-control form-control-sm" placeholder="Email Address" value="{{ old('email') ?: $senior->email }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">9. Religion</label>
                                    <select name="religion" class="form-select form-select-sm">
                                        <option value="">Select Religion</option>
                                        <option value="Roman Catholic" {{ old('religion', $senior->religion) == 'Roman Catholic' ? 'selected' : '' }}>Roman Catholic</option>
                                        <option value="Iglesia ni Cristo" {{ old('religion', $senior->religion) == 'Iglesia ni Cristo' ? 'selected' : '' }}>Iglesia ni Cristo</option>
                                        <option value="Evangelical" {{ old('religion', $senior->religion) == 'Evangelical' ? 'selected' : '' }}>Evangelical</option>
                                        <option value="Baptist" {{ old('religion', $senior->religion) == 'Baptist' ? 'selected' : '' }}>Baptist</option>
                                        <option value="Methodist" {{ old('religion', $senior->religion) == 'Methodist' ? 'selected' : '' }}>Methodist</option>
                                        <option value="Seventh Day Adventist" {{ old('religion', $senior->religion) == 'Seventh Day Adventist' ? 'selected' : '' }}>Seventh Day Adventist</option>
                                        <option value="Islam" {{ old('religion', $senior->religion) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                        <option value="Buddhism" {{ old('religion', $senior->religion) == 'Buddhism' ? 'selected' : '' }}>Buddhism</option>
                                        <option value="Jehovah's Witness" {{ old('religion', $senior->religion) == 'Jehovah\'s Witness' ? 'selected' : '' }}>Jehovah's Witness</option>
                                        <option value="Born Again Christian" {{ old('religion', $senior->religion) == 'Born Again Christian' ? 'selected' : '' }}>Born Again Christian</option>
                                        <option value="Aglipayan" {{ old('religion', $senior->religion) == 'Aglipayan' ? 'selected' : '' }}>Aglipayan</option>
                                        <option value="None" {{ old('religion', $senior->religion) == 'None' ? 'selected' : '' }}>None</option>
                                        <option value="Others" {{ old('religion', $senior->religion) == 'Others' ? 'selected' : '' }}>Others</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">10. Ethnic Origin</label>
                                    <input type="text" name="ethnic_origin" class="form-control form-control-sm" placeholder="Ethnic Origin" value="{{ old('ethnic_origin', $senior->ethnic_origin) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">11. Language Spoken</label>
                                    <input type="text" name="language" class="form-control form-control-sm" placeholder="Language Spoken" value="{{ old('language') ?: $senior->language }}">
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label small">12. OSCA ID No. <span style="color: red;">*</span></label>
                                    <input type="text" name="osca_id" class="form-control form-control-sm" placeholder="OSCA ID Number" value="{{ old('osca_id') ?: $senior->osca_id }}" required onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">13. GSIS/SSS No.</label>
                                    <input type="text" name="gsis_sss" class="form-control form-control-sm" placeholder="GSIS/SSS Number" value="{{ old('gsis_sss', $senior->gsis_sss) }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">14. TIN</label>
                                    <input type="text" name="tin" class="form-control form-control-sm" placeholder="Tax Identification Number" value="{{ old('tin', $senior->tin) }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label small">15. PhilHealth No.</label>
                                    <input type="text" name="philhealth" class="form-control form-control-sm" placeholder="PhilHealth Number" value="{{ old('philhealth', $senior->philhealth) }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">16. SC Association ID No.</label>
                                    <input type="text" name="sc_association" class="form-control form-control-sm" placeholder="Senior Citizen Association ID" value="{{ old('sc_association', $senior->sc_association) }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">17. Other Gov't ID No.</label>
                                    <input type="text" name="other_govt_id" class="form-control form-control-sm" placeholder="Other Government ID" value="{{ old('other_govt_id', $senior->other_govt_id) }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label small">18. Capability to Travel</label>
                                    <select name="can_travel" class="form-select form-select-sm">
                                        <option value="">Select</option>
                                        <option value="Yes" {{ old('can_travel', $senior->can_travel) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ old('can_travel', $senior->can_travel) == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">19. Service/Business/Employment</label>
                                    <input type="text" name="employment" class="form-control form-control-sm" placeholder="Specify" value="{{ old('employment', $senior->employment) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">20. Has Pension<span style="color: red;">*</span></label>
                                    <select name="has_pension" class="form-select form-select-sm" required>
                                        <option value="">Select</option>
                                        <option value="1" {{ old('has_pension', $senior->has_pension) == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('has_pension', $senior->has_pension) == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="step-navigation">
                            <button type="button" class="btn btn-secondary" id="prevBtn" onclick="changeStep(-1)" style="display: none;">Previous</button>
                            <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">Next</button>
                        </div>
                    </div>

                    <!-- Step 2: Family Information -->
                    <div class="form-step" id="step2">
                        <div class="mb-4">
                            <div class="mb-3">
                               <label class="form-label small">21. Name of Spouse</label>
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <input type="text" name="spouse_last_name" class="form-control form-control-sm" placeholder="Last Name" value="{{ old('spouse_last_name', $senior->spouse_last_name) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="spouse_first_name" class="form-control form-control-sm" placeholder="First Name" value="{{ old('spouse_first_name', $senior->spouse_first_name) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="spouse_middle_name" class="form-control form-control-sm" placeholder="Middle Name" value="{{ old('spouse_middle_name', $senior->spouse_middle_name) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="spouse_extension" class="form-control form-control-sm" placeholder="Extension(Jr, Sr)" value="{{ old('spouse_extension', $senior->spouse_extension) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">22. Father's Name</label>
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <input type="text" name="father_last_name" class="form-control form-control-sm" placeholder="Last Name" value="{{ old('father_last_name', $senior->father_last_name) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="father_first_name" class="form-control form-control-sm" placeholder="First Name" value="{{ old('father_first_name', $senior->father_first_name) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="father_middle_name" class="form-control form-control-sm" placeholder="Middle Name" value="{{ old('father_middle_name', $senior->father_middle_name) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="father_extension" class="form-control form-control-sm" placeholder="Extension(Jr, Sr)" value="{{ old('father_extension', $senior->father_extension) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">23. Mother's Maiden Name</label>
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <input type="text" name="mother_last_name" class="form-control form-control-sm" placeholder="Last Name" value="{{ old('mother_last_name', $senior->mother_last_name) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="mother_first_name" class="form-control form-control-sm" placeholder="First Name" value="{{ old('mother_first_name', $senior->mother_first_name) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="mother_middle_name" class="form-control form-control-sm" placeholder="Middle Name" value="{{ old('mother_middle_name', $senior->mother_middle_name) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="mother_extension" class="form-control form-control-sm" placeholder="Extension" value="{{ old('mother_extension', $senior->mother_extension) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small">24. Child(ren)</label>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Complete Name</th>
                                                <th>Occupation</th>
                                                <th>Income (Optional)</th>
                                                <th>Age</th>
                                                <th>Is Working?</th>
                                                <th class="align-middle text-center">
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <button type="button" class="table-action-add" id="addChildBtn" title="Add Child Row">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="childrenTableBody">
                                            @php
                                                // Prefer dedicated column; fallback to household_condition['children'] if columns are absent
                                                $children = is_array($senior->children) ? $senior->children : [];
                                                if (empty($children) && !empty($senior->household_condition)) {
                                                    $hc = is_string($senior->household_condition)
                                                        ? json_decode($senior->household_condition, true)
                                                        : (array) $senior->household_condition;
                                                    $children = $hc['children'] ?? [];
                                                }
                                            @endphp
                                            @if(is_array($children) && count($children) > 0)
                                                @foreach($children as $i => $child)
                                                @php $idx = $i + 1; @endphp
                                                <tr>
                                                    <td><input type="text" name="child_name_{{ $idx }}" class="form-control form-control-sm border-0" placeholder="Child Name" value="{{ $child['name'] ?? '' }}"></td>
                                                    <td><input type="text" name="child_occupation_{{ $idx }}" class="form-control form-control-sm border-0" placeholder="Occupation" value="{{ $child['occupation'] ?? '' }}"></td>
                                                    <td><input type="text" name="child_income_{{ $idx }}" class="form-control form-control-sm border-0" placeholder="Income" value="{{ $child['income'] ?? '' }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                                                    <td><input type="text" name="child_age_{{ $idx }}" class="form-control form-control-sm border-0" placeholder="Age" value="{{ $child['age'] ?? '' }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                                                    <td>
                                                        @php $working = $child['working'] ?? ''; @endphp
                                                        <select name="child_working_{{ $idx }}" class="form-select form-select-sm border-0">
                                                            <option value="">Is working?</option>
                                                            <option value="Yes" {{ $working === 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ $working === 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="table-action-delete delete-child-row" title="Delete Row">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                @for($i = 1; $i <= 2; $i++)
                                                <tr>
                                                    <td><input type="text" name="child_name_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Child Name"></td>
                                                    <td><input type="text" name="child_occupation_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Occupation"></td>
                                                    <td><input type="text" name="child_income_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Income" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                                                    <td><input type="text" name="child_age_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Age" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                                                    <td>
                                                        <select name="child_working_{{ $i }}" class="form-select form-select-sm border-0">
                                                            <option value="">Is working?</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="table-action-delete delete-child-row" title="Delete Row">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endfor
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small">25. Other Dependents</label>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Name of Dependent</th>
                                                <th>Occupation of Dependent</th>
                                                <th>Income</th>
                                                <th>Age</th>
                                                <th>Is Working?</th>
                                                <th class="align-middle text-center">
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <button type="button" class="table-action-add" id="addDependentBtn" title="Add Dependent Row">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="dependentsTableBody">
                                            @php
                                                // Prefer dedicated column; fallback to household_condition['dependent'] if columns are absent
                                                $dependents = is_array($senior->dependent) ? $senior->dependent : [];
                                                if (empty($dependents) && !empty($senior->household_condition)) {
                                                    $hc = is_string($senior->household_condition)
                                                        ? json_decode($senior->household_condition, true)
                                                        : (array) $senior->household_condition;
                                                    $dependents = $hc['dependent'] ?? [];
                                                }
                                                $otherDependents = array_filter($dependents, function($dep) {
                                                    return !empty($dep['name']) || !empty($dep['occupation']) || !empty($dep['income']) || !empty($dep['age']);
                                                });
                                            @endphp
                                            @if(is_array($otherDependents) && count($otherDependents) > 0)
                                                @foreach($otherDependents as $i => $dep)
                                                @php $idx = $i + 1; @endphp
                                                <tr>
                                                    <td><input type="text" name="dependent_name_{{ $idx }}" class="form-control form-control-sm border-0" placeholder="Name of Dependent" value="{{ $dep['name'] ?? '' }}"></td>
                                                    <td><input type="text" name="dependent_occupation_{{ $idx }}" class="form-control form-control-sm border-0" placeholder="Occupation of Dependent" value="{{ $dep['occupation'] ?? '' }}"></td>
                                                    <td><input type="text" name="dependent_income_{{ $idx }}" class="form-control form-control-sm border-0" placeholder="Income" value="{{ $dep['income'] ?? '' }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                                                    <td><input type="text" name="dependent_age_{{ $idx }}" class="form-control form-control-sm border-0" placeholder="Age" value="{{ $dep['age'] ?? '' }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                                                    <td>
                                                        @php $working = $dep['working'] ?? ''; @endphp
                                                        <select name="dependent_working_{{ $idx }}" class="form-select form-select-sm border-0">
                                                            <option value="">Is Working?</option>
                                                            <option value="Yes" {{ $working === 'Yes' ? 'selected' : '' }}>Yes</option>
                                                            <option value="No" {{ $working === 'No' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="table-action-delete delete-dependent-row" title="Delete Row">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                @for($i = 1; $i <= 2; $i++)
                                                <tr>
                                                    <td><input type="text" name="dependent_name_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Name of Dependent"></td>
                                                    <td><input type="text" name="dependent_occupation_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Occupation of Dependent"></td>
                                                    <td><input type="text" name="dependent_income_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Income" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                                                    <td><input type="text" name="dependent_age_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Age" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                                                    <td>
                                                        <select name="dependent_working_{{ $i }}" class="form-select form-select-sm border-0">
                                                            <option value="">Is Working?</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="table-action-delete delete-dependent-row" title="Delete Row">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endfor
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="step-navigation">
                            <button type="button" class="btn btn-secondary" id="prevBtn2" onclick="changeStep(-1)">Previous</button>
                            <button type="button" class="btn btn-primary" id="nextBtn2" onclick="changeStep(1)">Next</button>
                        </div>
                    </div>

                    <!-- Step 3: Education/HR Profile -->
                    <div class="form-step" id="step3">
                        <div class="mb-4">
                            <div class="row">
                                <!-- Left Column - Educational Attainment -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="input-label">26. Highest Educational Attainment</label>
                                        <div class="d-flex flex-column gap-1 mt-2">
                                            <label><input type="radio" name="education_level" value="Not Attended School" {{ old('education_level', $senior->education_level) == 'Not Attended School' ? 'checked' : '' }}> Not Attended School</label>
                                            <label><input type="radio" name="education_level" value="Elementary Level" {{ old('education_level', $senior->education_level) == 'Elementary Level' ? 'checked' : '' }}> Elementary Level</label>
                                            <label><input type="radio" name="education_level" value="Elementary Graduate" {{ old('education_level', $senior->education_level) == 'Elementary Graduate' ? 'checked' : '' }}> Elementary Graduate</label>
                                            <label><input type="radio" name="education_level" value="Highschool Level" {{ old('education_level', $senior->education_level) == 'Highschool Level' ? 'checked' : '' }}> Highschool Level</label>
                                            <label><input type="radio" name="education_level" value="Highschool Graduate" {{ old('education_level', $senior->education_level) == 'Highschool Graduate' ? 'checked' : '' }}> Highschool Graduate</label>
                                            <label><input type="radio" name="education_level" value="Vocational" {{ old('education_level', $senior->education_level) == 'Vocational' ? 'checked' : '' }}> Vocational</label>
                                            <label><input type="radio" name="education_level" value="College Level" {{ old('education_level', $senior->education_level) == 'College Level' ? 'checked' : '' }}> College Level</label>
                                            <label><input type="radio" name="education_level" value="College Graduate" {{ old('education_level', $senior->education_level) == 'College Graduate' ? 'checked' : '' }}> College Graduate</label>
                                            <label><input type="radio" name="education_level" value="Post Graduate" {{ old('education_level', $senior->education_level) == 'Post Graduate' ? 'checked' : '' }}> Post Graduate</label>
                                            <div class="mt-2">
                                                <label>Others, Specify</label>
                                                <input type="text" name="education_others_specify" class="form-control mt-1" placeholder="" value="{{ old('education_others_specify', extractOthersValue($senior->education_level ? [$senior->education_level] : [])) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Middle Column - Technical Skills -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="input-label">27. Specialization / Technical Skills <em>(Check all applicable)</em></label>
                                        <div class="d-flex flex-column gap-1 mt-2">
                                            @php $skills = is_array($senior->skills) ? $senior->skills : []; @endphp
                                            <label><input type="checkbox" name="skills[]" value="Medical" {{ in_array('Medical', old('skills', $skills)) ? 'checked' : '' }}> Medical</label>
                                            <label><input type="checkbox" name="skills[]" value="Dental" {{ in_array('Dental', old('skills', $skills)) ? 'checked' : '' }}> Dental</label>
                                            <label><input type="checkbox" name="skills[]" value="Fishing" {{ in_array('Fishing', old('skills', $skills)) ? 'checked' : '' }}> Fishing</label>
                                            <label><input type="checkbox" name="skills[]" value="Engineering" {{ in_array('Engineering', old('skills', $skills)) ? 'checked' : '' }}> Engineering</label>
                                            <label><input type="checkbox" name="skills[]" value="Barber" {{ in_array('Barber', old('skills', $skills)) ? 'checked' : '' }}> Barber</label>
                                            <label><input type="checkbox" name="skills[]" value="Evangelization" {{ in_array('Evangelization', old('skills', $skills)) ? 'checked' : '' }}> Evangelization</label>
                                            <label><input type="checkbox" name="skills[]" value="Midwifery" {{ in_array('Midwifery', old('skills', $skills)) ? 'checked' : '' }}> Midwifery</label>
                                            <label><input type="checkbox" name="skills[]" value="Teaching" {{ in_array('Teaching', old('skills', $skills)) ? 'checked' : '' }}> Teaching</label>
                                            <label><input type="checkbox" name="skills[]" value="Counselling" {{ in_array('Counselling', old('skills', $skills)) ? 'checked' : '' }}> Counselling</label>
                                            <label><input type="checkbox" name="skills[]" value="Cooking" {{ in_array('Cooking', old('skills', $skills)) ? 'checked' : '' }}> Cooking</label>
                                            <label><input type="checkbox" name="skills[]" value="Carpenter" {{ in_array('Carpenter', old('skills', $skills)) ? 'checked' : '' }}> Carpenter</label>
                                            <label><input type="checkbox" name="skills[]" value="Mason" {{ in_array('Mason', old('skills', $skills)) ? 'checked' : '' }}> Mason</label>
                                            <label><input type="checkbox" name="skills[]" value="Tailor" {{ in_array('Tailor', old('skills', $skills)) ? 'checked' : '' }}> Tailor</label>
                                            <label><input type="checkbox" name="skills[]" value="Legal Services" {{ in_array('Legal Services', old('skills', $skills)) ? 'checked' : '' }}> Legal Services</label>
                                            <label><input type="checkbox" name="skills[]" value="Farming" {{ in_array('Farming', old('skills', $skills)) ? 'checked' : '' }}> Farming</label>
                                            <label><input type="checkbox" name="skills[]" value="Arts" {{ in_array('Arts', old('skills', $skills)) ? 'checked' : '' }}> Arts</label>
                                            <label><input type="checkbox" name="skills[]" value="Plumber" {{ in_array('Plumber', old('skills', $skills)) ? 'checked' : '' }}> Plumber</label>
                                            <label><input type="checkbox" name="skills[]" value="Shoemaker" {{ in_array('Shoemaker', old('skills', $skills)) ? 'checked' : '' }}> Shoemaker</label>
                                            <label><input type="checkbox" name="skills[]" value="Chef/Cook" {{ in_array('Chef/Cook', old('skills', $skills)) ? 'checked' : '' }}> Chef/Cook</label>
                                            <label><input type="checkbox" name="skills[]" value="Information Technology" {{ in_array('Information Technology', old('skills', $skills)) ? 'checked' : '' }}> Information Technology</label>
                                            <div class="mt-2">
                                                <label>Others, Specify</label>
                                                <input type="text" name="skills_others_specify" class="form-control mt-1" placeholder="" value="{{ old('skills_others_specify', extractOthersValue($skills)) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column - Shared Skills and Community Activities -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="input-label">28. Shared Skills <em>(Community Service)</em></label>
                                        <textarea name="shared_skills" class="form-control mt-2" placeholder="type skills here separated by comma" rows="3">{{ old('shared_skills', $senior->shared_skills) }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="input-label">29. Involvement in Community Activities <em>(Check all applicable)</em></label>
                                        <div class="d-flex flex-column gap-1 mt-2">
                                            @php $activities = is_array($senior->community_activities) ? $senior->community_activities : []; @endphp
                                            <label><input type="checkbox" name="community_activities[]" value="Medical" {{ in_array('Medical', old('community_activities', $activities)) ? 'checked' : '' }}> Medical</label>
                                            <label><input type="checkbox" name="community_activities[]" value="Resource Volunteer" {{ in_array('Resource Volunteer', old('community_activities', $activities)) ? 'checked' : '' }}> Resource Volunteer</label>
                                            <label><input type="checkbox" name="community_activities[]" value="Community Beautification" {{ in_array('Community Beautification', old('community_activities', $activities)) ? 'checked' : '' }}> Community Beautification</label>
                                            <label><input type="checkbox" name="community_activities[]" value="Community / Organization Leader" {{ in_array('Community / Organization Leader', old('community_activities', $activities)) ? 'checked' : '' }}> Community / Organization Leader</label>
                                            <label><input type="checkbox" name="community_activities[]" value="Dental" {{ in_array('Dental', old('community_activities', $activities)) ? 'checked' : '' }}> Dental</label>
                                            <label><input type="checkbox" name="community_activities[]" value="Friendly Visits" {{ in_array('Friendly Visits', old('community_activities', $activities)) ? 'checked' : '' }}> Friendly Visits</label>
                                            <label><input type="checkbox" name="community_activities[]" value="Neighborhood Support Services" {{ in_array('Neighborhood Support Services', old('community_activities', $activities)) ? 'checked' : '' }}> Neighborhood Support Services</label>
                                            <label><input type="checkbox" name="community_activities[]" value="Legal Services" {{ in_array('Legal Services', old('community_activities', $activities)) ? 'checked' : '' }}> Legal Services</label>
                                            <label><input type="checkbox" name="community_activities[]" value="Religious" {{ in_array('Religious', old('community_activities', $activities)) ? 'checked' : '' }}> Religious</label>
                                            <label><input type="checkbox" name="community_activities[]" value="Counselling / Referral" {{ in_array('Counselling / Referral', old('community_activities', $activities)) ? 'checked' : '' }}> Counselling / Referral</label>
                                            <label><input type="checkbox" name="community_activities[]" value="Sponsorship" {{ in_array('Sponsorship', old('community_activities', $activities)) ? 'checked' : '' }}> Sponsorship</label>
                                            <div class="mt-2">
                                                <label>Others, Specify</label>
                                                <input type="text" name="community_activities_others_specify" class="form-control mt-1" placeholder="" value="{{ old('community_activities_others_specify', extractOthersValue($activities)) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step Navigation Buttons -->
                        <div class="step-navigation">
                            <button type="button" class="btn btn-secondary" id="prevBtn3" onclick="changeStep(-1)">Previous</button>
                            <button type="button" class="btn btn-primary" id="nextBtn3" onclick="changeStep(1)">Next</button>
                        </div>
                    </div>

                    <!-- Step 4: Dependency Profile -->
                    <div class="form-step" id="step4">
                        <div class="form-section dependency-profile-section">
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="input-label">30. Living Condition</label>
                                    <div class="mt-2">
                                        <div class="d-flex flex-column gap-1">
                                            <label><input type="radio" name="living_condition_primary" value="Living Alone" onchange="toggleLivingWithOptions()" {{ old('living_condition_primary', $senior->living_condition_primary) == 'Living Alone' ? 'checked' : '' }}> Living Alone</label>
                                            <label><input type="radio" name="living_condition_primary" value="Living with" onchange="toggleLivingWithOptions()" {{ old('living_condition_primary', $senior->living_condition_primary) == 'Living with' ? 'checked' : '' }}> Living with</label>
                                            <div id="living_with_options" class
                                                <label class="checkbox-item">
                                                    <input type="checkbox" name="area_difficulty[]" value="High Cost of medicines" {{ in_array('High Cost of medicines', old('area_difficulty', $areaDifficulty)) ? 'checked' : '' }}> High Cost of medicines</label>
                                                    <label class="checkbox-item"><input type="checkbox" name="area_difficulty[]" value="Lack of medicines" {{ in_array('Lack of medicines', old('area_difficulty', $areaDifficulty)) ? 'checked' : '' }}> Lack of medicines</label>
                                                    <label class="checkbox-item"><input type="checkbox" name="area_difficulty[]" value="Lack of medical attention" {{ in_array('Lack of medical attention', old('area_difficulty', $areaDifficulty)) ? 'checked' : '' }}> Lack of medical attention</label>
                                                    <div class="others-input-group">
                                                        <label class="checkbox-item">
                                                            <input type="checkbox" name="area_difficulty[]" value="Others" onchange="toggleAreaDifficultyOthersInput()" {{ in_array('Others', old('area_difficulty', $areaDifficulty)) || extractOthersValue($areaDifficulty) !== '' ? 'checked' : '' }}> Others, Specify
                                                        </label>
                                                        <input type="text" name="area_difficulty_others" id="area_difficulty_others_input" placeholder="Specify" class="form-control mt-2" value="{{ old('area_difficulty_others', extractOthersValue($areaDifficulty)) }}">
                                                    </div>
                                                </div>

                                    <div class="mb-4">
                                        <label class="input-label">43. List of Medicines for Maintenance</label>
                                        <em class="field-note d-block mb-2">(Type all your maintenance medicines. Example: Amlodipine 10mg, Losartan 50mg, etc.)</em>
                                        <textarea name="maintenance_medicines" class="form-control" rows="4" placeholder="List your maintenance medicines here...">{{ old('maintenance_medicines', $senior->maintenance_medicines) }}</textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label class="input-label">44. Do you have a scheduled medical/physical check-up?</label>
                                        <select name="scheduled_checkup" id="scheduled_checkup" class="form-control mt-3" onchange="toggleCheckupFrequency()">
                                            <option value="">Select</option>
                                            <option value="Yes" {{ old('scheduled_checkup', $senior->scheduled_checkup) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="No" {{ old('scheduled_checkup', $senior->scheduled_checkup) == 'No' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label class="input-label">45. If Yes, when is it done? <span id="checkup_frequency_required" class="text-danger" style="display: none;">*</span></label>
                                        <select name="checkup_frequency" id="checkup_frequency" class="form-control mt-3">
                                            <option value="">Select</option>
                                            <option value="Monthly" {{ old('checkup_frequency', $senior->checkup_frequency) == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                            <option value="Quarterly" {{ old('checkup_frequency', $senior->checkup_frequency) == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                                            <option value="Semi-annually" {{ old('checkup_frequency', $senior->checkup_frequency) == 'Semi-annually' ? 'selected' : '' }}>Semi-annually</option>
                                            <option value="Annually" {{ old('checkup_frequency', $senior->checkup_frequency) == 'Annually' ? 'selected' : '' }}>Annually</option>
                                            <option value="As needed" {{ old('checkup_frequency', $senior->checkup_frequency) == 'As needed' ? 'selected' : '' }}>As needed</option>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label class="input-label">46. Status <span style="color: red;">*</span></label>
                                        <select name="status" class="form-control mt-3" required>
                                            <option value="">Select Status</option>
                                            <option value="active" {{ (old('status') ?: $senior->status) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="deceased" {{ (old('status') ?: $senior->status) == 'deceased' ? 'selected' : '' }}>Deceased</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Step Navigation Buttons -->
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" id="prevBtn6" onclick="changeStep(-1)">Previous</button>
                                <button type="button" class="btn btn-primary" id="nextBtn6" onclick="changeStep(1)">Next</button>
                            </div>
                        </div>

                        <!-- Step Navigation Buttons -->
                        <div class="step-navigation">
                            <button type="button" class="btn btn-secondary" id="prevBtn7" onclick="changeStep(-1)">Previous</button>
                            <button type="button" class="btn btn-success" id="submitBtn7" onclick="showUpdateConfirmationModal()">Update Information</button>
                        </div>
                    </div>

                    <div class="submit-button-container" style="display:none;">
                        <button type="submit" class="submit-button"><i class="fas fa-save"></i> UPDATE SENIOR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
