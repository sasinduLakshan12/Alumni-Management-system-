admin.js// js/admin.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize admin dashboard functionality
    initAdminDashboard();
    initCharts();
    initModalHandlers();
    initSearchFunctionality();
});

// Initialize Admin Dashboard
function initAdminDashboard() {
    // Update real-time stats
    updateDashboardStats();
    
    // Load recent activity
    loadRecentActivity();
    
    // Setup quick action buttons
    setupQuickActions();
    
    // Check system status
    checkSystemStatus();
}

// Update Dashboard Statistics
function updateDashboardStats() {
    // In a real app, you would fetch this data from the server
    const stats = {
        totalAlumni: parseInt(document.querySelector('.stat-card.approved .stat-number')?.textContent || '0'),
        pending: parseInt(document.querySelector('.stat-card.pending .stat-number')?.textContent || '0'),
        totalEvents: parseInt(document.querySelector('.stat-card.events .stat-number')?.textContent || '0'),
        announcements: parseInt(document.querySelector('.stat-card.announcements .stat-number')?.textContent || '0')
    };
    
    // Animate counters
    animateStatsCounters(stats);
}

function animateStatsCounters(stats) {
    const statElements = {
        'total-alumni': stats.totalAlumni,
        'pending': stats.pending,
        'total-events': stats.totalEvents,
        'announcements': stats.announcements
    };
    
    for (const [className, target] of Object.entries(statElements)) {
        const element = document.querySelector(`.${className} .stat-number`);
        if (element) {
            animateCounter(element, target);
        }
    }
}

// Animate counter
function animateCounter(element, target) {
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current).toLocaleString();
    }, 30);
}

// Load Recent Activity
function loadRecentActivity() {
    // In a real app, you would fetch this from the server
    const activities = [
        {
            icon: 'user-check',
            title: 'New Alumni Approved',
            description: 'John Doe (2021/ICTS/42) approved',
            time: '5 minutes ago'
        },
        {
            icon: 'calendar-plus',
            title: 'Event Created',
            description: 'Annual Alumni Meet 2024',
            time: '1 hour ago'
        },
        {
            icon: 'bullhorn',
            title: 'Announcement Posted',
            description: 'Important university updates',
            time: '2 hours ago'
        },
        {
            icon: 'user-times',
            title: 'Account Deactivated',
            description: 'Inactive alumni account suspended',
            time: '1 day ago'
        }
    ];
    
    const activityList = document.querySelector('.activity-list');
    if (activityList) {
        activities.forEach(activity => {
            const activityItem = createActivityItem(activity);
            activityList.appendChild(activityItem);
        });
    }
}

function createActivityItem(activity) {
    const div = document.createElement('div');
    div.className = 'activity-item';
    div.innerHTML = `
        <div class="activity-icon">
            <i class="fas fa-${activity.icon}"></i>
        </div>
        <div class="activity-details">
            <h4>${activity.title}</h4>
            <p>${activity.description}</p>
            <small class="activity-time">${activity.time}</small>
        </div>
    `;
    return div;
}

// Setup Quick Actions
function setupQuickActions() {
    const actionButtons = document.querySelectorAll('.admin-btn[data-action]');
    actionButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action;
            handleQuickAction(action);
        });
    });
}

function handleQuickAction(action) {
    switch(action) {
        case 'approvals':
            window.location.href = 'approvals.php';
            break;
        case 'manage_alumni':
            window.location.href = 'manage_users.php';
            break;
        case 'add_event':
            window.location.href = 'add_event.php';
            break;
        case 'add_announcement':
            window.location.href = 'add_announcement.php';
            break;
        case 'reports':
            generateReport();
            break;
        case 'settings':
            showSettingsModal();
            break;
    }
}

// Initialize Charts
function initCharts() {
    // In a real app, you would use a charting library like Chart.js
    // For now, we'll just show placeholders
    
    const chartPlaceholders = document.querySelectorAll('.chart-placeholder');
    chartPlaceholders.forEach(placeholder => {
        placeholder.innerHTML = `
            <div style="text-align: center;">
                <i class="fas fa-chart-line" style="font-size: 3rem; color: #3498db; margin-bottom: 15px;"></i>
                <p>Chart would display here</p>
                <small style="color: #95a5a6;">Using Chart.js or similar library</small>
            </div>
        `;
    });
}

// Check System Status
function checkSystemStatus() {
    // In a real app, you would check server status
    const statusItems = document.querySelectorAll('.status-item');
    
    statusItems.forEach(item => {
        const status = Math.random(); // Simulate random status
        
        if (status < 0.7) {
            item.className = 'status-item';
            item.querySelector('.status-indicator').style.background = '#2ecc71';
        } else if (status < 0.9) {
            item.className = 'status-item warning';
            item.querySelector('.status-indicator').style.background = '#f39c12';
        } else {
            item.className = 'status-item offline';
            item.querySelector('.status-indicator').style.background = '#e74c3c';
        }
    });
}

// Modal Handlers
function initModalHandlers() {
    const modals = document.querySelectorAll('.admin-modal');
    const closeButtons = document.querySelectorAll('.close-modal');
    
    // Close modals on button click
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.admin-modal');
            if (modal) {
                closeModal(modal);
            }
        });
    });
    
    // Close modals on background click
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this);
            }
        });
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            modals.forEach(modal => {
                if (modal.style.display === 'flex') {
                    closeModal(modal);
                }
            });
        }
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modal) {
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Search Functionality
function initSearchFunctionality() {
    const searchInput = document.querySelector('.admin-search input');
    const searchButton = document.querySelector('.admin-search button');
    
    if (searchInput && searchButton) {
        searchButton.addEventListener('click', function() {
            performSearch(searchInput.value);
        });
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch(this.value);
            }
        });
    }
}

function performSearch(query) {
    if (query.trim()) {
        // In a real app, you would make an AJAX request
        console.log('Searching for:', query);
        showNotification(`Searching for "${query}"...`, 'info');
    }
}

// Generate Report
function generateReport() {
    showNotification('Generating report...', 'info');
    
    // Simulate report generation
    setTimeout(() => {
        const reportUrl = '#';
        showNotification('Report generated successfully!', 'success');
        
        // In a real app, you would initiate download
        // window.location.href = reportUrl;
    }, 2000);
}

// Show Settings Modal
function showSettingsModal() {
    // In a real app, you would load settings form
    openModal('settingsModal');
}

// Bulk Actions
function handleBulkAction(action) {
    const selectedItems = getSelectedItems();
    
    if (selectedItems.length === 0) {
        showNotification('Please select items first', 'error');
        return;
    }
    
    switch(action) {
        case 'approve':
            approveSelected(selectedItems);
            break;
        case 'reject':
            rejectSelected(selectedItems);
            break;
        case 'delete':
            deleteSelected(selectedItems);
            break;
        case 'export':
            exportSelected(selectedItems);
            break;
    }
}

function getSelectedItems() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function approveSelected(items) {
    if (confirm(`Approve ${items.length} selected item(s)?`)) {
        showNotification(`${items.length} item(s) approved successfully`, 'success');
    }
}

function rejectSelected(items) {
    if (confirm(`Reject ${items.length} selected item(s)?`)) {
        showNotification(`${items.length} item(s) rejected`, 'warning');
    }
}

function deleteSelected(items) {
    if (confirm(`Delete ${items.length} selected item(s)? This action cannot be undone.`)) {
        showNotification(`${items.length} item(s) deleted`, 'success');
    }
}

function exportSelected(items) {
    showNotification(`Exporting ${items.length} item(s)...`, 'info');
}

// Show Notification
function showNotification(message, type = 'info') {
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
`;
document.head.appendChild(style);