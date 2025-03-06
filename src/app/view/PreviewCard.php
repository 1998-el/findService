<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du professionnel</title>
    <link rel="stylesheet" href="../style/preview.css?=1.0.1">
</head>
<body>
<div class="appointment-container">
    <div class="appointment-card">
        <div class="image-container" id="workerImageContainer">
            <!-- Image dynamique -->
        </div>
        
        <div class="appointment-details">
            <div class="form-container">
                <div class="worker-info">
                    <h2 id="workerName">Chargement...</h2>
                    <p class="worker-profession" id="workerProfession"></p>
                    <p class="worker-rate" id="workerRate"></p>
                    <p class="worker-description" id="workerDescription"></p>
                </div>
                
                <div class="date-picker-group">
                    <label for="appointmentDate">Choisissez une date :</label>
                    <input type="date" 
                           id="appointmentDate" 
                           name="appointmentDate"
                           min="<?= date('Y-m-d') ?>" 
                           required
                           class="date-input">
                    <input type="hidden" id="selectedWorkerId" name="worker_id">
                </div>
                
                <button class="cta-button" onclick="handleAppointment()">
                    <span class="button-icon">📅</span>
                    Prendre Rendez-vous
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const workerId = urlParams.get('worker_id');
    
    if(workerId) {
        fetch(`/api/get_worker.php?worker_id=${workerId}`)
            .then(handleResponse)
            .then(updateUI)
            .catch(handleError);
    }
});

async function handleAppointment() {
    const workerId = document.getElementById('selectedWorkerId').value;
    const dateInput = document.getElementById('appointmentDate');
    
    if(!validateInputs(workerId, dateInput.value)) return;

    try {
        const response = await fetch('/api/create_appointment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                worker_id: workerId,
                date: dateInput.value
            })
        });
        
        const result = await handleResponse(response);
        showFeedback(result);
        
    } catch (error) {
        handleError(error);
    }
}

// Fonctions utilitaires
function handleResponse(response) {
    if (!response.ok) throw new Error('Erreur réseau');
    return response.json();
}

function updateUI(worker) {
    if(worker.error) throw new Error(worker.error);
    
    document.getElementById('workerName').textContent = 
        `${worker.first_name} ${worker.last_name}`;
    document.getElementById('workerProfession').textContent = worker.profession;
    document.getElementById('workerRate').textContent = 
        `Taux horaire: ${worker.hourly_rate}€/h`;
    document.getElementById('workerDescription').textContent = worker.description;
    
    const imageUrl = worker.photo_url ? 
        `/uploads/${worker.photo_url}` : 
        '/images/default.jpg';
    document.getElementById('workerImageContainer').style.backgroundImage = 
        `url('${imageUrl}')`;
    
    document.getElementById('selectedWorkerId').value = worker.id;
}

function validateInputs(workerId, date) {
    if (!workerId || !date) {
        alert('Veuillez sélectionner une date valide');
        return false;
    }
    return true;
}

function showFeedback(result) {
    if(result.success) {
        alert(`Rendez-vous confirmé pour le ${dateInput.value}`);
        window.location.href = '/mes-rendez-vous.php';
    } else {
        alert(result.message || 'Erreur lors de la prise de rendez-vous');
    }
}

function handleError(error) {
    console.error('Error:', error);
    alert(error.message || 'Une erreur est survenue');
}
</script>

</body>
</html>