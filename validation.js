document.addEventListener('DOMContentLoaded', function() {
    // Login form validation
    const loginForm = document.getElementById('loginForm');
    const loginUsername = document.getElementById('loginUsername');
    const loginPassword = document.getElementById('loginPassword');

    loginUsername.addEventListener('input', function() {
        validateLoginUsername();
    });

    loginPassword.addEventListener('input', function() {
        validateLoginPassword();
    });

    loginForm.addEventListener('submit', function(e) {
        if (!validateLoginUsername() || !validateLoginPassword()) {
            e.preventDefault();
        }
    });

    // Signup form validation
    const signupForm = document.getElementById('signupForm');
    const signupUsername = document.getElementById('signupUsername');
    const signupEmail = document.getElementById('signupEmail');
    const signupPassword = document.getElementById('signupPassword');
    const signupConfirmPassword = document.getElementById('signupConfirmPassword');

    signupUsername.addEventListener('input', function() {
        validateSignupUsername();
    });

    signupEmail.addEventListener('input', function() {
        validateSignupEmail();
    });

    signupPassword.addEventListener('input', function() {
        validateSignupPassword();
    });

    signupConfirmPassword.addEventListener('input', function() {
        validateSignupConfirmPassword();
    });

    signupForm.addEventListener('submit', function(e) {
        if (!validateSignupUsername() || !validateSignupEmail() || !validateSignupPassword() || !validateSignupConfirmPassword()) {
            e.preventDefault();
        }
    });

    // Validation functions
    function validateLoginUsername() {
        const username = loginUsername.value.trim();
        if (username.length < 3) {
            showError(loginUsername, 'Username must be at least 3 characters long');
            return false;
        }
        showSuccess(loginUsername);
        return true;
    }

    function validateLoginPassword() {
        const password = loginPassword.value.trim();
        if (password.length < 6) {
            showError(loginPassword, 'Password must be at least 6 characters long');
            return false;
        }
        showSuccess(loginPassword);
        return true;
    }

    function validateSignupUsername() {
        const username = signupUsername.value.trim();
        if (username.length < 3) {
            showError(signupUsername, 'Username must be at least 3 characters long');
            return false;
        }
        showSuccess(signupUsername);
        return true;
    }

    function validateSignupEmail() {
        const email = signupEmail.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showError(signupEmail, 'Please enter a valid email address');
            return false;
        }
        showSuccess(signupEmail);
        return true;
    }

    function validateSignupPassword() {
        const password = signupPassword.value.trim();
        if (password.length < 6) {
            showError(signupPassword, 'Password must be at least 6 characters long');
            return false;
        }
        showSuccess(signupPassword);
        return true;
    }

    function validateSignupConfirmPassword() {
        const password = signupPassword.value.trim();
        const confirmPassword = signupConfirmPassword.value.trim();
        if (confirmPassword !== password) {
            showError(signupConfirmPassword, 'Passwords do not match');
            return false;
        }
        showSuccess(signupConfirmPassword);
        return true;
    }

    function showError(input, message) {
        const errorElement = input.nextElementSibling;
        errorElement.textContent = message;
        errorElement.className = 'error-message error';
        input.className = 'error';
    }

    function showSuccess(input) {
        const errorElement = input.nextElementSibling;
        errorElement.textContent = '';
        errorElement.className = 'error-message';
        input.className = 'success';
    }
});