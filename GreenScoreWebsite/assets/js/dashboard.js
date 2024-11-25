document.addEventListener("DOMContentLoaded", () => {
    // Graphique consommation mensuelle
    const ctx = document.getElementById("co2Chart");
    if (ctx) {
        const dataByMonth = {
            labels: ["Jan", "Fév", "Mars", "Avr", "Mai", "Juin", "Juil", "Août", "Sept", "Oct", "Nov", "Déc"],
            datasets: [{
                label: "Consommation (gCO2e)",
                data: [10000, 20000, 15000, 25000, 12000, 30000, 18000, 12000, 15000, 29000, 20000, 18000],
                backgroundColor: ["#A8E6CF", "#DCEDC1", "#FFD3B6", "#FFAAA5", "#FF8C94", "#85C1E9", "#76D7C4", "#F8C471", "#D7BDE2", "#F1948A", "#85C1E9", "#BB8FCE"]
            }]
        };

        const chart = new Chart(ctx.getContext("2d"), {
            type: "bar",
            data: dataByMonth,
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Gestion du filtre
        document.getElementById("filter")?.addEventListener("change", (event) => {
            const filter = event.target.value;
            let newData;

            if (filter === "mois") {
                newData = dataByMonth;
            } else if (filter === "semaine") {
                newData = {
                    labels: ["Semaine 1", "Semaine 2", "Semaine 3", "Semaine 4"],
                    datasets: [{
                        label: "Consommation (gCO2e)",
                        data: [5000, 10000, 8000, 12000],
                        backgroundColor: ["#A8E6CF", "#DCEDC1", "#FFD3B6", "#FF8C94"]
                    }]
                };
            } else if (filter === "jour") {
                newData = {
                    labels: ["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"],
                    datasets: [{
                        label: "Consommation (gCO2e)",
                        data: [1200, 1800, 1500, 2000, 2200, 1900, 1600],
                        backgroundColor: ["#A8E6CF", "#DCEDC1", "#FFD3B6", "#FFAAA5", "#FF8C94", "#76D7C4", "#85C1E9"]
                    }]
                };
            }

            chart.data = newData;
            chart.update();
        });
    }

    // Graphique pollution
    const pollutionCanvas = document.getElementById("pollutionChart");
    if (pollutionCanvas) {
        const data = {
            labels: ["YouTube", "Facebook", "Netflix", "Instagram", "TikTok"],
            datasets: [{
                label: "Émissions (gCO2e)",
                data: [800, 750, 700, 650, 600],
                backgroundColor: ["#D4A3FF", "#A3D4FF", "#FFC3A3", "#FFA3A3", "#A3FFD4"],
                borderRadius: 10,
                barThickness: 20
            }]
        };

        new Chart(pollutionCanvas.getContext("2d"), {
            type: "bar",
            data: data,
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                },
                scales: {
                    x: { display: false },
                    y: { ticks: { font: { size: 14 }, color: "#333" }, grid: { display: false } }
                }
            }
        });
    }
});
