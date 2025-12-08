import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener("DOMContentLoaded", () => {

    // DESKTOP
    document.querySelectorAll(".toggle-status").forEach(toggle => {
        toggle.addEventListener("change", function () {
            const id = this.dataset.id;
            updateStatus(id);
        });
    });

    // MOBILE
    document.querySelectorAll(".toggle-status-mobile").forEach(toggle => {
        toggle.addEventListener("change", function () {
            const id = this.dataset.id;
            updateStatus(id);
        });
    });

    function updateStatus(id) {
        fetch(`/admin/design-types/${id}/toggle`, {
            method: "PATCH",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json",
            }
        })
        .then(res => res.json())
        .then(data => console.log("Updated:", data))
        .catch(err => console.error("Error:", err));
    }
});

// Toggle di halaman edit
const toggle = document.getElementById('statusToggle');
        const text = document.getElementById('statusText');

        toggle.addEventListener('change', () => {
            text.textContent = toggle.checked ? 'Aktif' : 'Nonaktif';
        });