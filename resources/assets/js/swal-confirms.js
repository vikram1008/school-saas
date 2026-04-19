/**
 * Replace all native confirm() delete forms with SweetAlert2
 * Add data-swal-confirm to any form to use this
 */
document.addEventListener('DOMContentLoaded', function () {

    // Handle all delete/confirm forms
    document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
        // Extract the confirm message from onsubmit
        const match = form.getAttribute('onsubmit')?.match(/confirm\(['"](.+?)['"]\)/);
        const message = match ? match[1] : 'Are you sure?';

        // Remove native confirm
        form.removeAttribute('onsubmit');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger me-2 waves-effect waves-light',
                    cancelButton:  'btn btn-outline-secondary waves-effect waves-light',
                },
                buttonsStyling: false,
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // Auto-dismiss success alerts after 4 seconds
    document.querySelectorAll('.alert-success[role!=alert]').forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert?.close();
        }, 4000);
    });

    // Flash success as SweetAlert toast
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        const msg = successAlert.querySelector('.alert-dismissible')?.textContent?.trim()
                 || successAlert.textContent?.trim();

        if (msg && msg.length < 200) {
            Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                customClass: {
                    popup: 'colored-toast',
                },
            }).fire({
                icon: 'success',
                title: msg.replace(/\s+/g, ' ').trim(),
            });
        }
    }
});