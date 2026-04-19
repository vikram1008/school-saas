/**
 * Hindi Auto-fill
 * Automatically translates English input fields to Hindi
 * Usage: add data-hindi-target="#target_field_id" to any English input
 */

const translateCache = {};
const pendingTimers  = {};

async function translateToHindi(text) {
    if (!text || text.trim().length < 2) return '';

    const cacheKey = text.trim().toLowerCase();
    if (translateCache[cacheKey]) return translateCache[cacheKey];

    try {
        const res  = await fetch(`/translate?q=${encodeURIComponent(text)}&to=hi`);
        const data = await res.json();
        if (data.text) {
            translateCache[cacheKey] = data.text;
        }
        return data.text || '';
    } catch (e) {
        return '';
    }
}

function initHindiAutofill() {
    // Find all fields with data-hindi-target attribute
    document.querySelectorAll('[data-hindi-target]').forEach(input => {
        const targetSelector = input.getAttribute('data-hindi-target');
        const targetField    = document.querySelector(targetSelector);

        if (!targetField) return;

        // Add indicator badge next to hindi field
        const badge = document.createElement('span');
        badge.className   = 'badge bg-label-warning ms-1 hindi-auto-badge';
        badge.style.fontSize = '10px';
        badge.textContent = 'Auto';
        badge.style.display = 'none';

        const label = targetField.closest('.col-sm-6, .col-sm-4, .col-12, .col-md-6')
            ?.querySelector('label');
        if (label) label.appendChild(badge);

        input.addEventListener('input', function () {
            const val = this.value.trim();

            // Clear pending timer
            if (pendingTimers[targetSelector]) {
                clearTimeout(pendingTimers[targetSelector]);
            }

            if (!val) {
                // Clear hindi field if english is cleared
                if (targetField.dataset.autoFilled === 'true') {
                    targetField.value = '';
                    badge.style.display = 'none';
                }
                return;
            }

            // Debounce 600ms
            pendingTimers[targetSelector] = setTimeout(async () => {
                // Only autofill if hindi field is empty or was auto-filled
                if (targetField.value && targetField.dataset.autoFilled !== 'true') {
                    return;
                }

                badge.style.display = 'none';
                badge.textContent   = '⟳';

                const hindi = await translateToHindi(val);

                if (hindi) {
                    targetField.value            = hindi;
                    targetField.dataset.autoFilled = 'true';
                    badge.textContent   = 'Auto ✓';
                    badge.style.display = 'inline';

                    // Highlight briefly
                    targetField.style.transition  = 'background 0.3s';
                    targetField.style.background  = '#fff9c4';
                    setTimeout(() => {
                        targetField.style.background = '';
                    }, 800);
                }
            }, 600);
        });

        // Mark as manually edited if user types in hindi field
        targetField.addEventListener('input', function () {
            this.dataset.autoFilled = 'false';
            badge.style.display = 'none';
        });
    });
}

// Init on DOM ready
document.addEventListener('DOMContentLoaded', initHindiAutofill);

// Also expose for stepper pages (re-init after step change)
window.initHindiAutofill = initHindiAutofill;

// Auto re-init for ALL modals across the entire app
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('shown.bs.modal', function () {
            // Re-init autofill for fields inside this modal
            this.querySelectorAll('[data-hindi-target]').forEach(input => {
                const targetSelector = input.getAttribute('data-hindi-target');
                const targetField    = this.querySelector(targetSelector)
                                    ?? document.querySelector(targetSelector);

                if (!targetField) return;

                // Skip if already initialized
                if (input.dataset.hindiInit === 'true') return;
                input.dataset.hindiInit = 'true';

                input.addEventListener('input', function () {
                    const val = this.value.trim();

                    if (pendingTimers[targetSelector]) {
                        clearTimeout(pendingTimers[targetSelector]);
                    }

                    if (!val) {
                        if (targetField.dataset.autoFilled === 'true') {
                            targetField.value = '';
                        }
                        return;
                    }

                    pendingTimers[targetSelector + '_modal'] = setTimeout(async () => {
                        if (targetField.value && targetField.dataset.autoFilled !== 'true') {
                            return;
                        }
                        const hindi = await translateToHindi(val);
                        if (hindi) {
                            targetField.value             = hindi;
                            targetField.dataset.autoFilled = 'true';
                            targetField.style.transition  = 'background 0.3s';
                            targetField.style.background  = '#fff9c4';
                            setTimeout(() => targetField.style.background = '', 800);
                        }
                    }, 600);
                });

                targetField.addEventListener('input', function () {
                    this.dataset.autoFilled = 'false';
                });
            });
        });
    });
});