// js/script.js

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            navLinks.classList.toggle('show');
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.navbar') && navLinks.classList.contains('show')) {
            navLinks.classList.remove('show');
        }
    });
    
    // Close mobile menu when clicking on a link
    const navLinksItems = document.querySelectorAll('.nav-links a');
    navLinksItems.forEach(link => {
        link.addEventListener('click', function() {
            navLinks.classList.remove('show');
        });
    });
});