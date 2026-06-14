// js/alumni.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard functionality
    initDashboardStats();
    initProfileCompleteness();
    initRecentActivity();
    initNotifications();
});

// Initialize Dashboard Stats
function initDashboardStats() {
    // Fetch and update dashboard statistics
    updateNetworkStats();
    updateProfileCompleteness();
}

// Update Network Statistics
function updateNetworkStats() {
    // In a real application, you would fetch this data from the server
    // For now, we'll use mock data
    const stats = {
        totalAlumni: 1350,
        sameFaculty: 450,
        sameYear: 120,
        connections: 45
    };
    
    // Update the stats display
    const statElements = {
        'total-alumni': stats.totalAlumni,
        'same-faculty': stats.sameFaculty,
        'same-year': stats.sameYear,
        'connections': stats.connections
    };
    
    for (const [id, value] of Object.entries(statElements)) {
        const element = document.getElementById(id);
        if (element) {
            animateCounter(element, value);
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

// Profile Completeness
function initProfileCompleteness() {
    const profileFields = [
        'current_job',
        'company',
        'phone',
        'linkedin',
        'profile_pic'
    ];
    
    let completed = 0;
    const total = profileFields.length + 5; // Basic fields + additional fields
    
    // In a real app, you would check which fields are filled
    // For now, we'll simulate 75% completion
    const completionPercentage = 75;
    
    const progressFill = document.querySelector('.progress-fill');
    const progressPercentage = document.querySelector('.progress-percentage');
    
    if (progressFill && progressPercentage) {
        progressFill.style.width = completionPercentage + '%';
        progressPercentage.textContent = completionPercentage + '%';
        
        // Add tips based on completion
        if (completionPercentage < 50) {
            addProfileTip('Complete your basic profile information');
        }
        if (completionPercentage < 75) {
            addProfileTip('Add your current job and company');
        }
        if (completionPercentage < 90) {
            addProfileTip('Upload a profile picture');
        }
        if (completionPercentage < 100) {
            addProfileTip('Add your LinkedIn profile URL');
        }
    }
}

function addProfileTip(tip) {
    const tipsList = document.querySelector('.profile-tips ul');
    if (tipsList) {
        const li = document.createElement('li');
        li.textContent = tip;
        tipsList.appendChild(li);
    }
}

// Recent Activity
function initRecentActivity() {
    // In a real app, you would fetch recent activity from the server
    const activities = [
        {
            icon: 'user-plus',
            title: 'New Connection',
            description: 'Connected with John Doe',
            time: '2 hours ago'
        },
        {
            icon: 'calendar-check',
            title: 'Event Registration',
            description: 'Registered for Annual Alumni Meet',
            time: '1 day ago'
        },
        {
            icon: 'edit',
            title: 'Profile Updated',
            description: 'Updated professional information',
            time: '3 days ago'
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
            <small class="text-muted">${activity.time}</small>
        </div>
    `;
    return div;
}

// Notifications
function initNotifications() {
    // Check for unread notifications
    checkUnreadNotifications();
    
    // Setup notification click handlers
    setupNotificationHandlers();
}

function checkUnreadNotifications() {
    // In a real app, you would check for unread notifications from the server
    const unreadCount = 3; // Mock data
    
    const notificationBadge = document.querySelector('.notification-badge');
    if (notificationBadge && unreadCount > 0) {
        notificationBadge.textContent = unreadCount;
        notificationBadge.style.display = 'flex';
    }
}

function setupNotificationHandlers() {
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        item.addEventListener('click', function() {
            this.classList.remove('unread');
            
            // Update unread count
            const badge = document.querySelector('.notification-badge');
            if (badge) {
                let count = parseInt(badge.textContent) || 0;
                count = Math.max(0, count - 1);
                badge.textContent = count;
                if (count === 0) {
                    badge.style.display = 'none';
                }
            }
        });
    });
}

// Event Registration
function registerForEvent(eventId, eventName) {
    if (confirm(`Register for "${eventName}"?`)) {
        // Here you would make an AJAX call to register
        console.log(`Registering for event ${eventId}`);
        
        showNotification(`Successfully registered for "${eventName}"`, 'success');
    }
}

// Mark Announcement as Read
function markAnnouncementRead(announcementId) {
    // In a real app, you would send this to the server
    console.log(`Marking announcement ${announcementId} as read`);
    
    const announcement = document.querySelector(`[data-id="${announcementId}"]`);
    if (announcement) {
        announcement.classList.remove('unread');
    }
}

// Quick Action Functions
function quickAction(action) {
    switch(action) {
        case 'update_profile':
            window.location.href = 'profile.php';
            break;
        case 'browse_alumni':
            window.location.href = 'directory.php';
            break;
        case 'view_events':
            window.location.href = 'events.php';
            break;
        case 'connect':
            showNotification('Connect feature coming soon!', 'info');
            break;
        case 'messages':
            showNotification('Messages feature coming soon!', 'info');
            break;
    }
}

// Show Notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `dashboard-notification notification-${type}`;
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
    
    .notification-badge {
        display: none;
        position: absolute;
        top: -5px;
        right: -5px;
        background: #e74c3c;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 0.8rem;
        align-items: center;
        justify-content: center;
    }
    
    .notification-item.unread {
        background: #e3f2fd;
        border-left-color: #3498db;
    }
    
    .text-muted {
        color: #7f8c8d;
    }
`;
document.head.appendChild(style);