import './bootstrap';

import Alpine from 'alpinejs';
import * as Turbo from '@hotwired/turbo';

window.Alpine = Alpine;

Alpine.start();

// Turbo Event Handlers - Fix for currency converter and RTL/LTR switching
document.addEventListener('turbo:load', function () {
    // Re-initialize any JavaScript that needs to run on every page load
    if (window.initializeCurrencyConverter) {
        window.initializeCurrencyConverter();
    }

    // Update HTML dir attribute based on current locale
    updateHTMLDirection();

    // Close mobile sidebar on navigation
    closeMobileSidebar();

    // Refresh CSRF token to prevent 419 errors
    refreshCSRFToken();
});

document.addEventListener('turbo:render', function () {
    // Update HTML dir attribute after Turbo renders new content
    updateHTMLDirection();

    // Close mobile sidebar on navigation
    closeMobileSidebar();
});

// Close sidebar before visiting new page to prevent stuck overlay
document.addEventListener('turbo:before-visit', function () {
    closeMobileSidebar();
});

// Function to update HTML direction based on locale
function updateHTMLDirection() {
    const htmlElement = document.documentElement;
    const currentLang = htmlElement.getAttribute('lang');

    // Set RTL for Arabic, LTR for English
    if (currentLang === 'ar') {
        htmlElement.setAttribute('dir', 'rtl');
    } else {
        htmlElement.setAttribute('dir', 'ltr');
    }
}

// Function to close mobile sidebar
function closeMobileSidebar() {
    // Reset Alpine.js sidebarOpen state if it exists
    if (window.Alpine) {
        const body = document.body;
        if (body && body._x_dataStack && body._x_dataStack[0]) {
            body._x_dataStack[0].sidebarOpen = false;
        }
    }
}

// Function to refresh CSRF token from meta tag
function refreshCSRFToken() {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) {
        const token = csrfMeta.getAttribute('content');
        // Update all CSRF inputs in forms
        document.querySelectorAll('input[name="_token"]').forEach(function (input) {
            input.value = token;
        });
        // Update axios default headers if used
        if (window.axios) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        }
    }
}

// Turbo progress bar configuration (optional - makes loading visible)
Turbo.setProgressBarDelay(100);

