<div x-data x-init="
    Livewire.on('showToast', (type, message) => {
        // Try SweetAlert2 if available
        if (window.Swal) {
            window.Swal.fire({toast:true, position:'top-end', showConfirmButton:false, timer:3000, icon: type, title: message});
            return;
        }
        // Fallback: create an element and insert our toast component
        const container = document.createElement('div');
        container.innerHTML = `<div>${message}</div>`;
        document.body.appendChild(container);
        setTimeout(() => container.remove(), 3500);
    });
">
</div>
