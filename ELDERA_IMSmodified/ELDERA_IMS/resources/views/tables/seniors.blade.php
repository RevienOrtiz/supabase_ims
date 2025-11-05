<x-sidebar>
    <x-header title="Senior Citizen" icon="fas fa-table">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <div class="main">
            @if(!isset($includeStylesOnly) || !$includeStylesOnly)
            <!-- Tab Navigation -->
            <div class="tab-navigation">
                <button class="tab-btn active" onclick="showTab('all-seniors')">
                    <i class="fas fa-users"></i> All Seniors
                </button>
                <button class="tab-btn" onclick="showTab('benefits-applicants')">
                    <i class="fas fa-hand-holding-usd"></i> Seniors Applying for Benefits
                </button>
                <button class="tab-btn" onclick="showTab('id-applicants')">
                    <i class="fas fa-id-card"></i> Senior ID Applicants
                </button>
            </div>
            
            <!-- Sub-tabs for Benefits Applicants -->
            <div class="sub-tab-navigation" id="benefits-sub-tabs" style="display: none;">
                <button class="sub-tab-btn active" onclick="showSubTab('existing-senior')">
                    <i class="fas fa-user-check"></i> ONCBP
                </button>
                <button class="sub-tab-btn" onclick="showSubTab('pension')">
                    <i class="fas fa-money-check-alt"></i> Social Pension
                </button>
            </div>

            <!-- Filter/Sort Bar -->
            <div class="filter-sort-bar">
                <!-- Filter/Sort Toggle Button -->
                <div class="filter-sort-toggle">
                    <button type="button" class="filter-sort-btn" id="filter-sort-btn">
                        <i class="fas fa-filter"></i>
                        <i class="fas fa-sort"></i>
                        Filter & Sort
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </button>
                    
                    <!-- Filter/Sort Dropdown Panel -->
                    <div class="filter-sort-panel" id="filter-sort-panel">
                        <!-- Search Section -->
                        <div class="panel-section">
                            <h4 class="section-title">Search</h4>
                            <div class="search-container">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" id="global-search" class="search-input" placeholder="Search seniors by name, OSCA ID, or barangay...">
                                <button class="clear-search" id="clear-search" style="display: none;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Global Filters Section -->
                        <div class="panel-section">
                            <h4 class="section-title">Global Filters</h4>
                            <div class="filter-controls">
                                <div class="filter-btn-container">
                                <button class="filter-btn" id="barangay-filter-btn">
                                    <i class="fas fa-map-marker-alt"></i> Barangay <i class="fas fa-chevron-down"></i>
                                </button>
                <!-- Barangay Filter Dropdown -->
                <div class="filter-dropdown" id="barangay-dropdown">
                    <div class="filter-dropdown-content">
                        <div class="filter-search">
                            <input type="text" id="barangay-search" placeholder="Search barangays...">
                        </div>
                        <div class="filter-options" id="barangay-options">
            @if(!isset($includeStylesOnly) || !$includeStylesOnly)
                @foreach($barangays ?? [] as $barangay)
                    <label class="filter-option">
                        <input type="checkbox" value="{{ $barangay->name }}" data-filter="barangay">
                        <span>{{ $barangay->name }}</span>
                    </label>
                @endforeach
            @endif
                        </div>
                        </div>
                    </div>
                </div>
            @endif

                <div class="filter-btn-container">
                                    <button class="filter-btn" id="gender-filter-btn">
                                        <i class="fas fa-venus-mars"></i> Gender <i class="fas fa-chevron-down"></i>
                                    </button>
                                    <!-- Gender Filter Dropdown -->
                                    <div class="filter-dropdown" id="gender-dropdown">
                                        <div class="filter-dropdown-content">
                                            <div class="filter-options">
                                                <label class="filter-option">
                                                    <input type="checkbox" value="Male" data-filter="gender">
                                                    <span>Male</span>
                                                </label>
                                                <label class="filter-option">
                                                    <input type="checkbox" value="Female" data-filter="gender">
                                                    <span>Female</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="filter-btn-container" id="pension-filter-container">
                                    <button class="filter-btn" id="pension-filter-btn">
                                        <i class="fas fa-money-check-alt"></i> Pension <i class="fas fa-chevron-down"></i>
                                    </button>
                <!-- Pension Filter Dropdown -->
                <div class="filter-dropdown" id="pension-dropdown">
                    <div class="filter-dropdown-content">
                        <div class="filter-options">
                            <label class="filter-option">
                                                    <input type="checkbox" value="With Pension" data-filter="pension">
                                <span>With Pension</span>
                            </label>
                            <label class="filter-option">
                                                    <input type="checkbox" value="Without Pension" data-filter="pension">
                                <span>Without Pension</span>
                            </label>
                                            </div>
                                        </div>
                                    </div>
                        </div>
                                
                                <!-- Status Filter (Global for All Seniors) -->
                                <div class="filter-btn-container" id="status-filter-container-global" style="display: none;">
                                    <button class="filter-btn" id="status-filter-btn-global">
                                        <i class="fas fa-info-circle"></i> Status <i class="fas fa-chevron-down"></i>
                                    </button>
                                    <div class="filter-dropdown" id="status-dropdown-global">
                                        <div class="filter-dropdown-content">
                                            <div class="filter-options" id="status-options-global">
                                                <!-- Global Status options will be populated dynamically -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    </div>
                </div>

                        <!-- Table-Specific Filters Section -->
                        <div class="panel-section" id="table-specific-filters-section" style="display: none;">
                            <h4 class="section-title">Table-Specific Filters</h4>
                            <div class="filter-controls" id="table-specific-filters">
                                <!-- Status Filter (for all tables except All Seniors) -->
                                <div class="filter-btn-container" id="status-filter-container" style="display: none;">
                                    <button class="filter-btn" id="status-filter-btn">
                                        <i class="fas fa-info-circle"></i> Status <i class="fas fa-chevron-down"></i>
                                    </button>
                                    <div class="filter-dropdown" id="status-dropdown">
                    <div class="filter-dropdown-content">
                                            <div class="filter-options" id="status-options">
                                                <!-- Status options will be populated dynamically -->
                        </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Milestone Age Filter (for Existing Senior Benefits only) -->
                                <div class="filter-btn-container" id="milestone-filter-container" style="display: none;">
                                    <button class="filter-btn" id="milestone-filter-btn">
                                        <i class="fas fa-birthday-cake"></i> Milestone Age <i class="fas fa-chevron-down"></i>
                                    </button>
                                    <div class="filter-dropdown" id="milestone-dropdown">
                                        <div class="filter-dropdown-content">
                                            <div class="filter-options">
                            <label class="filter-option">
                                                    <input type="checkbox" value="80" data-filter="milestone">
                                                    <span>80</span>
                            </label>
                            <label class="filter-option">
                                                    <input type="checkbox" value="85" data-filter="milestone">
                                                    <span>85</span>
                            </label>
                            <label class="filter-option">
                                                    <input type="checkbox" value="90" data-filter="milestone">
                                                    <span>90</span>
                            </label>
                            <label class="filter-option">
                                                    <input type="checkbox" value="95" data-filter="milestone">
                                                    <span>95</span>
                            </label>
                            <label class="filter-option">
                                                    <input type="checkbox" value="100" data-filter="milestone">
                                                    <span>100</span>
                            </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Monthly Income Filter (for Social Pension only) -->
                                <div class="filter-btn-container" id="income-filter-container" style="display: none;">
                                    <button class="filter-btn" id="income-filter-btn">
                                        <i class="fas fa-money-bill-wave"></i> Monthly Income <i class="fas fa-chevron-down"></i>
                                    </button>
                                    <div class="filter-dropdown" id="income-dropdown">
                                        <div class="filter-dropdown-content">
                                            <div class="filter-options">
                            <label class="filter-option">
                                                    <input type="checkbox" value="below-10k" data-filter="income">
                                                    <span>Below ?10,000</span>
                            </label>
                            <label class="filter-option">
                                                    <input type="checkbox" value="10k-15k" data-filter="income">
                                                    <span>?10,000 - ?15,000</span>
                            </label>
                            <label class="filter-option">
                                                    <input type="checkbox" value="15k-20k" data-filter="income">
                                                    <span>?15,000 - ?20,000</span>
                            </label>
                            <label class="filter-option">
                                                    <input type="checkbox" value="20k-25k" data-filter="income">
                                                    <span>?20,000 - ?25,000</span>
                                                </label>
                                                <label class="filter-option">
                                                    <input type="checkbox" value="above-25k" data-filter="income">
                                                    <span>Above ?25,000</span>
                            </label>
                                            </div>
                                        </div>
                                    </div>
                        </div>
                    </div>
                </div>

                        <!-- Sort Section -->
                        <div class="panel-section">
                            <h4 class="section-title">Sort</h4>
                            <div class="sort-options" id="sort-options">
                                <!-- Sort options will be dynamically populated based on active table -->
                        </div>
                        </div>

                        <!-- Reset Button Section -->
                        <div class="panel-section">
                            <div class="reset-actions">
                                <button class="reset-btn" id="reset-btn">
                                    <i class="fas fa-undo"></i> Reset All
                                </button>
                    </div>
                </div>

                        <!-- Active Filters -->
                        <div class="panel-section" id="active-filters-section" style="display: none;">
                            <div class="active-filters-header">
                                <h4 class="section-title">Active Filters</h4>
                                <button class="clear-all-filters" id="clear-all-filters">
                                    <i class="fas fa-times"></i> Clear All
                            </button>
                        </div>
                            <div class="filter-chips" id="filter-chips"></div>
                    </div>

                </div>
            </div>

                <!-- Action Section -->
                <div class="action-section">
                    <button class="add-btn" id="add-btn" onclick="redirectToForm()">
                        <i class="fas fa-plus"></i> <span id="add-btn-text">Add New Senior</span>
                    </button>
                    <a href="{{ route('seniors.pension.report') }}" class="report-btn" target="_blank">
                        <i class="fas fa-file-pdf"></i> Generate Pension Report
                    </a>
                </div>
            </div>
                    
            <style>
                .action-section {
                    display: flex;
                    gap: 10px;
                    align-items: center;
                }
                .report-btn {
                    background-color: #4CAF50;
                    color: white;
                    border: none;
                    padding: 10px 15px;
                    border-radius: 4px;
                    cursor: pointer;
                    text-decoration: none;
                    display: inline-flex;
                    align-items: center;
                    font-size: 14px;
                }
                .report-btn i {
                    margin-right: 5px;
                }
                .report-btn:hover {
                    background-color: #45a049;
                    color: white;
                }
            </style>


            <!-- Success/Error Messages - Now handled by popup_message.blade.php -->

            <!-- Error messages now handled by popup_message.blade.php -->

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

            @if(!isset($includeStylesOnly) || !$includeStylesOnly)
            <!-- All Seniors Table -->
            @include('tables.all-seniors-table', ['seniors' => $seniors])
            
            <!-- Benefits Applicants Table -->
            @include('tables.benefits-table', ['benefitsApplications' => $benefitsApplications ?? []])
            
            @include('tables.pension-table', ['pensionApplications' => $pensionApplications ?? []])
            
            <!-- Senior ID Applicants Table -->
            @include('tables.id-applicants-table', ['idApplications' => $idApplications ?? []])
            @endif
            </div>
            
        <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="modal-overlay" style="display: none;">
            <div class="modal-container">
                <div class="modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
            </div>
                <h2 class="modal-title">Delete Senior Citizen</h2>
                <p class="modal-message" id="deleteMessage">Are you sure you want to delete this senior? This action cannot be undone.</p>
                <div class="modal-actions">
                    <button class="modal-btn modal-btn-cancel" onclick="hideDeleteModal()">Cancel</button>
                    <button class="modal-btn modal-btn-confirm" id="confirmDeleteBtn">Yes</button>
                </div>
                </div>
        </div>

        <style>
            body { margin: 0; }
            .main {
                margin-left: 250px;
                margin-top: 60px;
                height: calc(100vh - 60px);
                display: flex;
                flex-direction: column;
                overflow: hidden;
                padding: 0;
            }

            /* Filter/Sort Bar Styles */
            .filter-sort-bar {
                display: flex;
                align-items: center;
                background: #222;
                padding: 12px 16px;
                gap: 16px;
                justify-content: space-between;
                position: sticky;
                top: 0;
                z-index: 10;
                flex-wrap: wrap;
            }

            .filter-sort-toggle {
                position: relative;
            }

            .filter-sort-btn {
                background: #fff;
                border: 2px solid #ddd;
                border-radius: 6px;
                padding: 10px 16px;
                font-size: 0.9rem;
                font-weight: 500;
                color: #333;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s ease;
                min-width: 180px;
                white-space: nowrap;
            }

            .filter-sort-btn:hover {
                background: #f8f9fa;
                border-color: #CC0052;
                color: #CC0052;
            }

            .filter-sort-btn.active {
                background: #CC0052;
                border-color: #CC0052;
                color: #fff;
            }

            .toggle-icon {
                margin-left: auto;
                transition: transform 0.3s ease;
            }

            .filter-sort-btn.active .toggle-icon {
                transform: rotate(180deg);
            }

            .reset-actions {
                display: flex;
                justify-content: center;
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid #eee;
            }

            .reset-btn {
                background: #dc3545;
                border: none;
                border-radius: 6px;
                padding: 10px 16px;
                font-size: 0.85rem;
                font-weight: 600;
                color: #fff;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                transition: all 0.3s ease;
                width: 100%;
            }

            .reset-btn:hover {
                background: #c82333;
                transform: translateY(-1px);
            }

            .filter-sort-panel {
                position: absolute;
                top: 100%;
                left: 0;
                background: #fff;
                border: 2px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                z-index: 1000;
                display: none;
                min-width: 400px;
                max-height: 500px;
                overflow-y: auto;
            }

            .filter-sort-panel.show {
                display: block;
            }

            .panel-section {
                padding: 20px;
                border-bottom: 1px solid #eee;
            }

            .panel-section:last-child {
                border-bottom: none;
            }

            .section-title {
                font-size: 16px;
                font-weight: 600;
                color: #333;
                margin: 0 0 15px 0;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .section-title:before {
                content: '';
                width: 3px;
                height: 16px;
                background: #CC0052;
                border-radius: 2px;
            }

            /* Search Section */
            .search-container {
                position: relative;
                display: flex;
                align-items: center;
            }

            .search-icon {
                position: absolute;
                left: 12px;
                color: #666;
                font-size: 0.9rem;
                z-index: 1;
            }

            .search-input {
                width: 100%;
                padding: 10px 40px 10px 35px;
                border: 2px solid #ddd;
                border-radius: 8px;
                font-size: 0.9rem;
                background: #fff;
                transition: all 0.3s ease;
            }

            .search-input:focus {
                outline: none;
                border-color: #CC0052;
                box-shadow: 0 0 0 3px rgba(204, 0, 82, 0.1);
            }

            .clear-search {
                position: absolute;
                right: 8px;
                background: none;
                border: none;
                color: #666;
                cursor: pointer;
                padding: 4px;
                border-radius: 4px;
                transition: all 0.2s ease;
            }

            .clear-search:hover {
                background: #f0f0f0;
                color: #333;
            }

            /* Filter Section */
            .filter-controls {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 10px;
            }

            .filter-btn {
                background: #fff;
                border: 2px solid #ddd;
                border-radius: 6px;
                padding: 8px 12px;
                font-size: 0.8rem;
                font-weight: 500;
                color: #333;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 6px;
                transition: all 0.3s ease;
                white-space: nowrap;
                justify-content: space-between;
                width: 100%;
            }

            .filter-btn:hover {
                background: #f8f9fa;
                border-color: #CC0052;
                color: #CC0052;
            }

            .filter-btn.active {
                background: #CC0052;
                border-color: #CC0052;
                color: #fff;
            }

            /* Sort Section */
            .sort-controls {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 8px;
            }

            .sort-option {
                background: #fff;
                border: 2px solid #ddd;
                border-radius: 6px;
                padding: 10px 12px;
                font-size: 0.8rem;
                font-weight: 500;
                color: #333;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s ease;
                white-space: nowrap;
                width: 100%;
                text-align: left;
            }

            .sort-option:hover {
                background: #f8f9fa;
                border-color: #CC0052;
                color: #CC0052;
            }

            .sort-option.active {
                background: #CC0052;
                border-color: #CC0052;
                color: #fff;
            }

            /* Active Filters Section */
            .active-filters-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 15px;
            }

            .clear-all-filters {
                background: #dc3545;
                color: #fff;
                border: none;
                border-radius: 4px;
                padding: 6px 12px;
                font-size: 0.75rem;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 4px;
                transition: background-color 0.2s ease;
            }

            .clear-all-filters:hover {
                background: #c82333;
            }

            .filter-chips {
                display: flex;
                gap: 6px;
                flex-wrap: wrap;
            }

            .filter-chip {
                background: #CC0052;
                color: #fff;
                padding: 4px 8px;
                border-radius: 16px;
                font-size: 0.75rem;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .filter-chip .remove-chip {
                background: none;
                border: none;
                color: #fff;
                cursor: pointer;
                padding: 0;
                margin-left: 4px;
                font-size: 0.7rem;
            }

            .filter-chip .remove-chip:hover {
                opacity: 0.7;
            }

            /* Filter Actions */
            .filter-actions {
                display: flex;
                gap: 8px;
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid #eee;
            }


            /* Action Section */
            .action-section {
                display: flex;
                gap: 10px;
            }

            .add-btn {
                background: #CC0052;
                color: #fff;
                border: none;
                border-radius: 6px;
                padding: 10px 16px;
                font-weight: bold;
                display: flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                transition: all 0.3s ease;
                white-space: nowrap;
            }

            .add-btn:hover {
                background: #a8003d;
                transform: translateY(-1px);
                box-shadow: 0 4px 8px rgba(204, 0, 82, 0.3);
            }

            /* Active Filters Styles */
            .active-filters {
                background: #f8f9fa;
                padding: 8px 16px;
                border-bottom: 1px solid #dee2e6;
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .filter-label {
                font-size: 0.8rem;
                font-weight: 600;
                color: #666;
            }

            .filter-chips {
                display: flex;
                gap: 6px;
                flex-wrap: wrap;
            }

            .filter-chip {
                background: #CC0052;
                color: #fff;
                padding: 4px 8px;
                border-radius: 16px;
                font-size: 0.75rem;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .filter-chip .remove-chip {
                background: none;
                border: none;
                color: #fff;
                cursor: pointer;
                padding: 0;
                margin-left: 4px;
                font-size: 0.7rem;
            }

            .clear-all-filters {
                background: #dc3545;
                color: #fff;
                border: none;
                border-radius: 4px;
                padding: 4px 8px;
                font-size: 0.75rem;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .clear-all-filters:hover {
                background: #c82333;
            }

            /* Filter Button Container */
            .filter-btn-container {
                position: relative;
                display: inline-block;
            }

            /* Filter Dropdowns Styles */
            .filter-dropdown {
                position: absolute;
                top: 100%;
                left: 0;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                z-index: 1000;
                min-width: 250px;
                max-width: 350px;
                display: none;
                margin-top: 4px;
            }

            .filter-dropdown.show {
                display: block;
            }

            .filter-dropdown-content {
                padding: 12px;
            }

            .filter-search {
                margin-bottom: 8px;
            }

            .filter-search input {
                width: 100%;
                padding: 8px 12px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 0.8rem;
            }

            .filter-options {
                max-height: 200px;
                overflow-y: auto;
            }

            .filter-option {
                display: flex;
                align-items: center;
                padding: 6px 8px;
                cursor: pointer;
                border-radius: 4px;
                transition: background-color 0.2s ease;
            }

            .filter-option:hover {
                background: #f8f9fa;
            }

            .filter-option input[type="checkbox"] {
                margin-right: 8px;
                accent-color: #CC0052;
            }

            .filter-option span {
                font-size: 0.8rem;
                color: #333;
            }

            .sort-options {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .sort-option {
                background: none;
                border: none;
                padding: 8px 12px;
                text-align: left;
                cursor: pointer;
                border-radius: 4px;
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.8rem;
                color: #333;
                transition: background-color 0.2s ease;
            }

            .sort-option:hover {
                background: #f8f9fa;
            }

            .sort-option.active {
                background: #CC0052;
                color: #fff;
            }

            /* Sortable Header Styles */
            .sortable-header {
                cursor: pointer;
                user-select: none;
                position: relative;
                transition: background-color 0.2s ease;
            }

            .sortable-header:hover {
                background-color: rgba(204, 0, 82, 0.1);
            }

            .sortable-header.active {
                background-color: rgba(204, 0, 82, 0.2);
            }

            .sort-indicator {
                margin-left: 8px;
                display: inline-block;
                opacity: 0.5;
                transition: opacity 0.2s ease;
            }

            .sortable-header:hover .sort-indicator,
            .sortable-header.active .sort-indicator {
                opacity: 1;
            }

            .sort-icon {
                font-size: 0.8rem;
                color: #666;
            }

            .sortable-header.active .sort-icon {
                color: #CC0052;
            }

            .sortable-header[data-direction="asc"] .sort-icon:before {
                content: "\f0de"; 
            }

            .sortable-header[data-direction="desc"] .sort-icon:before {
                content: "\f0dd"; 
            }
            
            /* Tab Navigation Styles */
            .tab-navigation {
                display: flex;
                background: #f8f9fa;
                border-bottom: 2px solid #dee2e6;
                padding: 0;
                margin: 0;
            }
            .tab-btn {
                background: transparent;
                border: none;
                padding: 12px 20px;
                font-size: 0.9rem;
                font-weight: 500;
                color: #6c757d;
                cursor: pointer;
                border-bottom: 3px solid transparent;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .tab-btn:hover {
                background: #e9ecef;
                color: #495057;
            }
            .tab-btn.active {
                color: #CC0052;
                border-bottom-color: #CC0052;
                background: #fff;
            }
            
            /* Sub-tab Navigation Styles */
            .sub-tab-navigation {
                display: flex;
                background: #fff;
                border-bottom: 1px solid #dee2e6;
                padding: 0;
                margin: 0;
                padding-left: 20px;
            }
            .sub-tab-btn {
                background: transparent;
                border: none;
                padding: 10px 16px;
                font-size: 0.8rem;
                font-weight: 400;
                color: #6c757d;
                cursor: pointer;
                border-bottom: 2px solid transparent;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .sub-tab-btn:hover {
                background: #f8f9fa;
                color: #495057;
            }
            .sub-tab-btn.active {
                color: #CC0052;
                border-bottom-color: #CC0052;
            }

.table-container {
    background: #fff;
    flex: 1;
    overflow-x: auto;
    display: flex;
    flex-direction: column;
    min-height: 0;
    border: 1px solid #ddd;
    width: 100%;
}

.records-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: auto; 
    word-wrap: break-word;
}

.records-table thead,
.records-table tbody {

}

.records-table th,
.records-table td {
    padding: 10px 12px;
    text-align: left;
    font-size: 0.85rem;
    border: 1px solid #ddd;
    white-space: normal; 
    word-wrap: break-word;
}
.records-table td:last-child,
.records-table th:last-child {
    white-space: nowrap; 
    width: 1%;            
    padding-right: 12px;  
}

.records-table th {
    background: #f5f5f5;
    color: #333;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    position: sticky;
    top: 0;
    z-index: 5;
}

.records-table tbody tr:hover {
    background: #f9f9f9;
}

.records-table td {
    color: #555;
}

/* Action buttons styling */
.action-buttons {
    display: flex;
    gap: 4px;
    flex-wrap: nowrap;
    justify-content: flex-start;
    align-items: center;
    white-space: nowrap;
}


.btn {
    border-radius: 4px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    padding: 6px 10px;
    font-size: 0.75rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
    color: white;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover {
    background: #218838;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}

.btn-info {
    background: #17a2b8;
    color: white;
}

.btn-info:hover {
    background: #138496;
}

.btn-warning {
    background: #ffc107;
    color: #212529;
}

        .btn-warning:hover {
            background: #e0a800;
        }

        /* Delete Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        }

        .modal-container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-icon {
            width: 80px;
            height: 80px;
            background: #e31575;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
            color: white;
        }

        .modal-title {
            color: #e31575;
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 15px 0;
        }

        .modal-message {
            color: #666;
            font-size: 16px;
            margin: 0 0 25px 0;
            line-height: 1.5;
        }

        .modal-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .modal-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 100px;
        }

        .modal-btn-cancel {
            background: #6c757d;
            color: white;
        }

        .modal-btn-cancel:hover {
            background: #5a6268;
        }

        .modal-btn-confirm {
            background: #e31575;
            color: white;
        }

        .modal-btn-confirm:hover {
            background: #c01060;
        }

            /* Results Info */
            .results-info {
            background: #fff;
            padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                margin-top: 20px;
                text-align: center;
                color: #666;
                font-size: 14px;
            }

            /* Status Badge Colors */
            .status-badge {
                padding: 4px 8px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                background-color: #f3f4f6 !important;
                color: #6b7280 !important;
            }
            
            .status-badge.status-pending {
                background-color: #fef3c7 !important;
                color: #92400e !important;
            }
            
.status-badge.status-received {
                background-color: #dbeafe !important;
                color: #1e40af !important;
            }
            
            .status-badge.status-approved {
                background-color: #d1fae5 !important;
                color: #065f46 !important;
            }
            
            .status-badge.status-rejected {
                background-color: #fee2e2 !important;
                color: #991b1b !important;
            }
            
            .status-badge.status-completed {
                background-color: #e0e7ff !important;
                color: #3730a3 !important;
            }
            
            .status-badge.status-active {
                background-color: #d1fae5 !important;
                color: #065f46 !important;
            }
            
            .status-badge.status-inactive {
                background-color: #f3f4f6 !important;
                color: #6b7280 !important;
            }
            
            /* Additional status colors for other possible values */
            .status-badge.status-draft {
                background-color: #f3f4f6 !important;
                color: #6b7280 !important;
            }
            
            .status-badge.status-cancelled {
                background-color: #fee2e2 !important;
                color: #991b1b !important;
            }
            
            .status-badge.status-processing {
                background-color: #fef3c7 !important;
                color: #92400e !important;
            }
            
            .status-badge.status-new {
                background-color: #e0e7ff !important;
                color: #3730a3 !important;
            }
            
            /* Pension Status Colors */
            .status-badge.status-with-pension {
                background-color: #d1fae5 !important;
                color: #065f46 !important;
            }
            
            .status-badge.status-without-pension {
                background-color: #fee2e2 !important;
                color: #991b1b !important;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .main {
                    margin-left: 0;
                    padding: 10px;
                }
                
                .filter-bar {
                    flex-direction: column;
                    align-items: stretch;
                }
                
                .sort-options {
                    justify-content: center;
                }
                
                .seniors-table {
                    font-size: 12px;
                }
                
                .seniors-table th,
                .seniors-table td {
                    padding: 8px 6px;
                }
        }
        </style>

        @if(!isset($includeStylesOnly) || !$includeStylesOnly)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Function to renumber visible rows sequentially
                function renumberVisibleRows(tableElement) {
                    const visibleRows = Array.from(tableElement.querySelectorAll('tbody tr')).filter(row => 
                        row.style.display !== 'none' && row.querySelectorAll('td').length > 0
                    );
                    
                    visibleRows.forEach((row, index) => {
                        const firstCell = row.querySelector('td:first-child');
                        if (firstCell) {
                            firstCell.textContent = index + 1;
                        }
                    });
                }
                
                // Global variables
                let activeFilters = {
                    barangay: [],
                    pension: [],
                    gender: [],
                    status: [],
                    milestone: [],
                    income: []
                };
                let currentSort = null;
                let searchTerm = '';
                let currentTab = 'all-seniors';
                let currentSubTab = 'existing-senior';
                let originalTableHTML = new Map(); // Store original table HTML

                // Tab switching functionality
                function showTab(tabName) {
                    // Hide all tables
                    document.getElementById('all-seniors-table').style.display = 'none';
                    document.getElementById('existing-senior-table').style.display = 'none';
                    document.getElementById('pension-table').style.display = 'none';
                    document.getElementById('id-applicants-table').style.display = 'none';
                    
                    // Hide sub-tabs
                    document.getElementById('benefits-sub-tabs').style.display = 'none';
                    
                    // Remove active class from all tabs
                    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                    
                    // Set current tab
                    currentTab = tabName;
                    
                    // Show selected tab and update button
                    if (tabName === 'all-seniors') {
                        document.getElementById('all-seniors-table').style.display = 'block';
                        document.querySelector('[onclick="showTab(\'all-seniors\')"]').classList.add('active');
                        updateAddButton('Add New Senior', "{{ route('add_new_senior') }}");
                    } else if (tabName === 'benefits-applicants') {
                        document.getElementById('benefits-sub-tabs').style.display = 'flex';
                        document.querySelector('[onclick="showTab(\'benefits-applicants\')"]').classList.add('active');
                        showSubTab(currentSubTab);
                    } else if (tabName === 'id-applicants') {
                        document.getElementById('id-applicants-table').style.display = 'block';
                        document.querySelector('[onclick="showTab(\'id-applicants\')"]').classList.add('active');
                        updateAddButton('Add Application', "{{ route('form_seniorID') }}");
                    }
                    
                    // Initialize sort options for the new tab
                    initializeSortOptions();
                    // Update dynamic filters for the new tab
                    updateDynamicFilters();
                    applyAllFilters();
                }
                
                function showSubTab(subTabName) {
                    // Hide benefit tables
                    document.getElementById('existing-senior-table').style.display = 'none';
                    document.getElementById('pension-table').style.display = 'none';
                    
                    // Remove active class from sub-tabs
                    document.querySelectorAll('.sub-tab-btn').forEach(btn => btn.classList.remove('active'));
                    
                    // Set current sub-tab
                    currentSubTab = subTabName;
                    
                    // Show selected sub-tab
                    if (subTabName === 'existing-senior') {
                        document.getElementById('existing-senior-table').style.display = 'block';
                        document.querySelector('[onclick="showSubTab(\'existing-senior\')"]').classList.add('active');
                        updateAddButton('Add Application', "{{ route('form_existing_senior') }}");
                    } else if (subTabName === 'pension') {
                        document.getElementById('pension-table').style.display = 'block';
                        document.querySelector('[onclick="showSubTab(\'pension\')"]').classList.add('active');
                        updateAddButton('Add Application', "{{ route('form_pension') }}");
                    }
                    
                    // Initialize sort options for the new sub-tab
                    initializeSortOptions();
                    // Update dynamic filters for the new sub-tab
                    updateDynamicFilters();
                    applyAllFilters();
                }
                
                function updateAddButton(text, url) {
                    document.getElementById('add-btn-text').textContent = text;
                    document.getElementById('add-btn').setAttribute('data-url', url);
                }
                
                function updateDynamicFilters() {
                    const tableSpecificSection = document.getElementById('table-specific-filters-section');
                    
                    // Hide all dynamic filters first
                    document.getElementById('status-filter-container').style.display = 'none';
                    const globalStatusContainer = document.getElementById('status-filter-container-global');
                    if (globalStatusContainer) globalStatusContainer.style.display = 'none';
                    document.getElementById('milestone-filter-container').style.display = 'none';
                    document.getElementById('income-filter-container').style.display = 'none';
                    
                    // Show/hide pension filter based on current table
                    const pensionContainer = document.getElementById('pension-filter-container');
                    if (currentTab === 'all-seniors') {
                        pensionContainer.style.display = 'inline-block';
                        // Show global Status filter for All Seniors
                        if (globalStatusContainer) {
                            globalStatusContainer.style.display = 'inline-block';
                            populateGlobalStatusOptions();
                        }
                    } else {
                        pensionContainer.style.display = 'none';
                    }
                    
                    // Show relevant table-specific filters
                    if (currentTab === 'benefits-applicants') {
                        tableSpecificSection.style.display = 'block';
                        if (currentSubTab === 'existing-senior') {
                            // Existing Senior Benefits: Show Status and Milestone Age filters (table-specific)
                            document.getElementById('status-filter-container').style.display = 'inline-block';
                            document.getElementById('milestone-filter-container').style.display = 'inline-block';
                            populateStatusOptions('benefits');
                        } else {
                            // Social Pension: Show Status and Monthly Income filters (table-specific)
                            document.getElementById('status-filter-container').style.display = 'inline-block';
                            document.getElementById('income-filter-container').style.display = 'inline-block';
                            populateStatusOptions('pension');
                        }
                    } else if (currentTab === 'id-applicants') {
                        tableSpecificSection.style.display = 'block';
                        // Senior ID Applicants: Show Status filter only (table-specific)
                        document.getElementById('status-filter-container').style.display = 'inline-block';
                        populateStatusOptions('id');
                    } else {
                        // All Seniors table - hide table-specific section
                        tableSpecificSection.style.display = 'none';
                    }
                }

                // Populate Global Status options (Active/Deceased) for All Seniors
                function populateGlobalStatusOptions() {
                    const statusOptionsContainer = document.getElementById('status-options-global');
                    if (!statusOptionsContainer) return;
                    statusOptionsContainer.innerHTML = '';
                    const statusOptions = [
                        { value: 'Active', label: 'Active' },
                        { value: 'Deceased', label: 'Deceased' }
                    ];
                    statusOptions.forEach(option => {
                        const label = document.createElement('label');
                        label.className = 'filter-option';
                        label.innerHTML = `
                            <input type="checkbox" value="${option.value}" data-filter="status">
                            <span>${option.label}</span>
                        `;
                        statusOptionsContainer.appendChild(label);
                    });
                    // Attach listeners to new checkboxes
                    attachFilterEventListeners();
                }
                
                function populateStatusOptions(tableType) {
                    const statusOptionsContainer = document.getElementById('status-options');
                    statusOptionsContainer.innerHTML = '';
                    
                    let statusOptions = [];
                    
                    if (tableType === 'benefits') {
                        // Benefits applications status options
                        statusOptions = [
                            { value: 'pending', label: 'Pending' },
                            { value: 'received', label: 'Received' },
                            { value: 'approved', label: 'Approved' },
                            { value: 'rejected', label: 'Rejected' }
                        ];
                    } else if (tableType === 'pension') {
                        // Pension applications status options
                        statusOptions = [
                            { value: 'pending', label: 'Pending' },
                            { value: 'received', label: 'Received' },
                            { value: 'approved', label: 'Approved' },
                            { value: 'rejected', label: 'Rejected' }
                        ];
                    } else if (tableType === 'id') {
                        // ID applications status options
                        statusOptions = [
                            { value: 'pending', label: 'Pending' },
                            { value: 'received', label: 'Received' },
                            { value: 'approved', label: 'Approved' },
                            { value: 'rejected', label: 'Rejected' }
                        ];
                    }
                    
                    statusOptions.forEach(option => {
                        const label = document.createElement('label');
                        label.className = 'filter-option';
                        label.innerHTML = `
                            <input type="checkbox" value="${option.value}" data-filter="status">
                            <span>${option.label}</span>
                        `;
                        statusOptionsContainer.appendChild(label);
                    });
                    
                    // Re-attach event listeners to new checkboxes
                    attachFilterEventListeners();
                }
                
                function redirectToForm() {
                    const url = document.getElementById('add-btn').getAttribute('data-url');
                    if (url) {
                        window.location.href = url;
                    }
                }

                // Filter/Sort Panel Toggle functionality
                const filterSortBtn = document.getElementById('filter-sort-btn');
                const filterSortPanel = document.getElementById('filter-sort-panel');
                
                // Reset Button functionality
                const resetBtn = document.getElementById('reset-btn');
                resetBtn.addEventListener('click', function() {
                    // Reset all filters
                    Object.keys(activeFilters).forEach(key => {
                        activeFilters[key] = [];
                    });
                    
                    // Clear search
                    const searchInput = document.getElementById('global-search');
                    if (searchInput) {
                        searchInput.value = '';
                        searchTerm = '';
                    }
                    
                    // Reset sort
                    currentSort = null;
                    
                    // Uncheck all checkboxes
                    const allFilterCheckboxes = document.querySelectorAll('.filter-option input[type="checkbox"]');
                    allFilterCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    
                    // Remove active class from sort options
                    const sortOptions = document.querySelectorAll('.sort-option');
                    sortOptions.forEach(option => option.classList.remove('active'));
                    
                    // Close filter panel
                    filterSortPanel.classList.remove('show');
                    filterSortBtn.classList.remove('active');
                    
                    // Restore original table state for all tabs
                    const allTables = ['all-seniors', 'existing-senior', 'pension', 'id-applicants'];
                    allTables.forEach(tableKey => {
                        const tableElement = document.getElementById(tableKey + '-table');
                        if (tableElement) {
                            const originalHTML = originalTableHTML.get(tableKey);
                            if (originalHTML) {
                                tableElement.innerHTML = originalHTML;
                            }
                        }
                    });
                    
                    // Update displays
                    updateFilterButtons();
                    updateActiveFiltersDisplay();
                    updateSortButton();
                    updateColumnHeaders();
                    
                    console.log('Reset completed - all tables restored to original state');
                });

                filterSortBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    filterSortPanel.classList.toggle('show');
                    this.classList.toggle('active');
                    
                    // Re-initialize sort options when panel is shown
                    if (filterSortPanel.classList.contains('show')) {
                        initializeSortOptions();
                    }
                });

                // Close panel when clicking outside
                document.addEventListener('click', function(e) {
                    // Only close if clicking outside the filter panel and button
                    if (!filterSortPanel.contains(e.target) && !filterSortBtn.contains(e.target)) {
                    filterSortPanel.classList.remove('show');
                    filterSortBtn.classList.remove('active');
                    }
                });

                // Prevent panel from closing when clicking inside
                filterSortPanel.addEventListener('click', function(e) {
                    e.stopPropagation();
                });

                // Search functionality
                const searchInput = document.getElementById('global-search');
                const clearSearchBtn = document.getElementById('clear-search');

                if (searchInput) {
                searchInput.addEventListener('input', function() {
                    searchTerm = this.value.toLowerCase();
                        if (clearSearchBtn) {
                    clearSearchBtn.style.display = searchTerm ? 'block' : 'none';
                        }
                        // Apply search immediately for better UX
                        applyAllFilters();
                });

                // Allow Enter key to apply search immediately
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        applyAllFilters();
                    }
                });
                }

                if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', function() {
                        if (searchInput) {
                    searchInput.value = '';
                    searchTerm = '';
                    this.style.display = 'none';
                    applyAllFilters();
                        }
                });
                }

                // Filter dropdown functionality
                const filterButtons = document.querySelectorAll('.filter-btn');
                const filterDropdowns = document.querySelectorAll('.filter-dropdown');

                filterButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const buttonId = this.id;
                        let targetDropdown;
                        
                        if (buttonId === 'barangay-filter-btn') {
                            targetDropdown = document.getElementById('barangay-dropdown');
                        } else if (buttonId === 'pension-filter-btn') {
                            targetDropdown = document.getElementById('pension-dropdown');
                        } else if (buttonId === 'gender-filter-btn') {
                            targetDropdown = document.getElementById('gender-dropdown');
                        } else if (buttonId === 'status-filter-btn') {
                            targetDropdown = document.getElementById('status-dropdown');
                        } else if (buttonId === 'status-filter-btn-global') {
                            targetDropdown = document.getElementById('status-dropdown-global');
                        } else if (buttonId === 'milestone-filter-btn') {
                            targetDropdown = document.getElementById('milestone-dropdown');
                        } else if (buttonId === 'income-filter-btn') {
                            targetDropdown = document.getElementById('income-dropdown');
                        }
                        
                        // Check if the target dropdown is already open
                        const isAlreadyOpen = targetDropdown && targetDropdown.classList.contains('show');
                        
                        // Close all dropdowns first
                        filterDropdowns.forEach(dropdown => {
                                dropdown.classList.remove('show');
                        });
                        
                        // Then open the clicked dropdown (toggle behavior)
                        if (targetDropdown && !isAlreadyOpen) {
                            targetDropdown.classList.add('show');
                        }
                    });
                });

                // Close filter dropdowns when clicking outside
                document.addEventListener('click', function(e) {
                    // Check if clicking inside a filter dropdown or button
                    const isInsideFilterDropdown = e.target.closest('.filter-dropdown');
                    const isInsideFilterBtn = e.target.closest('.filter-btn');
                    
                    // If clicking outside both dropdown and button, close all dropdowns
                    if (!isInsideFilterDropdown && !isInsideFilterBtn) {
                        filterDropdowns.forEach(dropdown => {
                            dropdown.classList.remove('show');
                        });
                    }
                });

                // Prevent filter dropdowns from closing when clicking inside
                filterDropdowns.forEach(dropdown => {
                    dropdown.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                });

                // Filter search functionality within dropdowns
                const barangaySearch = document.getElementById('barangay-search');
                const categorySearch = document.getElementById('category-search');

                if (barangaySearch) {
                    barangaySearch.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase();
                        const options = document.querySelectorAll('#barangay-options .filter-option');
                        
                        options.forEach(option => {
                            const text = option.textContent.toLowerCase();
                            option.style.display = text.includes(searchTerm) ? 'flex' : 'none';
                        });
                    });
                }


                // Function to attach event listeners to filter checkboxes
                function attachFilterEventListeners() {
                    const filterCheckboxes = document.querySelectorAll('.filter-option input[type="checkbox"]');
                    console.log('Found filter checkboxes:', filterCheckboxes.length);
                    filterCheckboxes.forEach(checkbox => {
                        // Remove existing event listeners to prevent duplicates
                        checkbox.removeEventListener('change', handleFilterChange);
                        checkbox.addEventListener('change', handleFilterChange);
                    });
                }
                
                function handleFilterChange() {
                        const filterType = this.getAttribute('data-filter');
                        const filterValue = this.value;
                    
                    console.log('Filter checkbox changed:', filterType, filterValue, this.checked);
                        
                        if (this.checked) {
                            if (!activeFilters[filterType].includes(filterValue)) {
                                activeFilters[filterType].push(filterValue);
                            }
                        } else {
                            activeFilters[filterType] = activeFilters[filterType].filter(v => v !== filterValue);
                        }
                        
                    console.log('Active filters after change:', activeFilters);
                        updateFilterButtons();
                        updateActiveFiltersDisplay();
                    // Apply filters immediately for better UX
                    applyAllFilters();
                }
                
                // Initial attachment of event listeners
                attachFilterEventListeners();

                // Sort functionality
                function initializeSortOptions() {
                    const sortOptionsContainer = document.getElementById('sort-options');
                    if (!sortOptionsContainer) return;
                    
                    // Clear existing options
                    sortOptionsContainer.innerHTML = '';
                    
                    // Define sort options based on current table
                    let sortOptions = [];
                    
                    if (currentTab === 'all-seniors') {
                        // All Seniors table: NO, OSCA ID, FULL NAME, AGE, GENDER, BARANGAY, STATUS, PENSION STATUS, APP ACCOUNT, ACTION
                        sortOptions = [
                            { field: 'name', order: 'asc', label: 'Name (A-Z)', icon: 'fas fa-sort-alpha-down' },
                            { field: 'name', order: 'desc', label: 'Name (Z-A)', icon: 'fas fa-sort-alpha-up' },
                            { field: 'age', order: 'asc', label: 'Age (Youngest First)', icon: 'fas fa-sort-numeric-down' },
                            { field: 'age', order: 'desc', label: 'Age (Oldest First)', icon: 'fas fa-sort-numeric-up' },
                            { field: 'barangay', order: 'asc', label: 'Barangay (A-Z)', icon: 'fas fa-sort-alpha-down' },
                            { field: 'barangay', order: 'desc', label: 'Barangay (Z-A)', icon: 'fas fa-sort-alpha-up' },
                            { field: 'status', order: 'asc', label: 'Status (A-Z)', icon: 'fas fa-sort-alpha-down' },
                            { field: 'status', order: 'desc', label: 'Status (Z-A)', icon: 'fas fa-sort-alpha-up' },
                            { field: 'pension', order: 'asc', label: 'Pension Status (A-Z)', icon: 'fas fa-sort-alpha-down' },
                            { field: 'pension', order: 'desc', label: 'Pension Status (Z-A)', icon: 'fas fa-sort-alpha-up' }
                        ];
                    } else if (currentTab === 'benefits-applicants') {
                        if (currentSubTab === 'existing-senior') {
                            // Existing Senior Benefits: NO, OSCA ID, FULL NAME, AGE, GENDER, BARANGAY, MILESTONE AGE, STATUS, ACTION
                            sortOptions = [
                                { field: 'name', order: 'asc', label: 'Name (A-Z)', icon: 'fas fa-sort-alpha-down' },
                                { field: 'name', order: 'desc', label: 'Name (Z-A)', icon: 'fas fa-sort-alpha-up' },
                                { field: 'age', order: 'asc', label: 'Age (Youngest First)', icon: 'fas fa-sort-numeric-down' },
                                { field: 'age', order: 'desc', label: 'Age (Oldest First)', icon: 'fas fa-sort-numeric-up' },
                                { field: 'barangay', order: 'asc', label: 'Barangay (A-Z)', icon: 'fas fa-sort-alpha-down' },
                                { field: 'barangay', order: 'desc', label: 'Barangay (Z-A)', icon: 'fas fa-sort-alpha-up' },
                                { field: 'milestone', order: 'asc', label: 'Milestone Age (Low to High)', icon: 'fas fa-sort-numeric-down' },
                                { field: 'milestone', order: 'desc', label: 'Milestone Age (High to Low)', icon: 'fas fa-sort-numeric-up' },
                                { field: 'status', order: 'asc', label: 'Status (A-Z)', icon: 'fas fa-sort-alpha-down' },
                                { field: 'status', order: 'desc', label: 'Status (Z-A)', icon: 'fas fa-sort-alpha-up' }
                            ];
                        } else {
                            // Social Pension: NO, OSCA ID, FULL NAME, AGE, GENDER, BARANGAY, MONTHLY INCOME, STATUS, ACTION
                            sortOptions = [
                                { field: 'name', order: 'asc', label: 'Name (A-Z)', icon: 'fas fa-sort-alpha-down' },
                                { field: 'name', order: 'desc', label: 'Name (Z-A)', icon: 'fas fa-sort-alpha-up' },
                                { field: 'age', order: 'asc', label: 'Age (Youngest First)', icon: 'fas fa-sort-numeric-down' },
                                { field: 'age', order: 'desc', label: 'Age (Oldest First)', icon: 'fas fa-sort-numeric-up' },
                                { field: 'barangay', order: 'asc', label: 'Barangay (A-Z)', icon: 'fas fa-sort-alpha-down' },
                                { field: 'barangay', order: 'desc', label: 'Barangay (Z-A)', icon: 'fas fa-sort-alpha-up' },
                                { field: 'income', order: 'asc', label: 'Monthly Income (Low to High)', icon: 'fas fa-sort-numeric-down' },
                                { field: 'income', order: 'desc', label: 'Monthly Income (High to Low)', icon: 'fas fa-sort-numeric-up' },
                                { field: 'status', order: 'asc', label: 'Status (A-Z)', icon: 'fas fa-sort-alpha-down' },
                                { field: 'status', order: 'desc', label: 'Status (Z-A)', icon: 'fas fa-sort-alpha-up' }
                            ];
                        }
                    } else if (currentTab === 'id-applicants') {
                        // Senior ID Applicants: NO, OSCA ID, FULL NAME, AGE, GENDER, BARANGAY, STATUS, ACTION
                        sortOptions = [
                            { field: 'name', order: 'asc', label: 'Name (A-Z)', icon: 'fas fa-sort-alpha-down' },
                            { field: 'name', order: 'desc', label: 'Name (Z-A)', icon: 'fas fa-sort-alpha-up' },
                            { field: 'age', order: 'asc', label: 'Age (Youngest First)', icon: 'fas fa-sort-numeric-down' },
                            { field: 'age', order: 'desc', label: 'Age (Oldest First)', icon: 'fas fa-sort-numeric-up' },
                            { field: 'barangay', order: 'asc', label: 'Barangay (A-Z)', icon: 'fas fa-sort-alpha-down' },
                            { field: 'barangay', order: 'desc', label: 'Barangay (Z-A)', icon: 'fas fa-sort-alpha-up' },
                            { field: 'status', order: 'asc', label: 'Status (A-Z)', icon: 'fas fa-sort-alpha-down' },
                            { field: 'status', order: 'desc', label: 'Status (Z-A)', icon: 'fas fa-sort-alpha-up' }
                        ];
                    }
                    
                    // Create sort option buttons
                 sortOptions.forEach(option => {
                        const button = document.createElement('button');
                        button.className = 'sort-option';
                        button.setAttribute('data-sort', option.field);
                        button.setAttribute('data-order', option.order);
                        button.innerHTML = `<i class="${option.icon}"></i> ${option.label}`;
                        
                        // Add event listener
                        button.addEventListener('click', handleSortClick);
                        
                        sortOptionsContainer.appendChild(button);
                    });
                    
                    console.log('Initialized sort options for:', currentTab, currentSubTab, 'Options:', sortOptions.length);
                }

                function handleSortClick() {
                         const field = this.getAttribute('data-sort');
                         const direction = this.getAttribute('data-order');
                         
                    console.log('Sort clicked:', field, direction);
                    console.log('Current tab:', currentTab);
                    console.log('Current sub tab:', currentSubTab);
                    
                    // Check if this sort option is already active
                    const isCurrentlyActive = currentSort && 
                                            currentSort.field === field && 
                                            currentSort.direction === direction;
                         
                         // Update active sort option
                    const sortOptions = document.querySelectorAll('.sort-option');
                         sortOptions.forEach(opt => opt.classList.remove('active'));
                    
                    if (isCurrentlyActive) {
                        // If clicking the same sort option, unselect it
                        currentSort = null;
                        console.log('Sort unselected, returning to default state');
                    } else {
                        // Select the new sort option
                        this.classList.add('active');
                         currentSort = { field, direction };
                        console.log('Current sort set to:', currentSort);
                    }
                    
                         updateSortButton();
                         updateColumnHeaders();
                    
                    console.log('About to call applyAllFilters...');
                         applyAllFilters();
                    console.log('applyAllFilters called');
                }

                // Initialize sort options will be called when tabs are switched

                 // Column header sorting functionality
                 const sortableHeaders = document.querySelectorAll('.sortable-header');
                 
                 sortableHeaders.forEach(header => {
                     header.addEventListener('click', function() {
                         const field = this.getAttribute('data-sort');
                         let direction = 'asc';
                         
                        // If clicking the same header, toggle direction or unselect
                        if (currentSort && currentSort.field === field) {
                            if (currentSort.direction === 'asc') {
                                direction = 'desc';
                            } else {
                                // If already desc, unselect (return to default)
                                currentSort = null;
                                updateSortButton();
                                updateColumnHeaders();
                                applyAllFilters();
                                return;
                            }
                         }
                         
                         currentSort = { field, direction };
                         updateSortButton();
                         updateColumnHeaders();
                         applyAllFilters();
                     });
                 });

                // Update filter button appearance
                function updateFilterButtons() {
                    Object.keys(activeFilters).forEach(filterType => {
                        let button;
                        if (filterType === 'barangay') {
                            button = document.getElementById('barangay-filter-btn');
                        } else if (filterType === 'pension') {
                            button = document.getElementById('pension-filter-btn');
                        } else if (filterType === 'gender') {
                            button = document.getElementById('gender-filter-btn');
                        } else if (filterType === 'status') {
                            // Use global Status button for All Seniors, else table-specific Status button
                            if (currentTab === 'all-seniors') {
                                button = document.getElementById('status-filter-btn-global');
                            } else {
                                button = document.getElementById('status-filter-btn');
                            }
                        } else if (filterType === 'milestone') {
                            button = document.getElementById('milestone-filter-btn');
                        } else if (filterType === 'income') {
                            button = document.getElementById('income-filter-btn');
                        }
                        
                        if (button) {
                            if (activeFilters[filterType].length > 0) {
                                button.classList.add('active');
                            } else {
                                button.classList.remove('active');
                            }
                        }
                    });
                }

                // Update sort button appearance
                function updateSortButton() {
                    // Update sort options visual state
                    const sortOptions = document.querySelectorAll('.sort-option');
                    sortOptions.forEach(option => {
                        const field = option.getAttribute('data-sort');
                        const direction = option.getAttribute('data-order');
                        
                        if (currentSort && currentSort.field === field && currentSort.direction === direction) {
                            option.classList.add('active');
                        } else {
                            option.classList.remove('active');
                        }
                    });
                }

                // Update column headers visual state
                function updateColumnHeaders() {
                    const sortableHeaders = document.querySelectorAll('.sortable-header');
                    
                    // Reset all headers
                    sortableHeaders.forEach(header => {
                        header.classList.remove('active');
                        header.removeAttribute('data-direction');
                    });
                    
                    // Set active header if sorting is applied
                    if (currentSort.field) {
                        const activeHeader = document.querySelector(`[data-sort="${currentSort.field}"]`);
                        if (activeHeader && activeHeader.classList.contains('sortable-header')) {
                            activeHeader.classList.add('active');
                            activeHeader.setAttribute('data-direction', currentSort.direction);
                        }
                    }
                }

                // Update active filters display
                function updateActiveFiltersDisplay() {
                    const activeFiltersContainer = document.getElementById('active-filters-section');
                    const filterChips = document.getElementById('filter-chips');
                    
                    // Clear existing chips
                    filterChips.innerHTML = '';
                    
                    let hasActiveFilters = false;
                    
                    Object.keys(activeFilters).forEach(filterType => {
                        activeFilters[filterType].forEach(value => {
                            hasActiveFilters = true;
                            const chip = document.createElement('div');
                            chip.className = 'filter-chip';
                            chip.innerHTML = `
                                <span>${value}</span>
                                <button class="remove-chip" data-filter="${filterType}" data-value="${value}">�</button>
                            `;
                            filterChips.appendChild(chip);
                        });
                    });
                    
                    activeFiltersContainer.style.display = hasActiveFilters ? 'block' : 'none';
                    
                    // Add event listeners to remove buttons
                    const removeButtons = filterChips.querySelectorAll('.remove-chip');
                    removeButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            const filterType = this.getAttribute('data-filter');
                            const filterValue = this.getAttribute('data-value');
                            
                            // Remove from active filters
                            activeFilters[filterType] = activeFilters[filterType].filter(v => v !== filterValue);
                            
                            // Uncheck the corresponding checkbox
                            const checkbox = document.querySelector(`input[data-filter="${filterType}"][value="${filterValue}"]`);
                            if (checkbox) {
                                checkbox.checked = false;
                            }
                            
                            updateFilterButtons();
                            updateActiveFiltersDisplay();
                            applyAllFilters();
                        });
                    });
                }

                // Clear all filters
                const clearAllFiltersBtn = document.getElementById('clear-all-filters');
                if (clearAllFiltersBtn) {
                    clearAllFiltersBtn.addEventListener('click', function() {
                        // Reset all filters
                        Object.keys(activeFilters).forEach(key => {
                            activeFilters[key] = [];
                        });
                        
                        // Clear search
                        const searchInput = document.getElementById('global-search');
                        if (searchInput) {
                            searchInput.value = '';
                            searchTerm = '';
                        }
                        
                        // Reset sort
                        currentSort = null;
                        
                        // Uncheck all checkboxes
                        const allFilterCheckboxes = document.querySelectorAll('.filter-option input[type="checkbox"]');
                        allFilterCheckboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });
                        
                        // Remove active class from sort options
                        const sortOptions = document.querySelectorAll('.sort-option');
                        sortOptions.forEach(option => option.classList.remove('active'));
                        
                        updateFilterButtons();
                        updateActiveFiltersDisplay();
                        updateSortButton();
                        updateColumnHeaders();
                        applyAllFilters();
                    });
                }


                // Apply all filters and sorting
                function applyAllFilters() {
                    console.log('=== applyAllFilters called ===');
                    console.log('Current tab:', currentTab);
                    console.log('Current sub tab:', currentSubTab);
                    console.log('Current sort:', currentSort);
                    
                    let activeTable;
                    if (currentTab === 'all-seniors') {
                        activeTable = document.querySelector('#all-seniors-table tbody');
                        console.log('Targeting all-seniors table');
                    } else if (currentTab === 'benefits-applicants') {
                        if (currentSubTab === 'existing-senior') {
                            activeTable = document.querySelector('#existing-senior-table tbody');
                        } else {
                            activeTable = document.querySelector('#pension-table tbody');
                        }
                    } else if (currentTab === 'id-applicants') {
                        activeTable = document.querySelector('#id-applicants-table tbody');
                    }
                    
                    if (!activeTable) return;
                    
                    // Debug: Log active filters
                    console.log('Active filters:', activeFilters);
                    console.log('Current tab:', currentTab);
                    console.log('Current sub tab:', currentSubTab);
                    console.log('Active table found:', activeTable);
                    
                    const rows = Array.from(activeTable.querySelectorAll('tr'));
                    console.log('Total rows found:', rows.length);
                    
                    // Store original table HTML if not already stored
                    if (!originalTableHTML.has(currentTab + '-' + currentSubTab)) {
                        originalTableHTML.set(currentTab + '-' + currentSubTab, activeTable.innerHTML);
                    }
                    
                    // Filter rows
                    const filteredRows = rows.filter(row => {
                        const cells = row.querySelectorAll('td');
                        if (cells.length === 0) return false;
                        
                        const rowText = row.textContent.toLowerCase();
                        
                        // Search filter
                        if (searchTerm && !rowText.includes(searchTerm)) {
                            return false;
                        }
                        
                        // Specific filters based on table structure
                        if (currentTab === 'all-seniors') {
                            // All Seniors table: OSCA ID, FULL NAME, AGE, GENDER, BARANGAY, STATUS, PENSION STATUS, APP ACCOUNT, ACTION
                            const gender = cells[4]?.textContent.trim() || '';
                            const barangay = cells[5]?.textContent.trim() || '';
                            const status = cells[6]?.textContent.trim() || '';
                            const pensionStatus = cells[7]?.textContent.trim() || '';
                            
                            // Gender filter - normalize values for comparison
                            if (activeFilters.gender.length > 0) {
                                const normalizedGender = gender.toLowerCase();
                                const hasGenderMatch = activeFilters.gender.some(filterGender => 
                                    normalizedGender === filterGender.toLowerCase()
                                );
                                if (!hasGenderMatch) return false;
                            }
                            
                            // Barangay filter - normalize values for comparison
                            if (activeFilters.barangay.length > 0) {
                                const normalizedBarangay = barangay.toLowerCase().replace(/\s+/g, '-');
                                const hasBarangayMatch = activeFilters.barangay.some(filterBarangay => {
                                    const normalizedFilter = filterBarangay.toLowerCase().replace(/\s+/g, '-');
                                    return normalizedBarangay === normalizedFilter;
                                });
                                if (!hasBarangayMatch) return false;
                            }
                            
                            // Status filter
                            if (activeFilters.status.length > 0) {
                                const normalizedStatus = status.toLowerCase().replace(/\s+/g, '_');
                                const hasStatusMatch = activeFilters.status.some(filterStatus => {
                                    const normalizedFilter = filterStatus.toLowerCase().replace(/\s+/g, '_');
                                    return normalizedStatus === normalizedFilter;
                                });
                                if (!hasStatusMatch) return false;
                            }

                            // Pension filter - normalize values for comparison
                            if (activeFilters.pension.length > 0) {
                                const normalizedPension = pensionStatus.toLowerCase();
                                const hasPensionMatch = activeFilters.pension.some(filterPension => 
                                    normalizedPension === filterPension.toLowerCase()
                                );
                                if (!hasPensionMatch) return false;
                            }
                        } else if (currentTab === 'benefits-applicants') {
                            if (currentSubTab === 'existing-senior') {
                                // Existing Senior Benefits: NO, OSCA ID, FULL NAME, AGE, GENDER, BARANGAY, MILESTONE AGE, STATUS, ACTION
                                const gender = cells[4]?.textContent.trim() || '';
                                const barangay = cells[5]?.textContent.trim() || '';
                                
                                // Gender filter
                                if (activeFilters.gender.length > 0) {
                                    const normalizedGender = gender.toLowerCase();
                                    const hasGenderMatch = activeFilters.gender.some(filterGender => 
                                        normalizedGender === filterGender.toLowerCase()
                                    );
                                    if (!hasGenderMatch) return false;
                                }
                                
                                // Barangay filter
                                if (activeFilters.barangay.length > 0) {
                                    const normalizedBarangay = barangay.toLowerCase().replace(/\s+/g, '-');
                                    const hasBarangayMatch = activeFilters.barangay.some(filterBarangay => {
                                        const normalizedFilter = filterBarangay.toLowerCase().replace(/\s+/g, '-');
                                        return normalizedBarangay === normalizedFilter;
                                    });
                                    if (!hasBarangayMatch) return false;
                                }
                                
                                // Pension filter (not applicable for this table, but keep for consistency)
                                if (activeFilters.pension.length > 0) {
                                    return false; // No pension data in this table
                                }
                                
                                // Status filter
                                if (activeFilters.status.length > 0) {
                                    const status = cells[7]?.textContent.trim() || '';
                                    const normalizedStatus = status.toLowerCase().replace(/\s+/g, '_');
                                    const hasStatusMatch = activeFilters.status.some(filterStatus => {
                                        const normalizedFilter = filterStatus.toLowerCase().replace(/\s+/g, '_');
                                        return normalizedStatus === normalizedFilter;
                                    });
                                    if (!hasStatusMatch) return false;
                                }
                                
                                // Milestone Age filter
                                if (activeFilters.milestone.length > 0) {
                                    const milestoneAge = cells[6]?.textContent.trim() || '';
                                    const hasMilestoneMatch = activeFilters.milestone.some(filterMilestone => 
                                        milestoneAge === filterMilestone
                                    );
                                    if (!hasMilestoneMatch) return false;
                                }
                            } else {
                                // Social Pension: NO, OSCA ID, FULL NAME, AGE, GENDER, BARANGAY, MONTHLY INCOME, STATUS, ACTION
                                const gender = cells[4]?.textContent.trim() || '';
                                const barangay = cells[5]?.textContent.trim() || '';
                                
                                // Gender filter
                                if (activeFilters.gender.length > 0) {
                                    const normalizedGender = gender.toLowerCase();
                                    const hasGenderMatch = activeFilters.gender.some(filterGender => 
                                        normalizedGender === filterGender.toLowerCase()
                                    );
                                    if (!hasGenderMatch) return false;
                                }
                                
                                // Barangay filter
                                if (activeFilters.barangay.length > 0) {
                                    const normalizedBarangay = barangay.toLowerCase().replace(/\s+/g, '-');
                                    const hasBarangayMatch = activeFilters.barangay.some(filterBarangay => {
                                        const normalizedFilter = filterBarangay.toLowerCase().replace(/\s+/g, '-');
                                        return normalizedBarangay === normalizedFilter;
                                    });
                                    if (!hasBarangayMatch) return false;
                                }
                                
                                // Pension filter (not applicable for this table, but keep for consistency)
                                if (activeFilters.pension.length > 0) {
                                    return false; // No pension data in this table
                                }
                                
                                // Status filter
                                if (activeFilters.status.length > 0) {
                                    const status = cells[7]?.textContent.trim() || '';
                                    const normalizedStatus = status.toLowerCase().replace(/\s+/g, '_');
                                    const hasStatusMatch = activeFilters.status.some(filterStatus => {
                                        const normalizedFilter = filterStatus.toLowerCase().replace(/\s+/g, '_');
                                        return normalizedStatus === normalizedFilter;
                                    });
                                    if (!hasStatusMatch) return false;
                                }
                                
                                // Monthly Income filter
                                if (activeFilters.income.length > 0) {
                                    const incomeText = cells[6]?.textContent.trim() || '';
                                    const incomeValue = parseFloat(incomeText.replace(/[?,]/g, '')) || 0;
                                    const hasIncomeMatch = activeFilters.income.some(filterIncome => {
                                        switch(filterIncome) {
                                            case 'below-10k':
                                                return incomeValue < 10000;
                                            case '10k-15k':
                                                return incomeValue >= 10000 && incomeValue <= 15000;
                                            case '15k-20k':
                                                return incomeValue >= 15000 && incomeValue <= 20000;
                                            case '20k-25k':
                                                return incomeValue >= 20000 && incomeValue <= 25000;
                                            case 'above-25k':
                                                return incomeValue > 25000;
                                            default:
                                    return false;
                                }
                                    });
                                    if (!hasIncomeMatch) return false;
                                }
                            }
                        } else if (currentTab === 'id-applicants') {
                            // Senior ID Applicants: NO, OSCA ID, FULL NAME, AGE, GENDER, BARANGAY, STATUS, ACTION
                            const gender = cells[4]?.textContent.trim() || '';
                            const barangay = cells[5]?.textContent.trim() || '';
                            
                            // Gender filter
                            if (activeFilters.gender.length > 0) {
                                const normalizedGender = gender.toLowerCase();
                                const hasGenderMatch = activeFilters.gender.some(filterGender => 
                                    normalizedGender === filterGender.toLowerCase()
                                );
                                if (!hasGenderMatch) return false;
                            }
                            
                            // Barangay filter
                            if (activeFilters.barangay.length > 0) {
                                const normalizedBarangay = barangay.toLowerCase().replace(/\s+/g, '-');
                                const hasBarangayMatch = activeFilters.barangay.some(filterBarangay => {
                                    const normalizedFilter = filterBarangay.toLowerCase().replace(/\s+/g, '-');
                                    return normalizedBarangay === normalizedFilter;
                                });
                                if (!hasBarangayMatch) return false;
                            }
                            
                            // Pension filter (not applicable for this table, but keep for consistency)
                            if (activeFilters.pension.length > 0) {
                                return false; // No pension data in this table
                            }
                            
                            // Status filter
                            if (activeFilters.status.length > 0) {
                                const status = cells[6]?.textContent.trim() || '';
                                const normalizedStatus = status.toLowerCase().replace(/\s+/g, '_');
                                const hasStatusMatch = activeFilters.status.some(filterStatus => {
                                    const normalizedFilter = filterStatus.toLowerCase().replace(/\s+/g, '_');
                                    return normalizedStatus === normalizedFilter;
                                });
                                if (!hasStatusMatch) return false;
                            }
                        }
                        
                        return true;
                    });
                    
                    // Sort rows
                    if (currentSort && currentSort.field) {
                        console.log('=== SORTING STARTED ===');
                        console.log('Sorting rows by:', currentSort.field, currentSort.direction, 'Total rows:', filteredRows.length);
                        
                        // Log first few rows before sorting
                        console.log('Before sorting - first 3 rows:');
                        for (let i = 0; i < Math.min(3, filteredRows.length); i++) {
                            const cells = filteredRows[i].querySelectorAll('td');
                            console.log(`Row ${i}:`, cells[2]?.textContent.trim(), cells[3]?.textContent.trim());
                        }
                        
                        filteredRows.sort((a, b) => {
                            let aValue, bValue;
                            
                            if (currentTab === 'all-seniors') {
                                // All Seniors table: NO, OSCA ID, FULL NAME, AGE, GENDER, BARANGAY, STATUS, PENSION STATUS, APP ACCOUNT, ACTION
                            switch (currentSort.field) {
                                case 'name':
                                        aValue = a.cells[2]?.textContent.trim() || '';
                                        bValue = b.cells[2]?.textContent.trim() || '';
                                    break;
                                case 'age':
                                        aValue = parseInt(a.cells[3]?.textContent.trim()) || 0;
                                        bValue = parseInt(b.cells[3]?.textContent.trim()) || 0;
                                    break;
                                case 'barangay':
                                        aValue = a.cells[5]?.textContent.trim() || '';
                                        bValue = b.cells[5]?.textContent.trim() || '';
                                        break;
                                case 'status':
                                        aValue = a.cells[6]?.textContent.trim() || '';
                                        bValue = b.cells[6]?.textContent.trim() || '';
                                    break;
                                    case 'pension':
                                        aValue = a.cells[7]?.textContent.trim() || '';
                                        bValue = b.cells[7]?.textContent.trim() || '';
                                    break;
                                default:
                                    return 0;
                                }
                            } else if (currentTab === 'benefits-applicants') {
                                if (currentSubTab === 'existing-senior') {
                                    // Existing Senior Benefits: NO, OSCA ID, FULL NAME, AGE, GENDER, BARANGAY, MILESTONE AGE, STATUS, ACTION
                                    switch (currentSort.field) {
                                        case 'name':
                                            aValue = a.cells[2]?.textContent.trim() || '';
                                            bValue = b.cells[2]?.textContent.trim() || '';
                                            break;
                                        case 'age':
                                            aValue = parseInt(a.cells[3]?.textContent.trim()) || 0;
                                            bValue = parseInt(b.cells[3]?.textContent.trim()) || 0;
                                            break;
                                        case 'barangay':
                                            aValue = a.cells[5]?.textContent.trim() || '';
                                            bValue = b.cells[5]?.textContent.trim() || '';
                                            break;
                                        case 'milestone':
                                            aValue = parseInt(a.cells[6]?.textContent.trim()) || 0;
                                            bValue = parseInt(b.cells[6]?.textContent.trim()) || 0;
                                            break;
                                        case 'status':
                                            aValue = a.cells[7]?.textContent.trim() || '';
                                            bValue = b.cells[7]?.textContent.trim() || '';
                                            break;
                                        default:
                                            return 0;
                                    }
                                } else {
                                    // Social Pension: NO, OSCA ID, FULL NAME, AGE, GENDER, BARANGAY, MONTHLY INCOME, STATUS, ACTION
                                    switch (currentSort.field) {
                                        case 'name':
                                            aValue = a.cells[2]?.textContent.trim() || '';
                                            bValue = b.cells[2]?.textContent.trim() || '';
                                            break;
                                        case 'age':
                                            aValue = parseInt(a.cells[3]?.textContent.trim()) || 0;
                                            bValue = parseInt(b.cells[3]?.textContent.trim()) || 0;
                                            break;
                                        case 'barangay':
                                            aValue = a.cells[5]?.textContent.trim() || '';
                                            bValue = b.cells[5]?.textContent.trim() || '';
                                            break;
                                        case 'income':
                                            aValue = parseFloat(a.cells[6]?.textContent.replace(/[?,]/g, '')) || 0;
                                            bValue = parseFloat(b.cells[6]?.textContent.replace(/[?,]/g, '')) || 0;
                                            break;
                                        case 'status':
                                            aValue = a.cells[7]?.textContent.trim() || '';
                                            bValue = b.cells[7]?.textContent.trim() || '';
                                            break;
                                        default:
                                            return 0;
                                    }
                                }
                            } else if (currentTab === 'id-applicants') {
                                // Senior ID Applicants: NO, OSCA ID, FULL NAME, AGE, GENDER, BARANGAY, STATUS, ACTION
                                switch (currentSort.field) {
                                    case 'name':
                                        aValue = a.cells[2]?.textContent.trim() || '';
                                        bValue = b.cells[2]?.textContent.trim() || '';
                                        break;
                                    case 'age':
                                        aValue = parseInt(a.cells[3]?.textContent.trim()) || 0;
                                        bValue = parseInt(b.cells[3]?.textContent.trim()) || 0;
                                        break;
                                    case 'barangay':
                                        aValue = a.cells[5]?.textContent.trim() || '';
                                        bValue = b.cells[5]?.textContent.trim() || '';
                                        break;
                                    case 'status':
                                        aValue = a.cells[6]?.textContent.trim() || '';
                                        bValue = b.cells[6]?.textContent.trim() || '';
                                        break;
                                    default:
                                        return 0;
                                }
                            }
                            
                            if (typeof aValue === 'string') {
                                aValue = aValue.toLowerCase();
                                bValue = bValue.toLowerCase();
                            }
                            
                            let comparison = 0;
                            if (aValue < bValue) comparison = -1;
                            else if (aValue > bValue) comparison = 1;
                            
                            const result = currentSort.direction === 'asc' ? comparison : -comparison;
                            console.log('Comparing:', aValue, 'vs', bValue, 'result:', result);
                            return result;
                        });
                        
                        // Log first few rows after sorting
                        console.log('After sorting - first 3 rows:');
                        for (let i = 0; i < Math.min(3, filteredRows.length); i++) {
                            const cells = filteredRows[i].querySelectorAll('td');
                            console.log(`Row ${i}:`, cells[2]?.textContent.trim(), cells[3]?.textContent.trim());
                        }
                        console.log('=== SORTING COMPLETED ===');
                    }
                    
                    // Hide all rows first
                    rows.forEach(row => row.style.display = 'none');
                    
                    if (currentSort && currentSort.field) {
                        // Show filtered and sorted rows in the correct order
                        filteredRows.forEach(row => {
                            row.style.display = '';
                            activeTable.appendChild(row);
                        });
                        
                        // Renumber the visible rows sequentially
                        renumberVisibleRows(activeTable);
                    } else {
                        // No sorting active - restore original order
                        const originalHTML = originalTableHTML.get(currentTab + '-' + currentSubTab);
                        if (originalHTML) {
                            // Restore original HTML
                            activeTable.innerHTML = originalHTML;
                            
                            // Re-apply filters to the restored rows
                            const restoredRows = Array.from(activeTable.querySelectorAll('tr'));
                            restoredRows.forEach(row => {
                                const cells = row.querySelectorAll('td');
                                if (cells.length === 0) return;
                                
                                let shouldShow = true;
                                
                                // Apply search filter
                                if (searchTerm) {
                                    const searchableText = Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ');
                                    if (!searchableText.includes(searchTerm)) {
                                        shouldShow = false;
                                    }
                                }
                                
                                // Apply other filters based on table structure
                                if (shouldShow && currentTab === 'all-seniors') {
                                    const gender = cells[4]?.textContent.trim() || '';
                                    const barangay = cells[5]?.textContent.trim() || '';
                                    const status = cells[6]?.textContent.trim() || '';
                                    const pensionStatus = cells[7]?.textContent.trim() || '';
                                    
                                    // Apply filters
                                    if (activeFilters.gender.length > 0) {
                                        const normalizedGender = gender.toLowerCase();
                                        const hasGenderMatch = activeFilters.gender.some(filterGender => 
                                            normalizedGender === filterGender.toLowerCase()
                                        );
                                        if (!hasGenderMatch) shouldShow = false;
                                    }
                                    
                                    if (shouldShow && activeFilters.barangay.length > 0) {
                                        const normalizedBarangay = barangay.toLowerCase().replace(/\s+/g, '-');
                                        const hasBarangayMatch = activeFilters.barangay.some(filterBarangay => {
                                            const normalizedFilter = filterBarangay.toLowerCase().replace(/\s+/g, '-');
                                            return normalizedBarangay === normalizedFilter;
                                        });
                                        if (!hasBarangayMatch) shouldShow = false;
                                    }
                                    
                                    // Status filter
                                    if (shouldShow && activeFilters.status.length > 0) {
                                        const normalizedStatus = status.toLowerCase().replace(/\s+/g, '_');
                                        const hasStatusMatch = activeFilters.status.some(filterStatus => {
                                            const normalizedFilter = filterStatus.toLowerCase().replace(/\s+/g, '_');
                                            return normalizedStatus === normalizedFilter;
                                        });
                                        if (!hasStatusMatch) shouldShow = false;
                                    }

                                    if (shouldShow && activeFilters.pension.length > 0) {
                                        const normalizedPension = pensionStatus.toLowerCase();
                                        const hasPensionMatch = activeFilters.pension.some(filterPension => 
                                            normalizedPension === filterPension.toLowerCase()
                                        );
                                        if (!hasPensionMatch) shouldShow = false;
                                    }
                                } else if (shouldShow && currentTab === 'benefits-applicants') {
                                    if (currentSubTab === 'existing-senior') {
                                        // Existing Senior Benefits: NO, OSCA ID, FULL NAME, AGE, GENDER, BARANGAY, MILESTONE AGE, STATUS, ACTION
                                        const gender = cells[4]?.textContent.trim() || '';
                                        const barangay = cells[5]?.textContent.trim() || '';
                                        
                                        // Apply filters
                                        if (activeFilters.gender.length > 0) {
                                            const normalizedGender = gender.toLowerCase();
                                            const hasGenderMatch = activeFilters.gender.some(filterGender => 
                                                normalizedGender === filterGender.toLowerCase()
                                            );
                                            if (!hasGenderMatch) shouldShow = false;
                                        }
                                        
                                        if (shouldShow && activeFilters.barangay.length > 0) {
                                            const normalizedBarangay = barangay.toLowerCase().replace(/\s+/g, '-');
                                            const hasBarangayMatch = activeFilters.barangay.some(filterBarangay => {
                                                const normalizedFilter = filterBarangay.toLowerCase().replace(/\s+/g, '-');
                                                return normalizedBarangay === normalizedFilter;
                                            });
                                            if (!hasBarangayMatch) shouldShow = false;
                                        }
                                        
                                        if (shouldShow && activeFilters.pension.length > 0) {
                                            shouldShow = false; // No pension data in this table
                                        }
                                        
                                        // Status filter
                                        if (shouldShow && activeFilters.status.length > 0) {
                                            const status = cells[7]?.textContent.trim() || '';
                                            const normalizedStatus = status.toLowerCase().replace(/\s+/g, '_');
                                            const hasStatusMatch = activeFilters.status.some(filterStatus => {
                                                const normalizedFilter = filterStatus.toLowerCase().replace(/\s+/g, '_');
                                                return normalizedStatus === normalizedFilter;
                                            });
                                            if (!hasStatusMatch) shouldShow = false;
                                        }
                                        
                                        // Milestone Age filter
                                        if (shouldShow && activeFilters.milestone.length > 0) {
                                            const milestoneAge = cells[6]?.textContent.trim() || '';
                                            const hasMilestoneMatch = activeFilters.milestone.some(filterMilestone => 
                                                milestoneAge === filterMilestone
                                            );
                                            if (!hasMilestoneMatch) shouldShow = false;
                                        }
                                    } else {
                                        // Social Pension: NO, OSCA ID, FULL NAME, AGE, GENDER, BARANGAY, MONTHLY INCOME, STATUS, ACTION
                                        const gender = cells[4]?.textContent.trim() || '';
                                        const barangay = cells[5]?.textContent.trim() || '';
                                        
                                        // Apply filters
                                        if (activeFilters.gender.length > 0) {
                                            const normalizedGender = gender.toLowerCase();
                                            const hasGenderMatch = activeFilters.gender.some(filterGender => 
                                                normalizedGender === filterGender.toLowerCase()
                                            );
                                            if (!hasGenderMatch) shouldShow = false;
                                        }
                                        
                                        if (shouldShow && activeFilters.barangay.length > 0) {
                                            const normalizedBarangay = barangay.toLowerCase().replace(/\s+/g, '-');
                                            const hasBarangayMatch = activeFilters.barangay.some(filterBarangay => {
                                                const normalizedFilter = filterBarangay.toLowerCase().replace(/\s+/g, '-');
                                                return normalizedBarangay === normalizedFilter;
                                            });
                                            if (!hasBarangayMatch) shouldShow = false;
                                        }
                                        
                                        if (shouldShow && activeFilters.pension.length > 0) {
                                            shouldShow = false; // No pension data in this table
                                        }
                                        
                                        // Status filter
                                        if (shouldShow && activeFilters.status.length > 0) {
                                            const status = cells[7]?.textContent.trim() || '';
                                            const normalizedStatus = status.toLowerCase().replace(/\s+/g, '_');
                                            const hasStatusMatch = activeFilters.status.some(filterStatus => {
                                                const normalizedFilter = filterStatus.toLowerCase().replace(/\s+/g, '_');
                                                return normalizedStatus === normalizedFilter;
                                            });
                                            if (!hasStatusMatch) shouldShow = false;
                                        }
                                        
                                        // Monthly Income filter
                                        if (shouldShow && activeFilters.income.length > 0) {
                                            const incomeText = cells[6]?.textContent.trim() || '';
                                            const incomeValue = parseFloat(incomeText.replace(/[?,]/g, '')) || 0;
                                            const hasIncomeMatch = activeFilters.income.some(filterIncome => {
                                                switch(filterIncome) {
                                                    case 'below-10k':
                                                        return incomeValue < 10000;
                                                    case '10k-15k':
                                                        return incomeValue >= 10000 && incomeValue <= 15000;
                                                    case '15k-20k':
                                                        return incomeValue >= 15000 && incomeValue <= 20000;
                                                    case '20k-25k':
                                                        return incomeValue >= 20000 && incomeValue <= 25000;
                                                    case 'above-25k':
                                                        return incomeValue > 25000;
                                                    default:
                                                        return false;
                                                }
                                            });
                                            if (!hasIncomeMatch) shouldShow = false;
                                        }
                                    }
                                } else if (shouldShow && currentTab === 'id-applicants') {
                                    // Senior ID Applicants: NO, OSCA ID, FULL NAME, AGE, GENDER, BARANGAY, STATUS, ACTION
                                    const gender = cells[4]?.textContent.trim() || '';
                                    const barangay = cells[5]?.textContent.trim() || '';
                                    
                                    // Apply filters
                                    if (activeFilters.gender.length > 0) {
                                        const normalizedGender = gender.toLowerCase();
                                        const hasGenderMatch = activeFilters.gender.some(filterGender => 
                                            normalizedGender === filterGender.toLowerCase()
                                        );
                                        if (!hasGenderMatch) shouldShow = false;
                                    }
                                    
                                    if (shouldShow && activeFilters.barangay.length > 0) {
                                        const normalizedBarangay = barangay.toLowerCase().replace(/\s+/g, '-');
                                        const hasBarangayMatch = activeFilters.barangay.some(filterBarangay => {
                                            const normalizedFilter = filterBarangay.toLowerCase().replace(/\s+/g, '-');
                                            return normalizedBarangay === normalizedFilter;
                                        });
                                        if (!hasBarangayMatch) shouldShow = false;
                                    }
                                    
                                    if (shouldShow && activeFilters.pension.length > 0) {
                                        shouldShow = false; // No pension data in this table
                                    }
                                    
                                    // Status filter
                                    if (shouldShow && activeFilters.status.length > 0) {
                                        const status = cells[6]?.textContent.trim() || '';
                                        const normalizedStatus = status.toLowerCase().replace(/\s+/g, '_');
                                        const hasStatusMatch = activeFilters.status.some(filterStatus => {
                                            const normalizedFilter = filterStatus.toLowerCase().replace(/\s+/g, '_');
                                            return normalizedStatus === normalizedFilter;
                                        });
                                        if (!hasStatusMatch) shouldShow = false;
                                    }
                                }
                                
                                row.style.display = shouldShow ? '' : 'none';
                            });
                            
                            // Renumber the visible rows sequentially after filtering
                            renumberVisibleRows(activeTable);
                        } else {
                            // Fallback: just show filtered rows
                            filteredRows.forEach(row => {
                                row.style.display = '';
                            });
                            
                            // Renumber the visible rows sequentially
                            renumberVisibleRows(activeTable);
                        }
                    }
                }

                // Make functions globally available
                window.showTab = showTab;
                window.showSubTab = showSubTab;
                window.redirectToForm = redirectToForm;

                // Initialize with first tab active
                showTab('all-seniors');
                updateActiveFiltersDisplay();

                // Store original HTML for all tables
                const allTables = ['all-seniors', 'existing-senior', 'pension', 'id-applicants'];
                allTables.forEach(tableKey => {
                    const tableElement = document.getElementById(tableKey + '-table');
                    if (tableElement) {
                        originalTableHTML.set(tableKey, tableElement.innerHTML);
                    }
                });
                
                // Apply initial filters to show all data
                applyAllFilters();
            });

            // Delete Modal Functions
            function showDeleteModal(id, name, type = 'senior') {
                const modal = document.getElementById('deleteModal');
                const message = document.getElementById('deleteMessage');
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                
                let itemType = 'senior';
                if (type === 'benefits') itemType = 'benefits application';
                else if (type === 'pension') itemType = 'pension application';
                else if (type === 'id') itemType = 'ID application';
                
                message.textContent = `Are you sure you want to delete ${name}'s ${itemType}? This action cannot be undone.`;
                
                // Remove any existing event listeners
                const newConfirmBtn = confirmBtn.cloneNode(true);
                confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
                
                // Add new event listener
                newConfirmBtn.addEventListener('click', function() {
                    if (type === 'senior') {
                        deleteSenior(id);
                    } else if (type === 'benefits') {
                        deleteBenefitsApplication(id);
                    } else if (type === 'pension') {
                        deletePensionApplication(id);
                    } else if (type === 'id') {
                        deleteIdApplication(id);
                    }
                });
                
                modal.style.display = 'flex';
            }

            function hideDeleteModal() {
                document.getElementById('deleteModal').style.display = 'none';
            }

            function deleteSenior(seniorId) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/Delete_senior/${seniorId}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }

            function deleteBenefitsApplication(applicationId) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/delete-benefits-application/${applicationId}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }

            function deletePensionApplication(applicationId) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/delete-pension-application/${applicationId}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }

            function deleteIdApplication(applicationId) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/delete-id-application/${applicationId}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }

            // Close modal when clicking outside
            document.getElementById('deleteModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    hideDeleteModal();
                }
            });
        </script>
        @endif
            
            <!-- Include Popup Messages -->
            @include('message.popup_message')
    </x-header>
</x-sidebar>
