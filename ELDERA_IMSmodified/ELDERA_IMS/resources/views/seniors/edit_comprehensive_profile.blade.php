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
                <!-- Header aligned with Add New Senior -->
                <div class="form-header">
                    <h2 class="form-title">I - IDENTIFYING INFORMATION</h2>
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
                        <input type="hidden" name="certification" id="certificationField" value="on">

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
                            <div class="form-section">
                                <!-- OCR Scan Panel -->
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

                                <div class="section-header">I. IDENTIFYING INFORMATION</div>

                                <!-- Name Fields -->
                                <div class="field-group">
                                    <label class="field-label">1. Name of Senior Citizen</label>
                                    <div class="name-fields-container">
                                        <div class="name-field">
                                            <input type="text" name="last_name" class="form-input" placeholder="Last Name" value="{{ old('last_name', $senior->last_name) }}" required>
                                        </div>
                                        <div class="name-field">
                                            <input type="text" name="first_name" class="form-input" placeholder="First Name" value="{{ old('first_name', $senior->first_name) }}" required>
                                        </div>
                                        <div class="name-field">
                                            <input type="text" name="middle_name" class="form-input" placeholder="Middle Name" value="{{ old('middle_name', $senior->middle_name) }}">
                                        </div>
                                        <div class="name-extension-field">
                                            <select name="name_extension" class="form-select">
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

                                <!-- Address Fields -->
                                <div class="field-group">
                                    <label class="field-label">2. Address</label>
                                    <div class="address-fields-container">
                                        <div class="address-row">
                                            <div class="address-field">
                                                <select name="region" class="form-select" required>
                                                    <option value="Region I" {{ old('region', $senior->region) == 'Region I' ? 'selected' : '' }}>Region I - Ilocos Region</option>
                                                </select>
                                            </div>
                                            <div class="address-field">
                                                <select name="province" class="form-select" required>
                                                    <option value="Pangasinan" {{ old('province', $senior->province) == 'Pangasinan' ? 'selected' : '' }}>Pangasinan</option>
                                                </select>
                                            </div>
                                            <div class="address-field">
                                                <select name="city" class="form-select" required>
                                                    <option value="Lingayen" {{ old('city', $senior->city) == 'Lingayen' ? 'selected' : '' }}>Lingayen</option>
                                                </select>
                                            </div>
                                            <div class="address-field">
                                                <select name="barangay" class="form-select" required>
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
                                        <div class="address-row">
                                            <div class="address-field">
                                                <input type="text" name="residence" class="form-input" placeholder="House No./Zone/Purok/Sitio" value="{{ old('residence', $senior->residence) }}">
                                            </div>
                                            <div class="address-field">
                                                <input type="text" name="street" class="form-input" placeholder="Street" value="{{ old('street', $senior->street) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Personal Information -->
                                <div class="personal-info-container">
                                    <div class="personal-info-row">
                                        <div class="field-group">
                                            <label class="field-label">3. Date of Birth <span class="required">*</span></label>
                                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-input" value="{{ old('date_of_birth', $senior->date_of_birth ? \Carbon\Carbon::parse($senior->date_of_birth)->format('Y-m-d') : '') }}" required>
                                            <small class="field-note">Must be 60 years or older</small>
                                        </div>
                                        <div class="field-group">
                                            <label class="field-label">4. Place of Birth <span class="required">*</span></label>
                                            <input type="text" name="birth_place" class="form-input" placeholder="Place of Birth" value="{{ old('birth_place', $senior->birth_place) }}" required>
                                        </div>
                                        <div class="field-group">
                                            <label class="field-label">5. Marital Status <span class="required">*</span></label>
                                            <select name="marital_status" class="form-select" required>
                                                <option value="">Select Marital Status</option>
                                                <option value="Single" {{ old('marital_status', $senior->marital_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                                <option value="Married" {{ old('marital_status', $senior->marital_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                                <option value="Widowed" {{ old('marital_status', $senior->marital_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                                <option value="Separated" {{ old('marital_status', $senior->marital_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                                                <option value="Others" {{ old('marital_status', $senior->marital_status) == 'Others' ? 'selected' : '' }}>Others</option>
                                            </select>
                                        </div>
                                        <div class="field-group">
                                            <label class="field-label">6. Gender <span class="required">*</span></label>
                                            <select name="sex" class="form-select" required>
                                                <option value="">Select</option>
                                                <option value="Male" {{ old('sex', $senior->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ old('sex', $senior->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="personal-info-row">
                                        <div class="field-group">
                                            <label class="field-label">7. Contact Number <span class="required">*</span></label>
                                            <input type="tel" name="contact_number" class="form-input" placeholder="Contact Number" value="{{ old('contact_number', $senior->contact_number) }}" required onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                                        </div>
                                        <div class="field-group">
                                            <label class="field-label">8. Email Address</label>
                                            <input type="email" name="email" class="form-input" placeholder="Email Address" value="{{ old('email', $senior->email) }}">
                                        </div>
                                        <div class="field-group">
                                            <label class="field-label">9. Religion</label>
                                            <select name="religion" class="form-select">
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
                                        <div class="field-group">
                                            <label class="field-label">10. Ethnic Origin</label>
                                            <input type="text" name="ethnic_origin" class="form-input" placeholder="Ethnic Origin" value="{{ old('ethnic_origin', $senior->ethnic_origin) }}">
                                        </div>
                                    </div>

                                    <div class="personal-info-row">
                                        <div class="field-group">
                                            <label class="field-label">11. Language Spoken</label>
                                            <input type="text" name="language" class="form-input" placeholder="Language Spoken" value="{{ old('language', $senior->language) }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- ID Numbers -->
                                <div class="id-numbers-container">
                                    <div class="id-numbers-row">
                                        <div class="field-group">
                                            <label class="field-label">12. OSCA ID No. <span class="required">*</span></label>
                                            <input type="text" name="osca_id" class="form-input" placeholder="OSCA ID Number" value="{{ old('osca_id', $senior->osca_id) }}" required onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                                        </div>
                                        <div class="field-group">
                                            <label class="field-label">13. GSIS/SSS No.</label>
                                            <input type="text" name="gsis_sss" class="form-input" placeholder="GSIS/SSS Number" value="{{ old('gsis_sss', $senior->gsis_sss) }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                                        </div>
                                        <div class="field-group">
                                            <label class="field-label">14. TIN</label>
                                            <input type="text" name="tin" class="form-input" placeholder="Tax Identification Number" value="{{ old('tin', $senior->tin) }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                                        </div>
                                    </div>

                                    <div class="id-numbers-row">
                                        <div class="field-group">
                                            <label class="field-label">15. PhilHealth No.</label>
                                            <input type="text" name="philhealth" class="form-input" placeholder="PhilHealth Number" value="{{ old('philhealth', $senior->philhealth) }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                                        </div>
                                        <div class="field-group">
                                            <label class="field-label">16. SC Association ID No.</label>
                                            <input type="text" name="sc_association" class="form-input" placeholder="Senior Citizen Association ID" value="{{ old('sc_association', $senior->sc_association) }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                                        </div>
                                        <div class="field-group">
                                            <label class="field-label">17. Other Gov't ID No.</label>
                                            <input type="text" name="other_govt_id" class="form-input" placeholder="Other Government ID" value="{{ old('other_govt_id', $senior->other_govt_id) }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Information -->
                                <div class="additional-info-container">
                                    <div class="additional-info-row">
                                        <div class="field-group">
                                            <label class="field-label">18. Capability to Travel</label>
                                            <select name="can_travel" class="form-select">
                                                <option value="">Select</option>
                                                <option value="Yes" {{ old('can_travel', $senior->can_travel) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                <option value="No" {{ old('can_travel', $senior->can_travel) == 'No' ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                        <div class="field-group">
                                            <label class="field-label">19. Service/Business/Employment</label>
                                            <input type="text" name="employment" class="form-input" placeholder="Specify" value="{{ old('employment', $senior->employment) }}">
                                        </div>
                                        <div class="field-group">
                                            <label class="field-label">20. Has Pension <span class="required">*</span></label>
                                            <select name="has_pension" class="form-select" required>
                                                <option value="">Select</option>
                                                <option value="1" {{ old('has_pension', $senior->has_pension) == 1 ? 'selected' : '' }}>Yes</option>
                                                <option value="0" {{ old('has_pension', $senior->has_pension) == 0 ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step Navigation -->
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" id="prevBtn1" onclick="changeStep(-1)" style="display: none;">Previous</button>
                                <button type="button" class="btn btn-primary" id="nextBtn1" onclick="changeStep(1)">Next</button>
                            </div>
                        </div>

                        <!-- Step 2: Family Information -->
                        <div class="form-step" id="step2">
                            <div class="form-section">
                                <div class="section-header">II. FAMILY INFORMATION</div>

                                <!-- 21. Name of Spouse -->
                                <div class="field-group">
                                    <label class="field-label">21. Name of Spouse</label>
                                    <div class="name-fields-container">
                                        <div class="name-field">
                                            <input type="text" name="spouse_last_name" class="form-input" placeholder="Last Name" value="{{ old('spouse_last_name', $senior->spouse_last_name) }}">
                                        </div>
                                        <div class="name-field">
                                            <input type="text" name="spouse_first_name" class="form-input" placeholder="First Name" value="{{ old('spouse_first_name', $senior->spouse_first_name) }}">
                                        </div>
                                        <div class="name-field">
                                            <input type="text" name="spouse_middle_name" class="form-input" placeholder="Middle Name" value="{{ old('spouse_middle_name', $senior->spouse_middle_name) }}">
                                        </div>
                                        <div class="name-extension-field">
                                            <input type="text" name="spouse_extension" class="form-input" placeholder="Extension (Jr, Sr)" value="{{ old('spouse_extension', $senior->spouse_extension) }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- 22. Father's Name -->
                                <div class="field-group">
                                    <label class="field-label">22. Father's Name</label>
                                    <div class="name-fields-container">
                                        <div class="name-field">
                                            <input type="text" name="father_last_name" class="form-input" placeholder="Last Name" value="{{ old('father_last_name', $senior->father_last_name) }}">
                                        </div>
                                        <div class="name-field">
                                            <input type="text" name="father_first_name" class="form-input" placeholder="First Name" value="{{ old('father_first_name', $senior->father_first_name) }}">
                                        </div>
                                        <div class="name-field">
                                            <input type="text" name="father_middle_name" class="form-input" placeholder="Middle Name" value="{{ old('father_middle_name', $senior->father_middle_name) }}">
                                        </div>
                                        <div class="name-extension-field">
                                            <input type="text" name="father_extension" class="form-input" placeholder="Extension (Jr, Sr)" value="{{ old('father_extension', $senior->father_extension) }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- 23. Mother's Maiden Name -->
                                <div class="field-group">
                                    <label class="field-label">23. Mother's Maiden Name</label>
                                    <div class="name-fields-container">
                                        <div class="name-field">
                                            <input type="text" name="mother_last_name" class="form-input" placeholder="Last Name" value="{{ old('mother_last_name', $senior->mother_last_name) }}">
                                        </div>
                                        <div class="name-field">
                                            <input type="text" name="mother_first_name" class="form-input" placeholder="First Name" value="{{ old('mother_first_name', $senior->mother_first_name) }}">
                                        </div>
                                        <div class="name-field">
                                            <input type="text" name="mother_middle_name" class="form-input" placeholder="Middle Name" value="{{ old('mother_middle_name', $senior->mother_middle_name) }}">
                                        </div>
                                        <div class="name-extension-field">
                                            <input type="text" name="mother_extension" class="form-input" placeholder="Extension" value="{{ old('mother_extension', $senior->mother_extension) }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- 24. Children -->
                                <div class="field-group">
                                    <label class="field-label mb-2">24. Child(ren)</label>
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
                                                            <button type="button" class="table-action-add" id="addChildBtn" title="Add Child Row" onclick="addChildRow()">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="childrenTableBody">
                                                @php
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
                                                        @php $idx = $i + 1; $working = $child['working'] ?? ''; @endphp
                                                        <tr>
                                                            <td><input type="text" name="child_name_{{ $idx }}" class="form-control form-control-sm border-0" placeholder="Last Name, First Name, Middle Name" value="{{ $child['name'] ?? '' }}"></td>
                                                            <td><input type="text" name="child_occupation_{{ $idx }}" class="form-control form-control-sm border-0" placeholder="Occupation" value="{{ $child['occupation'] ?? '' }}"></td>
                                                            <td><input type="text" name="child_income_{{ $idx }}" class="form-control form-control-sm border-0" placeholder="Income" value="{{ $child['income'] ?? '' }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                                                            <td><input type="text" name="child_age_{{ $idx }}" class="form-control form-control-sm border-0" placeholder="Age" value="{{ $child['age'] ?? '' }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                                                            <td>
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
                                                        <td><input type="text" name="child_name_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Last Name, First Name, Middle Name"></td>
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

                                <!-- 25. Other Dependents -->
                                <div class="field-group">
                                    <label class="field-label mb-2">25. Other Dependents</label>
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
                                                            <button type="button" class="table-action-add" id="addDependentBtn" title="Add Dependent Row" onclick="addDependentRow()">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="dependentsTableBody">
                                                @php
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
                                                        @php $idx = $i + 1; $working = $dep['working'] ?? ''; @endphp
                                                        <tr>
                                                            <td><input type="text" name="dependent_name_{{ $idx }}" class="form-control form-control-sm border-0" placeholder="Name of Dependent" value="{{ $dep['name'] ?? '' }}"></td>
                                                            <td><input type="text" name="dependent_occupation_{{ $idx }}" class="form-control form-control-sm border-0" placeholder="Occupation of Dependent" value="{{ $dep['occupation'] ?? '' }}"></td>
                                                            <td><input type="text" name="dependent_income_{{ $idx }}" class="form-control form-control-sm border-0" placeholder="Income" value="{{ $dep['income'] ?? '' }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                                                            <td><input type="text" name="dependent_age_{{ $idx }}" class="form-control form-control-sm border-0" placeholder="Age" value="{{ $dep['age'] ?? '' }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                                                            <td>
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

                            <!-- Step Navigation -->
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" id="prevBtn2" onclick="changeStep(-1)">Previous</button>
                                <button type="button" class="btn btn-primary" id="nextBtn2" onclick="changeStep(1)">Next</button>
                            </div>
                        </div>

                        <!-- Step 3: Education / HR Profile -->
                        <div class="form-step" id="step3">
                            <div class="form-section">
                                <div class="section-header">III. EDUCATION / HR PROFILE</div>

                                <div class="row">
                                    <!-- Highest Educational Attainment -->
                                    <div class="col-md-4">
                                        <div class="field-group">
                                            <label class="field-label">26. Highest Educational Attainment</label>
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
                                                    <input type="text" name="education_others_specify" class="form-input mt-1" value="{{ old('education_others_specify', extractOthersValue($senior->education_level ? [$senior->education_level] : [])) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Specialization / Technical Skills -->
                                    <div class="col-md-4">
                                        <div class="field-group">
                                            <label class="field-label">27. Specialization / Technical Skills (Check all applicable)</label>
                                            @php $skills = is_array($senior->skills) ? $senior->skills : []; @endphp
                                            <div class="d-flex flex-column gap-1 mt-2">
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
                                                    <input type="text" name="skills_others_specify" class="form-input mt-1" value="{{ old('skills_others_specify', extractOthersValue($skills)) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Shared Skills & Community Activities -->
                                    <div class="col-md-4">
                                        <div class="field-group">
                                            <label class="field-label">28. Shared Skills (Community Service)</label>
                                            <textarea name="shared_skills" class="form-input" rows="3" placeholder="Type skills separated by commas">{{ old('shared_skills', $senior->shared_skills) }}</textarea>
                                        </div>

                                        <div class="field-group">
                                            <label class="field-label">29. Involvement in Community Activities (Check all applicable)</label>
                                            @php $activities = is_array($senior->community_activities) ? $senior->community_activities : []; @endphp
                                            <div class="d-flex flex-column gap-1 mt-2">
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
                                                    <input type="text" name="community_activities_others_specify" class="form-input mt-1" value="{{ old('community_activities_others_specify', extractOthersValue($activities)) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step Navigation -->
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" id="prevBtn3" onclick="changeStep(-1)">Previous</button>
                                <button type="button" class="btn btn-primary" id="nextBtn3" onclick="changeStep(1)">Next</button>
                            </div>
                        </div>
                        
                        <!-- Step 4: Dependency Profile -->
                        <div class="form-step" id="step4">
                            <div class="form-section dependency-profile-section">
                                <div class="section-header">IV. DEPENDENCY PROFILE</div>

                                <div class="row g-4 mb-4">
                                    <!-- Living Condition & Arrangement -->
                                    <div class="col-md-6">
                                        <div class="field-group">
                                            <label class="field-label">30. Living Condition</label>
                                            <div class="d-flex flex-column gap-1 mt-2">
                                                <label><input type="radio" name="living_condition_primary" value="Living Alone" {{ old('living_condition_primary', $senior->living_condition_primary) == 'Living Alone' ? 'checked' : '' }}> Living Alone</label>
                                                <label><input type="radio" name="living_condition_primary" value="Living with" {{ old('living_condition_primary', $senior->living_condition_primary) == 'Living with' ? 'checked' : '' }}> Living with</label>

                                                @php $livingWith = is_array($senior->living_with) ? $senior->living_with : []; @endphp
                                                <div class="mt-2 ms-4">
                                                    <label><input type="checkbox" name="living_with[]" value="Grand Children" {{ in_array('Grand Children', old('living_with', $livingWith)) ? 'checked' : '' }}> Grand Children</label>
                                                    <label><input type="checkbox" name="living_with[]" value="Common Law Spouse" {{ in_array('Common Law Spouse', old('living_with', $livingWith)) ? 'checked' : '' }}> Common Law Spouse</label>
                                                    <label><input type="checkbox" name="living_with[]" value="Spouse" {{ in_array('Spouse', old('living_with', $livingWith)) ? 'checked' : '' }}> Spouse</label>
                                                    <label><input type="checkbox" name="living_with[]" value="In-laws" {{ in_array('In-laws', old('living_with', $livingWith)) ? 'checked' : '' }}> In-laws</label>
                                                    <label><input type="checkbox" name="living_with[]" value="Children" {{ in_array('Children', old('living_with', $livingWith)) ? 'checked' : '' }}> Children</label>
                                                    <label><input type="checkbox" name="living_with[]" value="Relatives" {{ in_array('Relatives', old('living_with', $livingWith)) ? 'checked' : '' }}> Relatives</label>
                                                    <label><input type="checkbox" name="living_with[]" value="Friends" {{ in_array('Friends', old('living_with', $livingWith)) ? 'checked' : '' }}> Friends</label>
                                                    <div class="mt-2">
                                                        <label class="mb-0">Others, Specify</label>
                                                        <input type="text" name="living_with_others_specify" class="form-input mt-1" value="{{ old('living_with_others_specify', $senior->living_with_others_specify) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Household Condition -->
                                    <div class="col-md-6">
                                        <div class="field-group">
                                            <label class="field-label">31. Household Condition</label>
                                            @php $householdCondition = is_array($senior->household_condition) ? $senior->household_condition : []; @endphp
                                            <div class="d-flex flex-column gap-1 mt-2">
                                                <label><input type="checkbox" name="household_condition[]" value="No privacy" {{ in_array('No privacy', old('household_condition', $householdCondition)) ? 'checked' : '' }}> No privacy</label>
                                                <label><input type="checkbox" name="household_condition[]" value="Overcrowded in home" {{ in_array('Overcrowded in home', old('household_condition', $householdCondition)) ? 'checked' : '' }}> Overcrowded in home</label>
                                                <label><input type="checkbox" name="household_condition[]" value="No permanent house" {{ in_array('No permanent house', old('household_condition', $householdCondition)) ? 'checked' : '' }}> No permanent house</label>
                                                <label><input type="checkbox" name="household_condition[]" value="High cost of rent" {{ in_array('High cost of rent', old('household_condition', $householdCondition)) ? 'checked' : '' }}> High cost of rent</label>
                                                <label><input type="checkbox" name="household_condition[]" value="Longing for independent living quiet atmosphere" {{ in_array('Longing for independent living quiet atmosphere', old('household_condition', $householdCondition)) ? 'checked' : '' }}> Longing for independent living quiet atmosphere</label>
                                                <div class="mt-2">
                                                    <label>Others, Specify</label>
                                                    <input type="text" name="household_condition_others_specify" class="form-input mt-1" value="{{ old('household_condition_others_specify', $senior->household_condition_others_specify) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step Navigation -->
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" id="prevBtn4" onclick="changeStep(-1)">Previous</button>
                                <button type="button" class="btn btn-primary" id="nextBtn4" onclick="changeStep(1)">Next</button>
                            </div>
                        </div>

                        <!-- Step 5: Economic Profile -->
                        <div class="form-step" id="step5">
                            <div class="form-section dependency-profile-section">
                                <div class="section-header">V. ECONOMIC PROFILE</div>

                                <div class="row g-4 mb-4">
                                    <!-- Source of Income and Assistance -->
                                    <div class="col-md-4">
                                        <div class="field-group">
                                            <label class="field-label">32. Source of Income and Assistance</label>
                                            @php $sourceIncome = is_array($senior->source_of_income) ? $senior->source_of_income : []; @endphp
                                            <div class="d-flex flex-column gap-1 mt-2">
                                                <label><input type="checkbox" name="source_of_income[]" value="Own earnings, salary / wages" {{ in_array('Own earnings, salary / wages', old('source_of_income', $sourceIncome)) ? 'checked' : '' }}> Own earnings, salary / wages</label>
                                                <label><input type="checkbox" name="source_of_income[]" value="Own Pension" {{ in_array('Own Pension', old('source_of_income', $sourceIncome)) ? 'checked' : '' }}> Own Pension</label>
                                                <label><input type="checkbox" name="source_of_income[]" value="Stocks / Dividends" {{ in_array('Stocks / Dividends', old('source_of_income', $sourceIncome)) ? 'checked' : '' }}> Stocks / Dividends</label>
                                                <label><input type="checkbox" name="source_of_income[]" value="Dependent on children / relatives" {{ in_array('Dependent on children / relatives', old('source_of_income', $sourceIncome)) ? 'checked' : '' }}> Dependent on children / relatives</label>
                                                <label><input type="checkbox" name="source_of_income[]" value="Spouse's salary" {{ in_array("Spouse's salary", old('source_of_income', $sourceIncome)) ? 'checked' : '' }}> Spouse's salary</label>
                                                <label><input type="checkbox" name="source_of_income[]" value="Spouse Pension" {{ in_array('Spouse Pension', old('source_of_income', $sourceIncome)) ? 'checked' : '' }}> Spouse Pension</label>
                                                <label><input type="checkbox" name="source_of_income[]" value="Rental / Sharecorp" {{ in_array('Rental / Sharecorp', old('source_of_income', $sourceIncome)) ? 'checked' : '' }}> Rental / Sharecrop</label>
                                                <label><input type="checkbox" name="source_of_income[]" value="Savings" {{ in_array('Savings', old('source_of_income', $sourceIncome)) ? 'checked' : '' }}> Savings</label>
                                                <label><input type="checkbox" name="source_of_income[]" value="Livestock / orchard / farm" {{ in_array('Livestock / orchard / farm', old('source_of_income', $sourceIncome)) ? 'checked' : '' }}> Livestock / orchard / farm</label>
                                                <label><input type="checkbox" name="source_of_income[]" value="Fishing" {{ in_array('Fishing', old('source_of_income', $sourceIncome)) ? 'checked' : '' }}> Fishing</label>
                                                <div class="mt-2">
                                                    <label>Others, Specify</label>
                                                    <input type="text" name="source_of_income_others" class="form-input mt-1" value="{{ old('source_of_income_others', $senior->source_of_income_others) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Assets: Real and Immovable -->
                                    <div class="col-md-4">
                                        <div class="field-group">
                                            <label class="field-label">33. Assets: Real and Immovable Properties</label>
                                            @php $realAssets = is_array($senior->real_assets) ? $senior->real_assets : []; @endphp
                                            <div class="d-flex flex-column gap-1 mt-2">
                                                <label><input type="checkbox" name="real_assets[]" value="House and Lot" {{ in_array('House and Lot', old('real_assets', $realAssets)) ? 'checked' : '' }}> House and Lot</label>
                                                <label><input type="checkbox" name="real_assets[]" value="Farm Land" {{ in_array('Farm Land', old('real_assets', $realAssets)) ? 'checked' : '' }}> Farm Land</label>
                                                <label><input type="checkbox" name="real_assets[]" value="Livestock" {{ in_array('Livestock', old('real_assets', $realAssets)) ? 'checked' : '' }}> Livestock</label>
                                                <label><input type="checkbox" name="real_assets[]" value="Orchard" {{ in_array('Orchard', old('real_assets', $realAssets)) ? 'checked' : '' }}> Orchard</label>
                                                <label><input type="checkbox" name="real_assets[]" value="Fishing Boat / Gear" {{ in_array('Fishing Boat / Gear', old('real_assets', $realAssets)) ? 'checked' : '' }}> Fishing Boat / Gear</label>
                                                <div class="mt-2">
                                                    <label>Others, Specify</label>
                                                    <input type="text" name="real_assets_others" class="form-input mt-1" value="{{ old('real_assets_others', $senior->real_assets_others) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Assets: Personal and Movable -->
                                    <div class="col-md-4">
                                        <div class="field-group">
                                            <label class="field-label">34. Assets: Personal and Movable Properties</label>
                                            @php $personalAssets = is_array($senior->personal_assets) ? $senior->personal_assets : []; @endphp
                                            <div class="d-flex flex-column gap-1 mt-2">
                                                <label><input type="checkbox" name="personal_assets[]" value="Jewelry" {{ in_array('Jewelry', old('personal_assets', $personalAssets)) ? 'checked' : '' }}> Jewelry</label>
                                                <label><input type="checkbox" name="personal_assets[]" value="Vehicle" {{ in_array('Vehicle', old('personal_assets', $personalAssets)) ? 'checked' : '' }}> Vehicle</label>
                                                <label><input type="checkbox" name="personal_assets[]" value="Appliances" {{ in_array('Appliances', old('personal_assets', $personalAssets)) ? 'checked' : '' }}> Appliances</label>
                                                <label><input type="checkbox" name="personal_assets[]" value="Livestock" {{ in_array('Livestock', old('personal_assets', $personalAssets)) ? 'checked' : '' }}> Livestock</label>
                                                <div class="mt-2">
                                                    <label>Others, Specify</label>
                                                    <input type="text" name="personal_assets_others" class="form-input mt-1" value="{{ old('personal_assets_others', $senior->personal_assets_others) }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="field-group mt-3">
                                            <label class="field-label">35. Monthly Income <em>(in Philippine Peso)</em></label>
                                            <div class="mt-2">
                                                <input type="text" name="monthly_income" class="form-input" placeholder="Enter monthly income amount" value="{{ old('monthly_income', $senior->monthly_income ?? 0) }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                                                <small class="text-muted">Please enter the exact monthly income amount (numbers only)</small>
                                            </div>
                                        </div>

                                        <div class="field-group">
                                            <label class="field-label">36. Problems / Needs Commonly Encountered</label>
                                            @php $problemsNeeds = is_array($senior->problems_needs) ? $senior->problems_needs : []; @endphp
                                            <div class="d-flex flex-column gap-1 mt-2">
                                                <label><input type="checkbox" name="problems_needs[]" value="Lack of income / resources" {{ in_array('Lack of income / resources', old('problems_needs', $problemsNeeds)) ? 'checked' : '' }}> Lack of income / resources</label>
                                                <label><input type="checkbox" name="problems_needs[]" value="Loss of income / resources" {{ in_array('Loss of income / resources', old('problems_needs', $problemsNeeds)) ? 'checked' : '' }}> Loss of income / resources</label>
                                                <label><input type="checkbox" name="problems_needs[]" value="Skills / capability training" {{ in_array('Skills / capability training', old('problems_needs', $problemsNeeds)) ? 'checked' : '' }}> Skills / capability training</label>
                                                <label><input type="checkbox" name="problems_needs[]" value="Livelihood Opportunities" {{ in_array('Livelihood Opportunities', old('problems_needs', $problemsNeeds)) ? 'checked' : '' }}> Livelihood Opportunities</label>
                                                <div class="mt-2">
                                                    <label>Others, Specify</label>
                                                    <input type="text" name="problems_needs_others" class="form-input mt-1" value="{{ old('problems_needs_others', $senior->problems_needs_others) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step Navigation -->
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" id="prevBtn5" onclick="changeStep(-1)">Previous</button>
                                <button type="button" class="btn btn-primary" id="nextBtn5" onclick="changeStep(1)">Next</button>
                            </div>
                        </div>

                        <!-- Step 6: Health Profile -->
                        <div class="form-step" id="step6">
                            <div class="form-section dependency-profile-section">
                                <div class="section-header">VI. HEALTH PROFILE</div>

                                <div class="row g-4 mb-4">
                                    <!-- Medical Concern -->
                                    <div class="col-md-4">
                                        <div class="field-group">
                                            <label class="field-label">37. Medical Concern</label>
                                            <label class="field-label mt-2">Blood Type</label>
                                            <select name="blood_type" class="form-select mb-3">
                                                @php $bloodType = old('blood_type', $senior->blood_type); @endphp
                                                <option value="">Select Blood Type</option>
                                                <option value="A+" {{ $bloodType == 'A+' ? 'selected' : '' }}>A+</option>
                                                <option value="A-" {{ $bloodType == 'A-' ? 'selected' : '' }}>A-</option>
                                                <option value="B+" {{ $bloodType == 'B+' ? 'selected' : '' }}>B+</option>
                                                <option value="B-" {{ $bloodType == 'B-' ? 'selected' : '' }}>B-</option>
                                                <option value="AB+" {{ $bloodType == 'AB+' ? 'selected' : '' }}>AB+</option>
                                                <option value="AB-" {{ $bloodType == 'AB-' ? 'selected' : '' }}>AB-</option>
                                                <option value="O+" {{ $bloodType == 'O+' ? 'selected' : '' }}>O+</option>
                                                <option value="O-" {{ $bloodType == 'O-' ? 'selected' : '' }}>O-</option>
                                            </select>

                                            <label class="field-label">Physical Disability</label>
                                            <input type="text" name="physical_disability" class="form-input mb-3" placeholder="Physical Disability type here" value="{{ old('physical_disability', $senior->physical_disability) }}">

                                            <label class="field-label">Health Problems / Ailments</label>
                                            @php $healthProblems = is_array($senior->health_problems) ? $senior->health_problems : []; @endphp
                                            <div class="d-flex flex-column gap-1 mt-2">
                                                <label><input type="checkbox" name="health_problems[]" value="Arthritis / Gout" {{ in_array('Arthritis / Gout', old('health_problems', $healthProblems)) ? 'checked' : '' }}> Arthritis / Gout</label>
                                                <label><input type="checkbox" name="health_problems[]" value="Coronary Heart Disease" {{ in_array('Coronary Heart Disease', old('health_problems', $healthProblems)) ? 'checked' : '' }}> Coronary Heart Disease</label>
                                                <label><input type="checkbox" name="health_problems[]" value="Diabetes" {{ in_array('Diabetes', old('health_problems', $healthProblems)) ? 'checked' : '' }}> Diabetes</label>
                                                <label><input type="checkbox" name="health_problems[]" value="Chronic Kidney Disease" {{ in_array('Chronic Kidney Disease', old('health_problems', $healthProblems)) ? 'checked' : '' }}> Chronic Kidney Disease</label>
                                                <label><input type="checkbox" name="health_problems[]" value="Alzheimer's / Dementia" {{ in_array("Alzheimer's / Dementia", old('health_problems', $healthProblems)) ? 'checked' : '' }}> Alzheimer's / Dementia</label>
                                                <label><input type="checkbox" name="health_problems[]" value="Chronic Obstructive Pulmonary Disease" {{ in_array('Chronic Obstructive Pulmonary Disease', old('health_problems', $healthProblems)) ? 'checked' : '' }}> Chronic Obstructive Pulmonary Disease</label>
                                                <div class="mt-2">
                                                    <label>Others, Specify</label>
                                                    <input type="text" name="health_problems_others" class="form-input mt-1" value="{{ old('health_problems_others', $senior->health_problems_others) }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="field-group">
                                            <label class="field-label">38. Dental Concern</label>
                                            @php $dentalConcern = is_array($senior->dental_concern) ? $senior->dental_concern : []; @endphp
                                            <div class="d-flex flex-column gap-1 mt-2">
                                                <label><input type="checkbox" name="dental_concern[]" value="Needs Dental Care" {{ in_array('Needs Dental Care', old('dental_concern', $dentalConcern)) ? 'checked' : '' }}> Needs Dental Care</label>
                                                <div class="mt-2">
                                                    <label>Others, Specify</label>
                                                    <input type="text" name="dental_concern_others" class="form-input mt-1" value="{{ old('dental_concern_others', $senior->dental_concern_others) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Visual / Hearing / Social Emotional -->
                                    <div class="col-md-4">
                                        <div class="field-group">
                                            <label class="field-label">39. Visual Concern</label>
                                            @php $visualConcern = is_array($senior->visual_concern) ? $senior->visual_concern : []; @endphp
                                            <div class="d-flex flex-column gap-1 mt-2">
                                                <label><input type="checkbox" name="visual_concern[]" value="Eye impairment" {{ in_array('Eye impairment', old('visual_concern', $visualConcern)) ? 'checked' : '' }}> Eye impairment</label>
                                                <label><input type="checkbox" name="visual_concern[]" value="Needs eye care" {{ in_array('Needs eye care', old('visual_concern', $visualConcern)) ? 'checked' : '' }}> Needs eye care</label>
                                                <div class="mt-2">
                                                    <label>Others, Specify</label>
                                                    <input type="text" name="visual_concern_others" class="form-input mt-1" value="{{ old('visual_concern_others', $senior->visual_concern_others) }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="field-group">
                                            <label class="field-label">40. Hearing Condition</label>
                                            @php $hearingCondition = is_array($senior->hearing_condition) ? $senior->hearing_condition : []; @endphp
                                            <div class="d-flex flex-column gap-1 mt-2">
                                                <label><input type="checkbox" name="hearing_condition[]" value="Aural impairment" {{ in_array('Aural impairment', old('hearing_condition', $hearingCondition)) ? 'checked' : '' }}> Aural impairment</label>
                                                <div class="mt-2">
                                                    <label>Others, Specify</label>
                                                    <input type="text" name="hearing_condition_others" class="form-input mt-1" value="{{ old('hearing_condition_others', $senior->hearing_condition_others) }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="field-group">
                                            <label class="field-label">41. Social / Emotional</label>
                                            @php $socialEmotional = is_array($senior->social_emotional) ? $senior->social_emotional : []; @endphp
                                            <div class="d-flex flex-column gap-1 mt-2">
                                                <label><input type="checkbox" name="social_emotional[]" value="Feeling neglect / rejection" {{ in_array('Feeling neglect / rejection', old('social_emotional', $socialEmotional)) ? 'checked' : '' }}> Feeling neglect / rejection</label>
                                                <label><input type="checkbox" name="social_emotional[]" value="Fear and anxiety" {{ in_array('Fear and anxiety', old('social_emotional', $socialEmotional)) ? 'checked' : '' }}> Fear and anxiety</label>
                                                <label><input type="checkbox" name="social_emotional[]" value="Depression" {{ in_array('Depression', old('social_emotional', $socialEmotional)) ? 'checked' : '' }}> Depression</label>
                                                <label><input type="checkbox" name="social_emotional[]" value="Nervousness" {{ in_array('Nervousness', old('social_emotional', $socialEmotional)) ? 'checked' : '' }}> Nervousness</label>
                                                <label><input type="checkbox" name="social_emotional[]" value="Hysterical" {{ in_array('Hysterical', old('social_emotional', $socialEmotional)) ? 'checked' : '' }}> Hysterical</label>
                                                <label><input type="checkbox" name="social_emotional[]" value="Hopelessness" {{ in_array('Hopelessness', old('social_emotional', $socialEmotional)) ? 'checked' : '' }}> Hopelessness</label>
                                                <label><input type="checkbox" name="social_emotional[]" value="Loss of self-confidence" {{ in_array('Loss of self-confidence', old('social_emotional', $socialEmotional)) ? 'checked' : '' }}> Loss of self-confidence</label>
                                                <label><input type="checkbox" name="social_emotional[]" value="Loss of self-respect" {{ in_array('Loss of self-respect', old('social_emotional', $socialEmotional)) ? 'checked' : '' }}> Loss of self-respect</label>
                                                <label><input type="checkbox" name="social_emotional[]" value="Loss of interest / initiative" {{ in_array('Loss of interest / initiative', old('social_emotional', $socialEmotional)) ? 'checked' : '' }}> Loss of interest / initiative</label>
                                                <label><input type="checkbox" name="social_emotional[]" value="Other psychiatric concern" {{ in_array('Other psychiatric concern', old('social_emotional', $socialEmotional)) ? 'checked' : '' }}> Other psychiatric concern</label>
                                                <div class="mt-2">
                                                    <label>Others, Specify</label>
                                                    <input type="text" name="social_emotional_others" class="form-input mt-1" value="{{ old('social_emotional_others', $senior->social_emotional_others) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Medicines and Check-up -->
                                    <div class="col-md-4">
                                        <div class="field-group">
                                            <label class="field-label">42. Maintenance Medicines</label>
                                            <textarea name="maintenance_medicines" class="form-input" rows="3" placeholder="List maintenance medicines">{{ old('maintenance_medicines', $senior->maintenance_medicines) }}</textarea>
                                        </div>

                                        <div class="field-group">
                                            <label class="field-label">43. Scheduled Check-up</label>
                                            @php $scheduled = old('scheduled_checkup', $senior->scheduled_checkup); @endphp
                                            <select name="scheduled_checkup" class="form-select">
                                                <option value="">Select</option>
                                                <option value="Yes" {{ $scheduled == 'Yes' ? 'selected' : '' }}>Yes</option>
                                                <option value="No" {{ $scheduled == 'No' ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>

                                        <div class="field-group">
                                            <label class="field-label">44. Check-up Frequency</label>
                                            @php $freq = old('checkup_frequency', $senior->checkup_frequency); @endphp
                                            <select name="checkup_frequency" class="form-select">
                                                <option value="">Select</option>
                                                <option value="Monthly" {{ $freq == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                                <option value="Quarterly" {{ $freq == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                                                <option value="Semi-annually" {{ $freq == 'Semi-annually' ? 'selected' : '' }}>Semi-annually</option>
                                                <option value="Annually" {{ $freq == 'Annually' ? 'selected' : '' }}>Annually</option>
                                                <option value="As needed" {{ $freq == 'As needed' ? 'selected' : '' }}>As needed</option>
                                            </select>
                                        </div>

                                        <div class="field-group">
                                            <label class="field-label">46. Status</label>
                                            @php $status = old('status', $senior->status); @endphp
                                            <select name="status" class="form-select">
                                                <option value="">Select Status</option>
                                                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="deceased" {{ $status == 'deceased' ? 'selected' : '' }}>Deceased</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step Navigation -->
                            <div class="step-navigation">
                                <button type="button" class="btn btn-secondary" id="prevBtn6" onclick="changeStep(-1)">Previous</button>
                                <button type="button" class="btn btn-primary" id="nextBtn6" onclick="changeStep(1)">Next</button>
                            </div>
                        </div>
                        
                        <!-- Step 7: Photo Upload & Finalize -->
                        <div class="form-step" id="step7">
                            <div class="form-section">
                                <div class="section-header">VII. PHOTO UPLOAD</div>
                                
                                <div class="row g-4 mb-4">
                                    <div class="col-md-12">
                                        <div class="field-group">
                                            <label class="field-label">47. Upload / Update Photo</label>
                                            <div class="photo-upload-section" style="display: flex; justify-content: center; align-items: center;">
                                                <x-photo-upload id="senior_photo" name="photo" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step Navigation -->
                                <div class="step-navigation">
                                    <button type="button" class="btn btn-secondary" id="prevBtn7" onclick="changeStep(-1)">Previous</button>
                                    <button type="button" class="btn btn-success" id="nextBtn7" onclick="showUpdateConfirmationModal()">Update Information</button>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </x-header>
</x-sidebar>

<!-- Add the CSS and JavaScript at the end -->
<style>
/* Copy all the CSS from add_new_senior.blade.php */
body { 
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: #2c3e50;
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
    border-radius: 0px;
}

/* Form and header styles */
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

/* Header styles identical to Add New Senior */
.form-header {
    background: linear-gradient(135deg, #ffb7ce 0%, #ff9bb8 100%);
    color: #2c3e50;
    padding: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    font-weight: 700;
    font-family: "Poppins", sans-serif;
    text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.8);
    border-bottom: 3px solid #e31575;
    box-shadow: 0 4px 12px rgba(227, 21, 117, 0.15);
}

.form-title {
    font-size: 1.4rem;
    font-weight: 700;
    margin: 0;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
/* Match Add New Senior section padding */
.form-step {
    display: block;
    padding: 2.5rem;
}

.form-section-content {
    /* placeholder for per-step wrappers; keep consistent spacing */
}

.progress-container {
    display: flex;
    align-items: center;
}

.progress-steps {
    display: flex;
    gap: 10px;
    align-items: center;
}

.step-indicator {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid rgba(255, 255, 255, 0.5);
}

.step-indicator.active {
    background: linear-gradient(135deg, #e31575 0%, #c01060 100%);
    color: white;
    border-color: #e31575;
    box-shadow: 0 2px 8px rgba(227, 21, 117, 0.3);
}

.step-indicator.completed {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border-color: #28a745;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
}

/* Progress steps baseline (other containers) */
/* Remove old wide indicator styles to avoid conflicts — header uses circular indicators */
/* (intentionally replaced by the .progress-steps and .step-indicator rules above) */

/* Section headers hidden (redundant now that form header shows titles) */
.section-header {
    display: none !important;
}

/* Form fields */
.field-group {
    margin-bottom: 16px;
}

.field-label {
    font-weight: 600;
    font-size: 15px;
    color: #2c3e50;
    margin-bottom: 8px;
    letter-spacing: 0.2px;
    display: block;
}

.form-section form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.form-input, .form-select {
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
    box-shadow: inset 0 2px 4px rgba(227, 21, 117, 0.1), inset 0 1px 2px rgba(227, 21, 117, 0.15);
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: #e31575;
    background-color: #fefefe;
    box-shadow: inset 0 3px 6px rgba(227, 21, 117, 0.15), inset 0 2px 4px rgba(227, 21, 117, 0.2), 0 0 0 3px rgba(227, 21, 117, 0.12);
    transform: translateY(-1px);
}

.form-input:hover, .form-select:hover {
    border-color: #c01060;
    box-shadow: inset 0 2px 5px rgba(227, 21, 117, 0.12), inset 0 1px 3px rgba(227, 21, 117, 0.18);
}

/* Enhanced Select Dropdown Styling */
.form-select {
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

.form-select:focus {
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23c01060' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
}

.required {
    color: #e31575;
}

.field-note {
    font-size: 11px;
    color: #666;
    margin-top: 4px;
    display: block;
}

/* Name fields container */
.name-fields-container {
    display: flex;
    gap: 10px;
    margin-bottom: 5px;
}

.name-field {
    flex: 1;
}

.name-extension-field {
    flex: 0.3;
}

/* Address fields */
.address-fields-container .address-row {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}

.address-field {
    flex: 1;
}

/* Personal info container */
.personal-info-container .personal-info-row {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.personal-info-row .field-group {
    flex: 1;
}

/* ID numbers container */
.id-numbers-container .id-numbers-row {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.id-numbers-row .field-group {
    flex: 1;
}

/* Additional info container */
.additional-info-container .additional-info-row {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.additional-info-row .field-group {
    flex: 1;
}

/* Step navigation (match add_new_senior) */
.step-navigation {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 1rem;
    margin-top: 2rem;
    margin-bottom: 2rem; /* ensure space below buttons */
    padding-bottom: 1rem; /* consistent visual breathing room */
}

.step-navigation .btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    border: none;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.btn-primary,
#nextBtn,
#nextBtn2,
#nextBtn3,
#nextBtn4,
#nextBtn5,
#nextBtn6 {
    padding: 10px 20px;
    background-color: #e31575;
    border-color: #e31575;
    color: white;
    font-weight: bold;
}

.btn-primary:hover,
#nextBtn:hover,
#nextBtn2:hover,
#nextBtn3:hover,
#nextBtn4:hover,
#nextBtn5:hover,
#nextBtn6:hover {
    background-color: #ffb7ce;
    border-color: #ffb7ce;
    color: #e31575;
}

.btn-success,
#nextBtn7,
#submitBtn {
    background-color: #e31575;
    border-color: #e31575;
    color: white;
}

.btn-success:hover,
#nextBtn7:hover,
#submitBtn:hover {
    background-color: #ffb7ce;
    border-color: #ffb7ce;
    color: #e31575;
}

/* ===== Table Action Buttons (mirrored from Add New Senior) ===== */
.table-action-add {
    width: 28px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #c01060;
    background: #fff;
    border: 2px solid #ffb7ce;
    border-radius: 50%;
    box-shadow: none;
    transition: background-color 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
}
.table-action-add:hover {
    background-color: #fff8fb;
    border-color: #e31575;
    transform: translateY(-1px);
}
.table-action-add i {
    font-size: 12px;
}

.table-action-delete {
    width: 28px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #c01060;
    background: #fff;
    border: 2px solid #ffb7ce;
    border-radius: 50%;
    box-shadow: none;
    transition: background-color 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
}
.table-action-delete:hover {
    background-color: #fff8fb;
    border-color: #e31575;
    transform: translateY(-1px);
}
.table-action-delete i {
    font-size: 12px;
}

/* Form steps */
.form-step {
    display: none;
}

.form-step.active {
    display: block;
}

/* Typing instructions */
.typing-instructions {
    margin-top: 8px;
    font-size: 12px;
    color: #666;
}

.typing-link {
    color: #e31575;
    text-decoration: none;
}

.typing-link:hover {
    text-decoration: underline;
}

/* OCR Panel Styles */
.ocr-minimal-panel {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.ocr-minimal-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.ocr-minimal-header i {
    color: #e31575;
    font-size: 18px;
}

.ocr-minimal-header span {
    font-weight: 600;
    color: #2c3e50;
    font-size: 16px;
}

.ocr-hint {
    color: #6c757d;
    font-size: 12px;
    margin-left: auto;
}

.ocr-minimal-content {
    display: flex;
    gap: 15px;
    align-items: center;
}

.ocr-file-input-wrapper {
    flex: 1;
}

.ocr-file-input {
    display: none;
}

.ocr-file-label {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: white;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #6c757d;
    font-size: 14px;
}

.ocr-file-label:hover {
    border-color: #e31575;
    background: #fef7fb;
    color: #e31575;
}

.ocr-scan-btn {
    padding: 12px 20px;
    background: linear-gradient(135deg, #e31575 0%, #c01060 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.ocr-scan-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #c01060 0%, #a00d50 100%);
    transform: translateY(-1px);
}

.ocr-scan-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.ocr-progress-minimal {
    margin-top: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.ocr-progress-bar-minimal {
    flex: 1;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.ocr-progress-fill-minimal {
    height: 100%;
    background: linear-gradient(135deg, #e31575 0%, #c01060 100%);
    width: 0%;
    transition: width 0.3s ease;
}

.ocr-progress-text-minimal {
    font-size: 12px;
    font-weight: 600;
    color: #2c3e50;
    min-width: 40px;
}

.ocr-status-minimal {
    margin-top: 10px;
    font-size: 13px;
}

.ocr-results-minimal {
    margin-top: 15px;
    padding: 12px 16px;
    background: #d4edda;
    border: 1px solid #c3e6cb;
    border-radius: 8px;
    color: #155724;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
}

.ocr-files-list {
    margin-top: 15px;
}

/* Responsive design */
@media (max-width: 768px) {
    .main {
        margin-left: 0;
        margin-top: 60px;
    }
    
    .name-fields-container,
    .address-row,
    .personal-info-row,
    .id-numbers-row,
    .additional-info-row,
    .education-profile-row {
        flex-direction: column;
        gap: 10px;
    }
    
    .progress-steps {
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .step-indicator {
        min-width: 80px;
    }
    
    .step-label {
        font-size: 10px;
    }
}
</style>

<script>
// JavaScript functions for form functionality
let currentStep = 1;
const totalSteps = 7;

// Map step numbers to header titles (match Add New Senior)
const stepTitles = {
    1: 'I - IDENTIFYING INFORMATION',
    2: 'II - FAMILY COMPOSITION',
    3: 'III - EDUCATION / HR PROFILE',
    4: 'IV - DEPENDENCY PROFILE',
    5: 'V - ECONOMIC PROFILE',
    6: 'VI - HEALTH PROFILE',
    7: 'VII - PHOTO IDENTIFICATION'
};

function setHeaderTitle(step) {
    const titleEl = document.querySelector('.form-header .form-title');
    if (titleEl) {
        titleEl.textContent = stepTitles[step] || 'EDIT SENIOR CITIZEN INFORMATION';
    }
}

function showStep(step) {
    // Hide all steps
    document.querySelectorAll('.form-step').forEach(stepEl => {
        stepEl.classList.remove('active');
    });
    
    // Show current step
    const currentStepEl = document.getElementById(`step${step}`);
    if (currentStepEl) {
        currentStepEl.classList.add('active');
    }
    
    // Update progress indicators to match add_new behavior
    updateProgressIndicators(step);
    
    // Update navigation buttons and header title
    updateNavigationButtons(step);
    setHeaderTitle(step);
}

function updateProgressIndicators(step) {
    for (let i = 1; i <= totalSteps; i++) {
        const indicator = document.querySelector(`.step-indicator[data-step="${i}"]`);
        if (indicator) {
            if (i < step) {
                indicator.classList.add('completed');
                indicator.classList.remove('active');
            } else if (i === step) {
                indicator.classList.add('active');
                indicator.classList.remove('completed');
            } else {
                indicator.classList.remove('active', 'completed');
            }
        }
    }
}
function changeStep(direction) {
    const newStep = currentStep + direction;
    
    if (newStep >= 1 && newStep <= totalSteps) {
        // Validate current step before moving
        if (direction > 0 && !validateCurrentStep()) {
            return;
        }
        
        currentStep = newStep;
        showStep(currentStep);
    }
}

function goToStep(step) {
    if (step >= 1 && step <= totalSteps) {
        currentStep = step;
        showStep(currentStep);
    }
}

function updateNavigationButtons(step) {
    const prevBtn = document.querySelector(`#prevBtn${step}`);
    const nextBtn = document.querySelector(`#nextBtn${step}`);
    
    if (prevBtn) {
        prevBtn.style.display = step === 1 ? 'none' : 'inline-block';
    }
    
    if (nextBtn) {
        if (step === totalSteps) {
            nextBtn.textContent = 'Update Information';
            nextBtn.onclick = () => showUpdateConfirmationModal();
        } else {
            nextBtn.textContent = 'Next';
            nextBtn.onclick = () => changeStep(1);
        }
    }
}

function validateCurrentStep() {
    const currentStepEl = document.getElementById(`step${currentStep}`);
    if (!currentStepEl) return true;
    
    const requiredFields = currentStepEl.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = '#dc3545';
            field.style.borderWidth = '2px';
            isValid = false;
        } else {
            field.style.borderColor = '';
            field.style.borderWidth = '';
        }
    });
    
    if (!isValid) {
        alert('Please fill in all required fields before proceeding.');
    }
    
    return isValid;
}

function showUpdateConfirmationModal() {
    if (!validateCurrentStep()) {
        return;
    }

    // Ensure certification passes Laravel 'accepted' rule
    const cert = document.getElementById('certificationField');
    if (cert) cert.value = 'on';

    // Use the system-wide confirmation modal for consistency
    const seniorName = '{{ $senior->first_name }} {{ $senior->last_name }}';
    showConfirmModal(
        'Update Senior Profile',
        `Are you sure you want to update ${seniorName}'s senior citizen information? This will save all changes made to the form.`,
        '{{ route("edit_senior.update", $senior->id) }}',
        'PUT'
    );
}

// Numeric validation functions
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

function validatePaste(evt) {
    let clipboardData = evt.clipboardData || window.clipboardData;
    let pastedData = clipboardData.getData('Text');
    
    if (!/^\d*$/.test(pastedData)) {
        return false;
    }
    return true;
}

// Typing instructions modal
function showTypingInstructions() {
    alert('To type Ñ or ñ:\n\n• Hold Alt and type 164 for Ñ\n• Hold Alt and type 165 for ñ\n• Or copy and paste: Ñ ñ');
}

// OCR functionality placeholder
function handleOcrScan() {
    alert('OCR functionality will be implemented here.');
}

// Dynamic table row management
function addChildRow() {
    const tbody = document.getElementById('childrenTableBody');
    const rowCount = tbody.querySelectorAll('tr').length + 1;
    
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td><input type="text" name="child_name_${rowCount}" class="form-control form-control-sm border-0" placeholder="Child Name"></td>
        <td><input type="text" name="child_occupation_${rowCount}" class="form-control form-control-sm border-0" placeholder="Occupation"></td>
        <td><input type="text" name="child_income_${rowCount}" class="form-control form-control-sm border-0" placeholder="Income" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
        <td><input type="text" name="child_age_${rowCount}" class="form-control form-control-sm border-0" placeholder="Age" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
        <td>
            <select name="child_working_${rowCount}" class="form-select form-select-sm border-0">
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
    `;
    
    tbody.appendChild(newRow);
}

function addDependentRow() {
    const tbody = document.getElementById('dependentsTableBody');
    const rowCount = tbody.querySelectorAll('tr').length + 1;
    
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td><input type="text" name="dependent_name_${rowCount}" class="form-control form-control-sm border-0" placeholder="Name of Dependent"></td>
        <td><input type="text" name="dependent_occupation_${rowCount}" class="form-control form-control-sm border-0" placeholder="Occupation of Dependent"></td>
        <td><input type="text" name="dependent_income_${rowCount}" class="form-control form-control-sm border-0" placeholder="Income" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
        <td><input type="text" name="dependent_age_${rowCount}" class="form-control form-control-sm border-0" placeholder="Age" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
        <td>
            <select name="dependent_working_${rowCount}" class="form-select form-select-sm border-0">
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
    `;
    
    tbody.appendChild(newRow);
}

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    showStep(1);
    
    // Add click handlers to step indicators
    document.querySelectorAll('.form-header .step-indicator').forEach(indicator => {
        indicator.addEventListener('click', function() {
            const step = parseInt(this.getAttribute('data-step'));
            goToStep(step);
        });
    });
    
    // Add event handlers for dynamic table buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('#addChildBtn')) {
            addChildRow();
        }
        
        if (e.target.closest('#addDependentBtn')) {
            addDependentRow();
        }
        
        if (e.target.closest('.delete-child-row')) {
            e.target.closest('tr').remove();
        }
        
        if (e.target.closest('.delete-dependent-row')) {
            e.target.closest('tr').remove();
        }
    });
    
    // Date of birth validation
    const dobField = document.getElementById('date_of_birth');
    if (dobField) {
        const today = new Date();
        const maxDate = new Date(today.getFullYear() - 60, today.getMonth(), today.getDate());
        const maxDateString = maxDate.toISOString().split('T')[0];
        dobField.setAttribute('max', maxDateString);
        
        const minDate = new Date(today.getFullYear() - 120, today.getMonth(), today.getDate());
        const minDateString = minDate.toISOString().split('T')[0];
        dobField.setAttribute('min', minDateString);
    }
    
    // Remove red border when user starts typing
    document.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
        field.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.style.borderColor = '';
                this.style.borderWidth = '';
            }
        });
        
        if (field.tagName === 'SELECT') {
            field.addEventListener('change', function() {
                if (this.value.trim() !== '') {
                    this.style.borderColor = '';
                    this.style.borderWidth = '';
                }
            });
        }
    });
});
</script>