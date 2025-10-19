<x-sidebar>
  <x-header title="EDIT SENIOR CITIZEN INFORMATION" icon="fas fa-user-edit">
    @include('message.popup_message')
    <div class="main">
        <div class="form">
            <div class="form-content">
                <div class="form-section">
                   
                        <div class="d-flex justify-content-between align-items-center mb-3">
                           <img src="{{ asset('images/OSCA.png') }}" alt="OSCA Logo" class="logo-osca" style="max-height: 60px;">
                            <img src="{{ asset('images/Bagong_Pilipinas.png') }}" alt="Bagong Pilipinas Logo" class="logo-bagong-pilipinas" style="max-height: 80px;">
                            <div class="text-center flex-grow-1">
                                <div class="title-main" style="color: #e31575; font-size: 24px; font-weight: 800;">SENIOR CITIZEN</div>
                                <div class="title-main" style="color: #e31575; font-size: 20px; font-weight: 800;">INFORMATION </div>
                            </div>
                        </div>
                        <!-- Pink line separator -->
                        <div style="height: 5px; background-color: #e31575; width: 100%; margin: 0 0 30px 0;"></div>

                        <!-- Success/Error Messages -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 20px;">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 20px;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 20px;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Please correct the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Required Fields Notice -->
                        <div class="alert alert-info mb-4" style="background-color: #e3f2fd; border: 1px solid #2196f3; color: #1976d2;">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Fields marked with <span class="text-danger fw-bold">*</span> are required and must be filled out.
                        </div>

                        <div class="mb-3">
                             <x-photo-upload id="photo_upload" name="photo" value="{{ $senior->photo_path }}" />
                        </div>

                          
              

            <form method="POST" action="{{ route('edit_senior.update', $senior->id) }}" enctype="multipart/form-data" id="editSeniorForm">
                @csrf
                @method('PUT')
                

                <!-- I. IDENTIFYING INFORMATION -->
                <div class="section-header">I. IDENTIFYING INFORMATION</div>

                <div class="form-section-content">
                    <!-- Name Fields -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small mb-3">1. NAME OF SENIOR CITIZEN</label>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control form-control-sm" placeholder="Last Name" value="{{ old('last_name') ?: $senior->last_name }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control form-control-sm" placeholder="First Name" value="{{ old('first_name') ?: $senior->first_name }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Middle Name</label>
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
                    </div>

                    <!-- Address -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small mb-3">2. ADDRESS</label>
                        <div class="row g-2 mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Region *</label>
                                <select name="region" class="form-control form-control-sm" required>
                                   <option value="Region I" {{ (old('region') ?: $senior->region) == 'Region I' ? 'selected' : '' }}> Region I - Ilocos Region</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Province *</label>
                                <select name="province" class="form-control form-control-sm" required>
                                    <option value="Pangasinan" {{ (old('province') ?: $senior->province) == 'Pangasinan' ? 'selected' : '' }}>Pangasinan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">City *</label>
                                <select name="city" class="form-control form-control-sm" required>
                                    <option value="Lingayen" {{ (old('city') ?: $senior->city) == 'Lingayen' ? 'selected' : '' }}>Lingayen</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Barangay *</label>
                                <select name="barangay" class="form-select form-select-sm" required>
                                    <option value="">Select Barangay</option>
                                    <option value="aliwekwek" {{ (old('barangay') ?: $senior->barangay) == 'aliwekwek' ? 'selected' : '' }}>Aliwekwek</option>
                                    <option value="baay" {{ (old('barangay') ?: $senior->barangay) == 'baay' ? 'selected' : '' }}>Baay</option>
                                    <option value="balangobong" {{ (old('barangay') ?: $senior->barangay) == 'balangobong' ? 'selected' : '' }}>Balangobong</option>
                                    <option value="balococ" {{ (old('barangay') ?: $senior->barangay) == 'balococ' ? 'selected' : '' }}>Balococ</option>
                                    <option value="bantayan" {{ (old('barangay') ?: $senior->barangay) == 'bantayan' ? 'selected' : '' }}>Bantayan</option>
                                    <option value="basing" {{ (old('barangay') ?: $senior->barangay) == 'basing' ? 'selected' : '' }}>Basing</option>
                                    <option value="capandanan" {{ (old('barangay') ?: $senior->barangay) == 'capandanan' ? 'selected' : '' }}>Capandanan</option>
                                    <option value="domalandan-center" {{ (old('barangay') ?: $senior->barangay) == 'domalandan-center' ? 'selected' : '' }}>Domalandan Center</option>
                                    <option value="domalandan-east" {{ (old('barangay') ?: $senior->barangay) == 'domalandan-east' ? 'selected' : '' }}>Domalandan East</option>
                                    <option value="domalandan-west" {{ (old('barangay') ?: $senior->barangay) == 'domalandan-west' ? 'selected' : '' }}>Domalandan West</option>
                                    <option value="dorongan" {{ (old('barangay') ?: $senior->barangay) == 'dorongan' ? 'selected' : '' }}>Dorongan</option>
                                    <option value="dulag" {{ (old('barangay') ?: $senior->barangay) == 'dulag' ? 'selected' : '' }}>Dulag</option>
                                    <option value="estanza" {{ (old('barangay') ?: $senior->barangay) == 'estanza' ? 'selected' : '' }}>Estanza</option>
                                    <option value="lasip" {{ (old('barangay') ?: $senior->barangay) == 'lasip' ? 'selected' : '' }}>Lasip</option>
                                    <option value="libsong-east" {{ (old('barangay') ?: $senior->barangay) == 'libsong-east' ? 'selected' : '' }}>Libsong East</option>
                                    <option value="libsong-west" {{ (old('barangay') ?: $senior->barangay) == 'libsong-west' ? 'selected' : '' }}>Libsong West</option>
                                    <option value="malawa" {{ (old('barangay') ?: $senior->barangay) == 'malawa' ? 'selected' : '' }}>Malawa</option>
                                    <option value="malimpuec" {{ (old('barangay') ?: $senior->barangay) == 'malimpuec' ? 'selected' : '' }}>Malimpuec</option>
                                    <option value="maniboc" {{ (old('barangay') ?: $senior->barangay) == 'maniboc' ? 'selected' : '' }}>Maniboc</option>
                                    <option value="matalava" {{ (old('barangay') ?: $senior->barangay) == 'matalava' ? 'selected' : '' }}>Matalava</option>
                                    <option value="naguelguel" {{ (old('barangay') ?: $senior->barangay) == 'naguelguel' ? 'selected' : '' }}>Naguelguel</option>
                                    <option value="namolan" {{ (old('barangay') ?: $senior->barangay) == 'namolan' ? 'selected' : '' }}>Namolan</option>
                                    <option value="pangapisan-north" {{ (old('barangay') ?: $senior->barangay) == 'pangapisan-north' ? 'selected' : '' }}>Pangapisan North</option>
                                    <option value="pangapisan-sur" {{ (old('barangay') ?: $senior->barangay) == 'pangapisan-sur' ? 'selected' : '' }}>Pangapisan Sur</option>
                                    <option value="poblacion" {{ (old('barangay') ?: $senior->barangay) == 'poblacion' ? 'selected' : '' }}>Poblacion</option>
                                    <option value="quibaol" {{ (old('barangay') ?: $senior->barangay) == 'quibaol' ? 'selected' : '' }}>Quibaol</option>
                                    <option value="rosario" {{ (old('barangay') ?: $senior->barangay) == 'rosario' ? 'selected' : '' }}>Rosario</option>
                                    <option value="sabangan" {{ (old('barangay') ?: $senior->barangay) == 'sabangan' ? 'selected' : '' }}>Sabangan</option>
                                    <option value="talogtog" {{ (old('barangay') ?: $senior->barangay) == 'talogtog' ? 'selected' : '' }}>Talogtog</option>
                                    <option value="tonton" {{ (old('barangay') ?: $senior->barangay) == 'tonton' ? 'selected' : '' }}>Tonton</option>
                                    <option value="tumbar" {{ (old('barangay') ?: $senior->barangay) == 'tumbar' ? 'selected' : '' }}>Tumbar</option>
                                    <option value="wawa" {{ (old('barangay') ?: $senior->barangay) == 'wawa' ? 'selected' : '' }}>Wawa</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">House No./Zone/Purok/Sitio *</label>
                                <input type="text" name="residence" class="form-control form-control-sm" placeholder="House No./Zone/Purok/Sitio" value="{{ old('residence') ?: $senior->residence }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Street</label>
                                <input type="text" name="street" class="form-control form-control-sm" placeholder="Street" value="{{ old('street', $senior->street) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Birth Date -->
                    <div class="mb-4">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">3. DATE OF BIRTH *</label>
                                <input type="date" name="date_of_birth" class="form-control form-control-sm" value="{{ old('date_of_birth') ?: ($senior->date_of_birth ? \Carbon\Carbon::parse($senior->date_of_birth)->format('Y-m-d') : '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">4. PLACE OF BIRTH *</label>
                                <input type="text" name="birth_place" class="form-control form-control-sm" placeholder="Place of Birth" value="{{ old('birth_place') ?: $senior->birth_place }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="row g-2 mb-3">
                           
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">5. MARITAL STATUS *</label>
                                <select name="marital_status" class="form-control form-control-sm" required>
                                    <option value="">Select Marital Status</option>
                                    <option value="Single" {{ (old('marital_status') ?: $senior->marital_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ (old('marital_status') ?: $senior->marital_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Widowed" {{ (old('marital_status') ?: $senior->marital_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="Separated" {{ (old('marital_status') ?: $senior->marital_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                                    <option value="Others" {{ (old('marital_status') ?: $senior->marital_status) == 'Others' ? 'selected' : '' }}>Others</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">6. GENDER *</label>
                                <select name="sex" class="form-control form-control-sm" required>
                                    <option value="">Select</option>
                                    <option value="Male" {{ (old('sex') ?: $senior->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ (old('sex') ?: $senior->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2">
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">7. CONTACT NUMBER *</label>
                                <input type="tel" name="contact_number" class="form-control form-control-sm" placeholder="Contact Number" value="{{ old('contact_number') ?: $senior->contact_number }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">8. EMAIL ADDRESS *</label>
                                <input type="email" name="email" class="form-control form-control-sm" placeholder="Email Address" value="{{ old('email') ?: $senior->email }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="mb-4">
                        <div class="row g-2">
                           
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">9. RELIGION *</label>
                                <select name="religion" id="religion" class="form-control form-control-sm" required>
                                    <option value="">Select Religion</option>
                                <option value="Catholic" {{ old('religion', $senior->religion) == 'Catholic' ? 'selected' : '' }}>Catholic</option>
                                <option value="Protestant" {{ old('religion', $senior->religion) == 'Protestant' ? 'selected' : '' }}>Protestant</option>
                                <option value="Islam" {{ old('religion', $senior->religion) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Buddhism" {{ old('religion', $senior->religion) == 'Buddhism' ? 'selected' : '' }}>Buddhism</option>
                                <option value="Others" {{ old('religion', $senior->religion) == 'Others' ? 'selected' : '' }}>Others</option>
                                </select>
                                <div class="invalid-feedback" id="religion-error" style="display: none;">
                                    Please select a religion.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">10. ETHNIC ORIGIN *</label>
                                <input type="text" name="ethnic_origin" id="ethnic_origin" class="form-control form-control-sm" placeholder="Ethnic Origin" value="{{ old('ethnic_origin', $senior->ethnic_origin) }}" required>
                                <div class="invalid-feedback" id="ethnic_origin-error" style="display: none;">
                                    Please enter ethnic origin.
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                           
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">11. LANGUAGE SPOKEN / WRITTEN *</label>
                                <input type="text" name="language" class="form-control form-control-sm" placeholder="Language Spoken" value="{{ old('language') ?: $senior->language }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- ID Numbers -->
                    <div class="mb-4">
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">12. OSCA ID NO. *</label>
                                <input type="text" name="osca_id" class="form-control form-control-sm" placeholder="OSCA ID Number" value="{{ old('osca_id') ?: $senior->osca_id }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">13. GSIS/SSS NO.</label>
                                <input type="text" name="gsis_sss" class="form-control form-control-sm" placeholder="GSIS/SSS Number" value="{{ old('gsis_sss', $senior->gsis_sss) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">14. TIN</label>
                                <input type="text" name="tin" class="form-control form-control-sm" placeholder="Tax Identification Number" value="{{ old('tin', $senior->tin) }}">
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">15. PHILHEALTH NO.</label>
                                <input type="text" name="philhealth" class="form-control form-control-sm" placeholder="PhilHealth Number" value="{{ old('philhealth', $senior->philhealth) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">16. SC ASSOCIATION / ORG ID NO.</label>
                                <input type="text" name="sc_association" class="form-control form-control-sm" placeholder="Senior Citizen Association ID" value="{{ old('sc_association', $senior->sc_association) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">17. OTHER GOV'T ID NO.</label>
                                <input type="text" name="other_govt_id" class="form-control form-control-sm" placeholder="Other Government ID" value="{{ old('other_govt_id', $senior->other_govt_id) }}">
                            </div>
                        </div>
                    </div>
                    <!-- Employment Information -->
                    <div class="mb-4">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">18. CAPABILITY TO TRAVEL</label>
                                <select name="can_travel" class="form-control form-control-sm">
                                    <option value="">Select</option>
                                    <option value="Yes" {{ old('can_travel', $senior->can_travel) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="No" {{ old('can_travel', $senior->can_travel) == 'No' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">19. SERVICE/BUSINESS/EMPLOYMENT</label>
                                <input type="text" name="employment" class="form-control form-control-sm" placeholder="Specify" value="{{ old('employment', $senior->employment) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">20. PENSION STATUS</label>
                                <select name="has_pension" class="form-control form-control-sm">
                                    <option value="">Select</option>
                                    <option value="1" {{ old('has_pension', $senior->has_pension) == 1 ? 'selected' : '' }}>With Pension</option>
                                    <option value="0" {{ old('has_pension', $senior->has_pension) == 0 ? 'selected' : '' }}>Without Pension</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- II. FAMILY COMPOSITION -->
                <div class="section-header">II. FAMILY COMPOSITION</div>
                
                <div class="mb-4">
                    <!-- 21. Name of your spouse -->
                    <div class="mb-4">
                       <label class="form-label fw-bold small">21. NAME OF SPOUSE</label>
                        <div class="row g-2 mb-3">
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

                    <!-- 22. Name of your father -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">22. FATHER'S NAME</label>
                        <div class="row g-2 mb-3">
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

                    <!-- 23. Name of your mother -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">23. MOTHER'S MAIDEN NAME</label>
                        <div class="row g-2 mb-3">
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
                </div>

                <!-- III. EDUCATION / HR PROFILE -->
                <div class="section-header">III. EDUCATION / HR PROFILE</div>
                
                <div class="mb-4">
                    <!-- 26. Highest Educational Attainment -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">26. EDUCATIONAL ATTAINMENT</label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="Not Attended School" id="edu1" {{ old('education_level', $senior->education_level) == 'Not Attended School' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu1">Not Attended School</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="Elementary Level" id="edu2" {{ old('education_level', $senior->education_level) == 'Elementary Level' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu2">Elementary Level</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="Elementary Graduate" id="edu3" {{ old('education_level', $senior->education_level) == 'Elementary Graduate' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu3">Elementary Graduate</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="Highschool Level" id="edu4" {{ old('education_level', $senior->education_level) == 'Highschool Level' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu4">Highschool Level</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="Highschool Graduate" id="edu5" {{ old('education_level', $senior->education_level) == 'Highschool Graduate' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu5">Highschool Graduate</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="Vocational" id="edu6" {{ old('education_level', $senior->education_level) == 'Vocational' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu6">Vocational</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="College Level" id="edu7" {{ old('education_level', $senior->education_level) == 'College Level' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu7">College Level</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="College Graduate" id="edu8" {{ old('education_level', $senior->education_level) == 'College Graduate' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu8">College Graduate</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="Post Graduate" id="edu9" {{ old('education_level', $senior->education_level) == 'Post Graduate' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu9">Post Graduate</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 28. Shared Skills -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">28. SHARE SKILLS (COMMUNITY SERVICE)</label>
                        <textarea name="shared_skills" class="form-control form-control-sm" placeholder="Type skills here separated by comma" rows="3">{{ old('shared_skills', $senior->shared_skills) }}</textarea>
                    </div>
                </div>

                <!-- IV. DEPENDENCY PROFILE -->
                <div class="section-header">IV. DEPENDENCY PROFILE</div>
                <div class="form-section">
                    <!-- Questions 30 & 31: Living Condition and Household Condition -->
                    <div class="row g-3">
                        <!-- Question 30: Living Condition -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">30. LIVING/RESIDING WITH (CHECK ALL APPLICABLE)</label>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="radio" name="living_condition_primary" value="Living Alone" class="form-check-input" id="living_alone" {{ old('living_condition_primary', $senior->living_condition_primary) == 'Living Alone' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="living_alone">Living Alone</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="living_condition_primary" value="Living with" class="form-check-input" id="living_with" {{ old('living_condition_primary', $senior->living_condition_primary) == 'Living with' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="living_with">Living with</label>
                                </div>
                                <div id="living_with_options" class="mt-2 ms-4">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <div class="form-check form-check-sm">
                                                <input type="checkbox" name="living_with[]" value="Grand Children" class="form-check-input" id="living_grandchildren" {{ in_array('Grand Children', old('living_with', $senior->living_with ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="living_grandchildren">Grand Children</label>
                                            </div>
                                            <div class="form-check form-check-sm">
                                                <input type="checkbox" name="living_with[]" value="Common Law Spouse" class="form-check-input" id="living_commonlaw" {{ in_array('Common Law Spouse', old('living_with', $senior->living_with ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="living_commonlaw">Common Law Spouse</label>
                                            </div>
                                            <div class="form-check form-check-sm">
                                                <input type="checkbox" name="living_with[]" value="Spouse" class="form-check-input" id="living_spouse" {{ in_array('Spouse', old('living_with', $senior->living_with ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="living_spouse">Spouse</label>
                                            </div>
                                            <div class="form-check form-check-sm">
                                                <input type="checkbox" name="living_with[]" value="In-laws" class="form-check-input" id="living_inlaws" {{ in_array('In-laws', old('living_with', $senior->living_with ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="living_inlaws">In-laws</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-check-sm">
                                                <input type="checkbox" name="living_with[]" value="Care Institution" class="form-check-input" id="living_institution" {{ in_array('Care Institution', old('living_with', $senior->living_with ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="living_institution">Care Institution</label>
                                            </div>
                                            <div class="form-check form-check-sm">
                                                <input type="checkbox" name="living_with[]" value="Children" class="form-check-input" id="living_children" {{ in_array('Children', old('living_with', $senior->living_with ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="living_children">Children</label>
                                            </div>
                                            <div class="form-check form-check-sm">
                                                <input type="checkbox" name="living_with[]" value="Relatives" class="form-check-input" id="living_relatives" {{ in_array('Relatives', old('living_with', $senior->living_with ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="living_relatives">Relatives</label>
                                            </div>
                                            <div class="form-check form-check-sm">
                                                <input type="checkbox" name="living_with[]" value="Friends" class="form-check-input" id="living_friends" {{ in_array('Friends', old('living_with', $senior->living_with ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="living_friends">Friends</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <div class="form-check form-check-sm">
                                            <input type="checkbox" name="living_with[]" value="Others" class="form-check-input" id="living_others" {{ in_array('Others', old('living_with', $senior->living_with ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="living_others">Others</label>
                                        </div>
                                        <input type="text" name="living_with_others_specify" class="form-control form-control-sm mt-1" placeholder="Specify" value="{{ old('living_with_others_specify') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Question 31: Household Condition -->
                        <div class="col-md-6">
                           <label class="form-label fw-bold small">31. HOUSEHOLD CONDITION (CHECK ALL APPLICABLE)</label>
                            <div class="mb-3">
                                <div class="form-check form-check-sm">
                                    <input type="checkbox" name="household_condition[]" value="No privacy" class="form-check-input" id="household_privacy" {{ in_array('No privacy', old('household_condition', $senior->household_condition ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="household_privacy">No privacy</label>
                                </div>
                                <div class="form-check form-check-sm">
                                    <input type="checkbox" name="household_condition[]" value="Overcrowded in home" class="form-check-input" id="household_overcrowded" {{ in_array('Overcrowded in home', old('household_condition', $senior->household_condition ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="household_overcrowded">Overcrowded in home</label>
                                </div>
                                <div class="form-check form-check-sm">
                                    <input type="checkbox" name="household_condition[]" value="Informal Settler" class="form-check-input" id="household_informal" {{ in_array('Informal Settler', old('household_condition', $senior->household_condition ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="household_informal">Informal Settler</label>
                                </div>
                                <div class="form-check form-check-sm">
                                    <input type="checkbox" name="household_condition[]" value="No permanent house" class="form-check-input" id="household_nopermanent" {{ in_array('No permanent house', old('household_condition', $senior->household_condition ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="household_nopermanent">No permanent house</label>
                                </div>
                                <div class="form-check form-check-sm">
                                    <input type="checkbox" name="household_condition[]" value="High cost of rent" class="form-check-input" id="household_rent" {{ in_array('High cost of rent', old('household_condition', $senior->household_condition ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="household_rent">High cost of rent</label>
                                </div>
                                <div class="form-check form-check-sm">
                                    <input type="checkbox" name="household_condition[]" value="Longing for independent living quiet atmosphere" class="form-check-input" id="household_independent" {{ in_array('Longing for independent living quiet atmosphere', old('household_condition', $senior->household_condition ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="household_independent">Longing for independent living quiet atmosphere</label>
                                </div>
                                <div class="form-check form-check-sm">
                                    <input type="checkbox" name="household_condition[]" value="Others" class="form-check-input" id="household_others" {{ in_array('Others', old('household_condition', $senior->household_condition ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="household_others">Others</label>
                                </div>
                                <input type="text" name="household_condition_others_specify" id="household_others_input" class="form-control form-control-sm mt-1" placeholder="Specify" value="{{ old('household_condition_others_specify') }}">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- V. ECONOMIC PROFILE -->
                <div class="section-header">V. ECONOMIC PROFILE</div>
                <div class="mb-4">
                    <!-- Question 32: Source of Income and Assistance -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">32. SOURCE OF INCOME AND ASSISTANCE (CHECK ALL APPLICABLE)</label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Own earnings, salary / wages" class="form-check-input" id="income_earnings" {{ in_array('Own earnings, salary / wages', old('source_of_income', $senior->source_of_income ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_earnings">Own earnings, salary / wages</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Own Pension" class="form-check-input" id="income_pension" {{ in_array('Own Pension', old('source_of_income', $senior->source_of_income ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_pension">Own Pension</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Stocks / Dividends" class="form-check-input" id="income_stocks" {{ in_array('Stocks / Dividends', old('source_of_income', $senior->source_of_income ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_stocks">Stocks / Dividends</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Dependent on children / relatives" class="form-check-input" id="income_dependent" {{ in_array('Dependent on children / relatives', old('source_of_income', $senior->source_of_income ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_dependent">Dependent on children / relatives</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Spouse's salary" class="form-check-input" id="income_spouse_salary" {{ in_array('Spouse\'s salary', old('source_of_income', $senior->source_of_income ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_spouse_salary">Spouse's salary</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Spouse Pension" class="form-check-input" id="income_spouse_pension" {{ in_array('Spouse Pension', old('source_of_income', $senior->source_of_income ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_spouse_pension">Spouse Pension</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Insurance" class="form-check-input" id="income_insurance" {{ in_array('Insurance', old('source_of_income', $senior->source_of_income ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_insurance">Insurance</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Rental / Sharecorp" class="form-check-input" id="income_rental" {{ in_array('Rental / Sharecorp', old('source_of_income', $senior->source_of_income ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_rental">Rental / Sharecorp</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Savings" class="form-check-input" id="income_savings" {{ in_array('Savings', old('source_of_income', $senior->source_of_income ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_savings">Savings</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Livestock / orchard / farm" class="form-check-input" id="income_livestock" {{ in_array('Livestock / orchard / farm', old('source_of_income', $senior->source_of_income ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_livestock">Livestock / orchard / farm</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Fishing" class="form-check-input" id="income_fishing" {{ in_array('Fishing', old('source_of_income', $senior->source_of_income ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_fishing">Fishing</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Others" class="form-check-input" id="income_others">
                                    <label class="form-check-label small" for="income_others">Others, Specify</label>
                                </div>
                                <input type="text" name="source_of_income_others" id="source_income_others_input" placeholder="Specify" class="form-control form-control-sm mt-2" value="{{ old('source_of_income_others') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Question 35: Monthly Income -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">35. MONTHLY INCOME (IN PHILIPPINE PESO)</label>
                                <input type="number" name="monthly_income" class="form-control form-control-sm" placeholder="Enter monthly income amount" value="{{ old('monthly_income', $senior->monthly_income ?? '') }}" min="0" step="0.01">
                                <small class="form-text text-muted">Enter the exact amount in Philippine Peso (e.g., 25000, 50000.50)</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- VI. HEALTH PROFILE -->
                <div class="section-header">VI. HEALTH PROFILE</div>
                <div class="mb-4">
                    <div class="row g-3">
                        <!-- Question 37: Medical Concern -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">37. MEDICAL CONCERN </label>
                                
                                <div class="mb-3">
                                    <label class="form-label small">Blood Type*</label>
                                    <select name="blood_type" class="form-select form-select-sm">
                                        <option value="">Select Blood Type</option>
                                        <option value="O+" {{ old('blood_type', $senior->blood_type) == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ old('blood_type', $senior->blood_type) == 'O-' ? 'selected' : '' }}>O-</option>

                                        <option value="A+" {{ old('blood_type', $senior->blood_type) == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ old('blood_type', $senior->blood_type) == 'A-' ? 'selected' : '' }}>A-</option>

                                        <option value="B+" {{ old('blood_type', $senior->blood_type) == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ old('blood_type', $senior->blood_type) == 'B-' ? 'selected' : '' }}>B-</option>

                                        <option value="AB+" {{ old('blood_type', $senior->blood_type) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ old('blood_type', $senior->blood_type) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                        <option value="DON'T KNOW" {{ old('blood_type', $senior->blood_type) == 'DON\'T KNOW' ? 'selected' : '' }}>DON'T KNOW</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label small">PHYSICAL DISABILITY</label>
                                    <input type="text" name="physical_disability" class="form-control form-control-sm" placeholder="Specify" value="{{ old('physical_disability', $senior->physical_disability) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small">HEALTH PROBLEMS / AILMENTS</label>
                                    <div class="d-flex flex-column gap-1">
                                        <div class="form-check">
                                            <input type="checkbox" name="health_problems[]" value="Hypertension" class="form-check-input" id="health_hypertension" {{ in_array('Hypertension', old('health_problems', $senior->health_problems ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="health_hypertension">Hypertension</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="health_problems[]" value="Arthritis / Gout" class="form-check-input" id="health_arthritis" {{ in_array('Arthritis / Gout', old('health_problems', $senior->health_problems ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="health_arthritis">Arthritis / Gout</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="health_problems[]" value="Coronary Heart Disease" class="form-check-input" id="health_heart" {{ in_array('Coronary Heart Disease', old('health_problems', $senior->health_problems ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="health_heart">Coronary Heart Disease</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="health_problems[]" value="Diabetes" class="form-check-input" id="health_diabetes" {{ in_array('Diabetes', old('health_problems', $senior->health_problems ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="health_diabetes">Diabetes</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="health_problems[]" value="Chronic Kidney Disease" class="form-check-input" id="health_kidney" {{ in_array('Chronic Kidney Disease', old('health_problems', $senior->health_problems ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="health_kidney">Chronic Kidney Disease</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="health_problems[]" value="Alzheimer's / Dementia" class="form-check-input" id="health_alzheimer" {{ in_array('Alzheimer\'s / Dementia', old('health_problems', $senior->health_problems ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="health_alzheimer">Alzheimer's / Dementia</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="health_problems[]" value="Chronic Obstructive Pulmonary Disease" class="form-check-input" id="health_copd" {{ in_array('Chronic Obstructive Pulmonary Disease', old('health_problems', $senior->health_problems ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="health_copd">Chronic Obstructive Pulmonary Disease</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="health_problems[]" value="Others" class="form-check-input" id="health_others">
                                            <label class="form-check-label small" for="health_others">Others, Specify</label>
                                        </div>
                                        <input type="text" name="health_problems_others" id="health_problems_others_input" placeholder="Specify" class="form-control form-control-sm mt-1" value="{{ old('health_problems_others') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Question 38: Dental Concern -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">38. DENTAL CONCERN</label>
                                <div class="d-flex flex-column gap-1">
                                    <div class="form-check">
                                        <input type="checkbox" name="dental_concern[]" value="Needs Dental Care" class="form-check-input" id="dental_needs_care" {{ in_array('Needs Dental Care', old('dental_concern', $senior->dental_concern ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="dental_needs_care">Needs Dental Care</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="dental_concern[]" value="Others" class="form-check-input" id="dental_others">
                                        <label class="form-check-label small" for="dental_others">Others, Specify</label>
                                    </div>
                                    <input type="text" name="dental_concern_others" id="dental_concern_others_input" placeholder="Specify" class="form-control form-control-sm mt-1" value="{{ old('dental_concern_others') }}">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold small">39. OPTICAL</label>
                                <div class="d-flex flex-column gap-1">
                                    <div class="form-check">
                                        <input type="checkbox" name="visual_concern[]" value="Eye impairment" class="form-check-input" id="visual_impairment" {{ in_array('Eye impairment', old('visual_concern', $senior->visual_concern ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="visual_impairment">Eye impairment</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="visual_concern[]" value="Needs eye care" class="form-check-input" id="visual_needs_care" {{ in_array('Needs eye care', old('visual_concern', $senior->visual_concern ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="visual_needs_care">Needs eye care</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="visual_concern[]" value="Others" class="form-check-input" id="visual_others">
                                        <label class="form-check-label small" for="visual_others">Others, Specify</label>
                                    </div>
                                    <input type="text" name="visual_concern_others" id="visual_concern_others_input" placeholder="Specify" class="form-control form-control-sm mt-1" value="{{ old('visual_concern_others') }}">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold small">40. HEARING</label>
                                <div class="d-flex flex-column gap-1">
                                    <div class="form-check">
                                        <input type="checkbox" name="hearing_condition[]" value="Aural impairment" class="form-check-input" id="hearing_impairment" {{ in_array('Aural impairment', old('hearing_condition', $senior->hearing_condition ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="hearing_impairment">Aural impairment</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="hearing_condition[]" value="Others" class="form-check-input" id="hearing_others">
                                        <label class="form-check-label small" for="hearing_others">Others, Specify</label>
                                    </div>
                                    <input type="text" name="hearing_condition_others" id="hearing_condition_others_input" placeholder="Specify" class="form-control form-control-sm mt-1" value="{{ old('hearing_condition_others') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Question 41: Social / Emotional -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">41. SOCIAL / EMOTIONAL</label>
                                <div class="d-flex flex-column gap-1">
                                    <div class="form-check">
                                        <input type="checkbox" name="social_emotional[]" value="Feeling neglect / rejection" class="form-check-input" id="social_neglect" {{ in_array('Feeling neglect / rejection', old('social_emotional', $senior->social_emotional ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="social_neglect">Feeling neglect / rejection</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="social_emotional[]" value="Feeling helplessness / worthlessness" class="form-check-input" id="social_helpless" {{ in_array('Feeling helplessness / worthlessness', old('social_emotional', $senior->social_emotional ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="social_helpless">Feeling helplessness / worthlessness</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="social_emotional[]" value="Feeling loneliness / isolate" class="form-check-input" id="social_lonely" {{ in_array('Feeling loneliness / isolate', old('social_emotional', $senior->social_emotional ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="social_lonely">Feeling loneliness / isolate</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="social_emotional[]" value="Lack leisure / recreational activities" class="form-check-input" id="social_leisure" {{ in_array('Lack leisure / recreational activities', old('social_emotional', $senior->social_emotional ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="social_leisure">Lack leisure / recreational activities</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="social_emotional[]" value="Lack SC friendly environment" class="form-check-input" id="social_environment" {{ in_array('Lack SC friendly environment', old('social_emotional', $senior->social_emotional ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="social_environment">Lack SC friendly environment</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="social_emotional[]" value="Others" class="form-check-input" id="social_others">
                                        <label class="form-check-label small" for="social_others">Others, Specify</label>
                                    </div>
                                    <input type="text" name="social_emotional_others" id="social_emotional_others_input" placeholder="Specify" class="form-control form-control-sm mt-1" value="{{ old('social_emotional_others') }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">42. AREA / DIFFICULTY</label>
                                <div class="d-flex flex-column gap-1">
                                    <div class="form-check">
                                        <input type="checkbox" name="area_difficulty[]" value="High Cost of medicines" class="form-check-input" id="area_cost" {{ in_array('High Cost of medicines', old('area_difficulty', $senior->area_difficulty ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="area_cost">High Cost of medicines</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="area_difficulty[]" value="Lack of medicines" class="form-check-input" id="area_lack_meds" {{ in_array('Lack of medicines', old('area_difficulty', $senior->area_difficulty ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="area_lack_meds">Lack of medicines</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="area_difficulty[]" value="Lack of medical attention" class="form-check-input" id="area_medical" {{ in_array('Lack of medical attention', old('area_difficulty', $senior->area_difficulty ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="area_medical">Lack of medical attention</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="area_difficulty[]" value="Others" class="form-check-input" id="area_others">
                                        <label class="form-check-label small" for="area_others">Others, Specify</label>
                                    </div>
                                    <input type="text" name="area_difficulty_others" id="area_difficulty_others_input" placeholder="Specify" class="form-control form-control-sm mt-1" value="{{ old('area_difficulty_others') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Question 43: List of Medicines for Maintenance -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">43. LIST OF MEDICINES FOR MAINTENANCE <em class="text-muted">(Type all your maintenance medicines. Example : Amlodipine 10mg, Losartan 50mg, etc.)</em></label>
                        <textarea name="maintenance_medicines" class="form-control form-control-sm mt-2" rows="4" placeholder="List your maintenance medicines here...">{{ old('maintenance_medicines', $senior->maintenance_medicines) }}</textarea>
                    </div>

                    <!-- Questions 44 & 45: Medical Check-up -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">44. DO YOU HAVE A SCHEDULED MEDICAL/PHYSICAL CHECK-UP?</label>
                            <select name="scheduled_checkup" class="form-select form-select-sm mt-2">
                                <option value="">Select</option>
                                <option value="Yes" {{ old('scheduled_checkup', $senior->scheduled_checkup) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ old('scheduled_checkup', $senior->scheduled_checkup) == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">45. IF YES, WHEN IS IT DONE?</label>
                            <select name="checkup_frequency" class="form-select form-select-sm mt-2">
                                <option value="">Select</option>
                                <option value="Monthly" {{ old('checkup_frequency', $senior->checkup_frequency) == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="Quarterly" {{ old('checkup_frequency', $senior->checkup_frequency) == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="Semi-annually" {{ old('checkup_frequency', $senior->checkup_frequency) == 'Semi-annually' ? 'selected' : '' }}>Semi-annually</option>
                                <option value="Annually" {{ old('checkup_frequency', $senior->checkup_frequency) == 'Annually' ? 'selected' : '' }}>Annually</option>
                                <option value="As needed" {{ old('checkup_frequency', $senior->checkup_frequency) == 'As needed' ? 'selected' : '' }}>As needed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- STATUS FIELD -->
                <div class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">46. STATUS *</label>
                            <select name="status" class="form-select form-select-sm mt-2" required>
                                <option value="">Select Status</option>
                                <option value="active" {{ (old('status') ?: $senior->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="deceased" {{ (old('status') ?: $senior->status) == 'deceased' ? 'selected' : '' }}>Deceased</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- CERTIFICATION -->
                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" name="certification" required class="form-check-input" id="certification" {{ old('certification') ? 'checked' : 'checked' }}>
                        <label class="form-check-label small" for="certification">
                            This certifies that I have willingly given my personal consent and willingfully participated in the provision of data anf relevant information regarding my person, being part of the establishment of database of Senior Citizens.
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="submit-button-container">
                     <div class="d-flex gap-3 justify-content-center">
                         <button type="button" class="btn btn-primary" onclick="confirmUpdate()">SAVE CHANGES</button>
                         <a href="{{ route('seniors') }}" class="btn btn-secondary"> BACK</a>
                         </a>
                     </div>
                </div>
            </form>
        </div>
    </div>
</div> 

    <style>
        /* Main layout structure */
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
            padding: 24px;
            margin: 0;
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
            box-shadow: inset 0 2px 4px rgba(227, 21, 117, 0.12), inset 0 1px 2px rgba(227, 21, 117, 0.18), 0 2px 4px rgba(227, 21, 117, 0.08);
        }

        /* Textarea specific styling */
        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        /* Radio buttons and checkboxes */
        input[type="radio"], input[type="checkbox"] {
            margin-right: 8px;
            accent-color: #e31575;
        }

        .form-check-input {
            margin-right: 8px;
            accent-color: #e31575;
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

        /* Custom styles for section headers and specific elements */
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

        .btn-secondary{
            margin-top: 30px;
            padding: 10px 20px;
            background-color: #333;
            border-color: #333;
            color: white;
            font-weight: bold;
        }
        .btn-secondary:hover{
            background-color: #555;
            border-color: #555;
            color: #fff;
            font-weight: bold;
        }
        
        .form-section-bg {
            background: #f5faff;
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

        .submit-button-container {
            text-align: center;
            margin: 30px 0;
        }

        .submit-button {
            background: linear-gradient(135deg, #e31575, #ff6b9d);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(227, 21, 117, 0.3);
        }

        .submit-button:hover {
            background: linear-gradient(135deg, #c01060, #e31575);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(227, 21, 117, 0.4);
        }

        .submit-button:active {
            transform: translateY(0);
        }
    </style>

    <script>
        function saveChanges() {
            document.getElementById('editSeniorForm').submit();
        }

        function goBack() {
            window.history.back();
        }

        // Calculate age when birth date is selected
        function calculateAge() {
            const birthDate = document.querySelector('input[name="date_of_birth"]').value;
            
            if (birthDate) {
                const today = new Date();
                const birth = new Date(birthDate);
                let age = today.getFullYear() - birth.getFullYear();
                const monthDiff = today.getMonth() - birth.getMonth();
                
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                    age--;
                }
                
                console.log('Calculated age:', age);
            }
        }
        
        // Add event listener to birth date field
        document.addEventListener('DOMContentLoaded', function() {
            const birthDateField = document.querySelector('input[name="date_of_birth"]');
            if (birthDateField) {
                birthDateField.addEventListener('change', calculateAge);
            }
            
            // Auto-fill form with existing data
            autoFillForm();
        });
        
        function autoFillForm() {
            console.log('Auto-filling form with existing data...');
            
            // Debug: Log the senior data
            const seniorData = @json($senior->toArray());
            console.log('Senior data:', seniorData);
            
            // Auto-fill education level radio buttons
            const educationLevel = seniorData.education_level;
            if (educationLevel) {
                const educationRadio = document.querySelector(`input[name="education_level"][value="${educationLevel}"]`);
                if (educationRadio) {
                    educationRadio.checked = true;
                    console.log('Set education level:', educationLevel);
                }
            }
            
            // Auto-fill living condition radio buttons
            const livingCondition = seniorData.living_condition_primary;
            if (livingCondition) {
                const livingRadio = document.querySelector(`input[name="living_condition_primary"][value="${livingCondition}"]`);
                if (livingRadio) {
                    livingRadio.checked = true;
                    console.log('Set living condition:', livingCondition);
                }
            }
            
            // Auto-fill living_with checkboxes
            const livingWith = seniorData.living_with || [];
            livingWith.forEach(value => {
                const checkbox = document.querySelector(`input[name="living_with[]"][value="${value}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    console.log('Set living with:', value);
                }
            });
            
            // Auto-fill household_condition checkboxes
            const householdCondition = seniorData.household_condition || [];
            householdCondition.forEach(value => {
                const checkbox = document.querySelector(`input[name="household_condition[]"][value="${value}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    console.log('Set household condition:', value);
                }
            });
            
            // Auto-fill source_of_income checkboxes
            const sourceOfIncome = seniorData.source_of_income || [];
            sourceOfIncome.forEach(value => {
                const checkbox = document.querySelector(`input[name="source_of_income[]"][value="${value}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    console.log('Set source of income:', value);
                }
            });
            
            // Monthly income is now auto-filled via the value attribute in the input field
            
            // Auto-fill health_problems checkboxes
            const healthProblems = seniorData.health_problems || [];
            healthProblems.forEach(value => {
                const checkbox = document.querySelector(`input[name="health_problems[]"][value="${value}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    console.log('Set health problem:', value);
                }
            });
            
            // Auto-fill dental_concern checkboxes
            const dentalConcern = seniorData.dental_concern || [];
            dentalConcern.forEach(value => {
                const checkbox = document.querySelector(`input[name="dental_concern[]"][value="${value}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    console.log('Set dental concern:', value);
                }
            });
            
            // Auto-fill visual_concern checkboxes
            const visualConcern = seniorData.visual_concern || [];
            visualConcern.forEach(value => {
                const checkbox = document.querySelector(`input[name="visual_concern[]"][value="${value}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    console.log('Set visual concern:', value);
                }
            });
            
            // Auto-fill hearing_condition checkboxes
            const hearingCondition = seniorData.hearing_condition || [];
            hearingCondition.forEach(value => {
                const checkbox = document.querySelector(`input[name="hearing_condition[]"][value="${value}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    console.log('Set hearing condition:', value);
                }
            });
            
            // Auto-fill social_emotional checkboxes
            const socialEmotional = seniorData.social_emotional || [];
            socialEmotional.forEach(value => {
                const checkbox = document.querySelector(`input[name="social_emotional[]"][value="${value}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    console.log('Set social emotional:', value);
                }
            });
            
            // Auto-fill area_difficulty checkboxes
            const areaDifficulty = seniorData.area_difficulty || [];
            areaDifficulty.forEach(value => {
                const checkbox = document.querySelector(`input[name="area_difficulty[]"][value="${value}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    console.log('Set area difficulty:', value);
                }
            });
            
            console.log('Form auto-fill completed!');
        }
        
        // Confirmation function for updating senior profile
    function confirmUpdate() {
        // Validate religion and ethnic origin before showing confirmation
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
        
        const firstName = document.querySelector('input[name="first_name"]').value || 'Senior';
        const lastName = document.querySelector('input[name="last_name"]').value || 'Citizen';
        
        const seniorName = firstName + ' ' + lastName;
        showConfirmModal(
            'Update Senior Profile',
            `Are you sure you want to update ${seniorName}'s profile information? This will save all changes made to the form.`,
            '{{ route("edit_senior.update", $senior->id) }}',
            'PUT'
        );
    }
    </script>
  </x-header>
</x-sidebar>
