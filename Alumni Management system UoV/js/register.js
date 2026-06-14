// js/register.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize registration functionality
    initPasswordStrengthChecker();
    initFormValidation();
    initFileUpload();
    initTermsAgreement();
});

// Password Strength Checker
function initPasswordStrengthChecker() {
    const passwordInput = document.querySelector('input[name="password"]');
    if (!passwordInput) return;
    
    const strengthBar = document.querySelector('.strength-bar');
    const strengthFill = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        
        // Update strength bar
        strengthBar.className = 'strength-bar ' + strength.class;
        if (strengthFill) {
            strengthFill.style.width = strength.percentage + '%';
        }
        
        // Update strength text
        if (strengthText) {
            strengthText.textContent = strength.text;
            strengthText.style.color = strength.color;
        }
    });
}

function calculatePasswordStrength(password) {
    let strength = 0;
    let tips = "";
    
    // Check password length
    if (password.length >= 8) strength += 25;
    if (password.length >= 12) strength += 25;
    
    // Check for mixed case
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 25;
    
    // Check for numbers
    if (/\d/.test(password)) strength += 15;
    
    // Check for special characters
    if (/[^A-Za-z0-9]/.test(password)) strength += 10;
    
    // Return results
    if (strength >= 80) {
        return {
            class: 'strength-strong',
            percentage: 100,
            text: 'Strong password',
            color: '#2ecc71'
        };
    } else if (strength >= 50) {
        return {
            class: 'strength-medium',
            percentage: 66,
            text: 'Medium strength',
            color: '#f39c12'
        };
    } else {
        return {
            class: 'strength-weak',
            percentage: 33,
            text: 'Weak password',
            color: '#e74c3c'
        };
    }
}

// Form Validation
function initFormValidation() {
    const form = document.querySelector('.registration-form form');
    if (!form) return;
    
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        if (validateForm()) {
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitBtn.disabled = true;
            
            // Submit form after validation
            setTimeout(() => {
                form.submit();
            }, 1000);
        }
    });
    
    // Real-time validation for each input
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            clearFieldError(this);
        });
    });
}

function validateForm() {
    const form = document.querySelector('.registration-form form');
    const inputs = form.querySelectorAll('input[required], select[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    // Check terms agreement
    const termsCheckbox = form.querySelector('input[name="terms"]');
    if (termsCheckbox && !termsCheckbox.checked) {
        showFieldError(termsCheckbox, 'You must agree to the terms and conditions');
        isValid = false;
    }
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    // Clear any existing error
    clearFieldError(field);
    
    // Check required fields
    if (field.hasAttribute('required') && !value) {
        errorMessage = 'This field is required';
        isValid = false;
    }
    
    // Email validation
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            errorMessage = 'Please enter a valid email address';
            isValid = false;
        }
    }
    
    // Password validation
    if (field.name === 'password' && value) {
        if (value.length < 6) {
            errorMessage = 'Password must be at least 6 characters';
            isValid = false;
        }
    }
    
    // Graduation year validation
    if (field.name === 'graduation_year' && value) {
        const year = parseInt(value);
        const currentYear = new Date().getFullYear();
        if (year < 1900 || year > currentYear + 1) {
            errorMessage = `Please enter a valid year (1900-${currentYear + 1})`;
            isValid = false;
        }
    }
    
    // Registration number validation
    if (field.name === 'reg_no' && value) {
        if (!/^[\w\/]+$/.test(value)) {
            errorMessage = 'Please enter a valid registration number';
            isValid = false;
        }
    }
    
    if (!isValid) {
        showFieldError(field, errorMessage);
    }
    
    return isValid;
}

function showFieldError(field, message) {
    field.classList.add('error');
    
    let errorElement = field.nextElementSibling;
    if (!errorElement || !errorElement.classList.contains('error-message')) {
        errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        field.parentNode.insertBefore(errorElement, field.nextSibling);
    }
    
    errorElement.textContent = message;
    errorElement.style.display = 'block';
}

function clearFieldError(field) {
    field.classList.remove('error');
    
    const errorElement = field.nextElementSibling;
    if (errorElement && errorElement.classList.contains('error-message')) {
        errorElement.style.display = 'none';
    }
}

// File Upload
function initFileUpload() {
    const fileInput = document.querySelector('.file-upload input[type="file"]');
    const fileUploadLabel = document.querySelector('.file-upload label');
    
    if (!fileInput || !fileUploadLabel) return;
    
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            const file = this.files[0];
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
            
            // Update label to show selected file
            fileUploadLabel.innerHTML = `
                <i class="fas fa-file-check"></i>
                <span>${fileName}</span>
                <small>${fileSize} MB</small>
            `;
        }
    });
}

// Terms Agreement
function initTermsAgreement() {
    const termsCheckbox = document.querySelector('input[name="terms"]');
    const submitBtn = document.querySelector('button[type="submit"]');
    
    if (!termsCheckbox || !submitBtn) return;
    
    termsCheckbox.addEventListener('change', function() {
        submitBtn.disabled = !this.checked;
        
        if (this.checked) {
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
        } else {
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
        }
    });
}

// Show/Hide Password
function togglePasswordVisibility(inputId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = passwordInput.nextElementSibling;
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        toggleIcon.className = 'fas fa-eye';
    }
}

// Show Notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    const form = document.querySelector('.registration-form');
    form.insertBefore(notification, form.firstChild);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Add CSS animation for slideOut
const style = document.createElement('style');
style.textContent = `
    @keyframes slideOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }
`;
document.head.appendChild(style);