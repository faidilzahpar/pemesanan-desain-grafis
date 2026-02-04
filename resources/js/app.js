import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener("DOMContentLoaded", () => {

    // === LOGIKA TOGGLE STATUS (DESKTOP) ===
    const desktopToggles = document.querySelectorAll(".toggle-status");
    if (desktopToggles.length > 0) {
        desktopToggles.forEach(toggle => {
            toggle.addEventListener("change", function () {
                if (this.dataset.id) updateStatus(this.dataset.id);
            });
        });
    }

    // === LOGIKA TOGGLE STATUS (MOBILE) ===
    const mobileToggles = document.querySelectorAll(".toggle-status-mobile");
    if (mobileToggles.length > 0) {
        mobileToggles.forEach(toggle => {
            toggle.addEventListener("change", function () {
                if (this.dataset.id) updateStatus(this.dataset.id);
            });
        });
    }

    function updateStatus(id) {
        // Cek CSRF Token dulu biar ga error console
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (!tokenMeta) return console.error("CSRF token not found");

        fetch(`/admin/design-types/${id}/toggle`, {
            method: "PATCH",
            headers: {
                "X-CSRF-TOKEN": tokenMeta.content,
                "Accept": "application/json",
            }
        })
        .then(res => res.json())
        .then(data => console.log("Updated:", data))
        .catch(err => console.error("Error:", err));
    }

    // === [FIX] LOGIKA TOGGLE HALAMAN EDIT ===
    // Diberi pengecekan if (toggle && text) agar TIDAK CRASH di halaman lain (seperti Chat)
    const toggle = document.getElementById('statusToggle');
    const text = document.getElementById('statusText');

    if (toggle && text) {
        toggle.addEventListener('change', () => {
            text.textContent = toggle.checked ? 'Aktif' : 'Nonaktif';
        });
    }
});