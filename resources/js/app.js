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
});

document.addEventListener('turbo:render', function () {
    // Update HTML dir attribute after Turbo renders new content
    updateHTMLDirection();
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

// Turbo progress bar configuration (optional - makes loading visible)
Turbo.setProgressBarDelay(100);
