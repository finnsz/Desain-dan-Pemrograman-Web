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

document.addEventListener("DOMContentLoaded", function () {
    initTableFilter();
});