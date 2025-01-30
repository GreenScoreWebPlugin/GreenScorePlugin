document.addEventListener("turbo:load", () => {
    // Code pour afficher les graphiques
    initCharts();
    initCircles();
    initAnimateCounter();

    document.querySelectorAll("[data-endpoint]").forEach(canvas => {
        const endpoint = canvas.getAttribute("data-endpoint");
        if (endpoint) {
            initTop5PollutionChart(canvas.id, endpoint);
        }
    });
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
}

function initTop5PollutionChart(canvasId, endpoint) {
    const canvas = document.getElementById(canvasId);

    if (!canvas) {
        console.error(`Required elements not found`);
        return;
    }
    
    fetch(endpoint)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Configuration des couleurs pastel exactes
            const backgroundColors = [
                'rgba(221, 192, 255, 0.7)', // violet pastel
                'rgba(179, 216, 255, 0.7)', // bleu pastel
                'rgba(255, 205, 184, 0.7)', // pêche pastel
                'rgba(255, 192, 192, 0.7)', // rose pastel
                'rgba(192, 255, 218, 0.7)'  // vert pastel
            ];

            const labels = data.map(site => site[0]);
            const dataValues = data.map(site => site[1]);

            new Chart(canvas.getContext("2d"), {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Emissions (gCO2e)",
                        data: dataValues,
                        backgroundColor: backgroundColors,
                        borderWidth: 0,
                        borderRadius: 6,
                        barThickness: 25
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            right: 80,
                        }
                    },
                    plugins: {
                        legend: { 
                            display: false 
                        },
                        tooltip: { 
                            enabled: false 
                        }
                    },
                    scales: {
                        x: {
                            display: false,
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 14,
                                    family: "system-ui"
                                },
                                color: "#000",
                                padding: 12
                            }
                        }
                    },
                    animation: {
                        duration: 1000
                    }
                },
                plugins: [{
                    id: 'valueLabels',
                    afterDraw: (chart) => {
                        const ctx = chart.ctx;
                        chart.data.datasets[0].data.forEach((value, index) => {
                            const meta = chart.getDatasetMeta(0);
                            const y = meta.data[index].y;
                            const x = chart.width - 80;
                            
                            ctx.fillStyle = '#000';
                            ctx.font = '14px system-ui';
                            ctx.textAlign = 'left';
                            ctx.textBaseline = 'middle';
                            ctx.fillText(
                                `${value}gCO2e`,
                                x,
                                y
                            );
                        });
                    }
                }]
            });

            canvas.innerHTML = '';
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des données :', error);
            const parent = canvas.parentElement;
            if (parent) {
                parent.innerHTML = '<p class="text-center text-gray-500">Erreur de chargement des données</p>';
            }
        });
}

function initCircles() {
    const circles = document.querySelectorAll("circle.text-gradient-purple");
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

function initAnimateCounter(){
    document.querySelectorAll('.animate-counter').forEach(counter => {
    const value = parseFloat(counter.getAttribute('data-value'));
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