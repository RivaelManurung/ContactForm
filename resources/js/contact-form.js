// public/js/contact-form.js

// Ambil site key dari .env yang sudah kita render di Blade
const RECAPTCHA_SITE_KEY = document.querySelector('script[src^="https://www.google.com/recaptcha/api.js"]').src.split('=')[1];

document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const form = document.querySelector('.needs-validation');
    if (!form) return;
    
    // ... (kode spinner, submitButton, alertContainer tetap sama) ...

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        event.stopPropagation();

        // ... (kode check inFlight dan validasi client-side tetap sama) ...

        const formData = new FormData(form);
        const endpoint = form.action; // Ambil endpoint dari atribut action form
        
        // ... (kode disable form & show spinner tetap sama) ...

        try {
            // ... (kode validasi reCAPTCHA key dan grecaptcha tetap sama) ...

            const token = await new Promise((resolve, reject) => {
                grecaptcha.ready(() => {
                    grecaptcha.execute(RECAPTCHA_SITE_KEY, { action: 'submit' }).then(resolve).catch(reject);
                });
            });
            
            // Ganti nama field token agar sesuai dengan validasi Laravel
            formData.append('g-recaptcha-response', token);

            const response = await fetch(endpoint, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    // Header penting untuk keamanan Laravel
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            });

            const result = await response.json();

            // Tangani jika response bukan OK (misal error 422 dari validasi)
            if (!response.ok) {
                // Laravel validation error
                throw new Error(result.message || `⚠️ Network error: ${response.status}`);
            }
            
            // ... (sisa kode untuk menampilkan pesan success/error dan reset form tetap sama) ...
            const success = result.success;
            const message = result.message;

            if (alertContainer) {
                alertContainer.className = `alert alert-${success ? 'success' : 'danger'} fade show`;
                alertContainer.textContent = message;
                alertContainer.classList.remove('d-none');
            }

            if (success) {
                form.reset();
                form.classList.remove('was-validated');
                form.querySelectorAll('.is-valid, .is-invalid').forEach(el => el.classList.remove('is-valid', 'is-invalid'));
            }

        } catch (err) {
            // ... (kode catch error tetap sama) ...
        } finally {
            // ... (kode finally untuk enable form & hide spinner tetap sama) ...
        }
    });
});