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
                <!-- Required Fields Notice -->
                <div class="alert alert-info mb-4" style="background-color: #e3f2fd; border: 1px solid #2196f3; color: #1976d2;">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Note:</strong> Fields marked with <span class="required-field">*</span> are required and must be filled out.
                </div>
                
                <!-- Google Vision OCR Panel -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="ocr-panel" style="background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); padding: 15px; margin-bottom: 20px; border: 2px solid #e31575;">
                            <h4 style="color: #e31575; margin-bottom: 15px; font-size: 18px; text-align: center;">
                                <i class="fas fa-file-alt me-2"></i>Form Scanner - Google Vision OCR
                            </h4>
                            <p class="small text-muted mb-3">Upload a completed form to automatically fill the fields using Google Vision OCR.</p>
                            
                            <div class="mb-3">
                                <label for="ocrFileUpload" class="form-label small">Upload Form Image</label>
                                <input type="file" class="form-control form-control-sm" id="ocrFileUpload" accept="image/*">
                            </div>
                            
                            <button type="button" id="processOcrBtn" class="btn btn-primary btn-sm w-100" style="background-color: #e31575; border-color: #e31575;">
                                <i class="fas fa-magic me-2"></i>Scan & Auto-fill
                            </button>
                            
                            <div id="ocrStatus" class="mt-3" style="display: none;">
                                <div class="progress" style="height: 10px;">
                                    <div id="ocrProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; background-color: #e31575;"></div>
                                </div>
                                <p id="ocrStatusText" class="small text-muted mt-2 mb-0">Processing...</p>
                            </div>
                            
                            <div id="ocrResults" class="mt-3" style="display: none;">
                                <h6 class="mb-2">Extracted Information:</h6>
                                <div class="small" id="ocrResultsList" style="max-height: 200px; overflow-y: auto;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('seniors.store') }}" enctype="multipart/form-data">
                    @csrf


                <div class="form-step active" id="step1">
                <div class="form-section-content">

                    <div class="mb-5">
                        <label class="form-label fw-bold mb-3" style="font-size: 16px; color: #2c3e50; font-weight: 700; letter-spacing: 0.3px;">1. Name of Senior Citizen</label>
                        <div class="row g-4">
                            <div class="col-md-3">
                                <label class="form-label small">Last Name *</label>
                                <input type="text" name="last_name" class="form-control form-control-sm" placeholder="Last Name" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">First Name *</label>
                                <input type="text" name="first_name" class="form-control form-control-sm" placeholder="First Name" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Middle Name *</label>
                                <input type="text" name="middle_name" class="form-control form-control-sm" placeholder="Middle Name" required>
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
                                <label class="form-label small">Region *</label>
                                <select name="region" class="form-select form-select-sm" required>
                                   <option value="Region I">Region I - Ilocos Region</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Province *</label>
                                <select name="province" class="form-select form-select-sm" required>
                                    <option value="Pangasinan" selected>Pangasinan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">City *</label>
                                <select name="city" class="form-select form-select-sm" required>
                                    <option value="Lingayen" selected>Lingayen</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Barangay *</label>
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
                                <label class="form-label small">House No./Zone/Purok/Sitio *</label>
                                <input type="text" name="residence" class="form-control form-control-sm" placeholder="House No./Zone/Purok/Sitio" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Street</label>
                                <input type="text" name="street" class="form-control form-control-sm" placeholder="Street">
                            </div>
                        </div>
                    </div>


                    <div class="mb-4">
                       <div class="col-md-3">
                             <label class="form-label fw-bold">3. Date of Birth *</label>
                            <input type="date" name="date_of_birth" class="form-control form-control-sm" required>
                        </div>
                    </div>


                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label small">4. Place of Birth *</label>
                            <input type="text" name="birth_place" class="form-control form-control-sm" placeholder="Place of Birth" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">5. Marital Status *</label>
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
                            <label class="form-label small">6. Gender *</label>
                            <select name="sex" class="form-select form-select-sm" required>
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">7. Contact Number *</label>
                            <input type="tel" name="contact_number" class="form-control form-control-sm" placeholder="Contact Number" required>
                        </div>
                    </div>


                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label small">8. Email Address *</label>
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="Email Address" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">9. Religion *</label>
                            <select name="religion" id="religion" class="form-select form-select-sm" required>
                                <option value="">Select Religion</option>
                                <option value="Catholic">Catholic</option>
                                <option value="Protestant">Protestant</option>
                                <option value="Islam">Islam</option>
                                <option value="Buddhism">Buddhism</option>
                                <option value="Others">Others</option>
                            </select>
                            <div class="invalid-feedback" id="religion-error" style="display: none;">
                                Please select a religion.
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">10. Ethnic Origin *</label>
                            <input type="text" name="ethnic_origin" id="ethnic_origin" class="form-control form-control-sm" placeholder="Ethnic Origin" required>
                            <div class="invalid-feedback" id="ethnic_origin-error" style="display: none;">
                                Please enter ethnic origin.
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">11. Language Spoken *</label>
                            <input type="text" name="language" class="form-control form-control-sm" placeholder="Language Spoken" required>
                        </div>
                    </div>


                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small">12. OSCA ID No. *</label>
                            <input type="text" name="osca_id" class="form-control form-control-sm" placeholder="OSCA ID Number" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">13. GSIS/SSS No.</label>
                            <input type="text" name="gsis_sss" class="form-control form-control-sm" placeholder="GSIS/SSS Number">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">14. TIN</label>
                            <input type="text" name="tin" class="form-control form-control-sm" placeholder="Tax Identification Number">
                        </div>
                    </div>


                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small">15. PhilHealth No.</label>
                            <input type="text" name="philhealth" class="form-control form-control-sm" placeholder="PhilHealth Number">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">16. SC Association ID No.</label>
                            <input type="text" name="sc_association" class="form-control form-control-sm" placeholder="Senior Citizen Association ID">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">17. Other Gov't ID No.</label>
                            <input type="text" name="other_govt_id" class="form-control form-control-sm" placeholder="Other Government ID">
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
                            <label class="form-label small">20. Has Pension</label>
                            <select name="has_pension" class="form-select form-select-sm">
                                <option value="">Select</option>
                                <option value="1">With Pension</option>
                                <option value="0">Without Pension</option>
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 1; $i <= 5; $i++)
                                    <tr>
                                        <td><input type="text" name="child_name_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Child Name"></td>
                                        <td><input type="text" name="child_occupation_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Occupation"></td>
                                        <td><input type="text" name="child_income_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Income"></td>
                                        <td><input type="number" name="child_age_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Age"></td>
                                        <td><select name="child_working_{{ $i }}" class="form-select form-select-sm border-0">
                                            <option value="">Is working?</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select></td>
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 1; $i <= 2; $i++)
                                    <tr>
                                        <td><input type="text" name="dependent_name_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Name of Dependent"></td>
                                        <td><input type="text" name="dependent_occupation_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Occupation of Dependent"></td>
                                        <td><input type="text" name="dependent_income_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Income"></td>
                                        <td><input type="number" name="dependent_age_{{ $i }}" class="form-control form-control-sm border-0" placeholder="Age"></td>
                                        <td><select name="dependent_working_{{ $i }}" class="form-select form-select-sm border-0">
                                            <option value="">Is Working?</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select></td>
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
                                    <input type="number" name="monthly_income" class="form-control form-control-sm" placeholder="Enter monthly income amount" value="{{ old('monthly_income', 0) }}" min="0" step="0.01">
                                    <small class="text-muted">Please enter the exact monthly income amount</small>
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
                                    <label class="field-label">Blood Type*</label>
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
                                <select name="scheduled_checkup" class="form-control mt-3">
                                    <option value="">Select</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="input-label">45. If Yes, when is it done?</label>
                                <select name="checkup_frequency" class="form-control mt-3">
                                    <option value="">Select</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Quarterly">Quarterly</option>
                                    <option value="Semi-annually">Semi-annually</option>
                                    <option value="Annually">Annually</option>
                                    <option value="As needed">As needed</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="input-label">46. Status *</label>
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
                                        <x-photo-upload id="senior_photo" name="senior_photo" />
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
            padding: 1.5rem 2rem;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
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
        
        function changeStep(direction) {
            const newStep = currentStep + direction;
            
            if (newStep >= 1 && newStep <= totalSteps) {
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
            
            // Submit the form directly
            const form = document.querySelector('form[method="POST"]');
            if (form) {
                form.submit();
            } else {
                console.error('Form not found');
            }
        }
        
        function submitForm() {
            
        // Google Vision OCR JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            const ocrFileUpload = document.getElementById('ocrFileUpload');
            const processOcrBtn = document.getElementById('processOcrBtn');
            const ocrStatus = document.getElementById('ocrStatus');
            const ocrProgressBar = document.getElementById('ocrProgressBar');
            const ocrStatusText = document.getElementById('ocrStatusText');
            const ocrResults = document.getElementById('ocrResults');
            const ocrResultsList = document.getElementById('ocrResultsList');
            
            // Process OCR button click handler
            processOcrBtn.addEventListener('click', function() {
                const file = ocrFileUpload.files[0];
                if (!file) {
                    alert('Please select a file first');
                    return;
                }
                
                // Show processing status
                ocrStatus.style.display = 'block';
                ocrResults.style.display = 'none';
                ocrProgressBar.style.width = '0%';
                ocrStatusText.textContent = 'Processing...';
                
                // Create form data
                const formData = new FormData();
                formData.append('form_image', file);
                
                // Simulate progress
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 5;
                    if (progress > 90) {
                        clearInterval(progressInterval);
                    }
                    ocrProgressBar.style.width = progress + '%';
                }, 100);
                
                // Send to backend for processing
                fetch('/api/vision/process-form', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    clearInterval(progressInterval);
                    ocrProgressBar.style.width = '100%';
                    
                    if (data.success) {
                        ocrStatusText.textContent = 'Processing complete!';
                        
                        // Display extracted data
                        ocrResults.style.display = 'block';
                        ocrResultsList.innerHTML = '';
                        
                        const formData = data.data;
                        let resultHtml = '<div class="mb-3">';
                        
                        for (const [key, value] of Object.entries(formData)) {
                            if (value) {
                                const fieldName = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                                resultHtml += `<div class="mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">${fieldName}:</span>
                                        <span class="fw-bold">${value}</span>
                                    </div>
                                </div>`;
                            }
                        }
                        
                        resultHtml += `<button type="button" class="btn btn-success btn-sm w-100 mt-2" id="applyOcrDataBtn">
                            <i class="fas fa-check me-2"></i>Apply to Form
                        </button>`;
                        resultHtml += '</div>';
                        
                        ocrResultsList.innerHTML = resultHtml;
                        
                        // Add event listener for apply button
                        document.getElementById('applyOcrDataBtn').addEventListener('click', function() {
                            applyExtractedData(formData);
                        });
                    } else {
                        ocrStatusText.textContent = 'Error: ' + data.message;
                    }
                })
                .catch(error => {
                    clearInterval(progressInterval);
                    ocrProgressBar.style.width = '100%';
                    ocrStatusText.textContent = 'Error: ' + error.message;
                });
            });
            
            // Function to apply extracted data to form fields
            function applyExtractedData(data) {
                // Map data to form fields
                if (data.last_name) document.querySelector('input[name="last_name"]').value = data.last_name;
                if (data.first_name) document.querySelector('input[name="first_name"]').value = data.first_name;
                if (data.middle_name) document.querySelector('input[name="middle_name"]').value = data.middle_name;
                
                // Handle birth date (may need formatting)
                if (data.birth_date) {
                    const birthDateInput = document.querySelector('input[name="birth_date"]');
                    if (birthDateInput) {
                        // Try to convert to YYYY-MM-DD format if needed
                        let formattedDate = data.birth_date;
                        
                        // Check if it's in MM/DD/YYYY format
                        const dateMatch = data.birth_date.match(/(\d{1,2})\/(\d{1,2})\/(\d{4})/);
                        if (dateMatch) {
                            formattedDate = `${dateMatch[3]}-${dateMatch[1].padStart(2, '0')}-${dateMatch[2].padStart(2, '0')}`;
                        }
                        
                        birthDateInput.value = formattedDate;
                    }
                }
                
                // Handle address fields
                if (data.address) {
                    const addressInput = document.querySelector('input[name="address"], textarea[name="address"]');
                    if (addressInput) addressInput.value = data.address;
                }
                
                // Handle senior citizen ID
                if (data.senior_citizen_id) {
                    const idInput = document.querySelector('input[name="senior_citizen_id"]');
                    if (idInput) idInput.value = data.senior_citizen_id;
                }
                
                // Handle contact number
                if (data.contact_number) {
                    const contactInput = document.querySelector('input[name="contact_number"]');
                    if (contactInput) contactInput.value = data.contact_number;
                }
                
                // Handle marital status
                if (data.marital_status) {
                    const maritalStatusSelect = document.querySelector('select[name="marital_status"]');
                    if (maritalStatusSelect) {
                        // Find the option that matches (case-insensitive)
                        const options = Array.from(maritalStatusSelect.options);
                        const matchingOption = options.find(option => 
                            option.text.toLowerCase() === data.marital_status.toLowerCase()
                        );
                        
                        if (matchingOption) {
                            maritalStatusSelect.value = matchingOption.value;
                        }
                    }
                }
                
                // Show success message
                alert('Form fields have been filled with the extracted data!');
            }
        });
            // This function is now replaced by showCertificationModal
            showCertificationModal();
        }
        
        // Initialize form on page load
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
            
            if (certificationCheckbox && finalSubmitBtn) {
                certificationCheckbox.addEventListener('change', function() {
                    finalSubmitBtn.disabled = !this.checked;
                });
            }
            
            // Add form validation for religion and ethnic origin
            const form = document.querySelector('form[action="{{ route('seniors.store') }}"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    let isValid = true;
                    let errorMessage = '';
                    
                    // Validate religion
                    const religionSelect = document.getElementById('religion');
                    const religionError = document.getElementById('religion-error');
                    if (!religionSelect.value || religionSelect.value === '') {
                        religionSelect.classList.add('is-invalid');
                        religionError.style.display = 'block';
                        isValid = false;
                        errorMessage += '• Please select a religion.\n';
                    } else {
                        religionSelect.classList.remove('is-invalid');
                        religionError.style.display = 'none';
                    }
                    
                    // Validate ethnic origin
                    const ethnicOriginInput = document.getElementById('ethnic_origin');
                    const ethnicOriginError = document.getElementById('ethnic_origin-error');
                    if (!ethnicOriginInput.value || ethnicOriginInput.value.trim() === '') {
                        ethnicOriginInput.classList.add('is-invalid');
                        ethnicOriginError.style.display = 'block';
                        isValid = false;
                        errorMessage += '• Please enter ethnic origin.\n';
                    } else {
                        ethnicOriginInput.classList.remove('is-invalid');
                        ethnicOriginError.style.display = 'none';
                    }
                    
                    if (!isValid) {
                        // Show error message using custom modal
                        showValidationErrorModal('Validation Error', errorMessage);
                        return false;
                    }
                    
                    // If validation passes, submit the form
                    form.submit();
                });
            }
        });
    </script>
  </x-header>
</x-sidebar>