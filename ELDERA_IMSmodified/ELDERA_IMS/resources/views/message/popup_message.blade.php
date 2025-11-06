<!-- ✅ Success Popup -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content success-card text-center p-4" id="successCard">
      <div class="mb-4">
        <div class="success-icon-circle d-inline-flex align-items-center justify-content-center" id="successIcon">
          <i class="fas fa-check text-white"></i>
        </div>
      </div>
      <h4 class="success-message-bold mb-3" id="successTitle">SUCCESS!</h4>
      <p class="success-description mb-4" id="successMessage">Action completed successfully.</p>
      <button type="button" class="continue-button w-100" data-bs-dismiss="modal" id="continueBtn">CONTINUE</button>
    </div>
  </div>
</div>

<!-- ❌ Error Popup -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="mb-3">
        <div class="rounded-circle bg-danger d-inline-flex align-items-center justify-content-center" style="width:60px; height:60px;">
          <i class="bi bi-x-lg text-white fs-2"></i>
        </div>
      </div>
      <h4 class="fw-bold text-danger">Error!</h4>
      <p id="errorMessage">Something went wrong.</p>
      <button type="button" class="btn btn-danger w-100 mt-2" data-bs-dismiss="modal">Try Again</button>
    </div>
  </div>
</div>

<!-- ⚠️ Confirmation Popup -->
<div id="confirmModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h2 class="modal-title" id="confirmTitle">Are you sure you want to logout?</h2>
        <p class="modal-message" id="confirmMessage">You will be redirected to the login page.</p>
        <div class="modal-actions">
            <button class="modal-btn modal-btn-cancel" onclick="hideConfirmModal()">Cancel</button>
            <button class="modal-btn modal-btn-confirm" id="confirmActionBtn" onclick="confirmAction()">OK</button>
        </div>
    </div>
</div>

<!-- ⚠️ Validation Error Popup -->
<div id="validationErrorModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-icon" style="background: #dc3545;">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <h2 class="modal-title" id="validationErrorTitle" style="color: #dc3545;">Validation Error</h2>
        <p class="modal-message" id="validationErrorMessage">Please fix the following errors:</p>
        <div class="modal-actions">
            <button class="modal-btn modal-btn-confirm" onclick="hideValidationErrorModal()" style="background: #dc3545;">OK</button>
        </div>
    </div>
</div>

<style>
    /* Success Card Styles */
    .success-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        background: white;
        max-width: 400px;
        margin: 0 auto;
    }

    .success-icon-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        font-size: 2rem;
        margin: 0 auto;
    }

    .success-message-bold {
        font-size: 1.5rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0;
    }

    .success-description {
        font-size: 1rem;
        color: #666;
        margin: 0;
        line-height: 1.4;
    }

    .continue-button {
        border: none;
        border-radius: 25px;
        padding: 12px 24px;
        font-weight: 700;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .continue-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    /* Green Theme - Successfully Added */
    .success-added {
        border-top: 5px solid #28a745;
    }

    .success-added-icon {
        background: linear-gradient(135deg, #28a745, #20c997);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }

    .success-added-text {
        color: #28a745;
    }

    .success-added-btn {
        background: #28a745;
        color: white;
        border: 2px solid #28a745;
    }

    .success-added-btn:hover {
        background: #218838;
        border-color: #218838;
        color: white;
    }

    /* Red Theme - Successfully Deleted */
    .success-deleted {
        border-top: 5px solid #dc3545;
    }

    .success-deleted-icon {
        background: linear-gradient(135deg, #dc3545, #e74c3c);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
    }

    .success-deleted-text {
        color: #dc3545;
    }

    .success-deleted-btn {
        background: #dc3545;
        color: white;
        border: 2px solid #dc3545;
    }

    .success-deleted-btn:hover {
        background: #c82333;
        border-color: #c82333;
        color: white;
    }

    /* Blue Theme - Successfully Updated */
    .success-updated {
        border-top: 5px solid #007bff;
    }

    .success-updated-icon {
        background: linear-gradient(135deg, #007bff, #0056b3);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
    }

    .success-updated-text {
        color: #007bff;
    }

    .success-updated-btn {
        background: #007bff;
        color: white;
        border: 2px solid #007bff;
    }

    .success-updated-btn:hover {
        background: #0056b3;
        border-color: #0056b3;
        color: white;
    }

    /* Animation Effects */
    .success-card {
        animation: slideInUp 0.5s ease-out;
    }

    .success-icon-circle {
        animation: bounceIn 0.6s ease-out 0.2s both;
    }

    .success-message-bold {
        animation: fadeInUp 0.5s ease-out 0.4s both;
    }

    .success-description {
        animation: fadeInUp 0.5s ease-out 0.5s both;
    }

    .continue-button {
        animation: fadeInUp 0.5s ease-out 0.6s both;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3);
        }
        50% {
            opacity: 1;
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (max-width: 576px) {
        .success-card {
            margin: 20px;
            max-width: none;
        }
        
        .success-icon-circle {
            width: 70px;
            height: 70px;
            font-size: 1.8rem;
        }
        
        .success-message-bold {
            font-size: 1.3rem;
        }
    }

    /* Confirmation Modal Styles */
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
        animation: modalSlideIn 0.3s ease-out;
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
        transform: translateY(-1px);
    }

    .modal-btn-confirm {
        background: #e31575;
        color: white;
    }

    .modal-btn-confirm:hover {
        background: #c01060;
        transform: translateY(-1px);
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-30px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Responsive design for modal */
    @media (max-width: 576px) {
        .modal-container {
            margin: 20px;
            padding: 20px;
        }
        
        .modal-icon {
            width: 60px;
            height: 60px;
            font-size: 28px;
        }
        
        .modal-title {
            font-size: 20px;
        }
        
        .modal-actions {
            flex-direction: column;
            gap: 10px;
        }
        
        .modal-btn {
            width: 100%;
        }
    }
</style>

@php
    // Pull and clear flash messages to ensure they don't persist across reloads
    $successMessage = session()->pull('success');
    $errorMessage = session()->pull('error');
@endphp

<script>
    // ✅ Show Success Modal if there is a success message
    @if(!empty($successMessage))
        document.addEventListener('DOMContentLoaded', function() {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'), {
                backdrop: 'static',
                keyboard: false
            });
            var successMessage = {!! json_encode($successMessage) !!};
            var successCard = document.getElementById('successCard');
            var successIcon = document.getElementById('successIcon');
            var successTitle = document.getElementById('successTitle');
            var continueBtn = document.getElementById('continueBtn');
            
            // Reset classes to prevent conflicts
            successCard.className = 'modal-content success-card text-center p-4';
            successIcon.className = 'success-icon-circle d-inline-flex align-items-center justify-content-center';
            successTitle.className = 'success-message-bold mb-3';
            continueBtn.className = 'continue-button w-100';
            
            // Detect message type and apply theme
            if (successMessage.toLowerCase().includes('added') || successMessage.toLowerCase().includes('created')) {
                // Green theme for "Added/Created"
                successCard.classList.add('success-added');
                successIcon.classList.add('success-added-icon');
                successTitle.classList.add('success-added-text');
                continueBtn.classList.add('success-added-btn');
                successTitle.innerText = 'SUCCESSFULLY ADDED!';
            } else if (successMessage.toLowerCase().includes('deleted') || successMessage.toLowerCase().includes('removed')) {
                // Red theme for "Deleted"
                successCard.classList.add('success-deleted');
                successIcon.classList.add('success-deleted-icon');
                successTitle.classList.add('success-deleted-text');
                continueBtn.classList.add('success-deleted-btn');
                successTitle.innerText = 'SUCCESSFULLY DELETED!';
            } else if (successMessage.toLowerCase().includes('updated') || successMessage.toLowerCase().includes('edited') || successMessage.toLowerCase().includes('modified')) {
                // Blue theme for "Updated/Edited"
                successCard.classList.add('success-updated');
                successIcon.classList.add('success-updated-icon');
                successTitle.classList.add('success-updated-text');
                continueBtn.classList.add('success-updated-btn');
                successTitle.innerText = 'SUCCESSFULLY UPDATED!';
            } else {
                // Default green theme
                successCard.classList.add('success-added');
                successIcon.classList.add('success-added-icon');
                successTitle.classList.add('success-added-text');
                continueBtn.classList.add('success-added-btn');
                successTitle.innerText = 'SUCCESS!';
            }
            
            document.getElementById('successMessage').innerText = successMessage;
            successModal.show();
            
            // Ensure proper modal dismissal
            continueBtn.addEventListener('click', function() {
                successModal.hide();
                // Force remove backdrop if it persists
                setTimeout(function() {
                    var backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }, 300);
            });
            
            // Auto-hide modal after 5 seconds as fallback
            setTimeout(function() {
                if (successModal._isShown) {
                    successModal.hide();
                    setTimeout(function() {
                        var backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) {
                            backdrop.remove();
                        }
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    }, 300);
                }
            }, 5000);
        });
    @endif

    // ❌ Show Error Modal if there is an error message
    @if(!empty($errorMessage))
        document.addEventListener('DOMContentLoaded', function() {
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            document.getElementById('errorMessage').innerText = {!! json_encode($errorMessage) !!};
            errorModal.show();
        });
    @endif

    // ⚠️ Show Confirmation Modal for Delete Operations
    function confirmDelete(seniorId, seniorName) {
        document.getElementById('confirmTitle').innerText = 'Delete Senior Citizen';
        document.getElementById('confirmMessage').innerText = `Are you sure you want to delete ${seniorName}? This action cannot be undone.`;

        let confirmForm = document.getElementById('confirmForm');
        confirmForm.action = `/Delete_senior/${seniorId}`;

        // Ensure DELETE method is set
        let methodInput = confirmForm.querySelector("input[name='_method']");
        if(methodInput) methodInput.value = 'DELETE';
        
        var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        confirmModal.show();
    }

    // ⚠️ Show Confirmation Modal for Edit Operations
    function confirmEdit(seniorId, seniorName) {
        document.getElementById('confirmTitle').innerText = 'Edit Senior Citizen';
        document.getElementById('confirmMessage').innerText = `Are you sure you want to edit ${seniorName}'s information?`;

        let confirmForm = document.getElementById('confirmForm');
        confirmForm.action = `/Edit_senior/${seniorId}`;
        confirmForm.method = 'GET'; // Edit redirects to edit form

        // Remove method input for GET request
        let methodInput = confirmForm.querySelector("input[name='_method']");
        if(methodInput) methodInput.remove();
        
        var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        confirmModal.show();
    }

    // Generic confirmation function for other operations
    function showConfirmModal(title, message, actionUrl, method = 'POST') {
        console.log('showConfirmModal called with:', { title, message, actionUrl, method });
        
        const titleElement = document.getElementById('confirmTitle');
        const messageElement = document.getElementById('confirmMessage');
        
        console.log('Title element:', titleElement);
        console.log('Message element:', messageElement);
        
        if (titleElement) titleElement.innerText = title;
        if (messageElement) messageElement.innerText = message;

        // Store the action URL and method for later use
        window.confirmActionUrl = actionUrl;
        window.confirmMethod = method;
        
        console.log('Stored confirmActionUrl:', window.confirmActionUrl);
        console.log('Stored confirmMethod:', window.confirmMethod);
        
        const modalElement = document.getElementById('confirmModal');
        console.log('Modal element:', modalElement);
        
        if (modalElement) {
            modalElement.style.display = 'flex';
            console.log('Modal displayed');
        } else {
            console.error('confirmModal element not found');
        }
    }

    // Function to hide the confirmation modal
    function hideConfirmModal() {
        const modalElement = document.getElementById('confirmModal');
        if (modalElement) {
            modalElement.style.display = 'none';
        }
    }

    // Function to handle the confirmation action
    function confirmAction() {
        if (window.confirmActionUrl) {
            if (window.confirmMethod === 'GET') {
                // For GET requests (like logout), redirect directly
                window.location.href = window.confirmActionUrl;
            } else {
                // For POST/DELETE requests, find and submit the main form
                let mainForm = document.getElementById('editSeniorForm') || 
                              document.getElementById('editBenefitsForm') ||
                              document.getElementById('editPensionForm') ||
                              document.getElementById('editSeniorIdForm') ||
                              document.getElementById('pensionForm') ||
                              document.getElementById('benefitsForm') ||
                              document.getElementById('seniorIdForm');
                
                if (mainForm) {
                    // Set the action and method
                    mainForm.action = window.confirmActionUrl;
                    const methodInput = mainForm.querySelector("input[name='_method']");
                    if(methodInput) methodInput.value = window.confirmMethod;
                    
                    // Submit the form with all the data
                    mainForm.submit();
                } else {
                    // If no form found, create a simple form for the action
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = window.confirmActionUrl;
                    
                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    form.appendChild(csrfToken);
                    
                    // Add method override if needed
                    if (window.confirmMethod !== 'POST') {
                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = window.confirmMethod;
                        form.appendChild(methodField);
                    }
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }
        hideConfirmModal();
    }

    // Close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('confirmModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    hideConfirmModal();
                }
            });
        }
    });
    
    // Function to handle the actual form submission (legacy support)
    function submitForm() {
        confirmAction();
    }

    // Function to show validation error modal
    function showValidationErrorModal(title, message) {
        const titleElement = document.getElementById('validationErrorTitle');
        const messageElement = document.getElementById('validationErrorMessage');
        
        if (titleElement) titleElement.innerText = title;
        if (messageElement) messageElement.innerText = message;
        
        const modalElement = document.getElementById('validationErrorModal');
        if (modalElement) {
            modalElement.style.display = 'flex';
        }
    }

    // Function to hide validation error modal
    function hideValidationErrorModal() {
        const modalElement = document.getElementById('validationErrorModal');
        if (modalElement) {
            modalElement.style.display = 'none';
        }
    }
</script>
