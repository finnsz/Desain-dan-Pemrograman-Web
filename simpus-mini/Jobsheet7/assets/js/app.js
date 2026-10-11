// ===== Sidebar toggle (mobile) =====
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebar-overlay");
    if (!toggleBtn || !sidebar) return;

    function closeSidebar() {
        sidebar.classList.remove("sidebar-open");
        if (overlay) overlay.classList.remove("overlay-open");
    }

    toggleBtn.addEventListener("click", function () {
        const isOpen = sidebar.classList.toggle("sidebar-open");
        if (overlay) overlay.classList.toggle("overlay-open", isOpen);
    });

    if (overlay) {
        overlay.addEventListener("click", closeSidebar);
    }

    // Tutup sidebar saat link diklik (mobile)
    sidebar.querySelectorAll("nav a").forEach(function (link) {
        link.addEventListener("click", function () {
            if (window.innerWidth <= 768) closeSidebar();
        });
    });
}

// ===== Konfirmasi hapus (front-end only, belum ke server) =====
// Memakai event delegation di document karena baris tabel / card sekarang
// dirender dinamis via fetch (lihat buku.js/anggota.js) sehingga
// tombol .btn-hapus belum tentu ada saat DOMContentLoaded.
function initHapusConfirm() {
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-hapus");
        if (!btn) return;

        const target = btn.closest("tr") || btn.closest(".card-item");
        const nama = target ? target.textContent.trim() : "data ini";
        const yakin = confirm("Yakin ingin menghapus \"" + nama + "\"?");
        if (yakin && target) {
            target.remove();
        }
    });
}

// ===== Filter/pencarian real-time (tabel & card) =====
function initTableFilter() {
    const input = document.getElementById("search-input");
    const rows = document.querySelectorAll(".table-responsive table tbody tr, .card-grid .card-item");
    if (!input || rows.length === 0) return;

    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase();
        rows.forEach(function (item) {
            const teks = item.textContent.toLowerCase();
            item.style.display = teks.includes(keyword) ? "" : "none";
        });
    });
}

// ===== Validasi form (client-side) =====
function tampilkanError(input, pesan) {
    hapusError(input);
    const span = document.createElement("span");
    span.className = "error";
    span.textContent = pesan;
    input.insertAdjacentElement("afterend", span);
}

function hapusError(input) {
    const next = input.nextElementSibling;
    if (next && next.classList.contains("error")) {
        next.remove();
    }
}

function initValidasiForm() {
    const form = document.getElementById("form-tambah");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;

        const judul = form.querySelector("[name='judul'], [name='nama']");
        if (judul && judul.value.trim() === "") {
            tampilkanError(judul, "Field ini wajib diisi.");
            valid = false;
        } else if (judul) {
            hapusError(judul);
        }

        const pengarang = form.querySelector("[name='pengarang']");
        if (pengarang && pengarang.value.trim() === "") {
            tampilkanError(pengarang, "Pengarang wajib diisi.");
            valid = false;
        } else if (pengarang) {
            hapusError(pengarang);
        }

        const tahun = form.querySelector("[name='tahun']");
        if (tahun) {
            const nilai = parseInt(tahun.value, 10);
            if (isNaN(nilai) || nilai < 1900 || nilai > 2026) {
                tampilkanError(tahun, "Tahun harus di antara 1900-2026.");
                valid = false;
            } else {
                hapusError(tahun);
            }
        }

        const stok = form.querySelector("[name='stok']");
        if (stok) {
            const nilai = parseInt(stok.value, 10);
            if (isNaN(nilai) || nilai < 0) {
                tampilkanError(stok, "Stok tidak boleh negatif.");
                valid = false;
            } else {
                hapusError(stok);
            }
        }

        if (!valid) {
            e.preventDefault();
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
});
