// ===== 1. Hamburger menu =====
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");
    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
    });
}

// ===== 2. Konfirmasi hapus =====
// Memakai event delegation di document karena baris tabel sekarang
// dirender dinamis via fetch (lihat buku.js/anggota.js) sehingga
// tombol .btn-hapus belum tentu ada saat DOMContentLoaded.
function initHapusConfirm() {
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-hapus");
        if (!btn) return;

        const row = btn.closest("tr");
        const nama = row ? row.querySelector("td")?.textContent : "data ini";
        const yakin = confirm("Yakin ingin menghapus \"" + nama + "\"?");
        if (yakin && row) {
            row.remove();
        }
    });
}

// ===== 3. Filter/pencarian tabel real-time =====
function initTableFilter() {
    const input = document.getElementById("search-input");
    // Fleksibel: mencari table langsung baik dengan atau tanpa .table-responsive
    const table = document.querySelector(".table-responsive table") || document.querySelector("table");
    if (!input || !table) return;

    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr");
        rows.forEach(function (row) {
            const teks = row.textContent.toLowerCase();
            row.style.display = teks.includes(keyword) ? "" : "none";
        });
    });
}

// ===== 4. Validasi form =====
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

        // Validasi Nama atau Judul
        const fieldUtama = form.querySelector("[name='nama'], [name='judul']");
        if (fieldUtama && fieldUtama.value.trim() === "") {
            tampilkanError(fieldUtama, "Field ini wajib diisi.");
            valid = false;
        } else if (fieldUtama) {
            hapusError(fieldUtama);
        }

        // Validasi No Anggota (jika di halaman anggota)
        const noAnggota = form.querySelector("[name='no_anggota']");
        if (noAnggota && noAnggota.value.trim() === "") {
            tampilkanError(noAnggota, "No. Anggota wajib diisi.");
            valid = false;
        } else if (noAnggota) {
            hapusError(noAnggota);
        }

        // Validasi ISBN (hanya angka dan tanda hubung jika diisi)
        const isbn = form.querySelector("[name='isbn']");
        const regexIsbn = /^[0-9-]+$/;
        if (isbn && isbn.value.trim() !== "" && !regexIsbn.test(isbn.value.trim())) {
            tampilkanError(isbn, "ISBN hanya boleh berisi angka dan tanda hubung (-).");
            valid = false;
        } else if (isbn) {
            hapusError(isbn);
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