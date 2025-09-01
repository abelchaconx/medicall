<!-- SweetAlert2 include (CDN) and helper -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.SwalHelper = {
        confirm(options = {}) {
            const defaults = {
                title: 'Are you sure?',
                text: '',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
            };
            return Swal.fire(Object.assign(defaults, options));
        },
        toast(options = {}) {
            const defaults = {
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
            };
            const cfg = Object.assign(defaults, options);
            return Swal.fire(cfg);
        }
    };
</script>
