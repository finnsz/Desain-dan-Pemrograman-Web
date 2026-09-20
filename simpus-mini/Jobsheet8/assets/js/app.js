// ===== 1. Hamburger Menu =====
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");
    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
    });
}

// ===== 2. Konfirmasi Hapus =====
// Menggunakan event delegation di document.
// e.preventDefault() dipanggil jika user membatalkan (klik Cancel/Batal)
// agar tautan href ke hapus.php tidak dieksekusi.
function initHapusConfirm() {
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-hapus") || e.target.closest("a[href*='hapus.php']");
        if (!btn) return;

        const row = btn.closest("tr");
        const nama = row ? row.querySelector("td")?.textContent.trim() : "data ini";
        const yakin = confirm("Yakin ingin menghapus \"" + nama + "\"?");

        if (!yakin) {
            e.preventDefault();
        }
    });
}

// ===== 3. Filter / Pencarian Tabel Real-Time =====
function initTableFilter() {
    const input = document.getElementById("search-input");
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

// ===== 4. Validasi Form Sisi Klien =====
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

// Inisialisasi setelah semua elemen DOM selesai dimuat
document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
});