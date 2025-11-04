<x-sidebar>
  <x-header title="ADD NEW SENIOR" icon="fas fa-user-plus">
    @include('message.popup_message')
    <div class="main">
        <div class="form">
    

                <div class="form-header">
                    <h2 class="form-title">I - IDENTIFYING INFORMATION</h2>
                    <div class="progress-container">
                        <div class="progress-steps">

                            <div class="step-indicator" data-step="1">1</div>
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
            <form method="POST" action="{{ route('seniors.store') }}" enctype="multipart/form-data" id="addSeniorForm">
            @csrf
            <!-- Hidden certification field to satisfy backend 'accepted' validation -->
            <input type="hidden" name="certification" id="certificationField" value="">

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
                                <input type="text" name="last_name" class="form-control form-control-sm" placeholder="Last Name" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">First Name <span style="color: red;">*</span></label>
                                <input type="text" name="first_name" class="form-control form-control-sm" placeholder="First Name" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control form-control-sm" placeholder="Middle Name">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Name Extension</label>
                                <select name="name_extension" class="form-select form-select-sm">
                                    <option value="">Extension</option>
                                    <option value="Jr.">Jr.</option>
                                    <option value="Sr.">Sr.</option>
                                    <option value="II">II</option>
                                    <option value="III">III</option>
                                    <option value="IV">IV</option>
                                    <option value="V">V</option>
                                    <option value="VI">VI</option>
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
                                   <option value="Region I">Region I - Ilocos Region</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Province <span style="color: red;">*</span></label>
                                <select name="province" class="form-select form-select-sm" required>
                                    <option value="Pangasinan" selected>Pangasinan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">City <span style="color: red;">*</span></label>
                                <select name="city" class="form-select form-select-sm" required>
                                    <option value="Lingayen" selected>Lingayen</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Barangay <span style="color: red;">*</span></label>
                                <select name="barangay" class="form-select form-select-sm" required>
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
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">House No./Zone/Purok/Sitio</label>
                            <input type="text" name="residence" class="form-control form-control-sm" placeholder="House No./Zone/Purok/Sitio">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Street</label>
                                <input type="text" name="street" class="form-control form-control-sm" placeholder="Street">
                            </div>
                        </div>
                    </div>


                    <div class="mb-4">
                       <div class="col-md-3">
                             <label class="form-label fw-bold">3. Date of Birth <span style="color: red;">*</span>  <small class="text-muted" style="font-size: 11px;">Must be 60 years or older</small></label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control form-control-sm" required>
                           
                        </div>
                    </div>


                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label small">4. Place of Birth <span style="color: red;">*</span></label>
                            <input type="text" name="birth_place" class="form-control form-control-sm" placeholder="Place of Birth" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">5. Marital Status <span style="color: red;">*</span></label>
                            <select name="marital_status" class="form-select form-select-sm" required>
                                <option value="">Select Marital Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Separated">Separated</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">6. Gender <span style="color: red;">*</span></label>
                            <select name="sex" class="form-select form-select-sm" required>
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">7. Contact Number <span style="color: red;">*</span></label>
                            <input type="tel" name="contact_number" class="form-control form-control-sm" placeholder="Contact Number" required onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                        </div>
                    </div>


                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label small">8. Email Address</label>
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="Email Address">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">9. Religion</label>
                            <select name="religion" class="form-select form-select-sm">
                                <option value="">Select Religion</option>
                                <option value="Roman Catholic">Roman Catholic</option>
                                <option value="Iglesia ni Cristo">Iglesia ni Cristo</option>
                                <option value="Evangelical">Evangelical</option>
                                <option value="Baptist">Baptist</option>
                                <option value="Methodist">Methodist</option>
                                <option value="Seventh Day Adventist">Seventh Day Adventist</option>
                                <option value="Islam">Islam</option>
                                <option value="Buddhism">Buddhism</option>
                                <option value="Jehovah's Witness">Jehovah's Witness</option>
                                <option value="Born Again Christian">Born Again Christian</option>
                                <option value="Aglipayan">Aglipayan</option>
                                <option value="None">None</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">10. Ethnic Origin</label>
                            <input type="text" name="ethnic_origin" class="form-control form-control-sm" placeholder="Ethnic Origin">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">11. Language Spoken</label>
                            <input type="text" name="language" class="form-control form-control-sm" placeholder="Language Spoken">
                        </div>
                    </div>


                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small">12. OSCA ID No. <span style="color: red;">*</span></label>
                            <input type="text" name="osca_id" class="form-control form-control-sm" placeholder="OSCA ID Number" required onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">13. GSIS/SSS No.</label>
                            <input type="text" name="gsis_sss" class="form-control form-control-sm" placeholder="GSIS/SSS Number" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">14. TIN</label>
                            <input type="text" name="tin" class="form-control form-control-sm" placeholder="Tax Identification Number" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                        </div>
                    </div>


                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small">15. PhilHealth No.</label>
                            <input type="text" name="philhealth" class="form-control form-control-sm" placeholder="PhilHealth Number" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">16. SC Association ID No.</label>
                            <input type="text" name="sc_association" class="form-control form-control-sm" placeholder="Senior Citizen Association ID" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">17. Other Gov't ID No.</label>
                            <input type="text" name="other_govt_id" class="form-control form-control-sm" placeholder="Other Government ID" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small">18. Capability to Travel</label>
                            <select name="can_travel" class="form-select form-select-sm">
                                <option value="">Select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">19. Service/Business/Employment</label>
                            <input type="text" name="employment" class="form-control form-control-sm" placeholder="Specify">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">20. Has Pension<span style="color: red;">*</span></label>
                            <select name="has_pension" class="form-select form-select-sm" required>
                                <option value="">Select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                
                <div class="step-navigation">
                    <button type="button" class="btn btn-secondary" id="prevBtn" onclick="changeStep(-1)" style="display: none;">Previous</button>
                    <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">Next</button>
                </div>
                </div>
                

                <div class="form-step" id="step2">
                <div class="mb-4">

                    <div class="mb-3">
                       <label class="form-label small">21. Name of Spouse</label>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <input type="text" name="spouse_last_name" class="form-control form-control-sm" placeholder="Last Name">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="spouse_first_name" class="form-control form-control-sm" placeholder="First Name">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="spouse_middle_name" class="form-control form-control-sm" placeholder="Middle Name">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="spouse_extension" class="form-control form-control-sm" placeholder="Extension(Jr, Sr)">
                            </div>
                        </div>
                    </div>


                    <div class="mb-3">
                        <label class="form-label small">22. Father's Name</label>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <input type="text" name="father_last_name" class="form-control form-control-sm" placeholder="Last Name">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="father_first_name" class="form-control form-control-sm" placeholder="First Name">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="father_middle_name" class="form-control form-control-sm" placeholder="Middle Name">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="father_extension" class="form-control form-control-sm" placeholder="Extension(Jr, Sr)">
                            </div>
                        </div>
                    </div>


                    <div class="mb-3">
                        <label class="form-label small">23. Mother's Maiden Name</label>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <input type="text" name="mother_last_name" class="form-control form-control-sm" placeholder="Last Name">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="mother_first_name" class="form-control form-control-sm" placeholder="First Name">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="mother_middle_name" class="form-control form-control-sm" placeholder="Middle Name">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="mother_extension" class="form-control form-control-sm" placeholder="Extension">
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
                                    @for($i = 1; $i <= 2; $i++)
                                    <tr>
                                        <td><input type="text" name="child_name_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Last Name, First Name, Middle Name"></td>
                                        <td><input type="text" name="child_occupation_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Occupation"></td>
                                        <td><input type="text" name="child_income_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Income" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                                        <td><input type="text" name="child_age_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Age" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                                        <td><select name="child_working_{{ $i }}" class="form-select form-select-sm border-0">
                                            <option value="">Is working?</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select></td>
                                        <td class="text-center">
                                            <button type="button" class="table-action-delete delete-child-row" title="Delete Row">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endfor
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
                                    @for($i = 1; $i <= 2; $i++)
                                    <tr>
                                        <td><input type="text" name="dependent_name_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Name of Dependent"></td>
                                        <td><input type="text" name="dependent_occupation_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Occupation of Dependent"></td>
                                        <td><input type="text" name="dependent_income_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Income" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                                        <td><input type="text" name="dependent_age_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Age" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                                        <td><select name="dependent_working_{{ $i }}" class="form-select form-select-sm border-0">
                                            <option value="">Is Working?</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select></td>
                                        <td class="text-center">
                                            <button type="button" class="table-action-delete delete-dependent-row" title="Delete Row">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endfor
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
                

                <div class="form-step" id="step3">
                <div class="mb-4">

                    <div class="row">
                        <!-- Left Column - Educational Attainment -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="input-label">26. Highest Educational Attainment</label>
                                <div class="d-flex flex-column gap-1 mt-2">
                                    <label><input type="radio" name="education_level" value="Not Attended School"> Not Attended School</label>
                                    <label><input type="radio" name="education_level" value="Elementary Level"> Elementary Level</label>
                                    <label><input type="radio" name="education_level" value="Elementary Graduate"> Elementary Graduate</label>
                                    <label><input type="radio" name="education_level" value="Highschool Level"> Highschool Level</label>
                                    <label><input type="radio" name="education_level" value="Highschool Graduate"> Highschool Graduate</label>
                                    <label><input type="radio" name="education_level" value="Vocational"> Vocational</label>
                                    <label><input type="radio" name="education_level" value="College Level"> College Level</label>
                                    <label><input type="radio" name="education_level" value="College Graduate"> College Graduate</label>
                                    <label><input type="radio" name="education_level" value="Post Graduate"> Post Graduate</label>
                                    <div class="mt-2">
                                        <label>Others, Specify</label>
                                        <input type="text" name="education_others_specify" class="form-control mt-1" placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Middle Column - Technical Skills -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="input-label">27. Specialization / Technical Skills <em>(Check all applicable)</em></label>
                                <div class="d-flex flex-column gap-1 mt-2">
                                    <label><input type="checkbox" name="skills[]" value="Medical"> Medical</label>
                                    <label><input type="checkbox" name="skills[]" value="Dental"> Dental</label>
                                    <label><input type="checkbox" name="skills[]" value="Fishing"> Fishing</label>
                                    <label><input type="checkbox" name="skills[]" value="Engineering"> Engineering</label>
                                    <label><input type="checkbox" name="skills[]" value="Barber"> Barber</label>
                                    <label><input type="checkbox" name="skills[]" value="Evangelization"> Evangelization</label>
                                    <label><input type="checkbox" name="skills[]" value="Midwifery"> Midwifery</label>
                                    <label><input type="checkbox" name="skills[]" value="Teaching"> Teaching</label>
                                    <label><input type="checkbox" name="skills[]" value="Counselling"> Counselling</label>
                                    <label><input type="checkbox" name="skills[]" value="Cooking"> Cooking</label>
                                    <label><input type="checkbox" name="skills[]" value="Carpenter"> Carpenter</label>
                                    <label><input type="checkbox" name="skills[]" value="Mason"> Mason</label>
                                    <label><input type="checkbox" name="skills[]" value="Tailor"> Tailor</label>
                                    <label><input type="checkbox" name="skills[]" value="Legal Services"> Legal Services</label>
                                    <label><input type="checkbox" name="skills[]" value="Farming"> Farming</label>
                                    <label><input type="checkbox" name="skills[]" value="Arts"> Arts</label>
                                    <label><input type="checkbox" name="skills[]" value="Plumber"> Plumber</label>
                                    <label><input type="checkbox" name="skills[]" value="Shoemaker"> Shoemaker</label>
                                    <label><input type="checkbox" name="skills[]" value="Chef/Cook"> Chef/Cook</label>
                                    <label><input type="checkbox" name="skills[]" value="Information Technology"> Information Technology</label>
                                    <div class="mt-2">
                                        <label>Others, Specify</label>
                                        <input type="text" name="skills_others_specify" class="form-control mt-1" placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Shared Skills and Community Activities -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="input-label">28. Shared Skills <em>(Community Service)</em></label>
                                <textarea name="shared_skills" class="form-control mt-2" placeholder="type skills here separated by comma" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="input-label">29. Involvement in Community Activities <em>(Check all applicable)</em></label>
                                <div class="d-flex flex-column gap-1 mt-2">
                                    <label><input type="checkbox" name="community_activities[]" value="Medical"> Medical</label>
                                    <label><input type="checkbox" name="community_activities[]" value="Resource Volunteer"> Resource Volunteer</label>
                                    <label><input type="checkbox" name="community_activities[]" value="Community Beautification"> Community Beautification</label>
                                    <label><input type="checkbox" name="community_activities[]" value="Community / Organization Leader"> Community / Organization Leader</label>
                                    <label><input type="checkbox" name="community_activities[]" value="Dental"> Dental</label>
                                    <label><input type="checkbox" name="community_activities[]" value="Friendly Visits"> Friendly Visits</label>
                                    <label><input type="checkbox" name="community_activities[]" value="Neighborhood Support Services"> Neighborhood Support Services</label>
                                    <label><input type="checkbox" name="community_activities[]" value="Legal Services"> Legal Services</label>
                                    <label><input type="checkbox" name="community_activities[]" value="Religious"> Religious</label>
                                    <label><input type="checkbox" name="community_activities[]" value="Counselling / Referral"> Counselling / Referral</label>
                                    <label><input type="checkbox" name="community_activities[]" value="Sponsorship"> Sponsorship</label>
                                    <div class="mt-2">
                                        <label>Others, Specify</label>
                                        <input type="text" name="community_activities_others_specify" class="form-control mt-1" placeholder="">
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
                

                <div class="form-step" id="step4">
                <div class="form-section dependency-profile-section">

                    <div class="row g-4 mb-4">

                        <div class="col-md-6">
                            <label class="input-label">30. Living Condition</label>
                            <div class="mt-2">
                                <div class="d-flex flex-column gap-1">
                                    <label><input type="radio" name="living_condition_primary" value="Living Alone" onchange="toggleLivingWithOptions()"> Living Alone</label>
                                    <label><input type="radio" name="living_condition_primary" value="Living with" onchange="toggleLivingWithOptions()"> Living with</label>
                                    <div id="living_with_options" class="mt-2 ms-4">
                                        <label><input type="checkbox" name="living_with[]" value="Grand Children"> Grand Children</label>
                                        <label><input type="checkbox" name="living_with[]" value="Common Law Spouse"> Common Law Spouse</label>
                                        <label><input type="checkbox" name="living_with[]" value="Spouse"> Spouse</label>
                                        <label><input type="checkbox" name="living_with[]" value="In-laws"> In-laws</label>
                                        <label><input type="checkbox" name="living_with[]" value="Care Institution"> Care Institution</label>
                                        <label><input type="checkbox" name="living_with[]" value="Children"> Children</label>
                                        <label><input type="checkbox" name="living_with[]" value="Relatives"> Relatives</label>
                                        <label><input type="checkbox" name="living_with[]" value="Friends"> Friends</label>
                                            
                                    <div class="mt-2">
                                        <label for="living_others" class="mb-0">Others</label>
                                        <input type="text" name="living_with_others_specify" class="form-control mt-1" placeholder="">
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                           <label class="input-label">31. Household Condition</label>
                            <div class="mt-2">
                                <label><input type="checkbox" name="household_condition[]" value="No privacy"> No privacy</label>
                                <label><input type="checkbox" name="household_condition[]" value="Overcrowded in home"> Overcrowded in home</label>
                                <label><input type="checkbox" name="household_condition[]" value="Informal Settler"> Informal Settler</label>
                                <label><input type="checkbox" name="household_condition[]" value="No permanent house"> No permanent house</label>
                                <label><input type="checkbox" name="household_condition[]" value="High cost of rent"> High cost of rent</label>
                                <label><input type="checkbox" name="household_condition[]" value="Longing for independent living quiet atmosphere"> Longing for independent living quiet atmosphere</label>
                               <div class="mt-2">
                                        <label>Others, Specify</label>
                                        <input type="text" name="household_condition_others_specify" class="form-control mt-1" placeholder="">
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Step Navigation Buttons -->
                <div class="step-navigation">
                    <button type="button" class="btn btn-secondary" id="prevBtn4" onclick="changeStep(-1)">Previous</button>
                    <button type="button" class="btn btn-primary" id="nextBtn4" onclick="changeStep(1)">Next</button>
                </div>
                </div>
                

                <div class="form-step" id="step5">
                <div class="form-section dependency-profile-section">
                    <div class="row g-4 mb-4">
                        <!-- Left Column - Source of Income -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="input-label">32. Source of Income and Assistance</label>
                                <div class="d-flex flex-column gap-1 mt-2">
                                    <label><input type="checkbox" name="source_of_income[]" value="Own earnings, salary / wages"> Own earnings, salary / wages</label>
                                    <label><input type="checkbox" name="source_of_income[]" value="Own Pension"> Own Pension</label>
                                    <label><input type="checkbox" name="source_of_income[]" value="Stocks / Dividends"> Stocks / Dividends</label>
                                    <label><input type="checkbox" name="source_of_income[]" value="Dependent on children / relatives"> Dependent on children / relatives</label>
                                    <label><input type="checkbox" name="source_of_income[]" value="Spouse's salary"> Spouse's salary</label>
                                    <label><input type="checkbox" name="source_of_income[]" value="Spouse Pension"> Spouse Pension</label>
                                    <label><input type="checkbox" name="source_of_income[]" value="Insurance"> Insurance</label>
                                    <label><input type="checkbox" name="source_of_income[]" value="Rental / Sharecorp"> Rental / Sharecrop</label>
                                    <label><input type="checkbox" name="source_of_income[]" value="Savings"> Savings</label>
                                    <label><input type="checkbox" name="source_of_income[]" value="Livestock / orchard / farm"> Livestock / orchard / farm</label>
                                    <label><input type="checkbox" name="source_of_income[]" value="Fishing"> Fishing</label>
                                    <div class="mt-2">
                                        <label>Others, Specify</label>
                                        <input type="text" name="source_of_income_others" class="form-control mt-1" placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Middle Column - Assets -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="input-label">33. Assets: Real and Immovable Properties</label>
                                <div class="d-flex flex-column gap-1 mt-2">
                                    <label><input type="checkbox" name="real_assets[]" value="House"> House</label>
                                    <label><input type="checkbox" name="real_assets[]" value="Lot / Farmland"> Lot / Farmland</label>
                                    <label><input type="checkbox" name="real_assets[]" value="House & Lot"> House & Lot</label>
                                    <label><input type="checkbox" name="real_assets[]" value="Commercial Building"> Commercial Building</label>
                                    <label><input type="checkbox" name="real_assets[]" value="Fishpond / resort"> Fishpond / resort</label>
                                    <div class="mt-2">
                                        <label>Others, Specify</label>
                                        <input type="text" name="assets_real_and_immovable_others" class="form-control mt-1" placeholder="">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="input-label">34. Assets: Personal and Movable Properties</label>
                                <div class="d-flex flex-column gap-1 mt-2">
                                    <label><input type="checkbox" name="personal_assets[]" value="Automobile"> Automobile</label>
                                    <label><input type="checkbox" name="personal_assets[]" value="Personal Computer"> Personal Computer</label>
                                    <label><input type="checkbox" name="personal_assets[]" value="Boats"> Boats</label>
                                    <label><input type="checkbox" name="personal_assets[]" value="Heavy Equipment"> Heavy Equipment</label>
                                    <label><input type="checkbox" name="personal_assets[]" value="Laptops"> Laptops</label>
                                    <label><input type="checkbox" name="personal_assets[]" value="Drones"> Drones</label>
                                    <label><input type="checkbox" name="personal_assets[]" value="Motorcycle"> Motorcycle</label>
                                    <label><input type="checkbox" name="personal_assets[]" value="Mobile Phones"> Mobile Phones</label>
                                    <div class="mt-2">
                                        <label>Others, Specify</label>
                                        <input type="text" name="personal_assets_others" class="form-control mt-1" placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Income and Problems -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="input-label">35. Monthly Income <em>(in Philippine Peso)</em></label>
                                <div class="mt-2">
                                    <input type="text" name="monthly_income" class="form-control form-control-sm" placeholder="Enter monthly income amount" value="{{ old('monthly_income', 0) }}" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)">
                                    <small class="text-muted">Please enter the exact monthly income amount (numbers only)</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="input-label">36. Problems / Needs Commonly Encountered</label>
                                <div class="d-flex flex-column gap-1 mt-2">
                                    <label><input type="checkbox" name="problems_needs[]" value="Lack of income / resources"> Lack of income / resources</label>
                                    <label><input type="checkbox" name="problems_needs[]" value="Loss of income / resources"> Loss of income / resources</label>
                                    <label><input type="checkbox" name="problems_needs[]" value="Skills / capability training"> Skills / capability training</label>
                                    <label><input type="checkbox" name="problems_needs[]" value="Livelihood Opportunities"> Livelihood Opportunities</label>
                                    <div class="mt-2">
                                        <label>Others, Specify</label>
                                        <input type="text" name="problems_needs_others" class="form-control mt-1" placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Step Navigation Buttons -->
                <div class="step-navigation">
                    <button type="button" class="btn btn-secondary" id="prevBtn5" onclick="changeStep(-1)">Previous</button>
                    <button type="button" class="btn btn-primary" id="nextBtn5" onclick="changeStep(1)">Next</button>
                </div>
                </div>
                

                <div class="form-step" id="step6">
                    <div class="form-section dependency-profile-section">

                    <div class="row g-4 mb-4">
                        <!-- Left Column: Medical and Dental Concerns -->
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="input-label">37. Medical Concern</label>
                                <div class="mt-3">
                                    <label class="field-label">Blood Type</label>
                                    <select name="blood_type" class="form-control mb-3">
                                        <option value="">Select Blood Type</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                    </select>
                                    
                                    <label class="field-label">Physical Disability</label>
                                    <input type="text" name="physical_disability" class="form-control mb-3" placeholder="Physical Disability type here">

                                    <label class="field-label">Health Problems / Ailments</label>
                                    <div class="checkbox-group">
                                        <label class="checkbox-item"><input type="checkbox" name="health_problems[]" value="Hypertension"> Hypertension</label>
                                        <label class="checkbox-item"><input type="checkbox" name="health_problems[]" value="Arthritis / Gout"> Arthritis / Gout</label>
                                        <label class="checkbox-item"><input type="checkbox" name="health_problems[]" value="Coronary Heart Disease"> Coronary Heart Disease</label>
                                        <label class="checkbox-item"><input type="checkbox" name="health_problems[]" value="Diabetes"> Diabetes</label>
                                        <label class="checkbox-item"><input type="checkbox" name="health_problems[]" value="Chronic Kidney Disease"> Chronic Kidney Disease</label>
                                        <label class="checkbox-item"><input type="checkbox" name="health_problems[]" value="Alzheimer's / Dementia"> Alzheimer's / Dementia</label>
                                        <label class="checkbox-item"><input type="checkbox" name="health_problems[]" value="Chronic Obstructive Pulmonary Disease"> Chronic Obstructive Pulmonary Disease</label>
                                        <div class="others-input-group">
                                            <label class="checkbox-item">
                                                <input type="checkbox" name="health_problems[]" value="Others" onchange="toggleHealthProblemsOthersInput()"> Others, Specify
                                            </label>
                                            <input type="text" name="health_problems_others" id="health_problems_others_input" placeholder="Specify" class="form-control mt-2">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="input-label">38. Dental Concern</label>
                                <div class="checkbox-group mt-3">
                                    <label class="checkbox-item"><input type="checkbox" name="dental_concern[]" value="Needs Dental Care"> Needs Dental Care</label>
                                    <div class="others-input-group">
                                        <label class="checkbox-item">
                                            <input type="checkbox" name="dental_concern[]" value="Others" onchange="toggleDentalConcernOthersInput()"> Others, Specify
                                        </label>
                                        <input type="text" name="dental_concern_others" id="dental_concern_others_input" placeholder="Specify" class="form-control mt-2">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Middle Column: Visual, Hearing, and Social/Emotional -->
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="input-label">39. Visual Concern</label>
                                <div class="checkbox-group mt-3">
                                    <label class="checkbox-item"><input type="checkbox" name="visual_concern[]" value="Eye impairment"> Eye impairment</label>
                                    <label class="checkbox-item"><input type="checkbox" name="visual_concern[]" value="Needs eye care"> Needs eye care</label>
                                    <div class="others-input-group">
                                        <label class="checkbox-item">
                                            <input type="checkbox" name="visual_concern[]" value="Others" onchange="toggleVisualConcernOthersInput()"> Others, Specify
                                        </label>
                                        <input type="text" name="visual_concern_others" id="visual_concern_others_input" placeholder="Specify" class="form-control mt-2">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="input-label">40. Hearing</label>
                                <div class="checkbox-group mt-3">
                                    <label class="checkbox-item"><input type="checkbox" name="hearing_condition[]" value="Aural impairment"> Aural impairment</label>
                                    <div class="others-input-group">
                                        <label class="checkbox-item">
                                            <input type="checkbox" name="hearing_condition[]" value="Others" onchange="toggleHearingConditionOthersInput()"> Others, Specify
                                        </label>
                                        <input type="text" name="hearing_condition_others" id="hearing_condition_others_input" placeholder="Specify" class="form-control mt-2">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="input-label">41. Social / Emotional</label>
                                <div class="checkbox-group mt-3">
                                    <label class="checkbox-item"><input type="checkbox" name="social_emotional[]" value="Feeling neglect / rejection"> Feeling neglect / rejection</label>
                                    <label class="checkbox-item"><input type="checkbox" name="social_emotional[]" value="Feeling helplessness / worthlessness"> Feeling helplessness / worthlessness</label>
                                    <label class="checkbox-item"><input type="checkbox" name="social_emotional[]" value="Feeling loneliness / isolate"> Feeling loneliness / isolate</label>
                                    <label class="checkbox-item"><input type="checkbox" name="social_emotional[]" value="Lack leisure / recreational activities"> Lack leisure / recreational activities</label>
                                    <label class="checkbox-item"><input type="checkbox" name="social_emotional[]" value="Lack SC friendly environment"> Lack SC friendly environment</label>
                                    <div class="others-input-group">
                                        <label class="checkbox-item">
                                            <input type="checkbox" name="social_emotional[]" value="Others" onchange="toggleSocialEmotionalOthersInput()"> Others, Specify
                                        </label>
                                        <input type="text" name="social_emotional_others" id="social_emotional_others_input" placeholder="Specify" class="form-control mt-2">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Area/Difficulty, Medicines, and Check-up -->
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label class="input-label">42. Area / Difficulty</label>
                                <div class="checkbox-group mt-3">
                                    <label class="checkbox-item"><input type="checkbox" name="area_difficulty[]" value="High Cost of medicines"> High Cost of medicines</label>
                                    <label class="checkbox-item"><input type="checkbox" name="area_difficulty[]" value="Lack of medicines"> Lack of medicines</label>
                                    <label class="checkbox-item"><input type="checkbox" name="area_difficulty[]" value="Lack of medical attention"> Lack of medical attention</label>
                                    <div class="others-input-group">
                                        <label class="checkbox-item">
                                            <input type="checkbox" name="area_difficulty[]" value="Others" onchange="toggleAreaDifficultyOthersInput()"> Others, Specify
                                        </label>
                                        <input type="text" name="area_difficulty_others" id="area_difficulty_others_input" placeholder="Specify" class="form-control mt-2">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="input-label">43. List of Medicines for Maintenance</label>
                                <em class="field-note d-block mb-2">(Type all your maintenance medicines. Example: Amlodipine 10mg, Losartan 50mg, etc.)</em>
                                <textarea name="maintenance_medicines" class="form-control" rows="4" placeholder="List your maintenance medicines here..."></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="input-label">44. Do you have a scheduled medical/physical check-up?</label>
                                <select name="scheduled_checkup" id="scheduled_checkup" class="form-control mt-3" onchange="toggleCheckupFrequency()">
                                    <option value="">Select</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="input-label">45. If Yes, when is it done? <span id="checkup_frequency_required" class="text-danger" style="display: none;">*</span></label>
                                <select name="checkup_frequency" id="checkup_frequency" class="form-control mt-3">
                                    <option value="">Select</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Quarterly">Quarterly</option>
                                    <option value="Semi-annually">Semi-annually</option>
                                    <option value="Annually">Annually</option>
                                    <option value="As needed">As needed</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="input-label">46. Status <span style="color: red;">*</span></label>
                                <select name="status" class="form-control mt-3" required>
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="deceased">Deceased</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                
                <!-- Step Navigation Buttons -->
                <div class="step-navigation">
                    <button type="button" class="btn btn-secondary" id="prevBtn6" onclick="changeStep(-1)">Previous</button>
                    <button type="button" class="btn btn-primary" id="nextBtn6" onclick="changeStep(1)">Next</button>
                </div>
                </div>
                
                <!-- Step 7: Image Upload -->
                <div class="form-step" id="step7">
                    <div class="form-section">
                        
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <div class="photo-upload-section" style="display: flex; justify-content: center; align-items: center;">
                                        <x-photo-upload id="senior_photo" name="photo" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step Navigation Buttons -->
                        <div class="step-navigation">
                            <button type="button" class="btn btn-secondary" id="prevBtn7" onclick="changeStep(-1)">Previous</button>
                            <button type="button" class="btn btn-success" id="submitBtn7" onclick="showCertificationModal()">Submit Form</button>
                        </div>
                    </div>
                </div>

                <div class="submit-button-container" style="display:none;">
                    <button type="submit" class="submit-button"><i class="fas fa-user-plus"></i> ADD NEW SENIOR</button>
                </div>
            </form>
            </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="typingModal" tabindex="-1" aria-labelledby="typingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="typingModalLabel">How do you type Ñ/ñ?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                
                

                <div class="instruction-box">
                    <h4 class="instruction-title">Using your Computer or Laptop</h4>
                    <p class="instruction-text">If your keyboard has a numeric keypad, you can type Ñ/ñ by following these steps:</p>
                    <ol class="instruction-list">
                        <li>Enable the numeric keypad by turning on the Num lock key.</li>
                        <li>Hold the Alt key then type 164 on the numeric keypad to create a lowercase ñ. For the uppercase Ñ, hold the Alt key then type 165.</li>
                    </ol>
                    <p class="instruction-note">Note that this would only work if your computer / laptop has a numeric keypad located on the right side of the keyboard.</p>
                    
                    <div class="shortcut-box">
                        <p class="shortcut-title">To easily remember, here are the keyboard shortcuts:</p>
                        <ul class="shortcut-list">
                            <li>ñ - Alt + 164</li>
                            <li>Ñ - Alt + 165</li>
                        </ul>
                    </div>
                    <div class="shortcut-box">
                        <p class="shortcut-title">For more easily step, copy and paste me.</p>
                        <ul class="shortcut-list">
                             <li>Ñ</li>
                            <li>ñ</li>
                        </ul>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Certification Modal -->
    <div class="modal fade" id="certificationModal" tabindex="-1" aria-labelledby="certificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-primary" id="certificationModalLabel">Data Privacy Certification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="certification-content">
                        <h6 class="mb-3">Senior Citizen Registration Consent</h6>
                        <p class="certification-text">
                            This certifies that I have willingly given my personal consent and willfully participated in the provision of data and relevant information regarding my person, being part of the establishment of database of Senior Citizens.
                        </p>
                        <p class="certification-text">
                            I understand that my personal information will be used solely for the purpose of senior citizen services and benefits, and will be handled in accordance with data privacy laws.
                        </p>
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="certificationAgree" required>
                            <label class="form-check-label" for="certificationAgree">
                                <strong>I agree to the terms and conditions stated above</strong>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="finalSubmitBtn" onclick="finalSubmit()" disabled>Submit Registration</button>
                </div>
            </div>
        </div>
    </div>

    <style>
   
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
            border-radius: px;
        }

        /* ===== SECTION HEADERS ===== */
        .section-header {
            background: linear-gradient(135deg, #e31575 0%, #c01060 100%);
            color: white;
            padding: 14px 20px;
            font-weight: 700;
            font-size: 17px;
            margin: 20px 0 16px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(227, 21, 117, 0.2);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* ===== HEADER AND LOGO STYLES ===== */
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

        .logo-container {
            padding-left: 15px;
        }

        .osca-logo {
            max-height: 60px;
        }

        .bagong-pilipinas-logo {
            max-height: 80px;
        }

        .header-title-container {
            text-align: center;
            margin-bottom: 20px;
            margin-top: 20px;
        }

        .header-title-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title-center {
            text-align: center;
            flex-grow: 1;
        }

        .add-senior-title {
            font-size: 20px;
            font-weight: 800;
            margin-top: 10px;
        }

        .photo-upload-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .photo-upload-box {
            width: 170px;
            height: 170px;
            border: 1px solid #999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .photo-input {
            display: none;
        }

        .photo-label {
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

        /* ===== FORM SECTION STYLES ===== */
        .form-section-content {
            margin-bottom: 20px;
        }

        .field-group {
            margin-bottom: 16px;
        }

        .field-label {
            font-weight: 600;
            font-size: 15px;
            color: #2c3e50;
            margin-bottom: 8px;
            letter-spacing: 0.2px;
        }

        .input-label {
            font-size: 14px;
            font-weight: 600 !important;
            display: block !important;
            margin-bottom: 10px !important;
            color: #2c3e50 !important;
            letter-spacing: 0.3px !important;
        }

        .form-input {
            width: 100%;
            font-size: 12px;
        }

        .form-select {
            width: 100%;
            font-size: 12px;
        }

        /* ===== NAME FIELDS ===== */
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

        .typing-instructions {
            margin-top: 5px;
            font-size: 12px;
            color: #cc0000;
        }

        .typing-link {
            color: #cc0000;
            text-decoration: underline;
            cursor: pointer;
        }

        /* ===== ADDRESS FIELDS ===== */
        .address-fields-container {
            display: flex;
            gap: 10px;
            margin-bottom: 5px;
        }

        .address-field {
            flex: 1;
        }

        .address-detail-fields {
            display: flex;
            gap: 10px;
        }

        .address-detail-field {
            flex: 1;
        }

        /* ===== BIRTH DATE FIELDS ===== */
        .birth-date-field {
            flex: 1;
        }

        .birth-date-input {
            width: 24%;
        }

        /* ===== PERSONAL INFO FIELDS ===== */
        .personal-info-container {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .personal-info-field {
            flex: 1;
        }

        /* ===== CONTACT INFO FIELDS ===== */
        .contact-info-container {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .contact-info-field {
            flex: 1;
        }

        /* ===== ID NUMBERS FIELDS ===== */
        .id-numbers-container {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .id-field {
            flex: 1;
        }

        /* ===== BOOTSTRAP OVERRIDES FOR CONSISTENCY ===== */
        .form-control-sm {
            font-size: 14px;
            font-weight: 400;
        }

        .form-select-sm {
            font-size: 14px;
            font-weight: 400;
        }

        .form-label.small {
            font-size: 13px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }
        
        /* Required field indicator styling */
        .form-label.small:contains("*"),
        .form-label:contains("*"),
        .input-label:contains("*") {
            position: relative;
        }
        
        .required-field {
            color: #dc3545;
            font-weight: bold;
            font-size: 16px;
            margin-left: 2px;
        }
        
        .required-field-label {
            color: #dc3545;
            font-weight: bold;
        }

        .table-sm td {
            padding: 0.25rem;
            vertical-align: middle;
        }

        .table-sm th {
            font-size: 12px;
            font-weight: bold;
            padding: 0.5rem 0.25rem;
        }

        .table-bordered td input.form-control,
        .table-bordered td select.form-select {
            background: transparent;
        }

        /* ===== ENHANCED FORM CONTROLS WITH INNER SHADOW ===== */
        .form-section form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

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
        select.form-control, .form-select, .form-select-sm {
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

        select.form-control:focus, .form-select:focus, .form-select-sm:focus {
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23c01060' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
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

        /* ===== PROFILE SECTIONS ===== */
        .education-hr-section label:not(.input-label),
        .dependency-profile-section label:not(.input-label),
        .form-section .economic-profile-section label:not(.input-label) {
            font-weight: normal;
            font-size: 14px;
            color: #333;
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .education-hr-section input[type="radio"],
        .education-hr-section input[type="checkbox"],
        .dependency-profile-section input[type="radio"],
        .dependency-profile-section input[type="checkbox"],
        .form-section .economic-profile-section input[type="radio"],
        .form-section .economic-profile-section input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.1);
        }

        .education-hr-section input[type="text"],
        .dependency-profile-section input[type="text"],
        .economic-profile-section input[type="text"] {
            font-size: 14px;
            padding: 10px 14px;
            border: 2px solid #e31575;
            border-radius: 8px;
            font-weight: 400;
            line-height: 1.5;
            background-color: #ffffff;
            transition: all 0.3s ease;
            box-shadow: inset 0 2px 4px rgba(227, 21, 117, 0.1), inset 0 1px 2px rgba(227, 21, 117, 0.15);
        }

        .education-hr-section input[type="text"]:focus,
        .dependency-profile-section input[type="text"]:focus,
        .economic-profile-section input[type="text"]:focus {
            outline: none;
            border-color: #e31575;
            background-color: #fefefe;
            box-shadow: inset 0 3px 6px rgba(227, 21, 117, 0.15), inset 0 2px 4px rgba(227, 21, 117, 0.2), 0 0 0 3px rgba(227, 21, 117, 0.12);
            transform: translateY(-1px);
        }

        .education-hr-section input[type="text"]:hover,
        .dependency-profile-section input[type="text"]:hover,
        .economic-profile-section input[type="text"]:hover {
            border-color: #c01060;
            box-shadow: inset 0 2px 5px rgba(227, 21, 117, 0.12), inset 0 1px 3px rgba(227, 21, 117, 0.18);
        }

        .education-hr-section textarea, textarea.form-control {
            font-size: 14px;
            padding: 12px 14px;
            border: 2px solid #e31575;
            border-radius: 8px;
            font-weight: 400;
            resize: vertical;
            min-height: 90px;
            line-height: 1.5;
            background-color: #ffffff;
            transition: all 0.3s ease;
            box-shadow: inset 0 2px 4px rgba(227, 21, 117, 0.1), inset 0 1px 2px rgba(227, 21, 117, 0.15);
            font-family: inherit;
        }

        .education-hr-section textarea:focus, textarea.form-control:focus {
            outline: none;
            border-color: #e31575;
            background-color: #fefefe;
            box-shadow: inset 0 3px 6px rgba(227, 21, 117, 0.15), inset 0 2px 4px rgba(227, 21, 117, 0.2), 0 0 0 3px rgba(227, 21, 117, 0.12);
            transform: translateY(-1px);
        }

        .education-hr-section textarea:hover, textarea.form-control:hover {
            border-color: #c01060;
            box-shadow: inset 0 2px 5px rgba(227, 21, 117, 0.12), inset 0 1px 3px rgba(227, 21, 117, 0.18);
        }

        .education-hr-section textarea:focus {
            outline: none;
            border-color: #e31575;
            box-shadow: 0 0 0 3px rgba(227, 21, 117, 0.15);
            transition: all 0.2s ease-in-out;
        }

        /* ===== UTILITY CLASSES ===== */
        .textarea-resizable { resize: vertical; }
        .others-input { width: 200px; margin: 0; }
        
        /* ===== DEPENDENCY PROFILE LAYOUT ===== */
        .dependency-field {
            margin-left: 20px;
        }
        
        .living-with-options {
            margin-left: 20px;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        /* ===== ECONOMIC PROFILE LAYOUT ===== */
        .economic-others-input,
        .problems-specify-input {
            width: 150px;
            margin-left: 10px;
        }

        /* ===== HEALTH PROFILE SECTION ===== */
        .blood-type-select {
            width: 180px;
        }

        .disability-input {
            width: 200px;
        }

        .health-problems-list {
            margin-left: 10px;
        }

        .health-problems-list label:not(.input-label) {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .health-others-input {
            width: 150px;
            margin-left: 10px;
        }
        
        /* Consolidated medicine note style */
        .medicine-note {
            font-weight: 500;
            font-size: 12px;
            font-style: italic;
            color: #666;
        }
        
        .certification-checkbox {
            margin-right: 8px;
            margin-top: 2px;
        }
        
        /* ===== CUSTOM INSTRUCTION STYLES ===== */
        .instruction-box {
            background: #f8f8f8;
            padding: 15px;
            border-radius: 8px;
        }
        
        .instruction-title {
            color: #0066cc;
            margin-bottom: 15px;
        }
        
        .instruction-text {
            margin-bottom: 10px;
        }
        
        .instruction-list {
            margin-left: 20px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .instruction-note {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .shortcut-box {
            background: #e8f5e8;
            padding: 10px;
            border-radius: 5px;
        }
        
        .shortcut-title {
            margin: 0;
            font-weight: bold;
        }
        
        .shortcut-list {
            margin: 10px 0 0 20px;
        }

        .submit-button{
            background: linear-gradient(135deg, #e31575 0%, #c01060 100%);
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            align-self: center;
            font-weight: 600;
            font-size: 16px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 4px 12px rgba(227, 21, 117, 0.3);
            transition: all 0.3s ease;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(227, 21, 117, 0.4);
            background: linear-gradient(135deg, #c01060 0%, #a00d50 100%);
        }

        /* ===== MINIMAL OCR STYLES ===== */
        .ocr-minimal-panel {
            background: linear-gradient(135deg, #fff 0%, #fffbfd 100%);
            border: 2px solid #ffb7ce;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 2px 8px rgba(227, 21, 117, 0.08);
            transition: all 0.3s ease;
        }

        .ocr-minimal-panel:hover {
            box-shadow: 0 4px 12px rgba(227, 21, 117, 0.12);
            border-color: #e31575;
        }

        .ocr-minimal-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            color: #e31575;
            font-weight: 600;
            font-size: 15px;
        }

        .ocr-minimal-header i {
            font-size: 18px;
        }

        .ocr-hint {
            color: #999;
            font-weight: 400;
            font-size: 12px;
            margin-left: auto;
        }

        .ocr-minimal-content {
            display: flex;
            gap: 10px;
            align-items: stretch;
        }

        .ocr-file-input-wrapper {
            flex: 1;
            position: relative;
        }

        .ocr-file-input {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .ocr-file-label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: white;
            border: 2px dashed #ffb7ce;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #666;
            font-size: 13px;
            margin: 0;
            height: 100%;
        }

        .ocr-file-label:hover {
            border-color: #e31575;
            background: #fff8fb;
        }

        .ocr-file-label.has-files {
            border-color: #28a745;
            background: #f0fff4;
            color: #28a745;
            border-style: solid;
        }

        .ocr-file-label i {
            font-size: 16px;
            color: #e31575;
        }

        .ocr-file-label.has-files i {
            color: #28a745;
        }

        .ocr-scan-btn {
            padding: 10px 20px;
            background: linear-gradient(135deg, #e31575 0%, #c01060 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            white-space: nowrap;
            box-shadow: 0 2px 6px rgba(227, 21, 117, 0.3);
        }

        .ocr-scan-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(227, 21, 117, 0.4);
        }

        .ocr-scan-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #ccc;
            box-shadow: none;
        }

        .ocr-scan-btn i {
            font-size: 14px;
        }

        .ocr-progress-minimal {
            margin-top: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ocr-progress-bar-minimal {
            flex: 1;
            height: 8px;
            background: #f0f0f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .ocr-progress-fill-minimal {
            height: 100%;
            background: linear-gradient(90deg, #e31575 0%, #ff6ba9 100%);
            border-radius: 4px;
            transition: width 0.3s ease;
            box-shadow: 0 0 8px rgba(227, 21, 117, 0.4);
        }

        .ocr-progress-text-minimal {
            font-size: 12px;
            font-weight: 600;
            color: #e31575;
            min-width: 40px;
            text-align: right;
        }

        .ocr-status-minimal {
            margin-top: 10px;
            font-size: 13px;
            padding: 8px 12px;
            border-radius: 6px;
            display: none;
        }

        .ocr-status-minimal:not(:empty) {
            display: block;
        }

        .ocr-results-minimal {
            margin-top: 10px;
            padding: 10px 14px;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 1px solid #28a745;
            border-radius: 8px;
            color: #155724;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .ocr-results-minimal i {
            color: #28a745;
            font-size: 16px;
        }

        .ocr-files-list {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            font-size: 12px;
        }

        .ocr-files-list > div {
            padding: 4px 0;
            color: #666;
        }

        .ocr-files-list i {
            color: #e31575;
            margin-right: 6px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .ocr-minimal-content {
                flex-direction: column;
            }

            .ocr-scan-btn {
                width: 100%;
                justify-content: center;
            }

            .ocr-hint {
                display: none;
            }
        }

        /* Multi-step form styles */
        
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
            background: rgba(255, 255, 255, 0.3);
            color: white;
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
        
        .form-step {
            display: none;
            padding: 2.5rem;
        }
        
        .form-step.active {
            display: block;
        }
        
        .step-navigation {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 1rem;
         
            margin-top: 2rem;
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
        
        .step-navigation .btn-primary,
        .step-navigation #nextBtn,
        .step-navigation #nextBtn2,
        .step-navigation #nextBtn3,
        .step-navigation #nextBtn4,
        .step-navigation #nextBtn5 {
           

            padding: 10px 20px;
            background-color: #e31575;
            border-color: #e31575;
            color: white;
            font-weight: bold;
        }
        
        .step-navigation .btn-primary:hover,
        .step-navigation #nextBtn:hover,
        .step-navigation #nextBtn2:hover,
        .step-navigation #nextBtn3:hover,
        .step-navigation #nextBtn4:hover,
        .step-navigation #nextBtn5:hover {
            background-color: #ffb7ce;
            border-color: #ffb7ce;
            color: #e31575;
        }
        
        .step-navigation .btn-success,
        .step-navigation #submitBtn {
            background-color: #e31575;
            border-color: #e31575;
            color: white;
        }
        
        .step-navigation .btn-success:hover,
            .step-navigation #submitBtn:hover {
             	 background-color: #ffb7ce;
                border-color: #ffb7ce;
                color: #e31575;
            }

            /* ===== Table Action Buttons (Professional style) ===== */
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
        
        .step-navigation .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }
        
        .submit-button-container {
            display: none;
        }
        
        /* Photo upload styling is now handled by the x-photo-upload component */
        
        /* Certification Modal Styling */
        .certification-content {
            padding: 1rem;
        }
        
        .certification-text {
            font-size: 1rem;
            line-height: 1.6;
            color: #495057;
            margin-bottom: 1rem;
        }
        
        .form-check {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .form-check-input:checked {
            background-color: #e31575;
            border-color: #e31575;
        }
        
        .form-check-label {
            font-size: 1rem;
            color: #495057;
        }
        
        #finalSubmitBtn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Shake animation for invalid fields */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .main {
                margin-left: 0;
                margin-top: 0;
            }
            
            .form-header {
                padding: 1.5rem 1rem;
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .form-title {
                font-size: 1.1rem;
            }
            
            .progress-steps {
                flex-wrap: wrap;
                gap: 0.5rem;
                justify-content: center;
            }
            
            .step-indicator {
                width: 32px;
                height: 32px;
                font-size: 0.75rem;
            }
            
            .form-step {
                padding: 1.5rem 1rem;
            }
            
            .form-section {
                padding: 16px;
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
        
        @media (max-width: 480px) {
            .form-header {
                padding: 1rem 0.75rem;
            }
            
            .form-title {
                font-size: 1rem;
            }
            
            .form-step {
                padding: 1rem 0.75rem;
            }
            
            .form-section {
                padding: 12px;
            }
            
            .step-indicator {
                width: 28px;
                height: 28px;
                font-size: 0.7rem;
            }
        }
        </style>
    
    <script>
        // Toggle living with options based on radio selection
        function toggleLivingWithOptions() {
            const livingAlone = document.querySelector('input[name="living_condition_primary"][value="Living Alone"]');
            const livingWith = document.querySelector('input[name="living_condition_primary"][value="Living with"]');
            const livingWithOptions = document.getElementById('living_with_options');
            const checkboxes = livingWithOptions.querySelectorAll('input[type="checkbox"], input[type="text"]');
            
            if (livingAlone.checked) {
                // Disable all checkboxes and text inputs
                checkboxes.forEach(input => {
                    input.disabled = true;
                    if (input.type === 'checkbox') {
                        input.checked = false;
                    } else if (input.type === 'text') {
                        input.value = '';
                    }
                });
                livingWithOptions.style.backgroundColor = '';
                livingWithOptions.style.border = '';
            } else if (livingWith.checked) {
                // Enable all checkboxes and text inputs
                checkboxes.forEach(input => {
                    input.disabled = false;
                });
                
            } else {
                // Default state - disable all
                checkboxes.forEach(input => {
                    input.disabled = true;
                    if (input.type === 'checkbox') {
                        input.checked = false;
                    } else if (input.type === 'text') {
                        input.value = '';
                    }
                });
                livingWithOptions.style.backgroundColor = '';
                livingWithOptions.style.border = '';
            }
        }
        
        // Initialize the living with options on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleLivingWithOptions();
        });

        // Calculate age when birth date is selected
        function calculateAge() {
            const month = document.querySelector('select[name="birth_month"]').value;
            const day = document.querySelector('select[name="birth_day"]').value;
            const year = document.querySelector('select[name="birth_year"]').value;
            
            if (month && day && year) {
                const birthDate = new Date(year, month - 1, day);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();
                
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                
                document.getElementById('calculated_age').textContent = age + ' years old';
            }
        }
        
        // Show typing instructions modal
        function showTypingInstructions() {
            const modal = new bootstrap.Modal(document.getElementById('typingModal'));
            modal.show();
        }
        

        
        // Generic function to toggle others input fields
        function toggleOthersInput(checkboxSelector, inputId) {
            const othersCheckbox = document.querySelector(checkboxSelector);
            const othersInput = document.getElementById(inputId);
            
            if (othersCheckbox && othersInput) {
                othersInput.disabled = !othersCheckbox.checked;
                if (!othersCheckbox.checked) {
                    othersInput.value = '';
                }
            }
        }
        
        // Specific toggle functions using the generic function
        function toggleHouseholdOthersInput() {
            toggleOthersInput('input[name="household_condition[]"][value="Others"]', 'household_others_input');
        }
        
        function toggleSourceIncomeOthersInput() {
            toggleOthersInput('input[name="source_of_income[]"][value="Others"]', 'source_income_others_input');
        }

        function toggleProblemsNeedsOthersInput() {
            toggleOthersInput('input[name="problems_needs[]"][value="Others"]', 'problems_needs_others_input');
        }
        
        function toggleRealAssetsOthersInput() {
            toggleOthersInput('input[name="real_assets[]"][value="Others"]', 'assets_real_and_immovable_others_input');
        }
        
        function togglePersonalAssetsOthersInput() {
            toggleOthersInput('input[name="personal_assets[]"][value="Others"]', 'personal_assets_others_input');
        }
        
        function toggleHealthProblemsOthersInput() {
            toggleOthersInput('input[name="health_problems[]"][value="Others"]', 'health_problems_others_input');
        }
        
        function toggleDentalConcernOthersInput() {
            toggleOthersInput('input[name="dental_concern[]"][value="Others"]', 'dental_concern_others_input');
        }
        
        function toggleVisualConcernOthersInput() {
            toggleOthersInput('input[name="visual_concern[]"][value="Others"]', 'visual_concern_others_input');
        }
        
        function toggleHearingConditionOthersInput() {
            toggleOthersInput('input[name="hearing_condition[]"][value="Others"]', 'hearing_condition_others_input');
        }
        
        function toggleSocialEmotionalOthersInput() {
            toggleOthersInput('input[name="social_emotional[]"][value="Others"]', 'social_emotional_others_input');
        }
        
        function toggleAreaDifficultyOthersInput() {
            toggleOthersInput('input[name="area_difficulty[]"][value="Others"]', 'area_difficulty_others_input');
        }
        
        // Address data removed - using static preselected values for Region I, Pangasinan, Lingayen
        
        // Function to populate provinces based on selected region - REMOVED
        // Function to populate cities based on selected province - REMOVED  
        // Function to enable barangay selection - REMOVED
        // Barangay data for major cities - REMOVED
        
        // All address dropdown population functions removed since address is now static
        
        // Function to populate provinces based on selected region - REMOVED
        // Function to populate cities based on selected province - REMOVED
        // Function to enable barangay selection - REMOVED
        // All address data removed - using static preselected values
        
        // Function to populate provinces based on selected region - REMOVED
        // All address data removed - using static preselected values (Region I, Pangasinan, Lingayen)
        // Barangay data kept only for Lingayen since it's the target municipality
        const barangayData = {
            'Lingayen': ['Aliwekwek', 'Baay', 'Balangobong', 'Balococ', 'Bantayan', 'Basing', 'Capandanan', 'Domalandan Center', 'Domalandan East', 'Domalandan West', 'Dorongan', 'Dulag', 'Estanza', 'Lasip', 'Libsong East', 'Libsong West', 'Malawa', 'Malimpuec', 'Maniboc', 'Matalava', 'Naguelguel', 'Namolan', 'Pangapisan North', 'Pangapisan Sur', 'Poblacion', 'Quibaol', 'Rosario', 'Sabangan', 'Talogtog', 'Tonton', 'Tumbar', 'Wawa']
        };

        // Simplified functions for static preselected values (Region I, Pangasinan, Lingayen)
        function populateProvinces() {
            const regionSelect = document.querySelector('select[name="region"]');
            const provinceSelect = document.querySelector('select[name="province"]');
            const citySelect = document.querySelector('select[name="city"]');
            const barangaySelect = document.querySelector('select[name="barangay"]');
            
            const selectedRegion = regionSelect.value;
            
            // Since we're using static values, only enable if Region I is selected
            if (selectedRegion === 'Region I') {
                provinceSelect.disabled = false;
                provinceSelect.innerHTML = '<option value="">Select Province</option><option value="Pangasinan" selected>Pangasinan</option>';
                provinceSelect.value = 'Pangasinan';
                populateCities(); // Auto-populate cities
            } else {
                provinceSelect.disabled = true;
                citySelect.disabled = true;
                barangaySelect.disabled = true;
                provinceSelect.innerHTML = '<option value="">Select Region First</option>';
                citySelect.innerHTML = '<option value="">Select Province First</option>';
                barangaySelect.innerHTML = '<option value="">Select City First</option>';
            }
        }

        // Function to populate cities - simplified for static values
        function populateCities() {
            const regionSelect = document.querySelector('select[name="region"]');
            const provinceSelect = document.querySelector('select[name="province"]');
            const citySelect = document.querySelector('select[name="city"]');
            const barangaySelect = document.querySelector('select[name="barangay"]');
            
            const selectedRegion = regionSelect.value;
            const selectedProvince = provinceSelect.value;
            
            // Since we're using static values, only enable if Region I and Pangasinan are selected
            if (selectedRegion === 'Region I' && selectedProvince === 'Pangasinan') {
                citySelect.disabled = false;
                citySelect.innerHTML = '<option value="">Select City</option><option value="Lingayen" selected>Lingayen</option>';
                citySelect.value = 'Lingayen';
                enableBarangay(); // Auto-populate barangays
            } else {
                citySelect.disabled = true;
                barangaySelect.disabled = true;
                citySelect.innerHTML = '<option value="">Select Province First</option>';
                barangaySelect.innerHTML = '<option value="">Select City First</option>';
            }
        }

        // Function to enable barangay selection with actual barangay data
        function enableBarangay() {
            const citySelect = document.querySelector('select[name="city"]');
            const barangaySelect = document.querySelector('select[name="barangay"]');
            
            const selectedCity = citySelect.value;
            
            if (selectedCity) {
                barangaySelect.disabled = false;
                barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
                
                // Check if we have specific barangay data for this city
                if (barangayData[selectedCity]) {
                    // Use actual barangay data
                    barangayData[selectedCity].forEach(barangay => {
                        const option = document.createElement('option');
                        option.value = barangay;
                        option.textContent = barangay;
                        barangaySelect.appendChild(option);
                    });
                } else {
                    // Use generic barangay options for cities without specific data
                    const genericBarangays = [
                        'Barangay 1 (Poblacion)', 'Barangay 2 (Poblacion)', 'Barangay 3 (Poblacion)',
                        'Barangay 4', 'Barangay 5', 'Barangay 6', 'Barangay 7', 'Barangay 8',
                        'Barangay 9', 'Barangay 10', 'Barangay 11', 'Barangay 12',
                        'San Antonio', 'San Jose', 'San Juan', 'San Miguel', 'San Nicolas',
                        'San Pedro', 'San Rafael', 'San Roque', 'Santa Cruz', 'Santa Maria',
                        'Santo Niño', 'Santo Tomas'
                    ];
                    
                    genericBarangays.forEach(barangay => {
                        const option = document.createElement('option');
                        option.value = barangay;
                        option.textContent = barangay;
                        barangaySelect.appendChild(option);
                    });
                }
            } else {
                barangaySelect.disabled = true;
                barangaySelect.innerHTML = '<option value="">Select City First</option>';
            }
        }
        
        // Add event listeners to birth date fields and address dropdowns
        document.addEventListener('DOMContentLoaded', function() {
            // Birth date event listeners
            const birthMonth = document.querySelector('select[name="birth_month"]');
            const birthDay = document.querySelector('select[name="birth_day"]');
            const birthYear = document.querySelector('select[name="birth_year"]');
            
            if (birthMonth) birthMonth.addEventListener('change', calculateAge);
            if (birthDay) birthDay.addEventListener('change', calculateAge);
            if (birthYear) birthYear.addEventListener('change', calculateAge);
            
            // Address dropdown event listeners
            const regionSelect = document.querySelector('select[name="region"]');
            const provinceSelect = document.querySelector('select[name="province"]');
            const citySelect = document.querySelector('select[name="city"]');
            
            if (regionSelect) {
                regionSelect.addEventListener('change', populateProvinces);
            }
            
            if (provinceSelect) {
                provinceSelect.addEventListener('change', populateCities);
            }
            
            if (citySelect) {
                citySelect.addEventListener('change', enableBarangay);
            }
        });
        
        // Multi-step form functionality
        let currentStep = 1;
        const totalSteps = 7;
        
        function showStep(step) {
            // Hide all steps
            for (let i = 1; i <= totalSteps; i++) {
                const stepElement = document.getElementById(`step${i}`);
                if (stepElement) {
                    stepElement.style.display = 'none';
                }
            }
            
            // Show current step
            const currentStepElement = document.getElementById(`step${step}`);
            if (currentStepElement) {
                currentStepElement.style.display = 'block';
            }
            
            // Update form header title based on current step
            const formTitle = document.querySelector('.form-title');
            const stepTitles = {
                1: 'I - IDENTIFYING INFORMATION',
                2: 'II - FAMILY COMPOSITION',
                3: 'III - EDUCATION / HR PROFILE',
                4: 'IV - DEPENDENCY PROFILE',
                5: 'V - ECONOMIC PROFILE',
                6: 'VI - HEALTH PROFILE',
                7: 'VII - PHOTO IDENTIFICATION'
            };
            
            if (formTitle && stepTitles[step]) {
                formTitle.textContent = stepTitles[step];
            }
            
            // Update progress indicators
            updateProgressIndicators(step);
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
        
        function calculateAge(birthDate) {
            const today = new Date();
            const birth = new Date(birthDate);
            let age = today.getFullYear() - birth.getFullYear();
            const monthDiff = today.getMonth() - birth.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            
            return age;
        }
        
        function validateDateOfBirth() {
            const dobField = document.getElementById('date_of_birth');
            if (!dobField || !dobField.value) return true;
            
            const age = calculateAge(dobField.value);
            
            if (age < 60) {
                dobField.style.borderColor = '#dc3545';
                dobField.style.borderWidth = '2px';
                
                // Show error message
                let errorMsg = dobField.parentElement.querySelector('.age-error-msg');
                if (!errorMsg) {
                    errorMsg = document.createElement('small');
                    errorMsg.className = 'age-error-msg text-danger d-block mt-1';
                    errorMsg.style.fontSize = '12px';
                    errorMsg.style.fontWeight = '600';
                    dobField.parentElement.appendChild(errorMsg);
                }
                errorMsg.textContent = `Age is ${age} years old. Must be 60 years or older to register as a senior citizen.`;
                
                return false;
            } else {
                dobField.style.borderColor = '';
                dobField.style.borderWidth = '';
                
                // Remove error message if exists
                const errorMsg = dobField.parentElement.querySelector('.age-error-msg');
                if (errorMsg) {
                    errorMsg.remove();
                }
                
                return true;
            }
        }
        
        function validateCurrentStep() {
            // Get current step element
            const currentStepElement = document.getElementById(`step${currentStep}`);
            if (!currentStepElement) return true;
            
            // Special validation for date of birth in step 1
            if (currentStep === 1) {
                if (!validateDateOfBirth()) {
                    const dobField = document.getElementById('date_of_birth');
                    dobField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => {
                        dobField.focus();
                        dobField.style.animation = 'shake 0.5s';
                        setTimeout(() => {
                            dobField.style.animation = '';
                        }, 500);
                    }, 300);
                    return false;
                }
            }
            
            // Special validation for checkup frequency in step 6 (health profile)
            if (currentStep === 6) {
                const scheduledCheckup = document.getElementById('scheduled_checkup');
                const checkupFrequency = document.getElementById('checkup_frequency');
                
                if (scheduledCheckup && checkupFrequency && scheduledCheckup.value === 'Yes' && checkupFrequency.value === '') {
                    checkupFrequency.style.borderColor = '#dc3545';
                    checkupFrequency.style.borderWidth = '2px';
                    
                    checkupFrequency.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => {
                        checkupFrequency.focus();
                        checkupFrequency.style.animation = 'shake 0.5s';
                        setTimeout(() => {
                            checkupFrequency.style.animation = '';
                        }, 500);
                    }, 300);
                    
                    return false;
                }
            }
            
            // Get all required fields in the current step
            const requiredFields = currentStepElement.querySelectorAll('[required]');
            let isValid = true;
            let firstInvalidField = null;
            
            // Check each required field
            requiredFields.forEach(field => {
                // Remove previous error styling
                field.style.borderColor = '';
                field.style.borderWidth = '';
                
                // Check if field is empty
                if (field.type === 'checkbox' || field.type === 'radio') {
                    // For checkboxes/radios, check if at least one with the same name is checked
                    const name = field.name;
                    const checkedFields = currentStepElement.querySelectorAll(`[name="${name}"]:checked`);
                    if (checkedFields.length === 0 && field.required) {
                        if (!firstInvalidField) firstInvalidField = field;
                        isValid = false;
                    }
                } else if (field.value.trim() === '') {
                    // For other inputs, check if value is empty
                    field.style.borderColor = '#dc3545';
                    field.style.borderWidth = '2px';
                    if (!firstInvalidField) firstInvalidField = field;
                    isValid = false;
                }
            });
            
            // If validation fails, scroll to first invalid field and focus it
            if (!isValid && firstInvalidField) {
                // Scroll to the field with some offset for better visibility
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Focus the field after a short delay to ensure scroll completes
                setTimeout(() => {
                    firstInvalidField.focus();
                    
                    // Add a shake animation to the field
                    firstInvalidField.style.animation = 'shake 0.5s';
                    setTimeout(() => {
                        firstInvalidField.style.animation = '';
                    }, 500);
                }, 300);
            }
            
            return isValid;
        }
        
        function changeStep(direction) {
            // If moving forward, validate current step first
            if (direction > 0) {
                if (!validateCurrentStep()) {
                    return; // Don't proceed if validation fails
                }
            }
            
            const newStep = currentStep + direction;
            
            if (newStep >= 1 && newStep <= totalSteps) {
                currentStep = newStep;
                showStep(currentStep);
            }
        }
        
        function goToStep(step) {
            // If trying to go forward, validate current step first
            if (step > currentStep) {
                if (!validateCurrentStep()) {
                    return; // Don't proceed if validation fails
                }
            }
            
            if (step >= 1 && step <= totalSteps) {
                currentStep = step;
                showStep(currentStep);
            }
        }
        
        // Photo upload functionality is now handled by the x-photo-upload component
        
        // Certification modal functions
        function showCertificationModal() {
            const modal = new bootstrap.Modal(document.getElementById('certificationModal'));
            modal.show();
        }
        
        function finalSubmit() {
            // Close the modal first
            const modal = bootstrap.Modal.getInstance(document.getElementById('certificationModal'));
            modal.hide();
            
            // Ensure certification flag is set before submission
            const certificationCheckbox = document.getElementById('certificationAgree');
            const certificationField = document.getElementById('certificationField');
            if (certificationCheckbox && certificationField) {
                if (!certificationCheckbox.checked) {
                    // Safety guard: should not happen because button is disabled when unchecked
                    alert('Please agree to the Data Privacy Certification to submit.');
                    return;
                }
                certificationField.value = 'on';
            }

            // Submit the form directly
            const form = document.querySelector('form[method="POST"]');
            if (form) {
                form.submit();
            } else {
                console.error('Form not found');
            }
        }
        
        function submitForm() {
            // This function is now replaced by showCertificationModal
            showCertificationModal();
        }
        
        // ===== OCR JAVASCRIPT (FROM MASTERPROFILE) =====
        
        // Display selected files when files are chosen
        document.getElementById('ocrFileUpload').addEventListener('change', function() {
            const filesContainer = document.getElementById('selectedFilesContainer');
            const filesList = document.getElementById('selectedFilesList');
            const fileLabel = document.querySelector('.ocr-file-label');
            const fileText = document.getElementById('ocrFileText');
            const scanBtn = document.getElementById('processOcrBtn');
            
            // Clear previous list
            filesList.innerHTML = '';
            
            if (this.files.length > 0) {
                // Update label appearance
                fileLabel.classList.add('has-files');
                
                // Update text based on number of files
                if (this.files.length === 1) {
                    fileText.textContent = this.files[0].name.length > 30 
                        ? this.files[0].name.substring(0, 30) + '...' 
                        : this.files[0].name;
                } else {
                    fileText.textContent = `${this.files.length} files selected`;
                }
                
                // Enable scan button
                scanBtn.disabled = false;
                
                // Show files list
                filesContainer.classList.remove('d-none');
                Array.from(this.files).forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.innerHTML = `<i class="fas fa-file"></i> ${file.name} <small class="text-muted">(${(file.size / 1024).toFixed(1)} KB)</small>`;
                    filesList.appendChild(fileItem);
                });
            } else {
                // Reset to default
                fileLabel.classList.remove('has-files');
                fileText.textContent = 'Choose files or drag here';
                scanBtn.disabled = true;
                filesContainer.classList.add('d-none');
            }
        });
        
        // Process multiple documents sequentially
        async function processOcrDocuments() {
            console.log("processOcrDocuments function called");
            const ocrFileUpload = document.getElementById('ocrFileUpload');
            const ocrStatus = document.getElementById('ocrStatus');
            const progressContainer = document.getElementById('ocrProgressContainer');
            const progressBar = document.getElementById('ocrProgressBar');
            
            if (!ocrFileUpload || !ocrFileUpload.files || !ocrFileUpload.files.length) {
                ocrStatus.innerHTML = '<span class="text-danger">Please select at least one document to scan</span>';
                return;
            }
            
            const files = Array.from(ocrFileUpload.files);
            const totalFiles = files.length;
            let processedFiles = 0;
            let successfulFiles = 0;
            
            // Show progress bar
            progressContainer.classList.remove('d-none');
            progressBar.style.width = '0%';
            
            // Update initial status message
            ocrStatus.innerHTML = `<span class="text-info"><i class="fas fa-spinner fa-spin me-1"></i> Processing ${totalFiles} document(s), please wait...</span>`;
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            // Process each file sequentially
            let combinedData = null;
            
            for (const file of files) {
                try {
                    const formData = new FormData();
                    formData.append('file', file);
                    
                    // Update status for current file
                    ocrStatus.innerHTML = `<span class="text-info"><i class="fas fa-spinner fa-spin me-1"></i> Processing file ${processedFiles + 1}/${totalFiles}: ${file.name}</span>`;
                    
                    console.log(`Processing file ${processedFiles + 1}/${totalFiles}: ${file.name}`);
                    
                    // Send to API endpoint
                    const response = await fetch('/api/ocr/process', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken || ''
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error(`Network error (${response.status})`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        successfulFiles++;
                        
                        // Merge data from this file with previous data
                        if (data.data) {
                            if (!combinedData) {
                                combinedData = data;
                            } else {
                                // Merge new data with existing data, prioritizing non-empty values
                                Object.keys(data.data).forEach(key => {
                                    if (data.data[key] && (!combinedData.data[key] || combinedData.data[key] === '')) {
                                        combinedData.data[key] = data.data[key];
                                    }
                                });
                            }
                        }
                    } else {
                        console.error(`OCR Error for ${file.name}:`, data.message || "Unknown error");
                    }
                } catch (error) {
                    console.error(`Error processing ${file.name}:`, error);
                }
                
                // Update progress
                processedFiles++;
                const progress = Math.round((processedFiles / totalFiles) * 100);
                progressBar.style.width = `${progress}%`;
                progressBar.setAttribute('aria-valuenow', progress);
                
                // Update progress text
                const progressText = document.getElementById('ocrProgressText');
                if (progressText) {
                    progressText.textContent = `${progress}% (${processedFiles}/${totalFiles})`;
                }
            }
            
            // All files processed - update final status
            if (successfulFiles > 0) {
                ocrStatus.innerHTML = `<span class="text-success"><i class="fas fa-check-circle me-1"></i> Processed ${successfulFiles} of ${totalFiles} documents successfully!</span>`;
                
                // Show results container
                const resultsContainer = document.getElementById('ocrResultsContainer');
                const resultsSummary = document.getElementById('ocrResultsSummary');
                if (resultsContainer && resultsSummary) {
                    resultsContainer.classList.remove('d-none');
                    resultsSummary.textContent = `Successfully processed ${successfulFiles} of ${totalFiles} documents. Form fields have been populated with the extracted data.`;
                }
                
                // Fill form fields with combined extracted data
                if (combinedData && combinedData.data) {
                    fillFormFields(combinedData);
                }
            } else {
                ocrStatus.innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> Failed to process any documents successfully.</span>`;
            }
        }
        
        // Global function for OCR scanning
        function handleOcrScan() {
            console.log("OCR Scan button clicked via onclick");
            processOcrDocuments();
        }
        
        // Direct event handler for scan button (backup)
        document.addEventListener('DOMContentLoaded', function() {
            const processBtn = document.getElementById('processOcrBtn');
            if (processBtn) {
                processBtn.addEventListener('click', function() {
                    console.log("Scan button clicked directly");
                    processOcrDocuments();
                });
            }
        });
        
        // Function to fill form fields with OCR data using multiple fallback patterns
        function fillFormFields(data) {
            console.log("Filling form fields with OCR data:", data);
            
            // Extract data object
            const ocrData = data.data || {};
            
            // Define pattern arrays for all fields
            const firstNamePatterns = ['first_name', 'firstname', 'first name', 'name_first', 'name first', 'given_name', 'givenname', 'given name', 'maiden_name', 'maiden name'];
            const lastNamePatterns = ['last_name', 'lastname', 'last name', 'name_last', 'name last', 'surname', 'family_name', 'familyname', 'family name'];
            const middleNamePatterns = ['middle_name', 'middlename', 'middle name', 'name_middle', 'name middle', 'middle_initial', 'middleinitial', 'middle initial'];
            const oscaIdPatterns = ['osca_id', 'oscaid', 'osca id', 'osca_id_number', 'osca_id_no', 'osca_number', 'oscanumber', 'osca number'];
            const gsisSssPatterns = ['gsis_sss', 'gsissss', 'gsis sss', 'gsis_sss_number', 'gsis_sss_no', 'sss_number', 'sssnumber', 'sss number', 'gsis_number', 'gsisnumber'];
            const tinPatterns = ['tin', 'tax_identification_number', 'tax identification number', 'tin_no', 'tin no', 'tax_id', 'tax id'];
            const philhealthPatterns = ['philhealth', 'philhealth_number', 'philhealth number', 'philhealth_no', 'philhealth no', 'philhealth_id'];
            const scAssociationPatterns = ['sc_association', 'scassociation', 'sc association', 'senior_citizen_association_id', 'senior citizen association id'];
            const otherGovtIdPatterns = ['other_govt_id', 'othergovtid', 'other govt id', 'other_government_id', 'other government id', 'other_id'];
            const dateOfBirthPatterns = ['date_of_birth', 'dateofbirth', 'date of birth', 'birth_date', 'birthdate', 'birth date', 'dob'];
            const birthPlacePatterns = ['birth_place', 'birthplace', 'birth place', 'place_of_birth', 'placeofbirth', 'place of birth'];
            const residencePatterns = ['residence', 'house_no', 'house no', 'zone', 'purok', 'sitio'];
            const streetPatterns = ['street', 'st', 'street_name', 'streetname', 'street name'];
            const ethnicOriginPatterns = ['ethnic_origin', 'ethnicorigin', 'ethnic origin', 'ethnicity', 'ethnic_group', 'ethnic group', 'tribe'];
            const languagePatterns = ['language', 'language_spoken', 'languagespoken', 'language spoken', 'dialect', 'mother_tongue', 'mothertongue', 'mother tongue'];
            const contactNumberPatterns = ['contact_number', 'contactnumber', 'contact number', 'phone', 'mobile', 'cellphone', 'cell phone', 'telephone'];
            const emailPatterns = ['email', 'email_address', 'emailaddress', 'email address', 'e-mail', 'e mail'];
            const maritalStatusPatterns = ['marital_status', 'maritalstatus', 'marital status', 'civil_status', 'civilstatus', 'civil status'];
            const sexPatterns = ['sex', 'gender'];
            
            // Function to find value using multiple patterns
            function findValueByPatterns(data, patterns) {
                // First try exact matches in the data object
                for (const pattern of patterns) {
                    if (data[pattern] !== undefined && data[pattern] !== null && data[pattern] !== '') {
                        console.log(`Found ${pattern} with value: ${data[pattern]}`);
                        return data[pattern];
                    }
                }
                
                // If no exact match, try case-insensitive search in all properties
                for (const key in data) {
                    const keyLower = key.toLowerCase();
                    for (const pattern of patterns) {
                        if (keyLower.includes(pattern.toLowerCase())) {
                            console.log(`Found similar key ${key} matching pattern ${pattern} with value: ${data[key]}`);
                            return data[key];
                        }
                    }
                }
                
                // If still not found, check if there's a nested 'data' object
                if (data.data && typeof data.data === 'object') {
                    return findValueByPatterns(data.data, patterns);
                }
                
                return null;
            }
            
            // Fill First Name
            const firstName = findValueByPatterns(ocrData, firstNamePatterns);
            if (firstName) {
                const field = document.querySelector('input[name="first_name"]');
                if (field) field.value = firstName;
            }
            
            // Fill Last Name
            const lastName = findValueByPatterns(ocrData, lastNamePatterns);
            if (lastName) {
                const field = document.querySelector('input[name="last_name"]');
                if (field) field.value = lastName;
            }
            
            // Fill Middle Name
            const middleName = findValueByPatterns(ocrData, middleNamePatterns);
            if (middleName) {
                const field = document.querySelector('input[name="middle_name"]');
                if (field) field.value = middleName;
            }
            
            // Fill OSCA ID
            const oscaId = findValueByPatterns(ocrData, oscaIdPatterns);
            if (oscaId) {
                const field = document.querySelector('input[name="osca_id"]');
                if (field) field.value = oscaId;
            }
            
            // Fill GSIS/SSS
            const gsisSss = findValueByPatterns(ocrData, gsisSssPatterns);
            if (gsisSss) {
                const field = document.querySelector('input[name="gsis_sss"]');
                if (field) field.value = gsisSss;
            }
            
            // Fill TIN
            const tin = findValueByPatterns(ocrData, tinPatterns);
            if (tin) {
                const field = document.querySelector('input[name="tin"]');
                if (field) field.value = tin;
            }
            
            // Fill PhilHealth
            const philhealth = findValueByPatterns(ocrData, philhealthPatterns);
            if (philhealth) {
                const field = document.querySelector('input[name="philhealth"]');
                if (field) field.value = philhealth;
            }
            
            // Fill SC Association
            const scAssociation = findValueByPatterns(ocrData, scAssociationPatterns);
            if (scAssociation) {
                const field = document.querySelector('input[name="sc_association"]');
                if (field) field.value = scAssociation;
            }
            
            // Fill Other Govt ID
            const otherGovtId = findValueByPatterns(ocrData, otherGovtIdPatterns);
            if (otherGovtId) {
                const field = document.querySelector('input[name="other_govt_id"]');
                if (field) field.value = otherGovtId;
            }
            
            // Fill Date of Birth
            const dateOfBirth = findValueByPatterns(ocrData, dateOfBirthPatterns);
            if (dateOfBirth) {
                const field = document.querySelector('input[name="date_of_birth"]');
                if (field) field.value = dateOfBirth;
            }
            
            // Fill Birth Place
            const birthPlace = findValueByPatterns(ocrData, birthPlacePatterns);
            if (birthPlace) {
                const field = document.querySelector('input[name="birth_place"]');
                if (field) field.value = birthPlace;
            }
            
            // Fill Residence
            const residence = findValueByPatterns(ocrData, residencePatterns);
            if (residence) {
                const field = document.querySelector('input[name="residence"]');
                if (field) field.value = residence;
            }
            
            // Fill Street
            const street = findValueByPatterns(ocrData, streetPatterns);
            if (street) {
                const field = document.querySelector('input[name="street"]');
                if (field) field.value = street;
            }
            
            // Fill Ethnic Origin
            const ethnicOrigin = findValueByPatterns(ocrData, ethnicOriginPatterns);
            if (ethnicOrigin) {
                const field = document.querySelector('input[name="ethnic_origin"]');
                if (field) field.value = ethnicOrigin;
            }
            
            // Fill Language
            const language = findValueByPatterns(ocrData, languagePatterns);
            if (language) {
                const field = document.querySelector('input[name="language"]');
                if (field) field.value = language;
            }
            
            // Fill Contact Number
            const contactNumber = findValueByPatterns(ocrData, contactNumberPatterns);
            if (contactNumber) {
                const field = document.querySelector('input[name="contact_number"]');
                if (field) field.value = contactNumber;
            }
            
            // Fill Email
            const email = findValueByPatterns(ocrData, emailPatterns);
            if (email) {
                const field = document.querySelector('input[name="email"]');
                if (field) field.value = email;
            }
            
            // Fill Marital Status
            const maritalStatus = findValueByPatterns(ocrData, maritalStatusPatterns);
            if (maritalStatus) {
                const field = document.querySelector('select[name="marital_status"]');
                if (field) {
                    // Try to find matching option
                    const options = Array.from(field.options);
                    const match = options.find(opt => opt.value.toLowerCase() === maritalStatus.toLowerCase() || opt.text.toLowerCase() === maritalStatus.toLowerCase());
                    if (match) field.value = match.value;
                }
            }
            
            // Fill Sex/Gender
            const sex = findValueByPatterns(ocrData, sexPatterns);
            if (sex) {
                const field = document.querySelector('select[name="sex"]');
                if (field) {
                    const sexValue = sex.toLowerCase();
                    if (sexValue.includes('male') && !sexValue.includes('female')) {
                        field.value = 'Male';
                    } else if (sexValue.includes('female')) {
                        field.value = 'Female';
                    }
                }
            }
            
            // Show success message
            alert('Form fields have been filled with the extracted OCR data!');
            
            // Scroll to top to see filled fields
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        // ===== END OF OCR JAVASCRIPT =====
        
        // Initialize form on page load
        // Function to validate numeric input
        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            // Allow only numbers (0-9)
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
        
        // Function to validate pasted content
        function validatePaste(evt) {
            // Get pasted data via clipboard API
            let clipboardData = evt.clipboardData || window.clipboardData;
            let pastedData = clipboardData.getData('Text');
            
            // Check if pasted data contains only numbers
            if (!/^\d*$/.test(pastedData)) {
                return false;
            }
            return true;
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            showStep(1);
            
            // Add click handlers to step indicators
            document.querySelectorAll('.step-indicator').forEach(indicator => {
                indicator.addEventListener('click', function() {
                    const step = parseInt(this.getAttribute('data-step'));
                    goToStep(step);
                });
            });
            
            // Add event listener for certification checkbox
            const certificationCheckbox = document.getElementById('certificationAgree');
            const finalSubmitBtn = document.getElementById('finalSubmitBtn');
            const certificationField = document.getElementById('certificationField');
            
            if (certificationCheckbox && finalSubmitBtn) {
                certificationCheckbox.addEventListener('change', function() {
                    finalSubmitBtn.disabled = !this.checked;
                    if (certificationField) {
                        certificationField.value = this.checked ? 'on' : '';
                    }
                });
            }
            
            // Add input event listeners to remove red border when user starts typing
            document.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
                field.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        this.style.borderColor = '';
                        this.style.borderWidth = '';
                    }
                });
                
                // For select elements, also listen to change event
                if (field.tagName === 'SELECT') {
                    field.addEventListener('change', function() {
                        if (this.value.trim() !== '') {
                            this.style.borderColor = '';
                            this.style.borderWidth = '';
                        }
                    });
                }
            });
            
            // Add event listener for date of birth validation and set max date
            const dobField = document.getElementById('date_of_birth');
            if (dobField) {
                // Set max date to 60 years ago from today
                const today = new Date();
                const maxDate = new Date(today.getFullYear() - 60, today.getMonth(), today.getDate());
                const maxDateString = maxDate.toISOString().split('T')[0];
                dobField.setAttribute('max', maxDateString);
                
                // Set a reasonable min date (e.g., 120 years ago)
                const minDate = new Date(today.getFullYear() - 120, today.getMonth(), today.getDate());
                const minDateString = minDate.toISOString().split('T')[0];
                dobField.setAttribute('min', minDateString);
                
                dobField.addEventListener('change', function() {
                    validateDateOfBirth();
                });
                
                // Also validate on input
                dobField.addEventListener('input', function() {
                    if (this.value) {
                        validateDateOfBirth();
                    }
                });
            }

            // ===== Dynamic Family Composition Rows =====
            const childrenBody = document.getElementById('childrenTableBody');
            const dependentsBody = document.getElementById('dependentsTableBody');
            const addChildBtn = document.getElementById('addChildBtn');
            const addDependentBtn = document.getElementById('addDependentBtn');

            function reindexChildren() {
                if (!childrenBody) return;
                const rows = childrenBody.querySelectorAll('tr');
                rows.forEach((row, idx) => {
                    const index = idx + 1;
                    const nameInput = row.querySelector('input[name^="child_name_"]');
                    const occInput = row.querySelector('input[name^="child_occupation_"]');
                    const incomeInput = row.querySelector('input[name^="child_income_"]');
                    const ageInput = row.querySelector('input[name^="child_age_"]');
                    const workingSelect = row.querySelector('select[name^="child_working_"]');
                    if (nameInput) nameInput.name = `child_name_${index}`;
                    if (occInput) occInput.name = `child_occupation_${index}`;
                    if (incomeInput) incomeInput.name = `child_income_${index}`;
                    if (ageInput) ageInput.name = `child_age_${index}`;
                    if (workingSelect) workingSelect.name = `child_working_${index}`;
                });
            }

            function reindexDependents() {
                if (!dependentsBody) return;
                const rows = dependentsBody.querySelectorAll('tr');
                rows.forEach((row, idx) => {
                    const index = idx + 1;
                    const nameInput = row.querySelector('input[name^="dependent_name_"]');
                    const occInput = row.querySelector('input[name^="dependent_occupation_"]');
                    const incomeInput = row.querySelector('input[name^="dependent_income_"]');
                    const ageInput = row.querySelector('input[name^="dependent_age_"]');
                    const workingSelect = row.querySelector('select[name^="dependent_working_"]');
                    if (nameInput) nameInput.name = `dependent_name_${index}`;
                    if (occInput) occInput.name = `dependent_occupation_${index}`;
                    if (incomeInput) incomeInput.name = `dependent_income_${index}`;
                    if (ageInput) ageInput.name = `dependent_age_${index}`;
                    if (workingSelect) workingSelect.name = `dependent_working_${index}`;
                });
            }

            function addChildRow() {
                if (!childrenBody) return;
                const nextIndex = childrenBody.querySelectorAll('tr').length + 1;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input type="text" name="child_name_${nextIndex}" class="form-control form-control-sm border-0" placeholder="Child Name"></td>
                    <td><input type="text" name="child_occupation_${nextIndex}" class="form-control form-control-sm border-0" placeholder="Occupation"></td>
                    <td><input type="text" name="child_income_${nextIndex}" class="form-control form-control-sm border-0" placeholder="Income" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                    <td><input type="text" name="child_age_${nextIndex}" class="form-control form-control-sm border-0" placeholder="Age" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                    <td>
                      <select name="child_working_${nextIndex}" class="form-select form-select-sm border-0">
                        <option value="">Is working?</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                      </select>
                    </td>
                    <td class="text-center">
                      <button type="button" class="table-action-delete delete-child-row" title="Delete Row"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                childrenBody.appendChild(tr);
                reindexChildren();
            }

            function addDependentRow() {
                if (!dependentsBody) return;
                const nextIndex = dependentsBody.querySelectorAll('tr').length + 1;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input type="text" name="dependent_name_${nextIndex}" class="form-control form-control-sm border-0" placeholder="Name of Dependent"></td>
                    <td><input type="text" name="dependent_occupation_${nextIndex}" class="form-control form-control-sm border-0" placeholder="Occupation of Dependent"></td>
                    <td><input type="text" name="dependent_income_${nextIndex}" class="form-control form-control-sm border-0" placeholder="Income" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                    <td><input type="text" name="dependent_age_${nextIndex}" class="form-control form-control-sm border-0" placeholder="Age" onkeypress="return isNumberKey(event)" onpaste="return validatePaste(event)"></td>
                    <td>
                      <select name="dependent_working_${nextIndex}" class="form-select form-select-sm border-0">
                        <option value="">Is Working?</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                      </select>
                    </td>
                    <td class="text-center">
                      <button type="button" class="table-action-delete delete-dependent-row" title="Delete Row"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                dependentsBody.appendChild(tr);
                reindexDependents();
            }

            // Attach add button handlers
            if (addChildBtn) {
                addChildBtn.addEventListener('click', addChildRow);
            }
            if (addDependentBtn) {
                addDependentBtn.addEventListener('click', addDependentRow);
            }

            // Delegate delete buttons
            if (childrenBody) {
                childrenBody.addEventListener('click', function(e) {
                    const btn = e.target.closest('.delete-child-row');
                    if (btn) {
                        const row = btn.closest('tr');
                        row.remove();
                        reindexChildren();
                    }
                });
            }
            if (dependentsBody) {
                dependentsBody.addEventListener('click', function(e) {
                    const btn = e.target.closest('.delete-dependent-row');
                    if (btn) {
                        const row = btn.closest('tr');
                        row.remove();
                        reindexDependents();
                    }
                });
            }
            
            // Initialize conditional validation for checkup frequency
            toggleCheckupFrequency();
        });
        
        function toggleCheckupFrequency() {
            const scheduledCheckup = document.getElementById('scheduled_checkup');
            const checkupFrequency = document.getElementById('checkup_frequency');
            const requiredIndicator = document.getElementById('checkup_frequency_required');
            
            if (scheduledCheckup && checkupFrequency && requiredIndicator) {
                if (scheduledCheckup.value === 'Yes') {
                    checkupFrequency.setAttribute('required', 'required');
                    requiredIndicator.style.display = 'inline';
                } else {
                    checkupFrequency.removeAttribute('required');
                    requiredIndicator.style.display = 'none';
                    
                    // If No is selected, clear the frequency field
                    if (scheduledCheckup.value === 'No') {
                        checkupFrequency.value = '';
                    }
                }
            }
        }
    </script>
  </x-header>
</x-sidebar>