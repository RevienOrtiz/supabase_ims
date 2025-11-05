<!-- All Seniors Table -->
<div class="table-container" id="all-seniors-table" style="display: block !important;">
    <table class="records-table">
        <thead>
            <tr>
                <th>NO.</th>
                <th>OSCA ID NO.</th>
                <th class="sortable-header" data-sort="name">
                    FULL NAME
                   
                </th>
                <th class="sortable-header" data-sort="age">
                    AGE
                  
                </th>
                <th>GENDER</th>
                <th class="sortable-header" data-sort="barangay">
                    BARANGAY
                    
                </th>
                <th class="sortable-header" data-sort="status">
                    STATUS
                    
                </th>
                <th>PENSION STATUS</th>
                <th>APP ACCOUNT</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
            @if($seniors && $seniors->count() > 0)
                @foreach($seniors as $index => $senior)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $senior->osca_id }}</td>
                    <td>{{ $senior->first_name }} {{ $senior->last_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($senior->date_of_birth)->age }}</td>
                    <td>{{ $senior->sex }}</td>
                    <td>{{ implode('-', array_map('ucfirst', explode('-', $senior->barangay))) }}</td>
                    <td>
                        <span class="status-badge status-{{ $senior->status }}">
                            {{ ucfirst($senior->status) }}
                        </span>
                    </td>
                    <td>{{ $senior->has_pension ? 'With Pension' : 'Without Pension' }}</td>
                    <td>
                        @if($senior->status === 'deceased')
                            <button class="btn btn-sm btn-secondary" disabled title="Cannot create app account for deceased senior">
                                APP ACCOUNT (DISABLED)
                            </button>
                        @elseif($senior->has_app_account)
                            <a href="{{ route('senior.app_account.edit', $senior->id) }}" class="btn btn-sm btn-success" style="background-color: green;">
                                APP ACCOUNT
                            </a>
                        @else
                            <a href="{{ route('senior.app_account.create', $senior->id) }}" class="btn btn-sm btn-danger" style="background-color: pink;">
                                APP ACCOUNT
                            </a>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('view_senior', ['id' => $senior->id]) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('edit_senior', ['id' => $senior->id]) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button type="button" class="btn btn-tsm btn-danger" onclick="showDeleteModal('{{ $senior->id }}', '{{ $senior->first_name }} {{ $senior->last_name }}')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px;">
                        No seniors found or data not loaded properly.
                        <br>Debug: Seniors count = {{ $seniors ? $seniors->count() : 'null' }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
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
            <button class="modal-btn modal-btn-confirm" id="confirmDeleteBtn">Yes, Proceed</button>
        </div>
    </div>
</div>

<!-- JavaScript for Delete Modal -->
<script>
let deleteItemId = null;

function showDeleteModal(id, name) {
    deleteItemId = id;
    document.getElementById('deleteMessage').textContent = `Are you sure you want to delete "${name}"? This action cannot be undone.`;
    document.getElementById('deleteModal').style.display = 'flex';
}

function hideDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    deleteItemId = null;
}

function deleteSenior() {
    if (deleteItemId) {
        // Create a form to submit the delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/seniors/${deleteItemId}`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add method override for DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Event listeners
document.getElementById('confirmDeleteBtn').addEventListener('click', deleteSenior);

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideDeleteModal();
    }
});
</script>

<!-- Styles are now consolidated in the main seniors.blade.php file -->