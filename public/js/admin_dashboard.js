document.addEventListener('DOMContentLoaded', function() {
    // On vérifie si les données sont bien arrivées
    if (!window.statsData) {
        console.error("Les données statsData sont introuvables.");
        return;
    }

    const data = window.statsData;

    // Graphique 1 : Trajets
    const ctxTrajets = document.getElementById('chartTrajets');
    if (ctxTrajets) {
        new Chart(ctxTrajets, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{ 
                    label: 'Trajets', 
                    data: data.trajets, 
                    borderColor: '#198754', 
                    tension: 0.3 
                }]
            }
        });
    }

    // Graphique 2 : Crédits
    const ctxCredits = document.getElementById('chartCredits');
    if (ctxCredits) {
        new Chart(ctxCredits, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{ 
                    label: 'Gains (€)', 
                    data: data.credits, 
                    backgroundColor: '#0d6efd' 
                }]
            }
        });
    }
});