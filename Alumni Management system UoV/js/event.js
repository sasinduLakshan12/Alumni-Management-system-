// js/events.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize events functionality
    initEventFilters();
    initViewToggle();
});

// Event Filter Functionality
function initEventFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const eventCards = document.querySelectorAll('.event-card');
    const currentDate = new Date().toISOString().split('T')[0];
    
    if (!filterButtons.length || !eventCards.length) return;
    
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            // Filter event cards
            eventCards.forEach(card => {
                const eventDate = card.dataset.date;
                let shouldShow = true;
                
                switch(filter) {
                    case 'upcoming':
                        shouldShow = eventDate >= currentDate;
                        break;
                    case 'past':
                        shouldShow = eventDate < currentDate;
                        break;
                    default: // 'all'
                        shouldShow = true;
                }
                
                // Apply display style
                card.style.display = shouldShow ? 'block' : 'none';
                
                // Add fade effect
                if (shouldShow) {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 10);
                }
            });
            
            // Check if no events are visible
            setTimeout(checkNoEventsVisible, 300);
        });
    });
}

// Check if no events are visible
function checkNoEventsVisible() {
    const eventCards = document.querySelectorAll('.event-card');
    const noEventsElement = document.querySelector('.no-events');
    const eventsGrid = document.querySelector('.events-grid');
    
    if (!eventCards.length) return;
    
    const visibleEvents = Array.from(eventCards).filter(card => 
        card.style.display !== 'none' && window.getComputedStyle(card).display !== 'none'
    );
    
    // If no events are visible, show a message
    if (visibleEvents.length === 0 && !noEventsElement) {
        const noEventsMsg = document.createElement('div');
        noEventsMsg.className = 'no-events';
        noEventsMsg.innerHTML = `
            <i class="fas fa-calendar-times"></i>
            <h3>No Events Found</h3>
            <p>Try selecting a different filter or check back later for upcoming events.</p>
        `;
        eventsGrid.appendChild(noEventsMsg);
    } else if (visibleEvents.length > 0 && noEventsElement) {
        noEventsElement.remove();
    }
}

// View Toggle Functionality
function initViewToggle() {
    const viewButtons = document.querySelectorAll('.view-btn');
    const calendarView = document.querySelector('.calendar-view');
    const gridView = document.querySelector('.events-grid');
    
    if (!viewButtons.length || !calendarView || !gridView) return;
    
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            viewButtons.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            const view = this.dataset.view;
            
            // Show/hide views
            if (view === 'calendar') {
                calendarView.classList.add('active');
                gridView.style.display = 'none';
            } else {
                calendarView.classList.remove('active');
                gridView.style.display = 'grid';
            }
        });
    });
}

// Register for event function
function registerForEvent(eventId, eventName) {
    if (confirm(`Are you sure you want to register for "${eventName}"?`)) {
        // Here you would typically make an AJAX call to register
        console.log(`Registering for event ${eventId}: ${eventName}`);
        
        // Show success message
        showNotification(`Successfully registered for "${eventName}"!`, 'success');
    }
}

// Share event function
function shareEvent(eventTitle, eventUrl) {
    if (navigator.share) {
        navigator.share({
            title: eventTitle,
            text: `Check out this event: ${eventTitle}`,
            url: eventUrl
        });
    } else {
        // Fallback: Copy to clipboard
        navigator.clipboard.writeText(eventUrl).then(() => {
            showNotification('Event link copied to clipboard!', 'success');
        });
    }
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
        background: ${type === 'success' ? '#2ecc71' : '#3498db'};
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

// Add CSS animations for notifications
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