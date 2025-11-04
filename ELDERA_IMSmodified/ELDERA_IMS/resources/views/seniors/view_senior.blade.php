<x-sidebar>
    <x-header title="SENIOR CITIZEN PROFILE" icon="fas fa-user">
      <div class="main">
          <div class="form">
              <div class="form-content">
                  <div class="form-section">
                     
                      <!-- Header with logos and title -->
                      <div class="d-flex justify-content-between align-items-center mb-3">
                          <img src="{{ asset('images/OSCA.png') }}" alt="OSCA Logo" style="max-height: 60px;">
                            <img src="{{ asset('images/Bagong_Pilipinas.png') }}" alt="Bagong Pilipinas Logo" style="max-height: 80px;">
                          <div class="text-center flex-grow-1">
                              <div style="color: #000; font-size: 18px; font-weight: bold;">OFFICE OF THE SENIOR CITIZENS AFFAIRS</div>
                          </div>
                        
                          <div class="button-group">
                              <a href="{{ route('seniors') }}" class="action-btn back-btn">
                                  <i class="fas fa-arrow-left"></i> Back to Seniors
                              </a>
                              <a href="{{ route('edit_senior', ['id' => $senior->id]) }}" class="action-btn edit-btn">
                                <i class="fas fa-edit"></i> Edit
                              </a>
                              <button onclick="generatePDF()" class="action-btn pdf-btn">
                                  <i class="fas fa-file-pdf"></i> Generate PDF
                              </button>

                          </div>
                      </div>
  
                      <!-- Pink section header -->
                      <div style="background-color: #e31575; color: white; padding: 10px; font-weight: bold; margin-bottom: 20px;">
                          I - IDENTIFYING INFORMATION
                      </div>
  
                      <!-- Profile Photo Section in Header -->
                      <div class="row mb-4" style="background-color: #f8f9fa; padding: 20px; border-radius: 8px;">
                          <div class="col-md-2">
                              <div class="profile-photo-section" style="text-align: left;">
                                  @if($senior->photo_path)
                                      <img src="{{ asset('storage/' . $senior->photo_path) }}" alt="Profile Photo" class="profile-photo" style="margin-left: 0;">
                                  @else
                                      <div class="profile-photo-placeholder" style="width: 120px; height: 120px; background-color: #e9ecef; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                          <i class="fas fa-user" style="font-size: 48px; color: #6c757d;"></i>
                                      </div>
                                  @endif
                              </div>
                          </div>
                          <div class="col-md-10">
                              <div class="profile-info-section" style="padding-left: px;">
                                  <h4 class="senior-name mb-1" style="text-align: left;">{{ $senior->last_name }}, {{ $senior->first_name }}</h4>
                                  <p class="senior-id mb-2" style="text-align: left;">{{ $senior->osca_id }}</p>
                               
                                      <span class="status-badge badge-active">{{ $senior->status ?? 'Active' }}</span>
                                      @if($senior->has_pension)
                                      <span class="status-badge badge-pension">Pension ✓</span>
                                      @endif
                                      <p class="text-muted small mt-1" style="text-align: left;">Record created: {{ $senior->created_at->format('F j, Y, g:i a') }}</p>
                               
                              </div>
                          </div>
                      </div>
  
                      <!-- Information Sections Below -->
                      <div class="row">
                          <!-- Left Column: Personal Information -->
                          <div class="col-md-6">
                              <div class="info-section-clean mb-4">
                                  <h6 class="section-title">PERSONAL INFORMATION</h6>
                                  <div class="info-content">
                                      <div class="info-row">
                                          <span class="info-label">Full Name:</span>
                                          <span class="info-value">{{ $senior->first_name }} {{ $senior->middle_name ?? '' }} {{ $senior->last_name }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Age:</span>
                                          <span class="info-value">{{ \Carbon\Carbon::parse($senior->date_of_birth)->age }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Gender:</span>
                                          <span class="info-value">{{ $senior->sex }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Date of Birth:</span>
                                          <span class="info-value">{{ date('F j, Y', strtotime($senior->date_of_birth)) }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Place of Birth:</span>
                                          <span class="info-value">{{ $senior->birth_place }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Marital Status:</span>
                                          <span class="info-value">{{ $senior->marital_status }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Ethnic Origin:</span>
                                          <span class="info-value">{{ $senior->ethnic_origin ?? 'Filipino' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Religion:</span>
                                          <span class="info-value">{{ $senior->religion ?? 'Roman Catholic' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Language:</span>
                                          <span class="info-value">{{ $senior->language ?? 'Tagalog, English' }}</span>
                                      </div>
                                  </div>
                              </div>
  
                              <!-- Address -->
                              <div class="info-section-clean mb-4">
                                  <h6 class="section-title">ADDRESS</h6>
                                  <div class="info-content">
                                      <div class="info-row">
                                          <span class="info-label">Region:</span>
                                          <span class="info-value">{{ $senior->region }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Province:</span>
                                          <span class="info-value">{{ $senior->province }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Municipality:</span>
                                          <span class="info-value">{{ $senior->city }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Barangay:</span>
                                          <span class="info-value">{{ $senior->barangay }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Street:</span>
                                          <span class="info-value">{{ $senior->street ?? '-----------' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">House No:</span>
                                          <span class="info-value">{{ $senior->residence ?? '-----------' }}</span>
                                      </div>
                                  </div>
                              </div>
  
                              <!-- Contact Information -->
                              <div class="info-section-clean mb-4">
                                  <h6 class="section-title">CONTACT INFORMATION</h6>
                                  <div class="info-content">
                                      <div class="info-row">
                                          <span class="info-label">Contact No:</span>
                                          <span class="info-value">{{ $senior->contact_number }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Email:</span>
                                          <span class="info-value">{{ $senior->email }}</span>
                                      </div>
                                  </div>
                              </div>
                          </div>
  
                          <!-- Right Column: IDs and Other Information -->
                          <div class="col-md-6">
                              <!-- ID's -->
                              <div class="info-section-clean mb-4">
                                  <h6 class="section-title">ID's</h6>
                                  <div class="info-content">
                                      <div class="info-row">
                                          <span class="info-label">OSCA ID No:</span>
                                          <span class="info-value">{{ $senior->osca_id }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">GSIS/SSS No:</span>
                                          <span class="info-value">{{ $senior->gsis_sss ?? '-----------' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">TIN No:</span>
                                          <span class="info-value">{{ $senior->tin ?? '-----------' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">PHILHEALTH No:</span>
                                          <span class="info-value">{{ $senior->philhealth ?? '-----------' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">SC Association No:</span>
                                          <span class="info-value">{{ $senior->sc_association ?? '-----------' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Other's Gov. ID:</span>
                                          <span class="info-value">{{ $senior->other_govt_id ?? '-----------' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Voters ID:</span>
                                          <span class="info-value">{{ $senior->other_govt_id ?? '-----------' }}</span>
                                      </div>
                                  </div>
                              </div>
  
                              <!-- Other Information -->
                              <div class="info-section-clean mb-4">
                                  <h6 class="section-title">OTHER INFORMATION</h6>
                                  <div class="info-content">
                                      <div class="info-row">
                                          <span class="info-label">Can Travel?:</span>
                                          <span class="info-value">{{ $senior->can_travel ? 'Yes' : 'No' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Employment:</span>
                                          <span class="info-value">{{ $senior->employment ?? '-----------' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Has Pension:</span>
                                          <span class="info-value">{{ $senior->has_pension ? 'Yes' : 'No' }}</span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
  
                      <!-- II - FAMILY COMPOSITION -->
                      <div style="background-color: #e31575; color: white; padding: 10px; font-weight: bold; margin: 30px 0 20px 0;">
                          II - FAMILY COMPOSITION
                      </div>
  
                      <div class="row">
                          <!-- Left Column: Family Information -->
                          <div class="col-md-6">
                              <div class="info-section-clean mb-4">
                                  <h6 class="section-title">FAMILY INFORMATION</h6>
                                  <div class="info-content">
                                      <div class="info-row">
                                          <span class="info-label">Name of Spouse:</span>
                                          <span class="info-value">{{ $senior->spouse_first_name ?? '' }} {{ $senior->spouse_last_name ?? '' }} {{ $senior->spouse_middle_name ?? '' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Father's Name:</span>
                                          <span class="info-value">{{ $senior->father_first_name ?? '' }} {{ $senior->father_last_name ?? '' }} {{ $senior->father_middle_name ?? '' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Mother's Maiden Name:</span>
                                          <span class="info-value">{{ $senior->mother_first_name ?? '' }} {{ $senior->mother_last_name ?? '' }} {{ $senior->mother_middle_name ?? '' }}</span>
                                      </div>
                                  </div>
                              </div>
                          </div>
  
                          <!-- Right Column: Dependents -->
                          <div class="col-md-6">
                              <div class="info-section-clean mb-4">
                                  <h6 class="section-title">CHILDREN AND DEPENDENTS</h6>
                                  <div class="info-content">
                                      <div class="info-row">
                                          <span class="info-label">Children:</span>
                                          <span class="info-value">
                                              @php
                                                  $hc = is_array($senior->household_condition ?? null) ? $senior->household_condition : [];
                                                  $childrenList = is_array($senior->children) && count($senior->children) > 0 ? $senior->children : ($hc['children'] ?? []);
                                              @endphp
                                              @if(is_array($childrenList) && count($childrenList) > 0)
                                                  @foreach($childrenList as $child)
                                                      {{ $child['name'] ?? '' }}
                                                      @php
                                                          $details = [];
                                                          if(!empty($child['occupation'])) $details[] = $child['occupation'];
                                                          if(!empty($child['age'])) $details[] = 'Age: '. $child['age'];
                                                          if(!empty($child['working'])) $details[] = 'Working: '. $child['working'];
                                                          if(!empty($child['income'])) $details[] = 'Income: '. $child['income'];
                                                      @endphp
                                                      @if(count($details) > 0)
                                                          ({{ implode(', ', $details) }})
                                                      @endif
                                                      @if(!$loop->last), @endif
                                                  @endforeach
                                              @else
                                                  -----------
                                              @endif
                                          </span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Dependents:</span>
                                          <span class="info-value">
                                              @php
                                                  $hc = is_array($senior->household_condition ?? null) ? $senior->household_condition : [];
$dependentsList = is_array($senior->dependent) && count($senior->dependent) > 0 ? $senior->dependent : ($hc['dependent'] ?? []);
                                              @endphp
                                              @if(is_array($dependentsList) && count($dependentsList) > 0)
                                                  @foreach($dependentsList as $dep)
                                                      {{ $dep['name'] ?? '' }}
                                                      @php
                                                          $details = [];
                                                          if(!empty($dep['occupation'])) $details[] = $dep['occupation'];
                                                          if(!empty($dep['age'])) $details[] = 'Age: '. $dep['age'];
                                                          if(!empty($dep['working'])) $details[] = 'Working: '. $dep['working'];
                                                          if(!empty($dep['income'])) $details[] = 'Income: '. $dep['income'];
                                                      @endphp
                                                      @if(count($details) > 0)
                                                          ({{ implode(', ', $details) }})
                                                      @endif
                                                      @if(!$loop->last), @endif
                                                  @endforeach
                                              @else
                                                  -----------
                                              @endif
                                          </span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
  
                      <!-- III - EDUCATION / HR PROFILE -->
                      <div style="background-color: #e31575; color: white; padding: 10px; font-weight: bold; margin-bottom: 20px;">
                          III - EDUCATION / HR PROFILE
                      </div>
  
                      <div class="row">
                          <!-- Left Column: Education -->
                          <div class="col-md-6">
                              <div class="info-section-clean mb-4">
                                  <h6 class="section-title">EDUCATION</h6>
                                  <div class="info-content">
                                      <div class="info-row">
                                          <span class="info-label">Highest Educational Attainment:</span>
                                          <span class="info-value">{{ $senior->education_level ?? '-----------' }}</span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          
                          <!-- Right Column: Human Resources -->
                          <div class="col-md-6">
                              <div class="info-section-clean mb-4">
                                  <h6 class="section-title">HUMAN RESOURCES</h6>
                                  <div class="info-content">
                                      <div class="info-row">
                                          <span class="info-label">Specialization / Technical Skills:</span>
                                          <span class="info-value">{{ is_array($senior->skills) ? implode(', ', $senior->skills) : ($senior->skills ?? '-----------') }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Shared Skills:</span>
                                          <span class="info-value">{{ $senior->shared_skills ?? '-----------' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Involvement in Community Activities:</span>
                                          <span class="info-value">{{ is_array($senior->community_activities) ? implode(', ', $senior->community_activities) : ($senior->community_activities ?? '-----------') }}</span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
  
                      <!-- IV - DEPENDENCY PROFILE -->
                      <div style="background-color: #e31575; color: white; padding: 10px; font-weight: bold; margin-bottom: 20px;">
                          IV - DEPENDENCY PROFILE
                      </div>
  
                      <div class="row">
                          <!-- Left Column: Living Conditions -->
                          <div class="col-md-6">
                              <div class="info-section-clean mb-4">
                                  <h6 class="section-title">LIVING CONDITIONS</h6>
                                  <div class="info-content">
                                      <div class="info-row">
                                          <span class="info-label">Living Condition:</span>
                                          <span class="info-value">{{ $senior->living_condition_primary ?? '-----------' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Living Arrangement:</span>
                                          <span class="info-value">{{ is_array($senior->living_with) ? implode(', ', $senior->living_with) : ($senior->living_with ?? '-----------') }}</span>
                                      </div>
                                  </div>
                              </div>
                          </div>
  
                          <!-- Right Column: Household Information -->
                          <div class="col-md-6">
                              <div class="info-section-clean mb-4">
                                  <h6 class="section-title">HOUSEHOLD INFORMATION</h6>
                                  <div class="info-content">
                                      <div class="info-row">
                                          <span class="info-label">Household Condition:</span>
                                          <span class="info-value">
                                              @php
                                                  $hc = $senior->household_condition ?? null;
                                                  // Normalize JSON string to array when needed
                                                  if (is_string($hc)) {
                                                      $decoded = json_decode($hc, true);
                                                      if (json_last_error() === JSON_ERROR_NONE) {
                                                          $hc = $decoded;
                                                      }
                                                  }
                                                  if (is_array($hc)) {
                                                      $parts = [];
                                                      foreach ($hc as $key => $val) {
                                                          // Only show scalar household condition values; children/dependents are shown above
                                                          if (is_array($val)) {
                                                              continue;
                                                          }
                                                          if (is_string($val)) {
                                                              $trimmed = trim($val);
                                                              if ($trimmed !== '') $parts[] = $trimmed;
                                                          } elseif (is_bool($val)) {
                                                              $parts[] = $val ? 'Yes' : 'No';
                                                          } elseif (is_numeric($val)) {
                                                              $parts[] = (string)$val;
                                                          }
                                                      }
                                                      echo count($parts) ? implode(', ', $parts) : '-----------';
                                                  } else {
                                                      echo ($hc !== null && trim((string)$hc) !== '') ? $hc : '-----------';
                                                  }
                                              @endphp
                                          </span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Support System:</span>
                                          <span class="info-value">{{ $senior->support_system ?? '-----------' }}</span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
  
                      <!-- V - ECONOMIC PROFILE -->
                      <div style="background-color: #e31575; color: white; padding: 10px; font-weight: bold; margin-bottom: 20px;">
                          V - ECONOMIC PROFILE
                      </div>
  
                      <div class="row">
                          <!-- Left Column: Income Information -->
                          <div class="col-md-6">
                              <div class="info-section-clean mb-4">
                                  <h6 class="section-title">INCOME INFORMATION</h6>
                                  <div class="info-content">
                                      <div class="info-row">
                                          <span class="info-label">Source of Income and Assets:</span>
                                          <span class="info-value">{{ is_array($senior->source_of_income) ? implode(', ', $senior->source_of_income) : ($senior->source_of_income ?? '-----------') }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Monthly Income Range:</span>
                                          <span class="info-value">{{ $senior->monthly_income ?? '-----------' }}</span>
                                      </div>
                                      
                                  </div>
                              </div>
                          </div>
  
                          <!-- Right Column: Assets & Needs -->
                          <div class="col-md-6">
                              <div class="info-section-clean mb-4">
                                  <h6 class="section-title">ASSETS & NEEDS</h6>
                                  <div class="info-content">
                                      <div class="info-row">
                                          <span class="info-label">Real and Immovable Properties:</span>
                                          <span class="info-value">{{ is_array($senior->real_assets) ? implode(', ', $senior->real_assets) : ($senior->real_assets ?? '-----------') }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Personal and Movable Properties:</span>
                                          <span class="info-value">{{ is_array($senior->personal_assets) ? implode(', ', $senior->personal_assets) : ($senior->personal_assets ?? '-----------') }}</span>
                                      </div>
                                     
                                      <div class="info-row">
                                          <span class="info-label">Common Problems/Needs:</span>
                                          <span class="info-value">{{ is_array($senior->problems_needs) ? implode(', ', $senior->problems_needs) : ($senior->problems_needs ?? '-----------') }}</span>
                                      </div>
                                     
                                  </div>
                              </div>
                          </div>
                      </div>
  
                      <!-- VI - HEALTH PROFILE -->
                      <div style="background-color: #e31575; color: white; padding: 10px; font-weight: bold; margin-bottom: 20px;">
                          VI - HEALTH PROFILE
                      </div>
  
                      <div class="row">
                          <!-- Left Column: Basic Health Info -->
                          <div class="col-md-6">
                              <div class="info-section-clean mb-4">
                                  <h6 class="section-title">BASIC HEALTH INFO</h6>
                                  <div class="info-content">
                                      <div class="info-row">
                                          <span class="info-label">Blood Type:</span>
                                          <span class="info-value">{{ $senior->blood_type ?? '-----------' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Physical Disability:</span>
                                          <span class="info-value">{{ $senior->physical_disability ?? 'None' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Maintenance Medicines:</span>
                                          <span class="info-value">{{ $senior->maintenance_medicines ?? '-----------' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Scheduled Check-up:</span>
                                          <span class="info-value">{{ $senior->scheduled_checkup ?? '-----------' }}</span>
                                      </div>
                                      <div class="info-row">
                                          <span class="info-label">Check-up Frequency:</span>
                                          <span class="info-value">{{ $senior->checkup_frequency ?? '-----------' }}</span>
                                      </div>
                                  </div>
                              </div>
                          </div>
  
                          <!-- Middle Column: Health Problems -->
                          <div class="col-md-6">
                              <div class="info-section-clean mb-4">
                                  <h6 class="section-title">HEALTH PROBLEMS AND CONCERNS</h6>
                                  <div class="info-content">
                                      <div class="info-row">
                                          <span class="info-label">Health Problems:</span>
                                          <span class="info-value">{{ is_array($senior->health_problems) ? implode(', ', $senior->health_problems) : ($senior->health_problems ?? '-----------') }}</span>
                                      </div>
                                     
                                      <div class="info-row">
                                          <span class="info-label">Dental Concerns:</span>
                                          <span class="info-value">{{ is_array($senior->dental_concern) ? implode(', ', $senior->dental_concern) : ($senior->dental_concern ?? '-----------') }}</span>
                                      </div>
                                     
                                      <div class="info-row">
                                          <span class="info-label">Visual Concerns:</span>
                                          <span class="info-value">{{ is_array($senior->visual_concern) ? implode(', ', $senior->visual_concern) : ($senior->visual_concern ?? '-----------') }}</span>
                                      </div>

                                      <div class="info-row">
                                        <span class="info-label">Hearing Conditions:</span>
                                        <span class="info-value">{{ is_array($senior->hearing_condition) ? implode(', ', $senior->hearing_condition) : ($senior->hearing_condition ?? '-----------') }}</span>
                                    </div>
                                   
                                    <div class="info-row">
                                        <span class="info-label">Social/Emotional:</span>
                                        <span class="info-value">{{ is_array($senior->social_emotional) ? implode(', ', $senior->social_emotional) : ($senior->social_emotional ?? '-----------') }}</span>
                                    </div>
                                   
                                    <div class="info-row">
                                        <span class="info-label">Area/Difficulty:</span>
                                        <span class="info-value">{{ is_array($senior->area_difficulty) ? implode(', ', $senior->area_difficulty) : ($senior->area_difficulty ?? '-----------') }}</span>
                                    </div>
                                     
                                  </div>
                              </div>
                          </div>
  
                          
                      </div>
                      
                     

                  </div>
              </div>
          </div>
      </div>
  
      <style>
          .form-content {
              background-color: #f8f9fa;
              padding: 20px;
              border-radius: 8px;
              box-shadow: 0 2px 4px rgba(0,0,0,0.1);
          }
          
          .profile-photo {
              width: 120px;
              height: 120px;
              border-radius: 10px;
              object-fit: cover;
              border: 2px solid #ddd;
          }
          
          .senior-name {
              font-size: 18px;
              font-weight: bold;
              color: #000;
              margin: 10px 0 5px 0;
          }
          
          .senior-id {
              font-size: 14px;
              color: #666;
              margin-bottom: 10px;
          }
          
          .info-section {
              background: transparent;
              border: none;
              padding: 15px 0;
              border-radius: 0;
              box-shadow: none;
          }
          
          .info-section-clean {
              padding: 15px 0;
              background: transparent;
              border: none;
              box-shadow: none;
          }
          
          .info-section h6,
          .section-title {
              color: #e31575;
              font-weight: bold;
              margin-bottom: 12px;
              border-bottom: 2px solid #e31575;
              padding-bottom: 5px;
              font-size: 16px;
              text-transform: uppercase;
          }
          
          .info-content {
              font-size: 14px;
          }
          
          .info-row {
              margin-bottom: 8px;
              display: flex;
              justify-content: space-between;
              align-items: flex-start;
          }
          
          .info-label {
              font-weight: bold;
              color: #333;
              flex-shrink: 0;
              margin-right: 10px;
              font-size: 14px;
          }
          
          .info-value {
              color: #666;
              text-align: right;
              word-break: break-word;
              flex: 1;
              font-size: 14px;
          }
  
          .profile-photo-section {
              text-align: center;
              padding: 15px 0;
          }
  
          .profile-card {
              background: transparent;
              border: none;
              padding: 0;
              border-radius: 0;
              box-shadow: none;
              text-align: center;
          }
  
          .status-badges {
              display: flex;
              justify-content: center;
              gap: 8px;
              margin-top: 10px;
          }
  
          .status-badge {
              padding: 6px 12px;
              border-radius: 15px;
              font-size: 11px;
              font-weight: bold;
          }
  
          .badge-active {
              background-color: #90EE90;
              color: #000;
          }
  
          .badge-pension {
              background-color: #87CEEB;
              color: #000;
          }

          /* Button styling */
          .button-group {
              display: flex;
              gap: 10px;
              align-items: center;
          }

          .action-btn {
              padding: 8px 16px;
              border: none;
              border-radius: 5px;
              font-size: 12px;
              font-weight: bold;
              text-decoration: none;
              cursor: pointer;
              transition: all 0.3s ease;
              display: inline-flex;
              align-items: center;
              gap: 5px;
          }

          .back-btn {
              background-color: #6c757d;
              color: white;
          }

          .back-btn:hover {
              background-color: #5a6268;
              color: white;
              text-decoration: none;
          }

          .edit-btn {
              background-color: #e31575;
              color: white;
          }

          .edit-btn:hover {
              background-color: #c01060;
              color: white;
          }

          .delete-btn {
              background-color: #dc3545;
              color: white;
          }

          .delete-btn:hover {
              background-color: #c82333;
              color: white;
          }
          
          @media (max-width: 768px) {
              .profile-photo {
                  width: 100px;
                  height: 100px;
              }
              
              .senior-name {
                  font-size: 16px;
              }
              
              .info-row {
                  flex-direction: column;
                  align-items: flex-start;
              }
              
              .info-value {
                  text-align: left;
                  margin-top: 2px;
              }

              .button-group {
                  flex-direction: column;
                  gap: 5px;
              }

              .action-btn {
                  width: 100%;
                  justify-content: center;
              }
          }
      </style>
  
      <script>
          function confirmEdit(seniorId, seniorName) {
              if (confirm(`Are you sure you want to edit the profile of ${seniorName}?`)) {
                  window.location.href = `/Edit_senior/${seniorId}`;
              }
          }
  
          function confirmDelete(seniorId, seniorName) {
              if (confirm(`Are you sure you want to delete the profile of ${seniorName}? This action cannot be undone.`)) {
                  // Add delete functionality here
                  console.log(`Deleting senior with ID: ${seniorId}`);
              }
          }

        function generatePDF() {
            // Hide action buttons
            const buttons = document.querySelectorAll('.button-group, .action-btn');
            buttons.forEach(btn => btn.style.display = 'none');
            
            // Hide sidebar
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) sidebar.style.display = 'none';
            
            // Adjust main content
            const main = document.querySelector('.main');
            if (main) {
                main.style.marginLeft = '0';
                main.style.width = '100%';
            }
            
            // Wait a moment for layout to adjust
            setTimeout(() => {
                // Use browser's print to PDF
                window.print();
                
                // Restore layout after printing
                setTimeout(() => {
                    buttons.forEach(btn => btn.style.display = '');
                    if (sidebar) sidebar.style.display = '';
                    if (main) {
                        main.style.marginLeft = '';
                        main.style.width = '';
                    }
                }, 1000);
            }, 500);
        }
      </script>

      
      <style>
        .pdf-btn {
            background: #dc3545 !important;
            color: white !important;
        }
        .pdf-btn:hover {
            background: #c82333 !important;
        }
          
          @media print {
              /* Hide sidebar and navigation */
              .sidebar, .sidebar * {
                  display: none !important;
              }
              
              /* Hide header section */
              .header, .header *, [class*="header"] {
                  display: none !important;
              }
              
              /* Hide action buttons */
              .button-group, .action-btn, .btn, button {
                  display: none !important;
              }
              
              /* Reset main layout for print */
              .main {
                  margin-left: 0 !important;
                  padding: 0 !important;
                  width: 100% !important;
                  max-width: none !important;
              }
              
              /* Ensure form content is visible */
              .form {
                  box-shadow: none !important;
                  border: none !important;
                  margin: 0 !important;
                  padding: 10px !important;
                  width: 100% !important;
                  max-width: none !important;
              }
              
              /* Ensure all content is visible */
              .form-content, .form-section {
                  display: block !important;
                  visibility: visible !important;
                  opacity: 1 !important;
              }
              
              /* Fix text visibility */
              body, .form, .form-content, .form-section {
                  color: #000 !important;
                  background: #fff !important;
              }
              
              /* Ensure images are visible */
              img {
                  max-width: 100% !important;
                  height: auto !important;
              }
              
              /* Professional PDF layout matching the image */
              .form-section {
                  page-break-inside: avoid;
                  break-inside: avoid;
                  margin-bottom: 15px !important;
                  padding: 10px !important;
              }
              
              /* Clean two-column layout */
              .row {
                  margin-bottom: 12px !important;
                  display: flex !important;
                  flex-wrap: wrap !important;
              }
              
              .col-md-6 {
                  width: 50% !important;
                  padding: 8px !important;
                  box-sizing: border-box !important;
              }
              
              .col-md-4 {
                  width: 33.333% !important;
                  padding: 8px !important;
                  box-sizing: border-box !important;
              }
              
              .col-md-3 {
                  width: 25% !important;
                  padding: 8px !important;
                  box-sizing: border-box !important;
              }
              
              /* Section headers with pink styling */
              .section-title {
                  font-size: 16px !important;
                  font-weight: bold !important;
                  color: #e31575 !important;
                  margin-bottom: 10px !important;
                  padding: 8px 12px !important;
                  background-color: #f8f9fa !important;
                  border-bottom: 2px solid #e31575 !important;
                  text-transform: uppercase !important;
              }
              
              /* Information display styling */
              .info-content {
                  font-size: 13px !important;
                  line-height: 1.5 !important;
                  margin-bottom: 8px !important;
                  display: block !important;
                  clear: both !important;
              }
              
              .info-label {
                  font-size: 13px !important;
                  font-weight: bold !important;
                  color: #333 !important;
                  display: inline-block !important;
                  width: 40% !important;
                  vertical-align: top !important;
              }
              
              .info-value {
                  font-size: 13px !important;
                  color: #666 !important;
                  display: inline-block !important;
                  width: 55% !important;
                  vertical-align: top !important;
                  margin-left: 5% !important;
              }
              
              /* Banner sections */
              .banner-section {
                  background-color: #e31575 !important;
                  color: white !important;
                  padding: 12px !important;
                  margin: 15px 0 !important;
                  text-align: center !important;
                  font-weight: bold !important;
                  font-size: 16px !important;
                  text-transform: uppercase !important;
              }
              
              /* Balanced margins and padding */
              .mb-3, .mb-4, .mb-5 {
                  margin-bottom: 12px !important;
              }
              
              .mt-3, .mt-4, .mt-5 {
                  margin-top: 12px !important;
              }
              
              .p-3, .p-4, .p-5 {
                  padding: 12px !important;
              }
              
              /* Profile section styling */
              .profile-photo-section {
                  width: 100px !important;
                  height: 100px !important;
                  margin: 0 auto !important;
              }
              
              .profile-photo, .profile-photo-placeholder {
                  width: 100px !important;
                  height: 100px !important;
                  border-radius: 8px !important;
              }
              
              /* Clean spacing */
              .d-flex {
                  margin-bottom: 8px !important;
              }
              
              /* Table styling */
              table {
                  font-size: 12px !important;
                  width: 100% !important;
                  border-collapse: collapse !important;
              }
              
              table td, table th {
                  padding: 6px 8px !important;
                  border: 1px solid #ddd !important;
              }
              
              table th {
                  background-color: #f8f9fa !important;
                  font-weight: bold !important;
              }
          }
      </style>
    </x-header>
  </x-sidebar>
