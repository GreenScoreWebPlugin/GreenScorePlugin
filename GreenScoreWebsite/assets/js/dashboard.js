document.addEventListener("turbo:load", () => {
    // Code pour afficher les graphiques
    initCharts();
    initCircles();
    iniAanimateCounter();
});

function initCharts() {
    const ctx = document.getElementById("co2Chart");
    if (ctx) {
        const dataByMonth = {
            labels: ["Jan", "Fev", "Mars", "Avr", "Mai", "Juin", "Juil", "Aout", "Sept", "Oct", "Nov", "Dec"],
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
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

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

    const pollutionCanvas = document.getElementById("pollutionChart");
    if (pollutionCanvas) {
        fetch('/api/top-sites')
    .then(response => response.json())
    .then(top5Sites => {
        const labels = top5Sites.map(site => site[0]);
        const dataValues = top5Sites.map(site => site[1]);

        const data = {
            labels: labels,
            datasets: [{
                label: "Emissions (gCO2e)",
                data: dataValues,
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
                maintainAspectRatio: false,
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
    })
    .catch(error => console.error('Erreur lors de la récupération des données :', error));

    }
}

function initCircles() {
    const circles = document.querySelectorAll("circle.text-green-600");
    const values = [85, 85, 85]; 

    circles.forEach((circle, index) => {
        const value = values[index];
        const targetStrokeDasharray = value;
        let currentStrokeDasharray = 0;

        const animationDuration = 1000;
        const intervalTime = 10;
        const increment = (targetStrokeDasharray / animationDuration) * intervalTime;

        const interval = setInterval(() => {
            if (currentStrokeDasharray >= targetStrokeDasharray) {
                currentStrokeDasharray = targetStrokeDasharray;
                clearInterval(interval);
            }
            circle.setAttribute("stroke-dasharray", `${currentStrokeDasharray} 100`);
            currentStrokeDasharray += increment;
        }, intervalTime);
    });
}

function iniAanimateCounter(){
    document.querySelectorAll('.animate-counter').forEach(counter => {
    const value = parseInt(counter.getAttribute('data-value'));
    let count = 0;
    const updateCount = () => {
        count += Math.ceil(value / 100);
        if (count > value) count = value;
        counter.textContent = count;
        if (count < value) requestAnimationFrame(updateCount);
    };
    requestAnimationFrame(updateCount);
    });
}