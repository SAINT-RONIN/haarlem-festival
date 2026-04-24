/**
 * Auto-scroll to Change Password section when password validation errors occur.
 * Enhances UX by automatically navigating users to the error location.
 */
document.addEventListener('DOMContentLoaded', function() {
    const passwordSection = document.getElementById('change-password-section');
    if (passwordSection) {
        passwordSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});

