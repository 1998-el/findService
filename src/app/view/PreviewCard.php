<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="src/app/style/preview.css?=1.0.1">
</head>
<body>
<div class="appointment-container">
    <div class="btn_close">
            <button>
                close
            </button>
        </div>
    <div class="appointment-card">
    
        <div class="image-container">
            <img src="https://example.com/calendar-illustration.jpg" 
                 alt="Illustration calendrier" 
                 class="calendar-image" />
        </div>
        
        <div class="appointment-container">
        <div class="btn_close">
            <button>close</button>
        </div>
        <div class="appointment-card">
            <div class="image-container">
                <img id="workerPhoto" src="" alt="Photo du worker" class="calendar-image" />
            </div>
            
            <div class="form-container">
                <div class="worker-info">
                    <h2 id="workerName"></h2>
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
                           class="date-input" />
                    <input type="hidden" id="selectedWorkerId" name="worker_id">
                </div>
                
                <button class="cta-button" onclick="handleAppointment()">
                    <span class="button-icon">📅</span>
                    Prendre Rendez-vous
                </button>
            </div>
        </div>
    </div>
</body>
</html>