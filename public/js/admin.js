(function () {
    window.toggleModal = function (id, show) {
        const modal = document.getElementById(id);
        if (!modal) return;

        modal.style.display = show ? "flex" : "none";
    };

    window.openStockModal = function (code, name, stock) {
        const codeInput = document.getElementById("stock-product-code");
        const nameInput = document.getElementById("stock-product-name");
        const stockInput = document.getElementById("stock-current");

        if (codeInput) codeInput.value = code;
        if (nameInput) nameInput.value = name;
        if (stockInput) stockInput.value = stock;

        window.toggleModal("modalTambahStok", true);
    };

    window.openTab = function (event, tabName) {
        document.querySelectorAll(".tab-content").forEach((tab) => tab.classList.remove("active"));
        document.querySelectorAll(".settings-nav .nav-item").forEach((item) => item.classList.remove("active"));

        document.getElementById(tabName)?.classList.add("active");
        event.currentTarget.classList.add("active");
    };

    function setupReportCharts() {
        if (typeof Chart === "undefined") return;

        const lineChart = document.getElementById("lineChart");
        if (lineChart) {
            new Chart(lineChart.getContext("2d"), {
                type: "line",
                data: {
                    labels: ["10 Nov", "11 Nov", "12 Nov", "13 Nov", "14 Nov", "15 Nov"],
                    datasets: [{
                        label: "Pendapatan",
                        data: [1250000, 1180000, 800000, 1100000, 750000, 1280000],
                        borderColor: "#71a32a",
                        backgroundColor: "rgba(113, 163, 42, 0.1)",
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: "#71a32a",
                    }],
                },
                options: { responsive: true, plugins: { legend: { display: false } } },
            });
        }

        const pieChart = document.getElementById("pieChart");
        if (pieChart) {
            new Chart(pieChart.getContext("2d"), {
                type: "doughnut",
                data: {
                    labels: ["Tunai", "QRIS"],
                    datasets: [{ data: [33, 33], backgroundColor: ["#2ecc71", "#f39c12"] }],
                },
            });
        }

        const barChart = document.getElementById("barChart");
        if (barChart) {
            new Chart(barChart.getContext("2d"), {
                type: "bar",
                data: {
                    labels: ["10 Nov", "11 Nov", "12 Nov", "15 Nov"],
                    datasets: [
                        { label: "Pendapatan", data: [1700000, 3000000, 2500000, 2500000], backgroundColor: "#71a32a" },
                        { label: "Pengeluaran", data: [600000, 1200000, 2700000, 2700000], backgroundColor: "#e74c3c" },
                    ],
                },
            });
        }
    }

    function setupPhotoPreview() {
        document.querySelectorAll("[data-photo-input]").forEach((input) => {
            input.addEventListener("change", () => {
                const target = document.querySelector(input.dataset.photoInput || "");
                const file = input.files?.[0];
                if (!target || !file) return;

                target.src = URL.createObjectURL(file);
            });
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        setupReportCharts();
        setupPhotoPreview();
    });
})();
