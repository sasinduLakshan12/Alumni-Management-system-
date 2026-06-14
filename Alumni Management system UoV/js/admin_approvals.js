// js/admin_approvals.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize approvals functionality
    initBulkSelection();
    initFilters();
    initSearch();
    initUserCheckboxes();
});

// Bulk Selection
function initBulkSelection() {
    const selectAllCheckbox = document.getElementById('select-all');
    const selectAllTableCheckbox = document.getElementById('select-all-table');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            userCheckboxes.forEach(cb => {
                cb.checked = isChecked;
            });
            updateSelectedCount();
            
            // Sync with table checkbox
            if (selectAllTableCheckbox) {
                selectAllTableCheckbox.checked = isChecked;
            }
        });
    }
    
    if (selectAllTableCheckbox) {
        selectAllTableCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            userCheckboxes.forEach(cb => {
                cb.checked = isChecked;
            });
            updateSelectedCount();
            
            // Sync with header checkbox
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = isChecked;
            }
        });
    }
}

// Update selected count
function updateSelectedCount() {
    const selectedCount = document.querySelectorAll('.user-checkbox:checked').length;
    const selectedCountElement = document.getElementById('selected-count');
    
    if (selectedCountElement) {
        selectedCountElement.textContent = `${selectedCount} application${selectedCount !== 1 ? 's' : ''} selected`;
    }
}

// User Checkboxes
function initUserCheckboxes() {
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    
    userCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateSelectedCount();
            updateSelectAllCheckboxes();
        });
    });
}

// Update select all checkboxes
function updateSelectAllCheckboxes() {
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const selectAllCheckbox = document.getElementById('select-all');
    const selectAllTableCheckbox = document.getElementById('select-all-table');
    
    const allChecked = userCheckboxes.length > 0 && 
                      Array.from(userCheckboxes).every(cb => cb.checked);
    const someChecked = Array.from(userCheckboxes).some(cb => cb.checked);
    
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = allChecked;
        selectAllCheckbox.indeterminate = someChecked && !allChecked;
    }
    
    if (selectAllTableCheckbox) {
        selectAllTableCheckbox.checked = allChecked;
        selectAllTableCheckbox.indeterminate = someChecked && !allChecked;
    }
}

// Filters
function initFilters() {
    const facultyFilter = document.getElementById('filter-faculty');
    const sortFilter = document.getElementById('sort-by');
    
    if (facultyFilter) {
        facultyFilter.addEventListener('change', function() {
            filterApplications();
        });
    }
    
    if (sortFilter) {
        sortFilter.addEventListener('change', function() {
            sortApplications();
        });
    }
}

// Filter applications
function filterApplications() {
    const faculty = document.getElementById('filter-faculty').value;
    const rows = document.querySelectorAll('.application-row');
    
    rows.forEach(row => {
        const rowFaculty = row.dataset.faculty;
        
        if (faculty === 'all' || rowFaculty === faculty) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update counter
    updateVisibleCount();
}

// Sort applications
function sortApplications() {
    const sortBy = document.getElementById('sort-by').value;
    const rows = Array.from(document.querySelectorAll('.application-row'));
    const tableBody = document.querySelector('.approvals-table tbody');
    
    rows.sort((a, b) => {
        switch(sortBy) {
            case 'oldest':
                return new Date(a.querySelector('td:nth-child(7)').textContent) - 
                       new Date(b.querySelector('td:nth-child(7)').textContent);
            case 'name':
                return a.querySelector('td:nth-child(3)').textContent.localeCompare(
                    b.querySelector('td:nth-child(3)').textContent
                );
            default: // 'newest'
                return new Date(b.querySelector('td:nth-child(7)').textContent) - 
                       new Date(a.querySelector('td:nth-child(7)').textContent);
        }
    });
    
    // Reorder rows in table
    rows.forEach(row => {
        tableBody.appendChild(row);
    });
    
    // Update row numbers
    updateRowNumbers();
}

// Update row numbers
function updateRowNumbers() {
    const rows = document.querySelectorAll('.application-row');
    let counter = 1;
    
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            row.querySelector('td:nth-child(2)').textContent = counter++;
        }
    });
}

// Update visible count
function updateVisibleCount() {
    const visibleRows = document.querySelectorAll('.application-row[style=""]').length;
    const totalRows = document.querySelectorAll('.application-row').length;
    
    // You could display this count somewhere if needed
    console.log(`${visibleRows} of ${totalRows} applications visible`);
}

// Search functionality
function initSearch() {
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Search applications...';
    searchInput.className = 'filter-select';
    searchInput.style.marginLeft = 'auto';
    
    const filterBar = document.querySelector('.filter-bar');
    if (filterBar) {
        filterBar.appendChild(searchInput);
        
        searchInput.addEventListener('input', function() {
            searchApplications(this.value);
        });
    }
}

// Search applications
function searchApplications(query) {
    const searchTerm = query.toLowerCase().trim();
    const rows = document.querySelectorAll('.application-row');
    
    rows.forEach(row => {
        const name = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        const email = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
        const regNo = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
        const faculty = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
        
        const matches = name.includes(searchTerm) || 
                       email.includes(searchTerm) || 
                       regNo.includes(searchTerm) || 
                       faculty.includes(searchTerm);
        
        // Respect existing faculty filter
        const facultyFilter = document.getElementById('filter-faculty');
        const shouldShowByFaculty = facultyFilter.value === 'all' || 
                                   row.dataset.faculty === facultyFilter.value;
        
        if (matches && shouldShowByFaculty) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    updateRowNumbers();
}

// Export functionality
function exportToCSV() {
    const rows = document.querySelectorAll('.application-row:not([style*="none"])');
    const headers = ['#', 'Name', 'Email', 'Registration No', 'Faculty', 'Graduation Year', 'Applied Date'];
    
    let csvContent = headers.join(',') + '\n';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const rowData = [
            cells[1].textContent,
            cells[2].textContent.split('\n')[0].trim(), // Just the name
            cells[3].querySelector('a').textContent,
            cells[4].textContent,
            cells[5].textContent,
            cells[6].textContent,
            cells[7].textContent.split('\n')[0].trim() // Just the date
        ];
        
        // Escape commas and quotes
        const escapedData = rowData.map(cell => 
            `"${cell.replace(/"/g, '""')}"`
        );
        
        csvContent += escapedData.join(',') + '\n';
    });
    
    // Create download link
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    
    a.href = url;
    a.download = `approvals_${new Date().toISOString().slice(0,10)}.csv`;
    a.click();
    
    window.URL.revokeObjectURL(url);
}

// Quick actions
function quickApproveAll() {
    if (confirm('Approve all visible pending applications?')) {
        const visibleRows = document.querySelectorAll('.application-row:not([style*="none"])');
        const ids = Array.from(visibleRows).map(row => 
            row.querySelector('.user-checkbox').value
        );
        
        if (ids.length > 0) {
            // In a real app, you would make an AJAX call
            console.log('Approving:', ids);
            alert(`${ids.length} applications will be approved.`);
        }
    }
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `admin-notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#2ecc71' : type === 'error' ? '#e74c3c' : '#3498db'};
        color: white;
        border-radius: 8px;
        z-index: 1000;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .text-muted {
        color: #7f8c8d;
    }
    
    .text-primary {
        color: #3498db;
        text-decoration: none;
    }
    
    .text-primary:hover {
        text-decoration: underline;
    }
    
    #select-all-table {
        margin: 0;
    }
    
    .admin-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    
    .admin-modal .modal-content {
        background: white;
        padding: 30px;
        border-radius: 15px;
        max-width: 600px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
    }
    
    .admin-modal .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .admin-modal .close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #7f8c8d;
        cursor: pointer;
        transition: color 0.3s;
    }
    
    .admin-modal .close-modal:hover {
        color: #e74c3c;
    }
`;
document.head.appendChild(style);