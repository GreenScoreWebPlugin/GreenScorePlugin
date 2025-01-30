import { Turbo } from "@hotwired/turbo";

document.addEventListener("turbo:load", () => {
    console.log("Turbo Drive a chargé une nouvelle page");
    
    initCircles();
    initAnimateCounter();

    document.querySelectorAll("[data-endpoint]").forEach(canvas => {
        const endpoint = canvas.getAttribute("data-endpoint");
        if (endpoint) {
            initTop5PollutionChart(canvas.id, endpoint);
        }
    });

    document.querySelectorAll("[data-ids]").forEach(canvas => {
        const idsUsers = canvas.getAttribute("data-ids");
        if (idsUsers) {
            initConsuFiltered(idsUsers);
        }
    });
});

function initConsuFiltered(idsUsers) {
    const ctx = document.getElementById("co2Chart");
    if (ctx) {
        if (ctx.chart) {
            ctx.chart.destroy();
        }

        ctx.chart = new Chart(ctx.getContext("2d"), {
            type: "bar",
            data: {
                labels: [],
                datasets: [{
                    label: "Consommation (gCO2e)",
                    data: [],
                    backgroundColor: []
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        async function updateChart(filter) {
            const url = `/api/${filter}-consu?usersIds=${idsUsers}`;
            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
                const data = await response.json();
                if (!data || !data.labels || !data.data) throw new Error("Données manquantes ou mal formatées");

                ctx.chart.data.labels = data.labels;
                ctx.chart.data.datasets[0].data = data.data;
                ctx.chart.data.datasets[0].backgroundColor = data.labels.map(() => getRandomColor());
                ctx.chart.update();
            } catch (error) {
                console.error("Erreur lors de la récupération des données:", error);
            }
        }

        function updateDynamicText(filter) {
            const dynamicText = document.getElementById('dynamic-text');
            const texts = {
                'jour': 'Vos données sur les 7 derniers jours',
                'semaine': 'Vos données sur les 4 dernières semaines',
                'mois': 'Vos données sur les 12 derniers mois'
            };
            dynamicText.textContent = texts[filter];
        }

        document.getElementById("filter")?.addEventListener("change", (event) => {
            const filter = event.target.value;
            updateDynamicText(filter);
            updateChart(filter);
        });

        // Initial load
        if (ctx.chart){
            updateChart('mois');
            updateDynamicText('mois');
        }
        
    }
}

function getRandomColor() {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
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