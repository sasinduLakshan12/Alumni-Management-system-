// js/announcements.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize announcements functionality
    initAnnouncementFilters();
    initAnnouncementSearch();
    initReadMoreButtons();
});

// Announcement Filter Functionality
function initAnnouncementFilters() {
    const categoryButtons = document.querySelectorAll('.category-btn');
    const announcementCards = document.querySelectorAll('.announcement-card');
    
    if (!categoryButtons.length || !announcementCards.length) return;
    
    categoryButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            categoryButtons.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            const category = this.dataset.category;
            
            // Filter announcement cards
            announcementCards.forEach(card => {
                const cardCategory = card.dataset.category;
                let shouldShow = true;
                
                if (category !== 'all') {
                    shouldShow = cardCategory === category;
                }
                
                // Apply display style with animation
                if (shouldShow) {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 10);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });
            
            // Check if no announcements are visible
            setTimeout(checkNoAnnouncementsVisible, 300);
        });
    });
}

// Announcement Search Functionality
function initAnnouncementSearch() {
    const searchInput = document.querySelector('.announcements-search input');
    const announcementCards = document.querySelectorAll('.announcement-card');
    
    if (!searchInput || !announcementCards.length) return;
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        announcementCards.forEach(card => {
            const title = card.querySelector('.announcement-title').textContent.toLowerCase();
            const content = card.querySelector('.announcement-content').textContent.toLowerCase();
            
            const matches = title.includes(searchTerm) || content.includes(searchTerm);
            
            // Apply search results with animation
            if (matches) {
                card.style.display = 'block';
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 10);
            } else {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.display = 'none';
                }, 300);
            }
        });
        
        // Check if no announcements are visible
        setTimeout(checkNoAnnouncementsVisible, 300);
    });
}

// Check if no announcements are visible
function checkNoAnnouncementsVisible() {
    const announcementCards = document.querySelectorAll('.announcement-card');
    const noAnnouncementsElement = document.querySelector('.no-announcements');
    const announcementsContainer = document.querySelector('.announcements-container');
    
    if (!announcementCards.length) return;
    
    const visibleAnnouncements = Array.from(announcementCards).filter(card => 
        card.style.display !== 'none' && window.getComputedStyle(card).display !== 'none'
    );
    
    // If no announcements are visible, show a message
    if (visibleAnnouncements.length === 0 && !noAnnouncementsElement) {
        const noAnnouncementsMsg = document.createElement('div');
        noAnnouncementsMsg.className = 'no-announcements';
        noAnnouncementsMsg.innerHTML = `
            <i class="fas fa-bullhorn"></i>
            <h3>No Announcements Found</h3>
            <p>Try selecting a different category or adjust your search term.</p>
        `;
        announcementsContainer.appendChild(noAnnouncementsMsg);
    } else if (visibleAnnouncements.length > 0 && noAnnouncementsElement) {
        noAnnouncementsElement.remove();
    }
}

// Read More Functionality
function initReadMoreButtons() {
    const readMoreButtons = document.querySelectorAll('.read-more');
    
    readMoreButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const announcementId = this.dataset.id;
            
            // Here you would typically load full announcement content
            // For now, we'll show an alert
            alert(`Loading full announcement with ID: ${announcementId}`);
            
            // You could implement AJAX loading here:
            // loadFullAnnouncement(announcementId);
        });
    });
}

// Load full announcement (example function)
function loadFullAnnouncement(announcementId) {
    // Example AJAX implementation
    fetch(`/api/announcements/${announcementId}`)
        .then(response => response.json())
        .then(data => {
            // Display full announcement in a modal or new page
            showAnnouncementModal(data);
        })
        .catch(error => {
            console.error('Error loading announcement:', error);
            showNotification('Failed to load announcement. Please try again.', 'error');
        });
}

// Show announcement modal
function showAnnouncementModal(announcement) {
    // Create modal element
    const modal = document.createElement('div');
    modal.className = 'announcement-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>${announcement.title}</h2>
            <div class="modal-meta">
                <span><i class="fas fa-calendar"></i> ${formatDate(announcement.posted_at)}</span>
                <span><i class="fas fa-user"></i> ${announcement.posted_by_name}</span>
            </div>
            <div class="modal-body">
                ${announcement.content}
            </div>
        </div>
    `;
    
    // Add styles
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        animation: fadeIn 0.3s ease;
    `;
    
    document.body.appendChild(modal);
    
    // Close modal functionality
    modal.querySelector('.close-modal').addEventListener('click', () => {
        modal.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => modal.remove(), 300);
    });
    
    // Close on background click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => modal.remove(), 300);
        }
    });
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Show notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#2ecc71' : type === 'error' ? '#e74c3c' : '#3498db'};
        color: white;
        border-radius: 5px;
        z-index: 1000;
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
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    
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
    
    .modal-content {
        background: white;
        padding: 30px;
        border-radius: 15px;
        max-width: 800px;
        max-height: 80vh;
        overflow-y: auto;
        position: relative;
    }
    
    .close-modal {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 2rem;
        cursor: pointer;
        color: #7f8c8d;
        transition: color 0.3s;
    }
    
    .close-modal:hover {
        color: #e74c3c;
    }
    
    .modal-body {
        margin-top: 20px;
        line-height: 1.7;
    }
    
    .modal-meta {
        display: flex;
        gap: 20px;
        color: #7f8c8d;
        margin-top: 10px;
    }
`;
document.head.appendChild(style);