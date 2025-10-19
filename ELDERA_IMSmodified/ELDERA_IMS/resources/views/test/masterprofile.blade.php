<x-sidebar>
  <x-header title="SENIOR CITIZEN INFORMATION" icon="fas fa-user-plus">
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
                             <x-photo-upload id="photo_upload" name="photo" />
                        </div>

                        <!-- Document Upload for OCR Scanning -->
                        <div class="mb-4">
                            <div class="card border-primary shadow">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-file-upload me-2"></i>Upload Documents for OCR Scanning</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Time-saving feature:</strong> Upload a completed form or document to automatically fill in the fields below using OCR technology.
                                    </div>
                                    <div class="mb-2">
                                        <input type="file" class="form-control form-control-sm" id="ocrFileUpload" name="ocr_document[]" accept=".jpg,.jpeg,.png,.pdf" multiple>
                                        <small class="text-muted mt-1">Select multiple files by holding Ctrl (or Cmd) while selecting</small>
                                    </div>
                                    <div id="selectedFilesContainer" class="mb-2 d-none">
                                        <p class="mb-1 fw-bold">Selected files:</p>
                                        <div id="selectedFilesList" class="small"></div>
                                    </div>
                                    <div class="mb-3">
                                        <button class="btn btn-primary btn-sm" style="width: 120px;" type="button" id="processOcrBtn" onclick="handleOcrScan()">
                                            <i class="fas fa-magic me-1"></i> Scan All
                                        </button>
                                        <div id="ocrProgressContainer" class="mt-2 d-none">
                                            <div class="progress" style="height: 15px;">
                                                <div id="ocrProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div id="ocrProgressText" class="text-center small mt-1">0% (0/0)</div>
                                        </div>
                                        <div id="ocrResultsContainer" class="mt-3 d-none">
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle me-2"></i>
                                                <span id="ocrResultsSummary">Processing complete</span>
                                            </div>
                                        </div>
                                        
                                        <script>
                                            // Display selected files when files are chosen
                                            document.getElementById('ocrFileUpload').addEventListener('change', function() {
                                                const filesContainer = document.getElementById('selectedFilesContainer');
                                                const filesList = document.getElementById('selectedFilesList');
                                                
                                                // Clear previous list
                                                filesList.innerHTML = '';
                                                
                                                if (this.files.length > 0) {
                                                    filesContainer.classList.remove('d-none');
                                                    
                                                    // Add each file to the list
                                                    Array.from(this.files).forEach((file, index) => {
                                                        const fileItem = document.createElement('div');
                                                        fileItem.innerHTML = `<i class="fas fa-file me-1"></i> ${file.name} <small class="text-muted">(${(file.size / 1024).toFixed(1)} KB)</small>`;
                                                        filesList.appendChild(fileItem);
                                                    });
                                                } else {
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
                                            document.getElementById('processOcrBtn').addEventListener('click', function() {
                                                console.log("Scan button clicked directly");
                                                processOcrDocuments();
                                            });
                                            
                                            // Function to fill form fields with OCR data using multiple fallback patterns
                                            function fillFormFields(data) {
                                                console.log("Filling form fields with OCR data:", data);
                                                
                                                // Get form fields
                                                const lastNameField = document.querySelector('input[name="last_name"]');
                                                const firstNameField = document.querySelector('input[name="first_name"]');
                                                const middleNameField = document.querySelector('input[name="middle_name"]');
                                                const oscaIdField = document.querySelector('input[name="osca_id"]');
                                                const gsisSssField = document.querySelector('input[name="gsis_sss"]');
                                                const tinField = document.querySelector('input[name="tin"]');
                                                const philhealthField = document.querySelector('input[name="philhealth"]');
                                                const scAssociationField = document.querySelector('input[name="sc_association"]');
                                                const otherGovtIdField = document.querySelector('input[name="other_govt_id"]');
                                                const dateOfBirthField = document.querySelector('input[name="date_of_birth"]');
                                                const birthPlaceField = document.querySelector('input[name="birth_place"]');
                                                const residenceField = document.querySelector('input[name="residence"]');
                                                const streetField = document.querySelector('input[name="street"]');
                                                const ethnicOriginField = document.querySelector('input[name="ethnic_origin"]');
                                                const languageField = document.querySelector('input[name="language"]');
                                                
                                                // Extract data object
                                                const ocrData = data.data || {};
                                                
                                                // First Name - try multiple patterns
                                                const firstNamePatterns = [
                                                    'first_name', 'firstname', 'first name', 'name_first', 'name first', 
                                                    'given_name', 'givenname', 'given name', 'maiden_name', 'maiden name'
                                                ];
                                                
                                                // Last Name - try multiple patterns
                                                const lastNamePatterns = [
                                                    'last_name', 'lastname', 'last name', 'name_last', 'name last',
                                                    'surname', 'family_name', 'familyname', 'family name'
                                                ];
                                                
                                                // Middle Name - try multiple patterns
                                                const middleNamePatterns = [
                                                    'middle_name', 'middlename', 'middle name', 'name_middle', 'name middle',
                                                    'middle_initial', 'middleinitial', 'middle initial'
                                                ];
                                                
                                                // OSCA ID - try multiple patterns
                                                const oscaIdPatterns = [
                                                    'osca_id', 'oscaid', 'osca id', 'osca_id_number', 'osca_id_no',
                                                    'osca_number', 'oscanumber', 'osca number'
                                                ];
                                                
                                                // GSIS/SSS - try multiple patterns
                                                const gsisSssPatterns = [
                                                    'gsis_sss', 'gsissss', 'gsis sss', 'gsis_sss_number', 'gsis_sss_no',
                                                    'sss_number', 'sssnumber', 'sss number', 'gsis_number', 'gsisnumber'
                                                ];
                                                
                                                // TIN - try multiple patterns
                                                const tinPatterns = [
                                                    'tin', 'tax_identification_number', 'tax identification number',
                                                    'tin_no', 'tin no', 'tax_id', 'tax id'
                                                ];
                                                
                                                // PhilHealth - try multiple patterns
                                                const philhealthPatterns = [
                                                    'philhealth', 'philhealth_number', 'philhealth number',
                                                    'philhealth_no', 'philhealth no', 'philhealth_id'
                                                ];
                                                
                                                // SC Association - try multiple patterns
                                                const scAssociationPatterns = [
                                                    'sc_association', 'scassociation', 'sc association',
                                                    'senior_citizen_association_id', 'senior citizen association id'
                                                ];
                                                
                                                // Other Govt ID - try multiple patterns
                                                const otherGovtIdPatterns = [
                                                    'other_govt_id', 'othergovtid', 'other govt id',
                                                    'other_government_id', 'other government id', 'other_id'
                                                ];
                                                
                                                // Date of Birth - try multiple patterns
                                                const dateOfBirthPatterns = [
                                                    'date_of_birth', 'dateofbirth', 'date of birth',
                                                    'birth_date', 'birthdate', 'birth date', 'dob'
                                                ];
                                                
                                                // Birth Place - try multiple patterns
                                                const birthPlacePatterns = [
                                                    'birth_place', 'birthplace', 'birth place',
                                                    'place_of_birth', 'placeofbirth', 'place of birth'
                                                ];
                                                
                                                // Residence - try multiple patterns
                                                const residencePatterns = [
                                                    'residence', 'house_no', 'house no', 'zone', 'purok', 'sitio'
                                                ];
                                                
                                                // Street - try multiple patterns
                                                const streetPatterns = [
                                                    'street', 'st', 'street_name', 'streetname', 'street name'
                                                ];
                                                
                                                // Ethnic Origin - try multiple patterns
                                                const ethnicOriginPatterns = [
                                                    'ethnic_origin', 'ethnicorigin', 'ethnic origin',
                                                    'ethnicity', 'ethnic_group', 'ethnic group', 'tribe'
                                                ];
                                                
                                                // Language - try multiple patterns
                                                const languagePatterns = [
                                                    'language', 'language_spoken', 'languagespoken', 'language spoken',
                                                    'dialect', 'mother_tongue', 'mothertongue', 'mother tongue'
                                                ];
                                                
                                                // Function to find value using multiple patterns
                                                function findValueByPatterns(data, patterns) {
                                                    // First try exact matches in the data object
                                                    for (const pattern of patterns) {
                                                        if (data[pattern] !== undefined && data[pattern] !== null && data[pattern] !== '') {
                                                            return data[pattern];
                                                        }
                                                    }
                                                    
                                                    // If no exact match, try case-insensitive search in all properties
                                                    for (const key in data) {
                                                        const keyLower = key.toLowerCase();
                                                        for (const pattern of patterns) {
                                                            if (keyLower.includes(pattern.toLowerCase())) {
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
                                                if (firstName && firstNameField) {
                                                    firstNameField.value = firstName;
                                                    console.log("Set First Name field to:", firstName);
                                                }
                                                
                                                // Fill Last Name
                                                const lastName = findValueByPatterns(ocrData, lastNamePatterns);
                                                if (lastName && lastNameField) {
                                                    lastNameField.value = lastName;
                                                    console.log("Set Last Name field to:", lastName);
                                                }
                                                
                                                // Fill Middle Name
                                                const middleName = findValueByPatterns(ocrData, middleNamePatterns);
                                                if (middleName && middleNameField) {
                                                    middleNameField.value = middleName;
                                                    console.log("Set Middle Name field to:", middleName);
                                                }
                                                
                                                // Fill OSCA ID
                                                const oscaId = findValueByPatterns(ocrData, oscaIdPatterns);
                                                if (oscaId && oscaIdField) {
                                                    oscaIdField.value = oscaId;
                                                    console.log("Set OSCA ID field to:", oscaId);
                                                }
                                                
                                                // Fill GSIS/SSS
                                                const gsisSss = findValueByPatterns(ocrData, gsisSssPatterns);
                                                if (gsisSss && gsisSssField) {
                                                    gsisSssField.value = gsisSss;
                                                    console.log("Set GSIS/SSS field to:", gsisSss);
                                                }
                                                
                                                // Fill TIN
                                                const tin = findValueByPatterns(ocrData, tinPatterns);
                                                if (tin && tinField) {
                                                    tinField.value = tin;
                                                    console.log("Set TIN field to:", tin);
                                                }
                                                
                                                // Fill PhilHealth
                                                const philhealth = findValueByPatterns(ocrData, philhealthPatterns);
                                                if (philhealth && philhealthField) {
                                                    philhealthField.value = philhealth;
                                                    console.log("Set PhilHealth field to:", philhealth);
                                                }
                                                
                                                // Fill SC Association
                                                const scAssociation = findValueByPatterns(ocrData, scAssociationPatterns);
                                                if (scAssociation && scAssociationField) {
                                                    scAssociationField.value = scAssociation;
                                                    console.log("Set SC Association field to:", scAssociation);
                                                }
                                                
                                                // Fill Other Govt ID
                                                const otherGovtId = findValueByPatterns(ocrData, otherGovtIdPatterns);
                                                if (otherGovtId && otherGovtIdField) {
                                                    otherGovtIdField.value = otherGovtId;
                                                    console.log("Set Other Govt ID field to:", otherGovtId);
                                                }
                                                
                                                // Fill Date of Birth
                                                const dateOfBirth = findValueByPatterns(ocrData, dateOfBirthPatterns);
                                                if (dateOfBirth && dateOfBirthField) {
                                                    // Try to convert to YYYY-MM-DD format if it's not already
                                                    try {
                                                        // Check if it's already in YYYY-MM-DD format
                                                        if (/^\d{4}-\d{2}-\d{2}$/.test(dateOfBirth)) {
                                                            dateOfBirthField.value = dateOfBirth;
                                                        } else {
                                                            // Try to parse various date formats
                                                            const dateParts = dateOfBirth.match(/(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{2,4})/);
                                                            if (dateParts) {
                                                                let day = dateParts[1].padStart(2, '0');
                                                                let month = dateParts[2].padStart(2, '0');
                                                                let year = dateParts[3];
                                                                
                                                                // Handle 2-digit years
                                                                if (year.length === 2) {
                                                                    const currentYear = new Date().getFullYear();
                                                                    const century = Math.floor(currentYear / 100) * 100;
                                                                    year = parseInt(year) + century;
                                                                    if (year > currentYear) {
                                                                        year -= 100;
                                                                    }
                                                                }
                                                                
                                                                dateOfBirthField.value = `${year}-${month}-${day}`;
                                                            } else {
                                                                // If we can't parse it, just set it as is
                                                                dateOfBirthField.value = dateOfBirth;
                                                            }
                                                        }
                                                        console.log("Set Date of Birth field to:", dateOfBirthField.value);
                                                    } catch (e) {
                                                        console.error("Error formatting date:", e);
                                                        // If there's an error, just set the raw value
                                                        dateOfBirthField.value = dateOfBirth;
                                                    }
                                                }
                                                
                                                // Fill Birth Place
                                                const birthPlace = findValueByPatterns(ocrData, birthPlacePatterns);
                                                if (birthPlace && birthPlaceField) {
                                                    birthPlaceField.value = birthPlace;
                                                    console.log("Set Birth Place field to:", birthPlace);
                                                }
                                                
                                                // Fill Residence
                                                const residence = findValueByPatterns(ocrData, residencePatterns);
                                                if (residence && residenceField) {
                                                    residenceField.value = residence;
                                                    console.log("Set Residence field to:", residence);
                                                }
                                                
                                                // Fill Street
                                                const street = findValueByPatterns(ocrData, streetPatterns);
                                                if (street && streetField) {
                                                    streetField.value = street;
                                                    console.log("Set Street field to:", street);
                                                }
                                                
                                                // Fill Ethnic Origin
                                                const ethnicOrigin = findValueByPatterns(ocrData, ethnicOriginPatterns);
                                                if (ethnicOrigin && ethnicOriginField) {
                                                    ethnicOriginField.value = ethnicOrigin;
                                                    console.log("Set Ethnic Origin field to:", ethnicOrigin);
                                                }
                                                
                                                // Fill Language
                                                const language = findValueByPatterns(ocrData, languagePatterns);
                                                if (language && languageField) {
                                                    languageField.value = language;
                                                    console.log("Set Language field to:", language);
                                                }
                                                
                                                // New form fields patterns
                                                const regionPatterns = ['region', 'region_name'];
                                                const provincePatterns = ['province', 'province_name'];
                                                const cityMunicipalityPatterns = ['city_municipality', 'city', 'municipality'];
                                                const barangayPatterns = ['barangay', 'brgy'];
                                                const maritalStatusPatterns = ['marital_status', 'civil_status'];
                                                const genderPatterns = ['gender', 'sex'];
                                                const contactNumberPatterns = ['contact_number', 'phone_number', 'mobile_number'];
                                                const emailAddressPatterns = ['email_address', 'email'];
                                                const religionPatterns = ['religion', 'religious_affiliation'];
                                                const capabilityToTravelPatterns = ['capability_to_travel'];
                                                const serviceBusinessEmploymentPatterns = ['service_business_employment'];
                                                const currentPensionPatterns = ['current_pension'];
                                                const educationalAttainmentPatterns = ['educational_attainment', 'education'];
                                                const specializationPatterns = ['specialization', 'skills', 'technical_skills'];
                                                
                                                // Fill Region
                                                const region = findValueByPatterns(ocrData, regionPatterns);
                                                const regionField = document.querySelector('select[name="region"]');
                                                if (region && regionField) {
                                                    for (let i = 0; i < regionField.options.length; i++) {
                                                        if (regionField.options[i].text.toLowerCase().includes(region.toLowerCase())) {
                                                            regionField.selectedIndex = i;
                                                            break;
                                                        }
                                                    }
                                                    console.log("Set Region field to:", region);
                                                }
                                                
                                                // Fill Province
                                                const province = findValueByPatterns(ocrData, provincePatterns);
                                                const provinceField = document.querySelector('select[name="province"]');
                                                if (province && provinceField) {
                                                    for (let i = 0; i < provinceField.options.length; i++) {
                                                        if (provinceField.options[i].text.toLowerCase().includes(province.toLowerCase())) {
                                                            provinceField.selectedIndex = i;
                                                            break;
                                                        }
                                                    }
                                                    console.log("Set Province field to:", province);
                                                }
                                                
                                                // Fill City/Municipality
                                                const cityMunicipality = findValueByPatterns(ocrData, cityMunicipalityPatterns);
                                                const cityMunicipalityField = document.querySelector('select[name="city_municipality"]');
                                                if (cityMunicipality && cityMunicipalityField) {
                                                    for (let i = 0; i < cityMunicipalityField.options.length; i++) {
                                                        if (cityMunicipalityField.options[i].text.toLowerCase().includes(cityMunicipality.toLowerCase())) {
                                                            cityMunicipalityField.selectedIndex = i;
                                                            break;
                                                        }
                                                    }
                                                    console.log("Set City/Municipality field to:", cityMunicipality);
                                                }
                                                
                                                // Fill Barangay
                                                const barangay = findValueByPatterns(ocrData, barangayPatterns);
                                                const barangayField = document.querySelector('select[name="barangay"]');
                                                if (barangay && barangayField) {
                                                    for (let i = 0; i < barangayField.options.length; i++) {
                                                        if (barangayField.options[i].text.toLowerCase().includes(barangay.toLowerCase())) {
                                                            barangayField.selectedIndex = i;
                                                            break;
                                                        }
                                                    }
                                                    console.log("Set Barangay field to:", barangay);
                                                }
                                                
                                                // Fill Marital Status
                                                const maritalStatus = findValueByPatterns(ocrData, maritalStatusPatterns);
                                                const maritalStatusField = document.querySelector('select[name="marital_status"]');
                                                if (maritalStatus && maritalStatusField) {
                                                    for (let i = 0; i < maritalStatusField.options.length; i++) {
                                                        if (maritalStatusField.options[i].value.toLowerCase().includes(maritalStatus.toLowerCase())) {
                                                            maritalStatusField.selectedIndex = i;
                                                            break;
                                                        }
                                                    }
                                                    console.log("Set Marital Status field to:", maritalStatus);
                                                }
                                                
                                                // Fill Gender
                                                const gender = findValueByPatterns(ocrData, genderPatterns);
                                                const genderField = document.querySelector('select[name="sex"]');
                                                if (gender && genderField) {
                                                    for (let i = 0; i < genderField.options.length; i++) {
                                                        if (genderField.options[i].value.toLowerCase().includes(gender.toLowerCase())) {
                                                            genderField.selectedIndex = i;
                                                            break;
                                                        }
                                                    }
                                                    console.log("Set Gender field to:", gender);
                                                }
                                                
                                                // Fill Contact Number
                                                const contactNumber = findValueByPatterns(ocrData, contactNumberPatterns);
                                                const contactNumberField = document.querySelector('input[name="contact_number"]');
                                                if (contactNumber && contactNumberField) {
                                                    contactNumberField.value = contactNumber;
                                                    console.log("Set Contact Number field to:", contactNumber);
                                                }
                                                
                                                // Fill Email Address
                                                const emailAddress = findValueByPatterns(ocrData, emailAddressPatterns);
                                                const emailAddressField = document.querySelector('input[name="email"]');
                                                if (emailAddress && emailAddressField) {
                                                    emailAddressField.value = emailAddress;
                                                    console.log("Set Email Address field to:", emailAddress);
                                                }
                                                
                                                // Fill Religion
                                                const religion = findValueByPatterns(ocrData, religionPatterns);
                                                const religionField = document.querySelector('input[name="religion"]');
                                                if (religion && religionField) {
                                                    religionField.value = religion;
                                                    console.log("Set Religion field to:", religion);
                                                }
                                                
                                                // Fill Educational Attainment
                                                const educationalAttainment = findValueByPatterns(ocrData, educationalAttainmentPatterns);
                                                const educationalAttainmentField = document.querySelector('select[name="educational_attainment"]');
                                                if (educationalAttainment && educationalAttainmentField) {
                                                    for (let i = 0; i < educationalAttainmentField.options.length; i++) {
                                                        if (educationalAttainmentField.options[i].value.toLowerCase().includes(educationalAttainment.toLowerCase())) {
                                                            educationalAttainmentField.selectedIndex = i;
                                                            break;
                                                        }
                                                    }
                                                    console.log("Set Educational Attainment field to:", educationalAttainment);
                                                }
                                                
                                                console.log("Field mapping completed");
                                            }
                                        </script>
                                    </div>
                                    <div id="ocrStatus" class="mt-2 small"></div>
                                    <div class="mt-2 small text-muted">
                                        <i class="fas fa-lightbulb me-1"></i> Supported file types: JPG, JPEG, PNG, PDF
                                    </div>
                                </div>
                            </div>
                        </div>
                          
              


            <form method="POST" action="{{ isset($senior) && $senior ? route('edit_senior.update', $senior->id) : route('seniors.store') }}" enctype="multipart/form-data" id="master-profile-form">
                @csrf
                @if(isset($senior) && $senior)
                    @method('PUT')
                @endif
                
                <!-- I. IDENTIFYING INFORMATION -->
                <div class="section-header">I. IDENTIFYING INFORMATION</div>

                <div class="form-section-content">
                    <!-- Name Fields -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small mb-3">1. NAME OF SENIOR CITIZEN</label>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control form-control-sm" placeholder="Last Name" value="{{ old('last_name', $senior->last_name ?? '') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control form-control-sm" placeholder="First Name" value="{{ old('first_name', $senior->first_name ?? '') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control form-control-sm" placeholder="Middle Name" value="{{ old('middle_name', $senior->middle_name ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Name Extension</label>
                                <select name="name_extension" class="form-select form-select-sm">
                                    <option value="">Extension</option>
                                    <option value="Jr." {{ old('name_extension', $senior->name_extension ?? '') == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                                    <option value="Sr." {{ old('name_extension', $senior->name_extension ?? '') == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                                    <option value="II" {{ old('name_extension', $senior->name_extension ?? '') == 'II' ? 'selected' : '' }}>II</option>
                                    <option value="III" {{ old('name_extension', $senior->name_extension ?? '') == 'III' ? 'selected' : '' }}>III</option>
                                    <option value="IV" {{ old('name_extension', $senior->name_extension ?? '') == 'IV' ? 'selected' : '' }}>IV</option>
                                    <option value="V" {{ old('name_extension', $senior->name_extension ?? '') == 'V' ? 'selected' : '' }}>V</option>
                                    <option value="VI" {{ old('name_extension', $senior->name_extension ?? '') == 'VI' ? 'selected' : '' }}>VI</option>
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
                                   <option value="Region I"> Region I - Ilocos Region</option>
                   
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Province *</label>
                                <select name="province" class="form-control form-control-sm" required>
                                    <option value="Pangasinan" selected>Pangasinan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">City *</label>
                                <select name="city" class="form-control form-control-sm" required>
                                    <option value="Lingayen" selected>Lingayen</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Barangay *</label>
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
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">House No./Zone/Purok/Sitio *</label>
                                <input type="text" name="residence" class="form-control form-control-sm" placeholder="House No./Zone/Purok/Sitio" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Street</label>
                                <input type="text" name="street" class="form-control form-control-sm" placeholder="Street">
                            </div>
                        </div>
                    </div>

                    <!-- Birth Date -->
                    <div class="mb-4">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">3. DATE OF BIRTH *</label>
                                <input type="date" name="date_of_birth" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">4. PLACE OF BIRTH *</label>
                                <input type="text" name="birth_place" class="form-control form-control-sm" placeholder="Place of Birth" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="row g-2 mb-3">
                           
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">5. MARITAL STATUS *</label>
                                <select name="marital_status" class="form-control form-control-sm" required>
                                    <option value="">Select Marital Status</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Separated">Separated</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">6. GENDER *</label>
                                <select name="sex" class="form-control form-control-sm" required>
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2">
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">7. CONTACT NUMBER *</label>
                                <input type="tel" name="contact_number" class="form-control form-control-sm" placeholder="Contact Number" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">8. EMAIL ADDRESS *</label>
                                <input type="email" name="email" class="form-control form-control-sm" placeholder="Email Address" required>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="mb-4">
                        <div class="row g-2">
                           
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">9. RELIGION</label>
                                <select name="religion" class="form-control form-control-sm">
                                    <option value="">Select Religion</option>
                                <option value="Catholic">Catholic</option>
                                <option value="Protestant">Protestant</option>
                                <option value="Islam">Islam</option>
                                <option value="Buddhism">Buddhism</option>
                                <option value="Others">Others</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">10. ETHNIC ORIGIN</label>
                                <input type="text" name="ethnic_origin" class="form-control form-control-sm" placeholder="Ethnic Origin">
                            </div>
                        </div>
                        <div class="row g-2">
                           
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">11. LANGUAGE SPOKEN / WRITTEN *</label>
                                <input type="text" name="language" class="form-control form-control-sm" placeholder="Language Spoken" required>
                            </div>
                        </div>
                    </div>

                    <!-- ID Numbers -->
                    <div class="mb-4">
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">12. OSCA ID NO. *</label>
                                <input type="text" name="osca_id" class="form-control form-control-sm" placeholder="OSCA ID Number" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">13. GSIS/SSS NO.</label>
                                <input type="text" name="gsis_sss" class="form-control form-control-sm" placeholder="GSIS/SSS Number">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">14. SENIOR STATUS *</label>
                                <select name="status" class="form-select form-select-sm" required>
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status', $senior->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="deceased" {{ old('status', $senior->status ?? '') == 'deceased' ? 'selected' : '' }}>Deceased</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">15. TIN</label>
                                <input type="text" name="tin" class="form-control form-control-sm" placeholder="Tax Identification Number">
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">16. PHILHEALTH NO.</label>
                                <input type="text" name="philhealth" class="form-control form-control-sm" placeholder="PhilHealth Number">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">17. SC ASSOCIATION / ORG ID NO.</label>
                                <input type="text" name="sc_association" class="form-control form-control-sm" placeholder="Senior Citizen Association ID">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">18. OTHER GOV'T ID NO.</label>
                                <input type="text" name="other_govt_id" class="form-control form-control-sm" placeholder="Other Government ID">
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
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">19. SERVICE/BUSINESS/EMPLOYMENT</label>
                                <input type="text" name="employment" class="form-control form-control-sm" placeholder="Specify">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">20. CURRENT PENSION</label>
                                <input type="text" name="current_pension" class="form-control form-control-sm" placeholder="Specify">
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
                                <input type="text" name="spouse_last_name" class="form-control form-control-sm" placeholder="Last Name" value="{{ old('spouse_last_name', $senior->spouse_last_name ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="spouse_first_name" class="form-control form-control-sm" placeholder="First Name" value="{{ old('spouse_first_name', $senior->spouse_first_name ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="spouse_middle_name" class="form-control form-control-sm" placeholder="Middle Name" value="{{ old('spouse_middle_name', $senior->spouse_middle_name ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="spouse_extension" class="form-control form-control-sm" placeholder="Extension(Jr, Sr)" value="{{ old('spouse_extension', $senior->spouse_extension ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- 22. Name of your father -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">22. FATHER'S NAME</label>
                        <div class="row g-2 mb-3">
                            <div class="col-md-3">
                                <input type="text" name="father_last_name" class="form-control form-control-sm" placeholder="Last Name" value="{{ old('father_last_name', $senior->father_last_name ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="father_first_name" class="form-control form-control-sm" placeholder="First Name" value="{{ old('father_first_name', $senior->father_first_name ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="father_middle_name" class="form-control form-control-sm" placeholder="Middle Name" value="{{ old('father_middle_name', $senior->father_middle_name ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="father_extension" class="form-control form-control-sm" placeholder="Extension(Jr, Sr)" value="{{ old('father_extension', $senior->father_extension ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- 23. Name of your mother -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">23. MOTHER'S MAIDEN NAME</label>
                        <div class="row g-2 mb-3">
                            <div class="col-md-3">
                                <input type="text" name="mother_last_name" class="form-control form-control-sm" placeholder="Last Name" value="{{ old('mother_last_name', $senior->mother_last_name ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="mother_first_name" class="form-control form-control-sm" placeholder="First Name" value="{{ old('mother_first_name', $senior->mother_first_name ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="mother_middle_name" class="form-control form-control-sm" placeholder="Middle Name" value="{{ old('mother_middle_name', $senior->mother_middle_name ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="mother_extension" class="form-control form-control-sm" placeholder="Extension" value="{{ old('mother_extension', $senior->mother_extension ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- 24. Name of your child(ren) -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold small mb-0">24. CHILD(REN)</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addChildRow()">
                                <i class="fas fa-plus"></i> Add Child
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="childrenTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Complete Name</th>
                                        <th>Occupation</th>
                                        <th>Income (Optional)</th>
                                        <th>Age</th>
                                        <th>Is Working?</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="childrenTableBody">
                                    @for($i = 1; $i <= 5; $i++)
                                    <tr>
                                        <td><input type="text" name="child_name_{{ $i }}" class="form-control form-control-sm" placeholder="Child Name"></td>
                                        <td><input type="text" name="child_occupation_{{ $i }}" class="form-control form-control-sm" placeholder="Occupation"></td>
                                        <td><input type="text" name="child_income_{{ $i }}" class="form-control form-control-sm" placeholder="Income"></td>
                                        <td><input type="number" name="child_age_{{ $i }}" class="form-control form-control-sm" placeholder="Age"></td>
                                        <td>
                                            <select name="child_working_{{ $i }}" class="form-control form-control-sm">
                                                <option value="">Is working?</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeChildRow(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 25. Other Dependents -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold small mb-0">25. OTHER DEPENDENTS</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addDependentRow()">
                                <i class="fas fa-plus"></i> Add Dependent
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="dependentsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name of Dependent</th>
                                        <th>Occupation of Dependent</th>
                                        <th>Income</th>
                                        <th>Age</th>
                                        <th>Is Working?</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="dependentsTableBody">
                                    @for($i = 1; $i <= 2; $i++)
                                    <tr>
                                        <td><input type="text" name="dependent_name_{{ $i }}" class="form-control form-control-sm" placeholder="Name of Dependent"></td>
                                        <td><input type="text" name="dependent_occupation_{{ $i }}" class="form-control form-control-sm" placeholder="Occupation of Dependent"></td>
                                        <td><input type="text" name="dependent_income_{{ $i }}" class="form-control form-control-sm" placeholder="Income"></td>
                                        <td><input type="number" name="dependent_age_{{ $i }}" class="form-control form-control-sm" placeholder="Age"></td>
                                        <td>
                                            <select name="dependent_working_{{ $i }}" class="form-control form-control-sm">
                                                <option value="">Is Working?</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDependentRow(this)">
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

                <!-- III. EDUCATION / HR PROFILE -->
                <div class="section-header">III. EDUCATION / HR PROFILE</div>
                
                <div class="mb-4">
                    <!-- 26. Highest Educational Attainment -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">26. EDUCATIONAL ATTAINMENT</label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="Not Attended School" id="edu1" {{ old('education_level', $senior->education_level ?? '') == 'Not Attended School' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu1">Not Attended School</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="Elementary Level" id="edu2" {{ old('education_level', $senior->education_level ?? '') == 'Elementary Level' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu2">Elementary Level</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="Elementary Graduate" id="edu3" {{ old('education_level', $senior->education_level ?? '') == 'Elementary Graduate' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu3">Elementary Graduate</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="Highschool Level" id="edu4" {{ old('education_level', $senior->education_level ?? '') == 'Highschool Level' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu4">Highschool Level</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="Highschool Graduate" id="edu5" {{ old('education_level', $senior->education_level ?? '') == 'Highschool Graduate' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu5">Highschool Graduate</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="Vocational" id="edu6" {{ old('education_level', $senior->education_level ?? '') == 'Vocational' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu6">Vocational</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="College Level" id="edu7" {{ old('education_level', $senior->education_level ?? '') == 'College Level' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu7">College Level</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="College Graduate" id="edu8" {{ old('education_level', $senior->education_level ?? '') == 'College Graduate' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu8">College Graduate</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="Post Graduate" id="edu9" {{ old('education_level', $senior->education_level ?? '') == 'Post Graduate' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="edu9">Post Graduate</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="education_level" value="Others" id="education_others" {{ old('education_level', $senior->education_level ?? '') == 'Others' ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="education_others">Others, Specify</label>
                                </div>
                                <input type="text" name="education_others_specify" class="form-control form-control-sm mt-2" placeholder="Specify" value="{{ old('education_others_specify', $senior->education_others_specify ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- 27. Specialization / Technical Skills -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">27. AREAS OF SPECIALIZATION / TECHNICAL SKILLS (CHECK ALL APPLICABLE)</label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Medical" class="form-check-input" id="skill_medical" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Medical', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_medical">Medical</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Dental" class="form-check-input" id="skill_dental" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Dental', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_dental">Dental</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Fishing" class="form-check-input" id="skill_fishing" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Fishing', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_fishing">Fishing</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Engineering" class="form-check-input" id="skill_engineering" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Engineering', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_engineering">Engineering</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Barber" class="form-check-input" id="skill_barber" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Barber', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_barber">Barber</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Evangelization" class="form-check-input" id="skill_evangelization" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Evangelization', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_evangelization">Evangelization</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Midwifery" class="form-check-input" id="skill_midwifery" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Midwifery', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_midwifery">Midwifery</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Teaching" class="form-check-input" id="skill_teaching" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Teaching', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_teaching">Teaching</label></div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Counselling" class="form-check-input" id="skill_counselling" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Counselling', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_counselling">Counselling</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Cooking" class="form-check-input" id="skill_cooking" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Cooking', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_cooking">Cooking</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Carpenter" class="form-check-input" id="skill_carpenter" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Carpenter', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_carpenter">Carpenter</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Mason" class="form-check-input" id="skill_mason" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Mason', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_mason">Mason</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Tailor" class="form-check-input" id="skill_tailor" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Tailor', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_tailor">Tailor</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Legal Services" class="form-check-input" id="skill_legal" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Legal Services', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_legal">Legal Services</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Farming" class="form-check-input" id="skill_farming" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Farming', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_farming">Farming</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Arts" class="form-check-input" id="skill_arts" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Arts', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_arts">Arts</label></div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Plumber" class="form-check-input" id="skill_plumber" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Plumber', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_plumber">Plumber</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Shoemaker" class="form-check-input" id="skill_shoemaker" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Shoemaker', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_shoemaker">Shoemaker</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Chef/Cook" class="form-check-input" id="skill_chef" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Chef/Cook', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_chef">Chef/Cook</label></div>
                                <div class="form-check"><input type="checkbox" name="skills[]" value="Information Technology" class="form-check-input" id="skill_it" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Information Technology', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="skill_it">Information Technology</label></div>
                                <div class="form-check">
                                    <input type="checkbox" name="skills[]" value="Others" class="form-check-input" id="skills_others" {{ (is_array(old('skills', $senior->skills ?? [])) && in_array('Others', old('skills', $senior->skills ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="skills_others">Others, Specify</label>
                                </div>
                                <input type="text" name="skills_others_specify" class="form-control form-control-sm mt-2" placeholder="Specify" value="{{ old('skills_others_specify', $senior->skills_others_specify ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- 28. Shared Skills -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">28. SHARE SKILLS (COMMUNITY SERVICE)</label>
                        <textarea name="shared_skills" class="form-control form-control-sm" placeholder="Type skills here separated by comma" rows="3">{{ old('shared_skills', $senior->shared_skills ?? '') }}</textarea>
                    </div>

                    <!-- 29. Involvement in Community Activities -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">29. COMMUNITY SERVICE AND INVOLVEMENT (CHECK ALL APPLICABLE)</label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="form-check"><input type="checkbox" name="community_activities[]" value="Medical" class="form-check-input" id="community_medical" {{ (is_array(old('community_activities', $senior->community_activities ?? [])) && in_array('Medical', old('community_activities', $senior->community_activities ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="community_medical">Medical</label></div>
                                <div class="form-check"><input type="checkbox" name="community_activities[]" value="Resource Volunteer" class="form-check-input" id="community_volunteer" {{ (is_array(old('community_activities', $senior->community_activities ?? [])) && in_array('Resource Volunteer', old('community_activities', $senior->community_activities ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="community_volunteer">Resource Volunteer</label></div>
                                <div class="form-check"><input type="checkbox" name="community_activities[]" value="Community Beautification" class="form-check-input" id="community_beautification" {{ (is_array(old('community_activities', $senior->community_activities ?? [])) && in_array('Community Beautification', old('community_activities', $senior->community_activities ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="community_beautification">Community Beautification</label></div>
                                <div class="form-check"><input type="checkbox" name="community_activities[]" value="Community / Organization Leader" class="form-check-input" id="community_leader" {{ (is_array(old('community_activities', $senior->community_activities ?? [])) && in_array('Community / Organization Leader', old('community_activities', $senior->community_activities ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="community_leader">Community / Organization Leader</label></div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check"><input type="checkbox" name="community_activities[]" value="Dental" class="form-check-input" id="community_dental" {{ (is_array(old('community_activities', $senior->community_activities ?? [])) && in_array('Dental', old('community_activities', $senior->community_activities ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="community_dental">Dental</label></div>
                                <div class="form-check"><input type="checkbox" name="community_activities[]" value="Friendly Visits" class="form-check-input" id="community_visits" {{ (is_array(old('community_activities', $senior->community_activities ?? [])) && in_array('Friendly Visits', old('community_activities', $senior->community_activities ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="community_visits">Friendly Visits</label></div>
                                <div class="form-check"><input type="checkbox" name="community_activities[]" value="Neighborhood Support Services" class="form-check-input" id="community_support" {{ (is_array(old('community_activities', $senior->community_activities ?? [])) && in_array('Neighborhood Support Services', old('community_activities', $senior->community_activities ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="community_support">Neighborhood Support Services</label></div>
                                <div class="form-check"><input type="checkbox" name="community_activities[]" value="Legal Services" class="form-check-input" id="community_legal" {{ (is_array(old('community_activities', $senior->community_activities ?? [])) && in_array('Legal Services', old('community_activities', $senior->community_activities ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="community_legal">Legal Services</label></div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check"><input type="checkbox" name="community_activities[]" value="Religious" class="form-check-input" id="community_religious" {{ (is_array(old('community_activities', $senior->community_activities ?? [])) && in_array('Religious', old('community_activities', $senior->community_activities ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="community_religious">Religious</label></div>
                                <div class="form-check"><input type="checkbox" name="community_activities[]" value="Counselling / Referral" class="form-check-input" id="community_counselling" {{ (is_array(old('community_activities', $senior->community_activities ?? [])) && in_array('Counselling / Referral', old('community_activities', $senior->community_activities ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="community_counselling">Counselling / Referral</label></div>
                                <div class="form-check"><input type="checkbox" name="community_activities[]" value="Sponsorship" class="form-check-input" id="community_sponsorship" {{ (is_array(old('community_activities', $senior->community_activities ?? [])) && in_array('Sponsorship', old('community_activities', $senior->community_activities ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="community_sponsorship">Sponsorship</label></div>
                                <div class="form-check">
                                    <input type="checkbox" name="community_activities[]" value="Others" class="form-check-input" id="community_others" {{ (is_array(old('community_activities', $senior->community_activities ?? [])) && in_array('Others', old('community_activities', $senior->community_activities ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="community_others">Others, Specify</label>
                                </div>
                                <input type="text" name="community_activities_others_specify" class="form-control form-control-sm mt-2" placeholder="Specify" value="{{ old('community_activities_others_specify', $senior->community_activities_others_specify ?? '') }}">
                            </div>
                        </div>
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
                                <div class="form-check"><input type="radio" name="living_condition_primary" value="Living Alone" class="form-check-input" id="living_alone" onchange="toggleLivingWithOptions()" {{ old('living_condition_primary', $senior->living_condition_primary ?? '') == 'Living Alone' ? 'checked' : '' }}><label class="form-check-label small" for="living_alone">Living Alone</label></div>
                                <div class="form-check"><input type="radio" name="living_condition_primary" value="Living with" class="form-check-input" id="living_with" onchange="toggleLivingWithOptions()" {{ old('living_condition_primary', $senior->living_condition_primary ?? '') == 'Living with' ? 'checked' : '' }}><label class="form-check-label small" for="living_with">Living with</label></div>
                                <div id="living_with_options" class="mt-2 ms-4">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <div class="form-check form-check-sm"><input type="checkbox" name="living_with[]" value="Grand Children" class="form-check-input" id="living_grandchildren" {{ (is_array(old('living_with', $senior->living_with ?? [])) && in_array('Grand Children', old('living_with', $senior->living_with ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="living_grandchildren">Grand Children</label></div>
                                            <div class="form-check form-check-sm"><input type="checkbox" name="living_with[]" value="Common Law Spouse" class="form-check-input" id="living_commonlaw" {{ (is_array(old('living_with', $senior->living_with ?? [])) && in_array('Common Law Spouse', old('living_with', $senior->living_with ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="living_commonlaw">Common Law Spouse</label></div>
                                            <div class="form-check form-check-sm"><input type="checkbox" name="living_with[]" value="Spouse" class="form-check-input" id="living_spouse" {{ (is_array(old('living_with', $senior->living_with ?? [])) && in_array('Spouse', old('living_with', $senior->living_with ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="living_spouse">Spouse</label></div>
                                            <div class="form-check form-check-sm"><input type="checkbox" name="living_with[]" value="In-laws" class="form-check-input" id="living_inlaws" {{ (is_array(old('living_with', $senior->living_with ?? [])) && in_array('In-laws', old('living_with', $senior->living_with ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="living_inlaws">In-laws</label></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-check-sm"><input type="checkbox" name="living_with[]" value="Care Institution" class="form-check-input" id="living_institution" {{ (is_array(old('living_with', $senior->living_with ?? [])) && in_array('Care Institution', old('living_with', $senior->living_with ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="living_institution">Care Institution</label></div>
                                            <div class="form-check form-check-sm"><input type="checkbox" name="living_with[]" value="Children" class="form-check-input" id="living_children" {{ (is_array(old('living_with', $senior->living_with ?? [])) && in_array('Children', old('living_with', $senior->living_with ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="living_children">Children</label></div>
                                            <div class="form-check form-check-sm"><input type="checkbox" name="living_with[]" value="Relatives" class="form-check-input" id="living_relatives" {{ (is_array(old('living_with', $senior->living_with ?? [])) && in_array('Relatives', old('living_with', $senior->living_with ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="living_relatives">Relatives</label></div>
                                            <div class="form-check form-check-sm"><input type="checkbox" name="living_with[]" value="Friends" class="form-check-input" id="living_friends" {{ (is_array(old('living_with', $senior->living_with ?? [])) && in_array('Friends', old('living_with', $senior->living_with ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="living_friends">Friends</label></div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <div class="form-check form-check-sm">
                                            <input type="checkbox" name="living_with[]" value="Others" class="form-check-input" id="living_others" {{ (is_array(old('living_with', $senior->living_with ?? [])) && in_array('Others', old('living_with', $senior->living_with ?? []))) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="living_others">Others</label>
                                        </div>
                                        <input type="text" name="living_with_others_specify" class="form-control form-control-sm mt-1" placeholder="Specify" value="{{ old('living_with_others_specify', $senior->living_with_others_specify ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Question 31: Household Condition -->
                        <div class="col-md-6">
                           <label class="form-label fw-bold small">31. HOUSEHOLD CONDITION (CHECK ALL APPLICABLE)</label>
                            <div class="mb-3">
                                <div class="form-check form-check-sm"><input type="checkbox" name="household_condition[]" value="No privacy" class="form-check-input" id="household_privacy" {{ (is_array(old('household_condition', $senior->household_condition ?? [])) && in_array('No privacy', old('household_condition', $senior->household_condition ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="household_privacy">No privacy</label></div>
                                <div class="form-check form-check-sm"><input type="checkbox" name="household_condition[]" value="Overcrowded in home" class="form-check-input" id="household_overcrowded" {{ (is_array(old('household_condition', $senior->household_condition ?? [])) && in_array('Overcrowded in home', old('household_condition', $senior->household_condition ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="household_overcrowded">Overcrowded in home</label></div>
                                <div class="form-check form-check-sm"><input type="checkbox" name="household_condition[]" value="Informal Settler" class="form-check-input" id="household_informal" {{ (is_array(old('household_condition', $senior->household_condition ?? [])) && in_array('Informal Settler', old('household_condition', $senior->household_condition ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="household_informal">Informal Settler</label></div>
                                <div class="form-check form-check-sm"><input type="checkbox" name="household_condition[]" value="No permanent house" class="form-check-input" id="household_nopermanent" {{ (is_array(old('household_condition', $senior->household_condition ?? [])) && in_array('No permanent house', old('household_condition', $senior->household_condition ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="household_nopermanent">No permanent house</label></div>
                                <div class="form-check form-check-sm"><input type="checkbox" name="household_condition[]" value="High cost of rent" class="form-check-input" id="household_rent" {{ (is_array(old('household_condition', $senior->household_condition ?? [])) && in_array('High cost of rent', old('household_condition', $senior->household_condition ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="household_rent">High cost of rent</label></div>
                                <div class="form-check form-check-sm"><input type="checkbox" name="household_condition[]" value="Longing for independent living quiet atmosphere" class="form-check-input" id="household_independent" {{ (is_array(old('household_condition', $senior->household_condition ?? [])) && in_array('Longing for independent living quiet atmosphere', old('household_condition', $senior->household_condition ?? []))) ? 'checked' : '' }}><label class="form-check-label small" for="household_independent">Longing for independent living quiet atmosphere</label></div>
                                <div class="form-check form-check-sm">
                                    <input type="checkbox" name="household_condition[]" value="Others" class="form-check-input" id="household_others" onchange="toggleHouseholdOthersInput()" {{ (is_array(old('household_condition', $senior->household_condition ?? [])) && in_array('Others', old('household_condition', $senior->household_condition ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="household_others">Others</label>
                                </div>
                                <input type="text" name="household_condition_others_specify" id="household_others_input" class="form-control form-control-sm mt-1" placeholder="Specify" value="{{ old('household_condition_others_specify', $senior->household_condition_others_specify ?? '') }}" disabled>
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
                                    <input type="checkbox" name="source_of_income[]" value="Own earnings, salary / wages" class="form-check-input" id="income_earnings" {{ (is_array(old('source_of_income', $senior->source_of_income ?? [])) && in_array('Own earnings, salary / wages', old('source_of_income', $senior->source_of_income ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_earnings">Own earnings, salary / wages</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Own Pension" class="form-check-input" id="income_pension" {{ (is_array(old('source_of_income', $senior->source_of_income ?? [])) && in_array('Own Pension', old('source_of_income', $senior->source_of_income ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_pension">Own Pension</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Stocks / Dividends" class="form-check-input" id="income_stocks" {{ (is_array(old('source_of_income', $senior->source_of_income ?? [])) && in_array('Stocks / Dividends', old('source_of_income', $senior->source_of_income ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_stocks">Stocks / Dividends</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Dependent on children / relatives" class="form-check-input" id="income_dependent" {{ (is_array(old('source_of_income', $senior->source_of_income ?? [])) && in_array('Dependent on children / relatives', old('source_of_income', $senior->source_of_income ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_dependent">Dependent on children / relatives</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Spouse's salary" class="form-check-input" id="income_spouse_salary" {{ (is_array(old('source_of_income', $senior->source_of_income ?? [])) && in_array('Spouse\'s salary', old('source_of_income', $senior->source_of_income ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_spouse_salary">Spouse's salary</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Spouse Pension" class="form-check-input" id="income_spouse_pension" {{ (is_array(old('source_of_income', $senior->source_of_income ?? [])) && in_array('Spouse Pension', old('source_of_income', $senior->source_of_income ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_spouse_pension">Spouse Pension</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Insurance" class="form-check-input" id="income_insurance" {{ (is_array(old('source_of_income', $senior->source_of_income ?? [])) && in_array('Insurance', old('source_of_income', $senior->source_of_income ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_insurance">Insurance</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Rental / Sharecorp" class="form-check-input" id="income_rental" {{ (is_array(old('source_of_income', $senior->source_of_income ?? [])) && in_array('Rental / Sharecorp', old('source_of_income', $senior->source_of_income ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_rental">Rental / Sharecorp</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Savings" class="form-check-input" id="income_savings" {{ (is_array(old('source_of_income', $senior->source_of_income ?? [])) && in_array('Savings', old('source_of_income', $senior->source_of_income ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_savings">Savings</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Livestock / orchard / farm" class="form-check-input" id="income_livestock" {{ (is_array(old('source_of_income', $senior->source_of_income ?? [])) && in_array('Livestock / orchard / farm', old('source_of_income', $senior->source_of_income ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_livestock">Livestock / orchard / farm</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Fishing" class="form-check-input" id="income_fishing" {{ (is_array(old('source_of_income', $senior->source_of_income ?? [])) && in_array('Fishing', old('source_of_income', $senior->source_of_income ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_fishing">Fishing</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="source_of_income[]" value="Others" class="form-check-input" id="income_others" onchange="toggleSourceIncomeOthersInput()" {{ (is_array(old('source_of_income', $senior->source_of_income ?? [])) && in_array('Others', old('source_of_income', $senior->source_of_income ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="income_others">Others, Specify</label>
                                </div>
                                <input type="text" name="source_of_income_others" id="source_income_others_input" placeholder="Specify" class="form-control form-control-sm mt-2" value="{{ old('source_of_income_others', $senior->source_of_income_others ?? '') }}" disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Question 33.A: Assets: Real and Immovable Properties -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">33. ASSETS: REAL AND IMMOVABLE PROPERTIES (CHECK ALL APPLICABLE)</label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="real_assets[]" value="House" class="form-check-input" id="real_house" {{ (is_array(old('real_assets', $senior->real_assets ?? [])) && in_array('House', old('real_assets', $senior->real_assets ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="real_house">House</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="real_assets[]" value="Lot / Farmland" class="form-check-input" id="real_lot" {{ (is_array(old('real_assets', $senior->real_assets ?? [])) && in_array('Lot / Farmland', old('real_assets', $senior->real_assets ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="real_lot">Lot / Farmland</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="real_assets[]" value="House & Lot" class="form-check-input" id="real_house_lot" {{ (is_array(old('real_assets', $senior->real_assets ?? [])) && in_array('House & Lot', old('real_assets', $senior->real_assets ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="real_house_lot">House & Lot</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="real_assets[]" value="Commercial Building" class="form-check-input" id="real_commercial" {{ (is_array(old('real_assets', $senior->real_assets ?? [])) && in_array('Commercial Building', old('real_assets', $senior->real_assets ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="real_commercial">Commercial Building</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="real_assets[]" value="Fishpond / resort" class="form-check-input" id="real_fishpond" {{ (is_array(old('real_assets', $senior->real_assets ?? [])) && in_array('Fishpond / resort', old('real_assets', $senior->real_assets ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="real_fishpond">Fishpond / resort</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="real_assets[]" value="Others" class="form-check-input" id="real_others" onchange="toggleRealAssetsOthersInput()" {{ (is_array(old('real_assets', $senior->real_assets ?? [])) && in_array('Others', old('real_assets', $senior->real_assets ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="real_others">Others, Specify</label>
                                </div>
                                <input type="text" name="assets_real_and_immovable_others" id="assets_real_and_immovable_others_input" placeholder="Specify" class="form-control form-control-sm mt-2" value="{{ old('assets_real_and_immovable_others', $senior->assets_real_and_immovable_others ?? '') }}" disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Question 34.B: Assets: Personal and Movable Properties -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">34. ASSETS: PERSONAL AND MOVABLE PROPERTIES (CHECK ALL APPLICABLE)</label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="personal_assets[]" value="Automobile" class="form-check-input" id="personal_automobile" {{ (is_array(old('personal_assets', $senior->personal_assets ?? [])) && in_array('Automobile', old('personal_assets', $senior->personal_assets ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="personal_automobile">Automobile</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="personal_assets[]" value="Personal Computer" class="form-check-input" id="personal_computer" {{ (is_array(old('personal_assets', $senior->personal_assets ?? [])) && in_array('Personal Computer', old('personal_assets', $senior->personal_assets ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="personal_computer">Personal Computer</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="personal_assets[]" value="Boats" class="form-check-input" id="personal_boats" {{ (is_array(old('personal_assets', $senior->personal_assets ?? [])) && in_array('Boats', old('personal_assets', $senior->personal_assets ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="personal_boats">Boats</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="personal_assets[]" value="Heavy Equipment" class="form-check-input" id="personal_heavy" {{ (is_array(old('personal_assets', $senior->personal_assets ?? [])) && in_array('Heavy Equipment', old('personal_assets', $senior->personal_assets ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="personal_heavy">Heavy Equipment</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="personal_assets[]" value="Laptops" class="form-check-input" id="personal_laptops" {{ (is_array(old('personal_assets', $senior->personal_assets ?? [])) && in_array('Laptops', old('personal_assets', $senior->personal_assets ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="personal_laptops">Laptops</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="personal_assets[]" value="Drones" class="form-check-input" id="personal_drones" {{ (is_array(old('personal_assets', $senior->personal_assets ?? [])) && in_array('Drones', old('personal_assets', $senior->personal_assets ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="personal_drones">Drones</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="personal_assets[]" value="Motorcycle" class="form-check-input" id="personal_motorcycle" {{ (is_array(old('personal_assets', $senior->personal_assets ?? [])) && in_array('Motorcycle', old('personal_assets', $senior->personal_assets ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="personal_motorcycle">Motorcycle</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="personal_assets[]" value="Mobile Phones" class="form-check-input" id="personal_phones" {{ (is_array(old('personal_assets', $senior->personal_assets ?? [])) && in_array('Mobile Phones', old('personal_assets', $senior->personal_assets ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="personal_phones">Mobile Phones</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" name="personal_assets[]" value="Others" class="form-check-input" id="personal_others" onchange="togglePersonalAssetsOthersInput()" {{ (is_array(old('personal_assets', $senior->personal_assets ?? [])) && in_array('Others', old('personal_assets', $senior->personal_assets ?? []))) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="personal_others">Others, Specify</label>
                                </div>
                                <input type="text" name="personal_assets_others" id="personal_assets_others_input" placeholder="Specify" class="form-control form-control-sm mt-2" value="{{ old('personal_assets_others', $senior->personal_assets_others ?? '') }}" disabled>
                            </div>
                        </div>
                    </div>
                    </div>

                    <div class="row g-3">
                        <!-- Question 35: Monthly Income -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">35. MONTHLY INCOME (IN PHILIPPINE PESO)</label>
                                <input type="number" name="monthly_income" class="form-control form-control-sm" placeholder="Enter monthly income amount" value="{{ old('monthly_income', $senior->monthly_income ?? '') }}" min="0" step="0.01">
                                <small class="form-text text-muted">Enter the exact amount in Philippine Peso (e.g., 25000, 50000.50)</small>
                            </div>
                        </div>

                        <!-- Question 36: Problems / Needs Commonly Encountered -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">36. PROBLEMS / NEEDS COMMONLY ENCOUNTERED (CHECK ALL APPLICABLE)</label>
                                <div class="d-flex flex-column gap-1">
                                    <div class="form-check">
                                        <input type="checkbox" name="problems_needs[]" value="Lack of income / resources" class="form-check-input" id="problems_lack_income" {{ (is_array(old('problems_needs', $senior->problems_needs ?? [])) && in_array('Lack of income / resources', old('problems_needs', $senior->problems_needs ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="problems_lack_income">Lack of income / resources</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="problems_needs[]" value="Loss of income / resources" class="form-check-input" id="problems_loss_income" {{ (is_array(old('problems_needs', $senior->problems_needs ?? [])) && in_array('Loss of income / resources', old('problems_needs', $senior->problems_needs ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="problems_loss_income">Loss of income / resources</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="problems_needs[]" value="Skills / capability training (specify)" class="form-check-input" id="problems_skills" {{ (is_array(old('problems_needs', $senior->problems_needs ?? [])) && in_array('Skills / capability training (specify)', old('problems_needs', $senior->problems_needs ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="problems_skills">Skills / capability training</label>
                                        <input type="text" name="skills_training_specify" placeholder="Specify" class="form-control form-control-sm mt-1" value="{{ old('skills_training_specify', $senior->skills_training_specify ?? '') }}">
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="problems_needs[]" value="Livelihood Opportunities" class="form-check-input" id="problems_livelihood" {{ (is_array(old('problems_needs', $senior->problems_needs ?? [])) && in_array('Livelihood Opportunities', old('problems_needs', $senior->problems_needs ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="problems_livelihood">Livelihood Opportunities</label>
                                        <input type="text" name="livelihood_opportunities" placeholder="Specify" class="form-control form-control-sm mt-1" value="{{ old('livelihood_opportunities', $senior->livelihood_opportunities ?? '') }}">
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="problems_needs[]" value="Others" class="form-check-input" id="problems_others" onchange="toggleProblemsNeedsOthersInput()" {{ (is_array(old('problems_needs', $senior->problems_needs ?? [])) && in_array('Others', old('problems_needs', $senior->problems_needs ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="problems_others">Others, Specify</label>
                                        <input type="text" name="problems_needs_others" id="problems_needs_others_input" placeholder="Specify" class="form-control form-control-sm mt-1" value="{{ old('problems_needs_others', $senior->problems_needs_others ?? '') }}" disabled>
                                    </div>
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
                                        <option value="O+" {{ old('blood_type', $senior->blood_type ?? '') == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ old('blood_type', $senior->blood_type ?? '') == 'O-' ? 'selected' : '' }}>O-</option>
                                        <option value="A+" {{ old('blood_type', $senior->blood_type ?? '') == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ old('blood_type', $senior->blood_type ?? '') == 'A-' ? 'selected' : '' }}>A-</option>
                                        <option value="B+" {{ old('blood_type', $senior->blood_type ?? '') == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ old('blood_type', $senior->blood_type ?? '') == 'B-' ? 'selected' : '' }}>B-</option>
                                        <option value="AB+" {{ old('blood_type', $senior->blood_type ?? '') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ old('blood_type', $senior->blood_type ?? '') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                        <option value="DON'T KNOW" {{ old('blood_type', $senior->blood_type ?? '') == 'DON\'T KNOW' ? 'selected' : '' }}>DON'T KNOW</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label small">PHYSICAL DISABILITY</label>
                                    <input type="text" name="physical_disability" class="form-control form-control-sm" placeholder="Specify" value="{{ old('physical_disability', $senior->physical_disability ?? '') }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small">HEALTH PROBLEMS / AILMENTS</label>
                                    <div class="d-flex flex-column gap-1">
                                        <div class="form-check">
                                            <input type="checkbox" name="health_problems[]" value="Hypertension" class="form-check-input" id="health_hypertension" {{ (is_array(old('health_problems', $senior->health_problems ?? [])) && in_array('Hypertension', old('health_problems', $senior->health_problems ?? []))) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="health_hypertension">Hypertension</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="health_problems[]" value="Arthritis / Gout" class="form-check-input" id="health_arthritis" {{ (is_array(old('health_problems', $senior->health_problems ?? [])) && in_array('Arthritis / Gout', old('health_problems', $senior->health_problems ?? []))) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="health_arthritis">Arthritis / Gout</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="health_problems[]" value="Coronary Heart Disease" class="form-check-input" id="health_heart" {{ (is_array(old('health_problems', $senior->health_problems ?? [])) && in_array('Coronary Heart Disease', old('health_problems', $senior->health_problems ?? []))) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="health_heart">Coronary Heart Disease</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="health_problems[]" value="Diabetes" class="form-check-input" id="health_diabetes" {{ (is_array(old('health_problems', $senior->health_problems ?? [])) && in_array('Diabetes', old('health_problems', $senior->health_problems ?? []))) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="health_diabetes">Diabetes</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="health_problems[]" value="Chronic Kidney Disease" class="form-check-input" id="health_kidney" {{ (is_array(old('health_problems', $senior->health_problems ?? [])) && in_array('Chronic Kidney Disease', old('health_problems', $senior->health_problems ?? []))) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="health_kidney">Chronic Kidney Disease</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="health_problems[]" value="Alzheimer's / Dementia" class="form-check-input" id="health_alzheimer" {{ (is_array(old('health_problems', $senior->health_problems ?? [])) && in_array('Alzheimer\'s / Dementia', old('health_problems', $senior->health_problems ?? []))) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="health_alzheimer">Alzheimer's / Dementia</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="health_problems[]" value="Chronic Obstructive Pulmonary Disease" class="form-check-input" id="health_copd" {{ (is_array(old('health_problems', $senior->health_problems ?? [])) && in_array('Chronic Obstructive Pulmonary Disease', old('health_problems', $senior->health_problems ?? []))) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="health_copd">Chronic Obstructive Pulmonary Disease</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="health_problems[]" value="Others" class="form-check-input" id="health_others" onchange="toggleHealthProblemsOthersInput()" {{ (is_array(old('health_problems', $senior->health_problems ?? [])) && in_array('Others', old('health_problems', $senior->health_problems ?? []))) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="health_others">Others, Specify</label>
                                            <input type="text" name="health_problems_others" id="health_problems_others_input" placeholder="Specify" class="form-control form-control-sm mt-1" value="{{ old('health_problems_others', $senior->health_problems_others ?? '') }}" disabled>
                                        </div>
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
                                        <input type="checkbox" name="dental_concern[]" value="Needs Dental Care" class="form-check-input" id="dental_needs_care" {{ (is_array(old('dental_concern', $senior->dental_concern ?? [])) && in_array('Needs Dental Care', old('dental_concern', $senior->dental_concern ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="dental_needs_care">Needs Dental Care</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="dental_concern[]" value="Others" class="form-check-input" id="dental_others" onchange="toggleDentalConcernOthersInput()" {{ (is_array(old('dental_concern', $senior->dental_concern ?? [])) && in_array('Others', old('dental_concern', $senior->dental_concern ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="dental_others">Others, Specify</label>
                                        <input type="text" name="dental_concern_others" id="dental_concern_others_input" placeholder="Specify" class="form-control form-control-sm mt-1" value="{{ old('dental_concern_others', $senior->dental_concern_others ?? '') }}" disabled>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold small">39. OPTICAL</label>
                                <div class="d-flex flex-column gap-1">
                                    <div class="form-check">
                                        <input type="checkbox" name="visual_concern[]" value="Eye impairment" class="form-check-input" id="visual_impairment" {{ (is_array(old('visual_concern', $senior->visual_concern ?? [])) && in_array('Eye impairment', old('visual_concern', $senior->visual_concern ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="visual_impairment">Eye impairment</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="visual_concern[]" value="Needs eye care" class="form-check-input" id="visual_needs_care" {{ (is_array(old('visual_concern', $senior->visual_concern ?? [])) && in_array('Needs eye care', old('visual_concern', $senior->visual_concern ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="visual_needs_care">Needs eye care</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="visual_concern[]" value="Others" class="form-check-input" id="visual_others" onchange="toggleVisualConcernOthersInput()" {{ (is_array(old('visual_concern', $senior->visual_concern ?? [])) && in_array('Others', old('visual_concern', $senior->visual_concern ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="visual_others">Others, Specify</label>
                                        <input type="text" name="visual_concern_others" id="visual_concern_others_input" placeholder="Specify" class="form-control form-control-sm mt-1" value="{{ old('visual_concern_others', $senior->visual_concern_others ?? '') }}" disabled>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold small">40. HEARING</label>
                                <div class="d-flex flex-column gap-1">
                                    <div class="form-check">
                                        <input type="checkbox" name="hearing_condition[]" value="Aural impairment" class="form-check-input" id="hearing_impairment" {{ (is_array(old('hearing_condition', $senior->hearing_condition ?? [])) && in_array('Aural impairment', old('hearing_condition', $senior->hearing_condition ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="hearing_impairment">Aural impairment</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="hearing_condition[]" value="Others" class="form-check-input" id="hearing_others" onchange="toggleHearingConditionOthersInput()" {{ (is_array(old('hearing_condition', $senior->hearing_condition ?? [])) && in_array('Others', old('hearing_condition', $senior->hearing_condition ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="hearing_others">Others, Specify</label>
                                        <input type="text" name="hearing_condition_others" id="hearing_condition_others_input" placeholder="Specify" class="form-control form-control-sm mt-1" value="{{ old('hearing_condition_others', $senior->hearing_condition_others ?? '') }}" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Question 41.: Social / Emotional -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">41. SOCIAL / EMOTIONAL</label>
                                <div class="d-flex flex-column gap-1">
                                    <div class="form-check">
                                        <input type="checkbox" name="social_emotional[]" value="Feeling neglect / rejection" class="form-check-input" id="social_neglect" {{ (is_array(old('social_emotional', $senior->social_emotional ?? [])) && in_array('Feeling neglect / rejection', old('social_emotional', $senior->social_emotional ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="social_neglect">Feeling neglect / rejection</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="social_emotional[]" value="Feeling helplessness / worthlessness" class="form-check-input" id="social_helpless" {{ (is_array(old('social_emotional', $senior->social_emotional ?? [])) && in_array('Feeling helplessness / worthlessness', old('social_emotional', $senior->social_emotional ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="social_helpless">Feeling helplessness / worthlessness</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="social_emotional[]" value="Feeling loneliness / isolate" class="form-check-input" id="social_lonely" {{ (is_array(old('social_emotional', $senior->social_emotional ?? [])) && in_array('Feeling loneliness / isolate', old('social_emotional', $senior->social_emotional ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="social_lonely">Feeling loneliness / isolate</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="social_emotional[]" value="Lack leisure / recreational activities" class="form-check-input" id="social_leisure" {{ (is_array(old('social_emotional', $senior->social_emotional ?? [])) && in_array('Lack leisure / recreational activities', old('social_emotional', $senior->social_emotional ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="social_leisure">Lack leisure / recreational activities</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="social_emotional[]" value="Lack SC friendly environment" class="form-check-input" id="social_environment" {{ (is_array(old('social_emotional', $senior->social_emotional ?? [])) && in_array('Lack SC friendly environment', old('social_emotional', $senior->social_emotional ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="social_environment">Lack SC friendly environment</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="social_emotional[]" value="Others" class="form-check-input" id="social_others" onchange="toggleSocialEmotionalOthersInput()" {{ (is_array(old('social_emotional', $senior->social_emotional ?? [])) && in_array('Others', old('social_emotional', $senior->social_emotional ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="social_others">Others, Specify</label>
                                        <input type="text" name="social_emotional_others" id="social_emotional_others_input" placeholder="Specify" class="form-control form-control-sm mt-1" value="{{ old('social_emotional_others', $senior->social_emotional_others ?? '') }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">42. AREA / DIFFICULTY</label>
                                <div class="d-flex flex-column gap-1">
                                    <div class="form-check">
                                        <input type="checkbox" name="area_difficulty[]" value="High Cost of medicines" class="form-check-input" id="area_cost" {{ (is_array(old('area_difficulty', $senior->area_difficulty ?? [])) && in_array('High Cost of medicines', old('area_difficulty', $senior->area_difficulty ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="area_cost">High Cost of medicines</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="area_difficulty[]" value="Lack of medicines" class="form-check-input" id="area_lack_meds" {{ (is_array(old('area_difficulty', $senior->area_difficulty ?? [])) && in_array('Lack of medicines', old('area_difficulty', $senior->area_difficulty ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="area_lack_meds">Lack of medicines</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="area_difficulty[]" value="Lack of medical attention" class="form-check-input" id="area_medical" {{ (is_array(old('area_difficulty', $senior->area_difficulty ?? [])) && in_array('Lack of medical attention', old('area_difficulty', $senior->area_difficulty ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="area_medical">Lack of medical attention</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="area_difficulty[]" value="Others" class="form-check-input" id="area_others" onchange="toggleAreaDifficultyOthersInput()" {{ (is_array(old('area_difficulty', $senior->area_difficulty ?? [])) && in_array('Others', old('area_difficulty', $senior->area_difficulty ?? []))) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="area_others">Others, Specify</label>
                                        <input type="text" name="area_difficulty_others" id="area_difficulty_others_input" placeholder="Specify" class="form-control form-control-sm mt-1" value="{{ old('area_difficulty_others', $senior->area_difficulty_others ?? '') }}" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Question 43: List of Medicines for Maintenance -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small">43. LIST OF MEDICINES FOR MAINTENANCE <em class="text-muted">(Type all your maintenance medicines. Example : Amlodipine 10mg, Losartan 50mg, etc.)</em></label>
                        <textarea name="maintenance_medicines" class="form-control form-control-sm mt-2" rows="4" placeholder="List your maintenance medicines here...">{{ old('maintenance_medicines', $senior->maintenance_medicines ?? '') }}</textarea>
                    </div>

                    <!-- Questions 44 & 45: Medical Check-up -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">44. DO YOU HAVE A SCHEDULED MEDICAL/PHYSICAL CHECK-UP?</label>
                            <select name="scheduled_checkup" class="form-select form-select-sm mt-2">
                                <option value="">Select</option>
                                <option value="Yes" {{ old('scheduled_checkup', $senior->scheduled_checkup ?? '') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ old('scheduled_checkup', $senior->scheduled_checkup ?? '') == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">45. IF YES, WHEN IS IT DONE?</label>
                            <select name="checkup_frequency" class="form-select form-select-sm mt-2">
                                <option value="">Select</option>
                                <option value="Monthly" {{ old('checkup_frequency', $senior->checkup_frequency ?? '') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="Quarterly" {{ old('checkup_frequency', $senior->checkup_frequency ?? '') == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="Semi-annually" {{ old('checkup_frequency', $senior->checkup_frequency ?? '') == 'Semi-annually' ? 'selected' : '' }}>Semi-annually</option>
                                <option value="Annually" {{ old('checkup_frequency', $senior->checkup_frequency ?? '') == 'Annually' ? 'selected' : '' }}>Annually</option>
                                <option value="As needed" {{ old('checkup_frequency', $senior->checkup_frequency ?? '') == 'As needed' ? 'selected' : '' }}>As needed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- CERTIFICATION -->
                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" name="certification" required class="form-check-input" id="certification">
                        <label class="form-check-label small" for="certification">
                            This certifies that I have willingly given my personal consent and willingfully participated in the provision of data anf relevant information regarding my person, being part of the establishment of database of Senior Citizens.
                        </label>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i>
                        {{ isset($senior) && $senior ? 'UPDATE SENIOR' : 'ADD NEW SENIOR' }}
                    </button>
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
                livingWithOptions.style.backgroundColor = '#e3f2fd';
                livingWithOptions.style.border = '2px solid #4285f4';
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
            document.getElementById('typingModal').style.display = 'block';
        }
        
        // Close typing instructions modal
        function closeTypingInstructions() {
            document.getElementById('typingModal').style.display = 'none';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('typingModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        // Toggle household others input field
        function toggleHouseholdOthersInput() {
            const othersCheckbox = document.querySelector('input[name="household_condition[]"][value="Others"]');
            const othersInput = document.getElementById('household_others_input');
            
            if (othersCheckbox && othersInput) {
                othersInput.disabled = !othersCheckbox.checked;
                if (!othersCheckbox.checked) {
                    othersInput.value = '';
                }
            }
        }
        
        // Toggle source of income others input field
        function toggleSourceIncomeOthersInput() {
            const othersCheckbox = document.querySelector('input[name="source_of_income[]"][value="Others"]');
            const othersInput = document.getElementById('source_income_others_input');
            
            if (othersCheckbox && othersInput) {
                othersInput.disabled = !othersCheckbox.checked;
                if (!othersCheckbox.checked) {
                    othersInput.value = '';
                }
            }
        }

        // Toggle problems/needs others input field
        function toggleProblemsNeedsOthersInput() {
            const othersCheckbox = document.querySelector('input[name="problems_needs[]"][value="Others"]');
            const othersInput = document.getElementById('problems_needs_others_input');
            
            if (othersCheckbox && othersInput) {
                othersInput.disabled = !othersCheckbox.checked;
                if (!othersCheckbox.checked) {
                    othersInput.value = '';
                }
            }
        }
        
        // Toggle real assets others input field
        function toggleRealAssetsOthersInput() {
            const othersCheckbox = document.querySelector('input[name="real_assets[]"][value="Others"]');
            const othersInput = document.getElementById('assets_real_and_immovable_others_input');
            
            if (othersCheckbox && othersInput) {
                othersInput.disabled = !othersCheckbox.checked;
                if (!othersCheckbox.checked) {
                    othersInput.value = '';
                }
            }
        }
        
        // Toggle personal assets others input field
        function togglePersonalAssetsOthersInput() {
            const othersCheckbox = document.querySelector('input[name="personal_assets[]"][value="Others"]');
            const othersInput = document.getElementById('personal_assets_others_input');
            
            if (othersCheckbox && othersInput) {
                othersInput.disabled = !othersCheckbox.checked;
                if (!othersCheckbox.checked) {
                    othersInput.value = '';
                }
            }
        }
        
        // Toggle health problems others input field
        function toggleHealthProblemsOthersInput() {
            const othersCheckbox = document.querySelector('input[name="health_problems[]"][value="Others"]');
            const othersInput = document.getElementById('health_problems_others_input');
            
            if (othersCheckbox && othersInput) {
                othersInput.disabled = !othersCheckbox.checked;
                if (!othersCheckbox.checked) {
                    othersInput.value = '';
                }
            }
        }
        
        // Toggle dental concern others input field
        function toggleDentalConcernOthersInput() {
            const othersCheckbox = document.querySelector('input[name="dental_concern[]"][value="Others"]');
            const othersInput = document.getElementById('dental_concern_others_input');
            
            if (othersCheckbox && othersInput) {
                othersInput.disabled = !othersCheckbox.checked;
                if (!othersCheckbox.checked) {
                    othersInput.value = '';
                }
            }
        }
        
        // Toggle visual concern others input field
        function toggleVisualConcernOthersInput() {
            const othersCheckbox = document.querySelector('input[name="visual_concern[]"][value="Others"]');
            const othersInput = document.getElementById('visual_concern_others_input');
            
            if (othersCheckbox && othersInput) {
                othersInput.disabled = !othersCheckbox.checked;
                if (!othersCheckbox.checked) {
                    othersInput.value = '';
                }
            }
        }
        
        // Toggle hearing condition others input field
        function toggleHearingConditionOthersInput() {
            const othersCheckbox = document.querySelector('input[name="hearing_condition[]"][value="Others"]');
            const othersInput = document.getElementById('hearing_condition_others_input');
            
            if (othersCheckbox && othersInput) {
                othersInput.disabled = !othersCheckbox.checked;
                if (!othersCheckbox.checked) {
                    othersInput.value = '';
                }
            }
        }
        
        // Toggle social emotional others input field
        function toggleSocialEmotionalOthersInput() {
            const othersCheckbox = document.querySelector('input[name="social_emotional[]"][value="Others"]');
            const othersInput = document.getElementById('social_emotional_others_input');
            
            if (othersCheckbox && othersInput) {
                othersInput.disabled = !othersCheckbox.checked;
                if (!othersCheckbox.checked) {
                    othersInput.value = '';
                }
            }
        }
        
        // Toggle area difficulty others input field
        function toggleAreaDifficultyOthersInput() {
            const othersCheckbox = document.querySelector('input[name="area_difficulty[]"][value="Others"]');
            const othersInput = document.getElementById('area_difficulty_others_input');
            
            if (othersCheckbox && othersInput) {
                othersInput.disabled = !othersCheckbox.checked;
                if (!othersCheckbox.checked) {
                    othersInput.value = '';
                }
            }
        }
        
        // Address data for cascading dropdowns
        const addressData = {
            'NCR': {
                'Metro Manila': ['Caloocan', 'Las Piñas', 'Makati', 'Malabon', 'Mandaluyong', 'Manila', 'Marikina', 'Muntinlupa', 'Navotas', 'Parañaque', 'Pasay', 'Pasig', 'Pateros', 'Quezon City', 'San Juan', 'Taguig', 'Valenzuela']
            },
            'CAR': {
                'Abra': ['Bangued', 'Boliney', 'Bucay', 'Bucloc', 'Daguioman', 'Danglas', 'Dolores', 'La Paz', 'Lacub', 'Lagangilang', 'Lagayan', 'Langiden', 'Licuan-Baay', 'Luba', 'Malibcong', 'Manabo', 'Peñarrubia', 'Pidigan', 'Pilar', 'Sallapadan', 'San Isidro', 'San Juan', 'San Quintin', 'Tayum', 'Tineg', 'Tubo', 'Villaviciosa'],
                'Benguet': ['Atok', 'Baguio', 'Bakun', 'Bokod', 'Buguias', 'Itogon', 'Kabayan', 'Kapangan', 'Kibungan', 'La Trinidad', 'Mankayan', 'Sablan', 'Tuba', 'Tublay']
            },
            'Region I': {
                'Ilocos Norte': ['Adams', 'Bacarra', 'Badoc', 'Bangui', 'Banna', 'Batac', 'Burgos', 'Carasi', 'Currimao', 'Dingras', 'Dumalneg', 'Laoag', 'Marcos', 'Nueva Era', 'Pagudpud', 'Paoay', 'Pasuquin', 'Piddig', 'Pinili', 'San Nicolas', 'Sarrat', 'Solsona', 'Vintar'],
                'Ilocos Sur': ['Alilem', 'Banayoyo', 'Bantay', 'Burgos', 'Cabugao', 'Candon', 'Caoayan', 'Cervantes', 'Galimuyod', 'Gregorio del Pilar', 'Lidlidda', 'Magsingal', 'Nagbukel', 'Narvacan', 'Quirino', 'Salcedo', 'San Emilio', 'San Esteban', 'San Ildefonso', 'San Juan', 'San Vicente', 'Santa', 'Santa Catalina', 'Santa Cruz', 'Santa Lucia', 'Santa Maria', 'Santiago', 'Santo Domingo', 'Sigay', 'Sinait', 'Sugpon', 'Suyo', 'Tagudin', 'Vigan'],
                'La Union': ['Agoo', 'Aringay', 'Bacnotan', 'Bagulin', 'Balaoan', 'Bangar', 'Bauang', 'Burgos', 'Caba', 'Luna', 'Naguilian', 'Pugo', 'Rosario', 'San Fernando', 'San Gabriel', 'San Juan', 'Santo Tomas', 'Santol', 'Sudipen', 'Tubao'],
                'Pangasinan': ['Agno', 'Aguilar', 'Alaminos', 'Alcala', 'Anda', 'Asingan', 'Balungao', 'Bani', 'Basista', 'Bautista', 'Bayambang', 'Binalonan', 'Binmaley', 'Bolinao', 'Bugallon', 'Burgos', 'Calasiao', 'Dagupan', 'Dasol', 'Infanta', 'Labrador', 'Laoac', 'Lingayen', 'Mabini', 'Malasiqui', 'Manaoag', 'Mangaldan', 'Mangatarem', 'Mapandan', 'Natividad', 'Pozorrubio', 'Rosales', 'San Carlos', 'San Fabian', 'San Jacinto', 'San Manuel', 'San Nicolas', 'San Quintin', 'Santa Barbara', 'Santa Maria', 'Santo Tomas', 'Sison', 'Sual', 'Tayug', 'Umingan', 'Urbiztondo', 'Urdaneta', 'Villasis']
            },
            'Region II': {
                'Batanes': ['Basco', 'Itbayat', 'Ivana', 'Mahatao', 'Sabtang', 'Uyugan'],
                'Cagayan': ['Abulug', 'Alcala', 'Allacapan', 'Amulung', 'Aparri', 'Baggao', 'Ballesteros', 'Buguey', 'Calayan', 'Camalaniugan', 'Claveria', 'Enrile', 'Gattaran', 'Gonzaga', 'Iguig', 'Lal-lo', 'Lasam', 'Pamplona', 'Peñablanca', 'Piat', 'Rizal', 'Sanchez-Mira', 'Santa Ana', 'Santa Praxedes', 'Santa Teresita', 'Santo Niño', 'Solana', 'Tuao', 'Tuguegarao']
            },
            'Region III': {
                'Bataan': ['Abucay', 'Bagac', 'Balanga', 'Dinalupihan', 'Hermosa', 'Limay', 'Mariveles', 'Morong', 'Orani', 'Orion', 'Pilar', 'Samal'],
                'Bulacan': ['Angat', 'Balagtas', 'Baliuag', 'Bocaue', 'Bulakan', 'Bustos', 'Calumpit', 'Guiguinto', 'Hagonoy', 'Malolos', 'Marilao', 'Meycauayan', 'Norzagaray', 'Obando', 'Pandi', 'Paombong', 'Plaridel', 'Pulilan', 'San Ildefonso', 'San Jose del Monte', 'San Miguel', 'San Rafael', 'Santa Maria'],
                'Nueva Ecija': ['Aliaga', 'Bongabon', 'Cabanatuan', 'Cabiao', 'Carranglan', 'Cuyapo', 'Gabaldon', 'Gapan', 'General Mamerto Natividad', 'General Tinio', 'Guimba', 'Jaen', 'Laur', 'Licab', 'Llanera', 'Lupao', 'Muñoz', 'Nampicuan', 'Pantabangan', 'Peñaranda', 'Quezon', 'Rizal', 'San Antonio', 'San Isidro', 'San Jose', 'San Leonardo', 'Santa Rosa', 'Santo Domingo', 'Talavera', 'Talugtug', 'Zaragoza'],
                'Pampanga': ['Angeles', 'Apalit', 'Arayat', 'Bacolor', 'Candaba', 'Floridablanca', 'Guagua', 'Lubao', 'Mabalacat', 'Macabebe', 'Magalang', 'Masantol', 'Mexico', 'Minalin', 'Porac', 'San Fernando', 'San Luis', 'San Simon', 'Santa Ana', 'Santa Rita', 'Santo Tomas', 'Sasmuan'],
                'Tarlac': ['Anao', 'Bamban', 'Camiling', 'Capas', 'Concepcion', 'Gerona', 'La Paz', 'Mayantoc', 'Moncada', 'Paniqui', 'Pura', 'Ramos', 'San Clemente', 'San Jose', 'San Manuel', 'Santa Ignacia', 'Tarlac City', 'Victoria'],
                'Zambales': ['Botolan', 'Cabangan', 'Candelaria', 'Castillejos', 'Iba', 'Masinloc', 'Olongapo', 'Palauig', 'San Antonio', 'San Felipe', 'San Marcelino', 'San Narciso', 'Santa Cruz', 'Subic']
            },
            'Region IV-A': {
                'Batangas': ['Agoncillo', 'Alitagtag', 'Balayan', 'Balete', 'Batangas City', 'Bauan', 'Calaca', 'Calatagan', 'Cuenca', 'Ibaan', 'Laurel', 'Lemery', 'Lian', 'Lipa', 'Lobo', 'Mabini', 'Malvar', 'Mataasnakahoy', 'Nasugbu', 'Padre Garcia', 'Rosario', 'San Jose', 'San Juan', 'San Luis', 'San Nicolas', 'San Pascual', 'Santa Teresita', 'Santo Tomas', 'Taal', 'Talisay', 'Tanauan', 'Taysan', 'Tingloy', 'Tuy'],
                'Cavite': ['Alfonso', 'Amadeo', 'Bacoor', 'Carmona', 'Cavite City', 'Dasmariñas', 'General Emilio Aguinaldo', 'General Mariano Alvarez', 'General Trias', 'Imus', 'Indang', 'Kawit', 'Magallanes', 'Maragondon', 'Mendez', 'Naic', 'Noveleta', 'Rosario', 'Silang', 'Tagaytay', 'Tanza', 'Ternate', 'Trece Martires'],
                'Laguna': ['Alaminos', 'Bay', 'Biñan', 'Cabuyao', 'Calamba', 'Calauan', 'Cavinti', 'Famy', 'Kalayaan', 'Liliw', 'Los Baños', 'Luisiana', 'Lumban', 'Mabitac', 'Magdalena', 'Majayjay', 'Nagcarlan', 'Paete', 'Pagsanjan', 'Pakil', 'Pangil', 'Pila', 'Rizal', 'San Pablo', 'San Pedro', 'Santa Cruz', 'Santa Maria', 'Santa Rosa', 'Siniloan', 'Victoria'],
                'Quezon': ['Agdangan', 'Alabat', 'Atimonan', 'Buenavista', 'Burdeos', 'Calauag', 'Candelaria', 'Catanauan', 'Dolores', 'General Luna', 'General Nakar', 'Guinayangan', 'Gumaca', 'Infanta', 'Jomalig', 'Lopez', 'Lucban', 'Lucena', 'Macalelon', 'Mauban', 'Mulanay', 'Padre Burgos', 'Pagbilao', 'Panukulan', 'Patnanungan', 'Perez', 'Pitogo', 'Plaridel', 'Polillo', 'Quezon', 'Real', 'Sampaloc', 'San Andres', 'San Antonio', 'San Francisco', 'San Narciso', 'Sariaya', 'Tagkawayan', 'Tayabas', 'Tiaong', 'Unisan'],
                'Rizal': ['Angono', 'Antipolo', 'Baras', 'Binangonan', 'Cainta', 'Cardona', 'Jalajala', 'Morong', 'Pililla', 'Rodriguez', 'San Mateo', 'Tanay', 'Taytay', 'Teresa']
            },
            'Region IV-B': {
                'Marinduque': ['Boac', 'Buenavista', 'Gasan', 'Mogpog', 'Santa Cruz', 'Torrijos'],
                'Occidental Mindoro': ['Abra de Ilog', 'Calintaan', 'Looc', 'Lubang', 'Magsaysay', 'Mamburao', 'Paluan', 'Rizal', 'Sablayan', 'San Jose', 'Santa Cruz'],
                'Oriental Mindoro': ['Baco', 'Bansud', 'Bongabong', 'Bulalacao', 'Calapan', 'Gloria', 'Mansalay', 'Naujan', 'Pinamalayan', 'Pola', 'Puerto Galera', 'Roxas', 'San Teodoro', 'Socorro', 'Victoria'],
                'Palawan': ['Aborlan', 'Agutaya', 'Araceli', 'Balabac', 'Bataraza', 'Brookes Point', 'Busuanga', 'Cagayancillo', 'Coron', 'Culion', 'Cuyo', 'Dumaran', 'El Nido', 'Kalayaan', 'Linapacan', 'Magsaysay', 'Narra', 'Puerto Princesa', 'Quezon', 'Rizal', 'Roxas', 'San Vicente', 'Sofronio Española', 'Taytay'],
                'Romblon': ['Alcantara', 'Banton', 'Cajidiocan', 'Calatrava', 'Concepcion', 'Corcuera', 'Ferrol', 'Looc', 'Magdiwang', 'Odiongan', 'Romblon', 'San Agustin', 'San Andres', 'San Fernando', 'San Jose', 'Santa Fe', 'Santa Maria']
            },
            'Region V': {
                'Albay': ['Bacacay', 'Camalig', 'Daraga', 'Guinobatan', 'Jovellar', 'Legazpi', 'Libon', 'Ligao', 'Malilipot', 'Malinao', 'Manito', 'Oas', 'Pio Duran', 'Polangui', 'Rapu-Rapu', 'Santo Domingo', 'Tabaco', 'Tiwi'],
                'Camarines Norte': ['Basud', 'Capalonga', 'Daet', 'Jose Panganiban', 'Labo', 'Mercedes', 'Paracale', 'San Lorenzo Ruiz', 'San Vicente', 'Santa Elena', 'Talisay', 'Vinzons'],
                'Camarines Sur': ['Baao', 'Balatan', 'Bato', 'Bombon', 'Buhi', 'Bula', 'Cabusao', 'Calabanga', 'Camaligan', 'Canaman', 'Caramoan', 'Del Gallego', 'Gainza', 'Garchitorena', 'Goa', 'Iriga', 'Lagonoy', 'Libmanan', 'Lupi', 'Magarao', 'Milaor', 'Minalabac', 'Nabua', 'Naga', 'Ocampo', 'Pamplona', 'Pasacao', 'Pili', 'Presentacion', 'Ragay', 'Sagñay', 'San Fernando', 'San Jose', 'Sipocot', 'Siruma', 'Tigaon', 'Tinambac'],
                'Catanduanes': ['Bagamanoc', 'Baras', 'Bato', 'Caramoran', 'Gigmoto', 'Pandan', 'Panganiban', 'San Andres', 'San Miguel', 'Viga', 'Virac'],
                'Masbate': ['Aroroy', 'Baleno', 'Balud', 'Batuan', 'Cataingan', 'Cawayan', 'Claveria', 'Dimasalang', 'Esperanza', 'Mandaon', 'Masbate City', 'Milagros', 'Mobo', 'Monreal', 'Palanas', 'Pio V. Corpuz', 'Placer', 'San Fernando', 'San Jacinto', 'San Pascual', 'Uson'],
                'Sorsogon': ['Barcelona', 'Bulan', 'Bulusan', 'Casiguran', 'Castilla', 'Donsol', 'Gubat', 'Irosin', 'Juban', 'Magallanes', 'Matnog', 'Pilar', 'Prieto Diaz', 'Santa Magdalena', 'Sorsogon City']
            },
            'Region VI': {
                'Aklan': ['Altavas', 'Balete', 'Banga', 'Batan', 'Buruanga', 'Ibajay', 'Kalibo', 'Lezo', 'Libacao', 'Madalag', 'Makato', 'Malay', 'Malinao', 'Nabas', 'New Washington', 'Numancia', 'Tangalan'],
                'Antique': ['Anini-y', 'Barbaza', 'Belison', 'Bugasong', 'Caluya', 'Culasi', 'Hamtic', 'Laua-an', 'Libertad', 'Pandan', 'Patnongon', 'San Jose', 'San Remigio', 'Sebaste', 'Sibalom', 'Tibiao', 'Tobias Fornier', 'Valderrama'],
                'Capiz': ['Cuartero', 'Dao', 'Dumalag', 'Dumarao', 'Ivisan', 'Jamindan', 'Ma-ayon', 'Mambusao', 'Panay', 'Panitan', 'Pilar', 'Pontevedra', 'President Roxas', 'Roxas City', 'Sapian', 'Sigma', 'Tapaz'],
                'Guimaras': ['Buenavista', 'Jordan', 'Nueva Valencia', 'San Lorenzo', 'Sibunag'],
                'Iloilo': ['Ajuy', 'Alimodian', 'Anilao', 'Badiangan', 'Balasan', 'Banate', 'Barotac Nuevo', 'Barotac Viejo', 'Batad', 'Bingawan', 'Cabatuan', 'Calinog', 'Carles', 'Concepcion', 'Dingle', 'Dueñas', 'Dumangas', 'Estancia', 'Guimbal', 'Igbaras', 'Iloilo City', 'Janiuay', 'Lambunao', 'Leganes', 'Lemery', 'Leon', 'Maasin', 'Miagao', 'Mina', 'New Lucena', 'Oton', 'Passi', 'Pavia', 'Pototan', 'San Dionisio', 'San Enrique', 'San Joaquin', 'San Miguel', 'San Rafael', 'Santa Barbara', 'Sara', 'Tigbauan', 'Tubungan', 'Zarraga'],
                'Negros Occidental': ['Bacolod', 'Bago', 'Binalbagan', 'Cadiz', 'Calatrava', 'Candoni', 'Cauayan', 'Enrique B. Magalona', 'Escalante', 'Himamaylan', 'Hinigaran', 'Hinoba-an', 'Ilog', 'Isabela', 'Kabankalan', 'La Carlota', 'La Castellana', 'Manapla', 'Moises Padilla', 'Murcia', 'Pontevedra', 'Pulupandan', 'Sagay', 'Salvador Benedicto', 'San Carlos', 'San Enrique', 'Silay', 'Sipalay', 'Talisay', 'Toboso', 'Valladolid', 'Victorias']
            },
            'Region VII': {
                'Bohol': ['Alburquerque', 'Alicia', 'Anda', 'Antequera', 'Baclayon', 'Balilihan', 'Batuan', 'Bien Unido', 'Bilar', 'Buenavista', 'Calape', 'Candijay', 'Carmen', 'Catigbian', 'Clarin', 'Corella', 'Cortes', 'Dagohoy', 'Danao', 'Dauis', 'Dimiao', 'Duero', 'Garcia Hernandez', 'Getafe', 'Guindulman', 'Inabanga', 'Jagna', 'Jetafe', 'Lila', 'Loay', 'Loboc', 'Loon', 'Mabini', 'Maribojoc', 'Panglao', 'Pilar', 'President Carlos P. Garcia', 'Sagbayan', 'San Isidro', 'San Miguel', 'Sevilla', 'Sierra Bullones', 'Sikatuna', 'Tagbilaran', 'Talibon', 'Trinidad', 'Tubigon', 'Ubay', 'Valencia'],
                'Cebu': ['Alcantara', 'Alcoy', 'Alegria', 'Aloguinsan', 'Argao', 'Asturias', 'Badian', 'Balamban', 'Bantayan', 'Barili', 'Bogo', 'Boljoon', 'Borbon', 'Carcar', 'Carmen', 'Catmon', 'Cebu City', 'Compostela', 'Consolacion', 'Cordova', 'Daanbantayan', 'Dalaguete', 'Danao', 'Dumanjug', 'Ginatilan', 'Lapu-Lapu', 'Liloan', 'Madridejos', 'Malabuyoc', 'Mandaue', 'Medellin', 'Minglanilla', 'Moalboal', 'Naga', 'Oslob', 'Pilar', 'Pinamungajan', 'Poro', 'Ronda', 'Samboan', 'San Fernando', 'San Francisco', 'San Remigio', 'Santa Fe', 'Santander', 'Sibonga', 'Sogod', 'Tabogon', 'Tabuelan', 'Talisay', 'Toledo', 'Tuburan', 'Tudela'],
                'Negros Oriental': ['Amlan', 'Ayungon', 'Bacong', 'Bais', 'Basay', 'Bayawan', 'Bindoy', 'Canlaon', 'Dauin', 'Dumaguete', 'Guihulngan', 'Jimalalud', 'La Libertad', 'Mabinay', 'Manjuyod', 'Pamplona', 'San Jose', 'Santa Catalina', 'Siaton', 'Sibulan', 'Tanjay', 'Tayasan', 'Valencia', 'Vallehermoso', 'Zamboanguita'],
                'Siquijor': ['Enrique Villanueva', 'Larena', 'Lazi', 'Maria', 'San Juan', 'Siquijor']
            },
            'Region VIII': {
                'Biliran': ['Almeria', 'Biliran', 'Cabucgayan', 'Caibiran', 'Culaba', 'Kawayan', 'Maripipi', 'Naval'],
                'Eastern Samar': ['Arteche', 'Balangiga', 'Balangkayan', 'Borongan', 'Can-avid', 'Dolores', 'General MacArthur', 'Giporlos', 'Guiuan', 'Hernani', 'Jipapad', 'Lawaan', 'Llorente', 'Maslog', 'Maydolong', 'Mercedes', 'Oras', 'Quinapondan', 'Salcedo', 'San Julian', 'San Policarpo', 'Sulat', 'Taft'],
                'Leyte': ['Abuyog', 'Alangalang', 'Albuera', 'Babatngon', 'Barugo', 'Bato', 'Baybay', 'Burauen', 'Calubian', 'Capoocan', 'Carigara', 'Dagami', 'Dulag', 'Hilongos', 'Hindang', 'Inopacan', 'Isabel', 'Jaro', 'Javier', 'Julita', 'Kananga', 'La Paz', 'Leyte', 'MacArthur', 'Mahaplag', 'Matag-ob', 'Matalom', 'Mayorga', 'Merida', 'Ormoc', 'Palo', 'Palompon', 'Pastrana', 'San Isidro', 'San Miguel', 'Santa Fe', 'Tabango', 'Tabontabon', 'Tacloban', 'Tanauan', 'Tolosa', 'Tunga', 'Villaba'],
                'Northern Samar': ['Allen', 'Biri', 'Bobon', 'Capul', 'Catarman', 'Catubig', 'Gamay', 'Laoang', 'Lapinig', 'Las Navas', 'Lavezares', 'Lope de Vega', 'Mapanas', 'Mondragon', 'Palapag', 'Pambujan', 'Rosario', 'San Antonio', 'San Isidro', 'San Jose', 'San Roque', 'San Vicente', 'Silvino Lobos', 'Victoria'],
                'Samar': ['Almagro', 'Basey', 'Calbayog', 'Calbiga', 'Catbalogan', 'Daram', 'Gandara', 'Hinabangan', 'Jiabong', 'Marabut', 'Matuguinao', 'Motiong', 'Pinabacdao', 'San Jorge', 'San Jose de Buan', 'San Sebastian', 'Santa Margarita', 'Santa Rita', 'Santo Niño', 'Tagapul-an', 'Talalora', 'Tarangnan', 'Villareal', 'Zumarraga'],
                'Southern Leyte': ['Anahawan', 'Bontoc', 'Hinunangan', 'Hinundayan', 'Libagon', 'Liloan', 'Limasawa', 'Maasin', 'Macrohon', 'Malitbog', 'Padre Burgos', 'Pintuyan', 'Saint Bernard', 'San Francisco', 'San Juan', 'San Ricardo', 'Silago', 'Sogod', 'Tomas Oppus']
            },
            'Region IX': {
                'Zamboanga del Norte': ['Baliguian', 'Godod', 'Gutalac', 'Kalawit', 'Labason', 'Leon B. Postigo', 'Liloy', 'Manukan', 'Mutia', 'Piñan', 'Polanco', 'Pres. Manuel A. Roxas', 'Rizal', 'Salug', 'Sergio Osmeña Sr.', 'Siayan', 'Sibuco', 'Sibutad', 'Sindangan', 'Siocon', 'Sirawai', 'Tampilisan'],
                'Zamboanga del Sur': ['Aurora', 'Bayog', 'Dimataling', 'Dinas', 'Dumalinao', 'Dumingag', 'Guipos', 'Josefina', 'Kumalarang', 'Labangan', 'Lakewood', 'Lapuyan', 'Mahayag', 'Margosatubig', 'Midsalip', 'Molave', 'Pagadian', 'Pitogo', 'Ramon Magsaysay', 'San Miguel', 'San Pablo', 'Sominot', 'Tabina', 'Tambulig', 'Tigbao', 'Tukuran', 'Vincenzo A. Sagun'],
                'Zamboanga Sibugay': ['Alicia', 'Buug', 'Diplahan', 'Imelda', 'Ipil', 'Kabasalan', 'Mabuhay', 'Malangas', 'Naga', 'Olutanga', 'Payao', 'Roseller Lim', 'Siay', 'Talusan', 'Titay', 'Tungawan']
            },
            'Region X': {
                'Bukidnon': ['Baungon', 'Cabanglasan', 'Damulog', 'Dangcagan', 'Don Carlos', 'Impasugong', 'Kadingilan', 'Kalilangan', 'Kibawe', 'Kitaotao', 'Lantapan', 'Libona', 'Malaybalay', 'Malitbog', 'Manolo Fortich', 'Maramag', 'Pangantucan', 'Quezon', 'San Fernando', 'Sumilao', 'Talakag', 'Valencia'],
                'Camiguin': ['Catarman', 'Guinsiliban', 'Mahinog', 'Mambajao', 'Sagay'],
                'Lanao del Norte': ['Bacolod', 'Baloi', 'Bayang', 'Iligan', 'Kapatagan', 'Kauswagan', 'Kolambugan', 'Lala', 'Linamon', 'Magsaysay', 'Maigo', 'Matungao', 'Munai', 'Nunungan', 'Pantao Ragat', 'Pantar', 'Poona Piagapo', 'Salvador', 'Sapad', 'Sultan Naga Dimaporo', 'Tagoloan', 'Tangcal', 'Tubod'],
                'Misamis Occidental': ['Aloran', 'Baliangao', 'Bonifacio', 'Calamba', 'Clarin', 'Concepcion', 'Don Victoriano Chiongbian', 'Jimenez', 'Lopez Jaena', 'Oroquieta', 'Ozamiz', 'Panaon', 'Plaridel', 'Sapang Dalaga', 'Sinacaban', 'Tangub', 'Tudela'],
                'Misamis Oriental': ['Alubijid', 'Balingasag', 'Balingoan', 'Binuangan', 'Cagayan de Oro', 'Catarman', 'Claveria', 'El Salvador', 'Gingoog', 'Gitagum', 'Initao', 'Jasaan', 'Kinoguitan', 'Lagonglong', 'Laguindingan', 'Libertad', 'Lugait', 'Magsaysay', 'Manticao', 'Medina', 'Naawan', 'Opol', 'Salay', 'Sugbongcogon', 'Tagoloan', 'Talisayan', 'Villanueva']
            },
            'Region XI': {
                'Davao de Oro': ['Compostela', 'Laak', 'Mabini', 'Maco', 'Maragusan', 'Mawab', 'Monkayo', 'Montevista', 'Nabunturan', 'New Bataan', 'Pantukan'],
                'Davao del Norte': ['Asuncion', 'Braulio E. Dujali', 'Carmen', 'Kapalong', 'New Corella', 'Panabo', 'Samal', 'San Isidro', 'Santo Tomas', 'Tagum', 'Talaingod'],
                'Davao del Sur': ['Bansalan', 'Davao City', 'Digos', 'Hagonoy', 'Kiblawan', 'Magsaysay', 'Malalag', 'Matanao', 'Padada', 'Santa Cruz', 'Sulop'],
                'Davao Occidental': ['Don Marcelino', 'Jose Abad Santos', 'Malita', 'Santa Maria', 'Sarangani'],
                'Davao Oriental': ['Baganga', 'Banaybanay', 'Boston', 'Caraga', 'Cateel', 'Governor Generoso', 'Lupon', 'Manay', 'Mati', 'San Isidro', 'Tarragona']
            },
            'Region XII': {
                'Cotabato': ['Alamada', 'Aleosan', 'Antipas', 'Arakan', 'Banisilan', 'Carmen', 'Kabacan', 'Kidapawan', 'Libungan', 'M\'lang', 'Magpet', 'Makilala', 'Matalam', 'Midsayap', 'Pigcawayan', 'Pikit', 'President Roxas', 'Tulunan'],
                'Sarangani': ['Alabel', 'Glan', 'Kiamba', 'Maasim', 'Maitum', 'Malapatan', 'Malungon'],
                'South Cotabato': ['Banga', 'General Santos', 'Koronadal', 'Lake Sebu', 'Norala', 'Polomolok', 'Santo Niño', 'Surallah', 'T\'boli', 'Tampakan', 'Tantangan', 'Tupi'],
                'Sultan Kudarat': ['Bagumbayan', 'Columbio', 'Esperanza', 'Isulan', 'Kalamansig', 'Lambayong', 'Lebak', 'Lutayan', 'Palimbang', 'President Quirino', 'Senator Ninoy Aquino', 'Tacurong']
            },
            'Region XIII': {
                'Agusan del Norte': ['Buenavista', 'Butuan', 'Cabadbaran', 'Carmen', 'Jabonga', 'Kitcharao', 'Las Nieves', 'Magallanes', 'Nasipit', 'Remedios T. Romualdez', 'Santiago', 'Tubay'],
                'Agusan del Sur': ['Bayugan', 'Bunawan', 'Esperanza', 'La Paz', 'Loreto', 'Prosperidad', 'Rosario', 'San Francisco', 'San Luis', 'Santa Josefa', 'Sibagat', 'Talacogon', 'Trento', 'Veruela'],
                'Dinagat Islands': ['Basilisa', 'Cagdianao', 'Dinagat', 'Libjo', 'Loreto', 'San Jose', 'Tubajon'],
                'Surigao del Norte': ['Alegria', 'Bacuag', 'Burgos', 'Claver', 'Dapa', 'Del Carmen', 'General Luna', 'Gigaquit', 'Mainit', 'Malimono', 'Pilar', 'Placer', 'San Benito', 'San Francisco', 'San Isidro', 'Santa Monica', 'Sison', 'Socorro', 'Surigao City', 'Tagana-an', 'Tubod'],
                'Surigao del Sur': ['Barobo', 'Bayabas', 'Bislig', 'Cagwait', 'Cantilan', 'Carmen', 'Carrascal', 'Cortes', 'Hinatuan', 'Lanuza', 'Lianga', 'Lingig', 'Madrid', 'Marihatag', 'San Agustin', 'San Miguel', 'Tagbina', 'Tago', 'Tandag']
            },
            'NIR': {
                'Negros Occidental': ['Bacolod', 'Bago', 'Binalbagan', 'Cadiz', 'Calatrava', 'Candoni', 'Cauayan', 'Enrique B. Magalona', 'Escalante', 'Himamaylan', 'Hinigaran', 'Hinoba-an', 'Ilog', 'Isabela', 'Kabankalan', 'La Carlota', 'La Castellana', 'Manapla', 'Moises Padilla', 'Murcia', 'Pontevedra', 'Pulupandan', 'Sagay', 'Salvador Benedicto', 'San Carlos', 'San Enrique', 'Silay', 'Sipalay', 'Talisay', 'Toboso', 'Valladolid', 'Victorias'],
                'Negros Oriental': ['Amlan', 'Ayungon', 'Bacong', 'Bais', 'Basay', 'Bayawan', 'Bindoy', 'Canlaon', 'Dauin', 'Dumaguete', 'Guihulngan', 'Jimalalud', 'La Libertad', 'Mabinay', 'Manjuyod', 'Pamplona', 'San Jose', 'Santa Catalina', 'Siaton', 'Sibulan', 'Tanjay', 'Tayasan', 'Valencia', 'Vallehermoso', 'Zamboanguita'],
                'Siquijor': ['Enrique Villanueva', 'Larena', 'Lazi', 'Maria', 'San Juan', 'Siquijor']
            },
            'BARMM': {
                'Basilan': ['Akbar', 'Al-Barka', 'Hadji Mohammad Ajul', 'Hadji Muhtamad', 'Isabela City', 'Lamitan', 'Lantawan', 'Maluso', 'Sumisip', 'Tabuan-Lasa', 'Tipo-Tipo', 'Tuburan', 'Ungkaya Pukan'],
                'Lanao del Sur': ['Amai Manabilang', 'Bacolod-Kalawi', 'Balabagan', 'Balindong', 'Bayang', 'Binidayan', 'Buadiposo-Buntong', 'Bubong', 'Butig', 'Calanogas', 'Ditsaan-Ramain', 'Ganassi', 'Kapai', 'Kapatagan', 'Lumba-Bayabao', 'Lumbaca-Unayan', 'Lumbatan', 'Lumbayanague', 'Madalum', 'Madamba', 'Maguing', 'Malabang', 'Marantao', 'Marawi', 'Marogong', 'Masiu', 'Mulondo', 'Pagayawan', 'Piagapo', 'Picong', 'Poona Bayabao', 'Pualas', 'Saguiaran', 'Sultan Dumalondong', 'Tagoloan II', 'Tamparan', 'Taraka', 'Tubaran', 'Tugaya', 'Wao'],
                'Maguindanao del Norte': ['Barira', 'Buldon', 'Datu Blah T. Sinsuat', 'Datu Odin Sinsuat', 'Kabuntalan', 'Matanog', 'Northern Kabuntalan', 'Parang', 'Sultan Kudarat', 'Sultan Mastura', 'Talitay', 'Upi'],
                'Maguindanao del Sur': ['Ampatuan', 'Buluan', 'Datu Abdullah Sangki', 'Datu Anggal Midtimbang', 'Datu Hoffer Ampatuan', 'Datu Montawal', 'Datu Paglas', 'Datu Piang', 'Datu Salibo', 'Datu Saudi-Ampatuan', 'Datu Unsay', 'General Salipada K. Pendatun', 'Guindulungan', 'Mamasapano', 'Mangudadatu', 'Pagalungan', 'Paglat', 'Pandag', 'Rajah Buayan', 'Shariff Aguak', 'Shariff Saydona Mustapha', 'South Upi', 'Sultan sa Barongis', 'Talayan'],
                'Sulu': ['Banguingui', 'Hadji Panglima Tahil', 'Indanan', 'Jolo', 'Kalingalan Caluang', 'Lugus', 'Luuk', 'Maimbung', 'Old Panamao', 'Omar', 'Pandami', 'Panglima Estino', 'Pangutaran', 'Parang', 'Pata', 'Patikul', 'Siasi', 'Talipao', 'Tapul'],
                'Tawi-Tawi': ['Bongao', 'Languyan', 'Mapun', 'Panglima Sugala', 'Sapa-Sapa', 'Sibutu', 'Simunul', 'Sitangkai', 'South Ubian', 'Tandubas', 'Turtle Islands']
            }
        };

        // Barangay data for major cities (expandable)
        const barangayData = {
            'Lingayen': ['Aliwekwek', 'Baay', 'Balangobong', 'Balococ', 'Bantayan', 'Basing', 'Capandanan', 'Domalandan Center', 'Domalandan East', 'Domalandan West', 'Dorongan', 'Dulag', 'Estanza', 'Lasip', 'Libsong East', 'Libsong West', 'Malawa', 'Malimpuec', 'Maniboc', 'Matalava', 'Naguelguel', 'Namolan', 'Pangapisan North', 'Pangapisan Sur', 'Poblacion', 'Quibaol', 'Rosario', 'Sabangan', 'Talogtog', 'Tonton', 'Tumbar', 'Wawa'],
            'Quezon City': ['Alicia', 'Bagong Pag-asa', 'Bahay Toro', 'Balingasa', 'Bungad', 'Damar', 'Damayan', 'Del Monte', 'Diliman', 'Don Manuel', 'Duyan-Duyan', 'E. Rodriguez', 'East Kamias', 'Escolta', 'Fairview', 'Gulod', 'Kaligayahan', 'Kamuning', 'Katipunan', 'Krus Na Ligas', 'Laging Handa', 'Libis', 'Loyola Heights', 'Maharlika', 'Malaya', 'Mariana', 'Mariblo', 'Marilag', 'Masambong', 'Matandang Balara', 'Milagrosa', 'N.S. Amoranto', 'Nagkaisang Nayon', 'Nayong Kanluran', 'New Era', 'North Fairview', 'Novaliches Proper', 'Obrero', 'Old Balara', 'Paang Bundok', 'Pag-ibig sa Nayon', 'Payatas', 'Phil-Am', 'Pinagkaisahan', 'Pinyahan', 'Project 6', 'Roxas', 'Sacred Heart', 'San Agustin', 'San Antonio', 'San Bartolome', 'San Isidro Labrador', 'San Jose', 'San Martin de Porres', 'San Roque', 'Santa Cruz', 'Santa Lucia', 'Santa Monica', 'Santa Teresita', 'Santo Cristo', 'Santo Domingo', 'Santo Niño', 'Sienna', 'Silangan', 'Socorro', 'Tagumpay', 'Talayan', 'Tandang Sora', 'Tatalon', 'Teachers Village East', 'Teachers Village West', 'U.P. Campus', 'Ugong Norte', 'Unang Sigaw', 'Valencia', 'Vasra', 'Veterans Village', 'Villa Maria Clara', 'West Kamias', 'West Triangle', 'White Plains'],
            'Manila': ['Barangay 1', 'Barangay 2', 'Barangay 3', 'Barangay 4', 'Barangay 5', 'Barangay 6', 'Barangay 7', 'Barangay 8', 'Barangay 9', 'Barangay 10', 'Binondo', 'Ermita', 'Intramuros', 'Malate', 'Paco', 'Pandacan', 'Port Area', 'Quiapo', 'Sampaloc', 'San Andres', 'San Miguel', 'San Nicolas', 'Santa Ana', 'Santa Cruz', 'Santa Mesa', 'Tondo'],
            'Makati': ['Bangkal', 'Bel-Air', 'Cembo', 'Comembo', 'Dasmariñas', 'East Rembo', 'Forbes Park', 'Guadalupe Nuevo', 'Guadalupe Viejo', 'Kasilawan', 'La Paz', 'Magallanes', 'Olympia', 'Palanan', 'Pembo', 'Pinagkaisahan', 'Pio del Pilar', 'Pitogo', 'Poblacion', 'Post Proper Northside', 'Post Proper Southside', 'Rizal', 'San Antonio', 'San Isidro', 'San Lorenzo', 'Santa Cruz', 'Singkamas', 'South Cembo', 'Tejeros', 'Urdaneta', 'Valenzuela', 'West Rembo'],
            'Cebu City': ['Adlaon', 'Agsungot', 'Apas', 'Bacayan', 'Banilad', 'Basak Pardo', 'Basak San Nicolas', 'Binaliw', 'Bonbon', 'Budla-an', 'Buhisan', 'Bulacao', 'Busay', 'Calamba', 'Cambinocot', 'Capitol Site', 'Carreta', 'Cogon Pardo', 'Cogon Ramos', 'Day-as', 'Duljo Fatima', 'Ermita', 'Guadalupe', 'Guba', 'Hipodromo', 'Inayawan', 'Kalubihan', 'Kalunasan', 'Kamagayan', 'Kamputhaw', 'Kasambagan', 'Kinasang-an Pardo', 'Labangon', 'Lahug', 'Lorega San Miguel', 'Lusaran', 'Luz', 'Mabini', 'Mabolo', 'Malubog', 'Mambaling', 'Pahina Central', 'Pahina San Nicolas', 'Pardo', 'Pari-an', 'Paril', 'Pasil', 'Pit-os', 'Poblacion Pardo', 'Pulangbato', 'Pung-ol Sibugay', 'Punta Princesa', 'Quiot', 'Sambag I', 'Sambag II', 'San Antonio', 'San Jose', 'San Nicolas Central', 'San Nicolas Proper', 'San Roque', 'Santa Cruz', 'Santo Niño', 'Sapangdaku', 'Sawang Calero', 'Sinsin', 'Sirao', 'Suba', 'Sudlon I', 'Sudlon II', 'Tabunan', 'Tagba-o', 'Talamban', 'Taptap', 'Tejero', 'Tinago', 'Tisa', 'To-ong', 'Zapatera'],
            'Davao City': ['Agdao', 'Alambre', 'Atan-Awe', 'Baganihan', 'Bago Aplaya', 'Bago Gallera', 'Bago Oshiro', 'Baguio', 'Balengaeng', 'Baliok', 'Bangkas Heights', 'Bantol', 'Baracatan', 'Barangay 1-A', 'Barangay 2-A', 'Barangay 3-A', 'Barangay 4-A', 'Barangay 5-A', 'Barangay 6-A', 'Barangay 7-A', 'Barangay 8-A', 'Barangay 9-A', 'Barangay 10-A', 'Barangay 11-B', 'Barangay 12-B', 'Barangay 13-B', 'Barangay 14-B', 'Barangay 15-B', 'Barangay 16-B', 'Barangay 17-B', 'Barangay 18-B', 'Barangay 19-B', 'Barangay 20-B', 'Barangay 21-C', 'Barangay 22-C', 'Barangay 23-C', 'Barangay 24-C', 'Barangay 25-C', 'Barangay 26-C', 'Barangay 27-C', 'Barangay 28-C', 'Barangay 29-C', 'Barangay 30-C', 'Barangay 31-D', 'Barangay 32-D', 'Barangay 33-D', 'Barangay 34-D', 'Barangay 35-D', 'Barangay 36-D', 'Barangay 37-D', 'Barangay 38-D', 'Barangay 39-D', 'Barangay 40-D', 'Bucana', 'Buda', 'Buhangin', 'Bunawan', 'Cabantian', 'Cadalian', 'Calinan', 'Callawa', 'Camansi', 'Carmen', 'Catalunan Grande', 'Catalunan Pequeño', 'Catitipan', 'Cawayan', 'Centro', 'Communal', 'Crossing Bayabas', 'Dacudao', 'Daliao', 'Daliaon Plantation', 'Datu Salumay', 'Eden', 'Fatima', 'Gatungan', 'Gubat', 'Gumitan', 'Ilang', 'Inayangan', 'Indangan', 'Kap. Tomas Monteverde Sr.', 'Kilate', 'Lacson', 'Lamanan', 'Lampianao', 'Langub', 'Lapu-lapu', 'Leon Garcia', 'Lizada', 'Los Amigos', 'Lubogan', 'Lumiad', 'Ma-a', 'Mabuhay', 'Magsaysay', 'Mahayag', 'Malabog', 'Malagos', 'Malamba', 'Manambulan', 'Mandug', 'Manuel Guianga', 'Mapula', 'Marapangi', 'Marilog', 'Matina Aplaya', 'Matina Biao', 'Matina Crossing', 'Matina Pangi', 'Mintal', 'Mudiang', 'Mulig', 'New Carmen', 'New Valencia', 'Pampanga', 'Panacan', 'Panabo', 'Paquibato', 'Paradise Embac', 'Riverside', 'Saloy', 'Sambulog', 'San Isidro', 'Santo Tomas', 'Sasa', 'Sibulan', 'Sirawan', 'Sirib', 'Suawan', 'Subasta', 'Sumimao', 'Tacunan', 'Tagakpan', 'Tagurano', 'Talandang', 'Talomo', 'Tamayong', 'Tambobong', 'Tamugan', 'Tapak', 'Tawan-tawan', 'Tibuloy', 'Tibungco', 'Tigatto', 'Toril', 'Tugbok', 'Tungkalan', 'Ubalde', 'Ula', 'Waan', 'Wangan', 'Wilfredo Aquino']
        };

        // Function to populate provinces based on selected region
        function populateProvinces() {
            console.log('populateProvinces function called');
            
            const regionSelect = document.querySelector('select[name="region"]');
            const provinceSelect = document.querySelector('select[name="province"]');
            const citySelect = document.querySelector('select[name="city"]');
            const barangaySelect = document.querySelector('select[name="barangay"]');
            
            console.log('Elements found:', {
                regionSelect: regionSelect,
                provinceSelect: provinceSelect,
                citySelect: citySelect,
                barangaySelect: barangaySelect
            });
            
            const selectedRegion = regionSelect.value;
            console.log('Selected region:', selectedRegion);
            console.log('Address data for region:', addressData[selectedRegion]);
            
            // Clear and reset dependent dropdowns
            provinceSelect.innerHTML = '<option value="">Select Province</option>';
            citySelect.innerHTML = '<option value="">Select Province First</option>';
            barangaySelect.innerHTML = '<option value="">Select City First</option>';
            
            if (selectedRegion && addressData[selectedRegion]) {
                console.log('Enabling province dropdown and populating options');
                provinceSelect.disabled = false;
                citySelect.disabled = true;
                barangaySelect.disabled = true;
                
                // Populate provinces
                Object.keys(addressData[selectedRegion]).forEach(province => {
                    const option = document.createElement('option');
                    option.value = province;
                    option.textContent = province;
                    provinceSelect.appendChild(option);
                    console.log('Added province option:', province);
                });
            } else {
                console.log('No data found for region or region not selected');
                provinceSelect.disabled = true;
                citySelect.disabled = true;
                barangaySelect.disabled = true;
                provinceSelect.innerHTML = '<option value="">Select Region First</option>';
            }
        }

        // Function to populate cities based on selected province
        function populateCities() {
            const regionSelect = document.querySelector('select[name="region"]');
            const provinceSelect = document.querySelector('select[name="province"]');
            const citySelect = document.querySelector('select[name="city"]');
            const barangaySelect = document.querySelector('select[name="barangay"]');
            
            const selectedRegion = regionSelect.value;
            const selectedProvince = provinceSelect.value;
            
            // Clear and reset dependent dropdowns
            citySelect.innerHTML = '<option value="">Select City</option>';
            barangaySelect.innerHTML = '<option value="">Select City First</option>';
            
            if (selectedRegion && selectedProvince && addressData[selectedRegion] && addressData[selectedRegion][selectedProvince]) {
                citySelect.disabled = false;
                barangaySelect.disabled = true;
                
                // Populate cities
                addressData[selectedRegion][selectedProvince].forEach(city => {
                    const option = document.createElement('option');
                    option.value = city;
                    option.textContent = city;
                    citySelect.appendChild(option);
                });
            } else {
                citySelect.disabled = true;
                barangaySelect.disabled = true;
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
            
            // Address dropdown event listeners with error checking
            const regionSelect = document.querySelector('select[name="region"]');
            const provinceSelect = document.querySelector('select[name="province"]');
            const citySelect = document.querySelector('select[name="city"]');
            
            console.log('Region select found:', regionSelect);
            console.log('Province select found:', provinceSelect);
            console.log('City select found:', citySelect);
            
            if (regionSelect) {
                regionSelect.addEventListener('change', function() {
                    console.log('Region changed to:', this.value);
                    populateProvinces();
                });
            }
            
            if (provinceSelect) {
                provinceSelect.addEventListener('change', function() {
                    console.log('Province changed to:', this.value);
                    populateCities();
                });
            }
            
            if (citySelect) {
                citySelect.addEventListener('change', function() {
                    console.log('City changed to:', this.value);
                    enableBarangay();
                });
            }
        });

        // Dynamic Children and Dependents Management
        let childRowCount = 5; // Starting count (already has 5 rows)
        let dependentRowCount = 2; // Starting count (already has 2 rows)

        // Add new child row
        function addChildRow() {
            childRowCount++;
            const tbody = document.getElementById('childrenTableBody');
            const newRow = document.createElement('tr');
            
            newRow.innerHTML = `
                <td><input type="text" name="child_name_${childRowCount}" class="form-control form-control-sm" placeholder="Child Name"></td>
                <td><input type="text" name="child_occupation_${childRowCount}" class="form-control form-control-sm" placeholder="Occupation"></td>
                <td><input type="text" name="child_income_${childRowCount}" class="form-control form-control-sm" placeholder="Income"></td>
                <td><input type="number" name="child_age_${childRowCount}" class="form-control form-control-sm" placeholder="Age"></td>
                <td>
                    <select name="child_working_${childRowCount}" class="form-control form-control-sm">
                        <option value="">Is working?</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeChildRow(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            
            tbody.appendChild(newRow);
        }

        // Remove child row
        function removeChildRow(button) {
            const row = button.closest('tr');
            const tbody = document.getElementById('childrenTableBody');
            
            // Don't remove if it's the last row
            if (tbody.children.length > 1) {
                row.remove();
            } else {
                // If it's the last row, just clear the inputs instead of removing
                const inputs = row.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.type === 'checkbox') {
                        input.checked = false;
                    } else {
                        input.value = '';
                    }
                });
            }
        }

        // Add new dependent row
        function addDependentRow() {
            dependentRowCount++;
            const tbody = document.getElementById('dependentsTableBody');
            const newRow = document.createElement('tr');
            
            newRow.innerHTML = `
                <td><input type="text" name="dependent_name_${dependentRowCount}" class="form-control form-control-sm" placeholder="Name of Dependent"></td>
                <td><input type="text" name="dependent_occupation_${dependentRowCount}" class="form-control form-control-sm" placeholder="Occupation of Dependent"></td>
                <td><input type="text" name="dependent_income_${dependentRowCount}" class="form-control form-control-sm" placeholder="Income"></td>
                <td><input type="number" name="dependent_age_${dependentRowCount}" class="form-control form-control-sm" placeholder="Age"></td>
                <td>
                    <select name="dependent_working_${dependentRowCount}" class="form-control form-control-sm">
                        <option value="">Is Working?</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDependentRow(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            
            tbody.appendChild(newRow);
        }

        // Remove dependent row
        function removeDependentRow(button) {
            const row = button.closest('tr');
            const tbody = document.getElementById('dependentsTableBody');
            
            // Don't remove if it's the last row
            if (tbody.children.length > 1) {
                row.remove();
            } else {
                // If it's the last row, just clear the inputs instead of removing
                const inputs = row.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.type === 'checkbox') {
                        input.checked = false;
                    } else {
                        input.value = '';
                    }
                });
            }
        }

        // Add form submission validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('master-profile-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    
                    // Form action is now properly set for both create and update
                    
                    // Check if status is selected
                    const statusSelect = document.querySelector('select[name="status"]');
                    if (!statusSelect || !statusSelect.value) {
                        e.preventDefault();
                        alert('Please select a senior status.');
                        return false;
                    }
                    
                    // Form validation complete
                    
                    // Form submission proceeding
                });
            }
        });

        // Monthly income is now auto-filled via the value attribute in the input field
        
        // OCR Document Processing Function
        function processOcrDocument() {
            console.log("processOcrDocument function called");
            const ocrFileUpload = document.getElementById('ocrFileUpload');
            const ocrStatus = document.getElementById('ocrStatus');
            
            if (!ocrFileUpload || !ocrFileUpload.files || !ocrFileUpload.files.length) {
                console.log("No file selected");
                ocrStatus.innerHTML = '<span class="text-danger">Please select a document to scan</span>';
                return;
            }
            
            console.log("File selected:", ocrFileUpload.files[0].name);
            const file = ocrFileUpload.files[0];
            const formData = new FormData();
            formData.append('file', file); // Changed from 'form_image' to 'file'
            
            // Check if file is PDF
            const isPdf = file.type === 'application/pdf';
            console.log("File type:", file.type, "Is PDF:", isPdf);
            
            // Update status message based on file type
            if (isPdf) {
                ocrStatus.innerHTML = '<span class="text-info"><i class="fas fa-spinner fa-spin me-1"></i> Processing PDF document, this may take longer...</span>';
            } else {
                ocrStatus.innerHTML = '<span class="text-info"><i class="fas fa-spinner fa-spin me-1"></i> Processing document, please wait...</span>';
            }
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            console.log("CSRF Token:", csrfToken ? "Found" : "Not found");
            
            // Send to API endpoint
            fetch('/ocr/process', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log("OCR Response received:", response.status);
                
                // Enhanced error logging
                if (!response.ok) {
                    // Clone the response to read it twice
                    return response.text().then(text => {
                        console.error("OCR Error Response:", {
                            status: response.status,
                            statusText: response.statusText,
                            headers: Object.fromEntries([...response.headers.entries()]),
                            body: text
                        });
                        throw new Error('Network response was not ok: ' + response.status + ' - ' + text);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log("OCR Data received:", data);
                if (data.success) {
                    ocrStatus.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i> Document processed successfully!</span>';
                    
                    // Fill form fields with extracted data
                    fillFormFields(data);
                } else {
                    console.error("OCR Error:", data.message || "Unknown error");
                    ocrStatus.innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> ${data.message || 'Error processing document'}</span>`;
                }
            })
            .catch(error => {
                console.error('OCR processing error:', error);
                ocrStatus.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> Error processing document: ' + error.message + '</span>';
            });
        }
        
        // Helper function to fill form fields with fallback patterns
        function fillFormFields(data) {
            if (data.data) {
                console.log("Filling form fields with data:", data.data);
                
                // Last name with fallback patterns
                const lastNameValue = 
                    data.data.last_name || 
                    data.data.lastname || 
                    data.data.surname || 
                    data.data.family_name || 
                    data.data.familyname || 
                    data.data.lname;
                
                if (lastNameValue) document.querySelector('input[name="last_name"]').value = lastNameValue;
                
                // First name with fallback patterns
                const firstNameValue = 
                    data.data.first_name || 
                    data.data.firstname || 
                    data.data.given_name || 
                    data.data.givenname || 
                    data.data.name || 
                    data.data.name_first || 
                    data.data.maiden_name || 
                    data.data.fname;
                
                if (firstNameValue) document.querySelector('input[name="first_name"]').value = firstNameValue;
                
                // Middle name with fallback patterns
                const middleNameValue = 
                    data.data.middle_name || 
                    data.data.middlename || 
                    data.data.middle_initial || 
                    data.data.middleinitial || 
                    data.data.mname || 
                    data.data.mi;
                
                if (middleNameValue) document.querySelector('input[name="middle_name"]').value = middleNameValue;
                
                // Other fields without fallbacks
                if (data.data.date_of_birth) document.querySelector('input[name="date_of_birth"]').value = data.data.date_of_birth;
                if (data.data.birth_place) document.querySelector('input[name="birth_place"]').value = data.data.birth_place;
                
                console.log("Form fields filled successfully");
            } else {
                console.error("No data available to fill form fields");
            }
        }
        
        // Original OCR Document Processing (keeping for compatibility)
        document.addEventListener('DOMContentLoaded', function() {
            console.log("DOM Content Loaded - Setting up OCR functionality");
            const ocrFileUpload = document.getElementById('ocrFileUpload');
            const processOcrBtn = document.getElementById('processOcrBtn');
            const ocrStatus = document.getElementById('ocrStatus');
            
            console.log("OCR Button:", processOcrBtn);
            console.log("OCR File Upload:", ocrFileUpload);
            console.log("OCR Status:", ocrStatus);
            
            if (processOcrBtn) {
                console.log("Adding click event listener to OCR button");
                processOcrBtn.addEventListener('click', function(e) {
                    console.log("OCR Scan button clicked");
                    e.preventDefault();
                    
                    if (!ocrFileUpload || !ocrFileUpload.files || !ocrFileUpload.files.length) {
                        console.log("No file selected");
                        ocrStatus.innerHTML = '<span class="text-danger">Please select a document to scan</span>';
                        return;
                    }
                    
                    console.log("File selected:", ocrFileUpload.files[0].name);
                    const file = ocrFileUpload.files[0];
                    const formData = new FormData();
                    formData.append('form_image', file);
                    
                    // Check if file is PDF
                    const isPdf = file.type === 'application/pdf';
                    console.log("File type:", file.type, "Is PDF:", isPdf);
                    
                    // Update status message based on file type
                    if (isPdf) {
                        ocrStatus.innerHTML = '<span class="text-info"><i class="fas fa-spinner fa-spin me-1"></i> Processing PDF document, this may take longer...</span>';
                    } else {
                        ocrStatus.innerHTML = '<span class="text-info"><i class="fas fa-spinner fa-spin me-1"></i> Processing document, please wait...</span>';
                    }
                    
                    // Send to API endpoint
                    console.log("Sending OCR request to server...");
                    
                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    console.log("CSRF Token:", csrfToken ? "Found" : "Not found");
                    
                    // Use the correct API endpoint
                    fetch('/ocr/process', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken || ''
                        }
                    })
                    .then(response => {
                        console.log("OCR Response received:", response.status);
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Check if processing is asynchronous (for PDF files)
                            if (data.status === 'processing') {
                                // Set up polling for async processing
                                ocrStatus.innerHTML = '<span class="text-info"><i class="fas fa-spinner fa-spin me-1"></i> Processing in progress, please wait...</span>';
                                
                                // Poll for results every 3 seconds
                                const pollInterval = setInterval(() => {
                                    fetch(`/api/vision/check-status/${data.job_id}`, {
                                        method: 'GET',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(statusData => {
                                        if (statusData.status === 'completed') {
                                            clearInterval(pollInterval);
                                            ocrStatus.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i> Document processed successfully!</span>';
                                            
                                            // Fill form fields with extracted data from completed job
                                            fillFormFields(statusData.data);
                                        } else if (statusData.status === 'failed') {
                                            clearInterval(pollInterval);
                                            ocrStatus.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> Processing failed: ' + statusData.message + '</span>';
                                        }
                                    })
                                    .catch(error => {
                                        clearInterval(pollInterval);
                                        console.error('Error checking status:', error);
                                        ocrStatus.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> Error checking status</span>';
                                    });
                                }, 3000);
                            } else {
                                ocrStatus.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i> Document processed successfully!</span>';
                                
                                // Fill form fields with extracted data
                                fillFormFields(data.data);
                            }
                        } else {
                            ocrStatus.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> Processing failed: ' + data.message + '</span>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        ocrStatus.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> Error processing document. Please try again.</span>';
                    });
                    
                    // Helper function to fill form fields
                    function fillFormFields(data) {
                        if (data.data) {
                            if (data.data.last_name) document.querySelector('input[name="last_name"]').value = data.data.last_name;
                            if (data.data.first_name) document.querySelector('input[name="first_name"]').value = data.data.first_name;
                            if (data.data.middle_name) document.querySelector('input[name="middle_name"]').value = data.data.middle_name;
                            if (data.data.date_of_birth) document.querySelector('input[name="date_of_birth"]').value = data.data.date_of_birth;
                            if (data.data.birth_place) document.querySelector('input[name="birth_place"]').value = data.data.birth_place;
                            if (data.data.marital_status) {
                                const maritalSelect = document.querySelector('select[name="marital_status"]');
                                for (let i = 0; i < maritalSelect.options.length; i++) {
                                    if (maritalSelect.options[i].value.toLowerCase() === data.data.marital_status.toLowerCase()) {
                                        maritalSelect.selectedIndex = i;
                                        break;
                                    }
                                }
                            }
                            if (data.data.contact_number) document.querySelector('input[name="contact_number"]').value = data.data.contact_number;
                            if (data.data.osca_id) document.querySelector('input[name="osca_id"]').value = data.data.osca_id;
                            if (data.data.residence) document.querySelector('input[name="residence"]').value = data.data.residence;
                            if (data.data.street) document.querySelector('input[name="street"]').value = data.data.street;
                            if (data.data.email) document.querySelector('input[name="email"]').value = data.data.email;
                            
                            // Handle other fields as needed
                        } else {
                            ocrStatus.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> ' + (data.message || 'Error processing document') + '</span>';
                        }
                    }
                });
            });
        }
    });
    </script>
  </x-head>
</x-sidebar>