<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulation - BNGRC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .nav {
            margin-bottom: 20px;
            text-align: center;
        }
        .nav a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 5px;
        }
        .nav a:hover {
            background-color: #0056b3;
        }
        .nav a.active {
            background-color: #6c757d;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .last-update {
            color: #666;
            font-size: 14px;
        }
        .btn-actualiser {
            padding: 12px 30px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-actualiser:hover:not(:disabled) {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .btn-actualiser:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
            vertical-align: middle;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .hidden {
            display: none;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .stat-card.primary {
            border-left: 5px solid #007bff;
        }
        .stat-card.success {
            border-left: 5px solid #28a745;
        }
        .stat-card.warning {
            border-left: 5px solid #ffc107;
        }
        .stat-card.danger {
            border-left: 5px solid #dc3545;
        }
        .stat-card h3 {
            margin: 0 0 15px 0;
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-card.primary .value { color: #007bff; }
        .stat-card.success .value { color: #28a745; }
        .stat-card.warning .value { color: #ffc107; }
        .stat-card.danger .value { color: #dc3545; }
        .stat-card .label {
            color: #999;
            font-size: 13px;
        }
        .progress-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .progress-section h2 {
            margin: 0 0 20px 0;
            color: #333;
        }
        .progress-bar-container {
            background: #e9ecef;
            border-radius: 25px;
            height: 40px;
            overflow: hidden;
            position: relative;
        }
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            border-radius: 25px;
            transition: width 0.8s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .progress-text {
            color: white;
            font-weight: bold;
            font-size: 16px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        .summary-table {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .summary-table h2 {
            margin: 0 0 20px 0;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
        }
        table tr:hover {
            background-color: #f8f9fa;
        }
        .amount {
            font-weight: bold;
            font-size: 18px;
        }
        .amount.positive { color: #28a745; }
        .amount.negative { color: #dc3545; }
        .amount.neutral { color: #007bff; }
        .besoins-detail {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge.satisfait { background: #d4edda; color: #155724; }
        .badge.partiel { background: #fff3cd; color: #856404; }
        .badge.attente { background: #f8d7da; color: #721c24; }
        .update-animation {
            animation: pulse 0.5s ease-in-out;
        }
        @keyframes pulse {
            0% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.02); }
            100% { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>
    <h1>📊 Récapitulation des Besoins</h1>

    <div class="nav">
        <a href="/">Saisie</a>
        <a href="/dashboard">Tableau de Bord</a>
        <a href="/achats">Achats</a>
        <a href="/simulation">Simulation</a>
        <a href="/recap" class="active">Récapitulation</a>
        <a href="/reset" style="background-color: #dc3545;" onclick="return confirm('Voulez-vous vraiment réinitialiser toutes les données ?');">Reset</a>
    </div>

    <div class="header-section">
        <div class="last-update">
            Dernière mise à jour : <strong id="derniereMaj">-</strong>
        </div>
        <button id="btnActualiser" class="btn-actualiser" onclick="actualiser()">
            <span id="spinner" class="spinner hidden"></span>
            🔄 Actualiser
        </button>
    </div>

    <!-- Cartes de statistiques -->
    <div class="stats-grid">
        <div class="stat-card primary" id="cardTotal">
            <h3>📋 Besoins Totaux</h3>
            <div class="value" id="besoinsTotaux">-</div>
            <div class="label">Nombre total de besoins enregistrés</div>
            <div class="besoins-detail" id="besoinsDetail">
                <span class="badge satisfait" id="badgeSatisfaits">0 satisfaits</span>
                <span class="badge partiel" id="badgePartiels">0 partiels</span>
                <span class="badge attente" id="badgeAttente">0 en attente</span>
            </div>
        </div>
        
        <div class="stat-card primary" id="cardMontantTotal">
            <h3>💰 Montant Total des Besoins</h3>
            <div class="value" id="montantTotal">-</div>
            <div class="label">Valeur totale des besoins exprimés</div>
        </div>
        
        <div class="stat-card success" id="cardSatisfait">
            <h3>✅ Montant Satisfait</h3>
            <div class="value" id="montantSatisfait">-</div>
            <div class="label">Valeur des besoins déjà couverts</div>
        </div>
        
        <div class="stat-card danger" id="cardRestant">
            <h3>⚠️ Montant Restant</h3>
            <div class="value" id="montantRestant">-</div>
            <div class="label">Valeur encore nécessaire</div>
        </div>
    </div>

    <!-- Barre de progression -->
    <div class="progress-section">
        <h2>Progression Globale</h2>
        <div class="progress-bar-container">
            <div class="progress-bar" id="progressBar" style="width: 0%;">
                <span class="progress-text" id="progressText">0%</span>
            </div>
        </div>
    </div>

    <!-- Tableau récapitulatif -->
    <div class="summary-table">
        <h2>Résumé Financier</h2>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total des besoins exprimés</td>
                    <td style="text-align: right;"><span class="amount neutral" id="resumeTotal">-</span></td>
                </tr>
                <tr>
                    <td>Montant déjà collecté / satisfait</td>
                    <td style="text-align: right;"><span class="amount positive" id="resumeCollecte">-</span></td>
                </tr>
                <tr>
                    <td>Montant restant à collecter</td>
                    <td style="text-align: right;"><span class="amount negative" id="resumeRestant">-</span></td>
                </tr>
                <tr>
                    <td><strong>Taux de complétion</strong></td>
                    <td style="text-align: right;"><span class="amount neutral" id="resumePourcentage">-</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        function formatMontant(montant) {
            return new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(montant) + ' Ar';
        }

        function actualiser() {
            const btn = document.getElementById('btnActualiser');
            const spinner = document.getElementById('spinner');
            
            btn.disabled = true;
            spinner.classList.remove('hidden');
            
            fetch('/recap/stats', {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                spinner.classList.add('hidden');
                btn.disabled = false;
                
                // Animer les cartes
                document.querySelectorAll('.stat-card').forEach(card => {
                    card.classList.add('update-animation');
                    setTimeout(() => card.classList.remove('update-animation'), 500);
                });
                
                // Mettre à jour les valeurs
                document.getElementById('besoinsTotaux').textContent = data.besoins_totaux;
                document.getElementById('montantTotal').textContent = formatMontant(data.montant_total_besoins);
                document.getElementById('montantSatisfait').textContent = formatMontant(data.montant_satisfait);
                document.getElementById('montantRestant').textContent = formatMontant(data.montant_restant);
                
                // Badges détail
                document.getElementById('badgeSatisfaits').textContent = data.besoins_satisfaits + ' satisfaits';
                document.getElementById('badgePartiels').textContent = data.besoins_partiels + ' partiels';
                document.getElementById('badgeAttente').textContent = data.besoins_attente + ' en attente';
                
                // Barre de progression
                const progressBar = document.getElementById('progressBar');
                const progressText = document.getElementById('progressText');
                progressBar.style.width = data.pourcentage_completion + '%';
                progressText.textContent = data.pourcentage_completion + '%';
                
                // Tableau récapitulatif
                document.getElementById('resumeTotal').textContent = formatMontant(data.montant_total_besoins);
                document.getElementById('resumeCollecte').textContent = formatMontant(data.montant_satisfait);
                document.getElementById('resumeRestant').textContent = formatMontant(data.montant_restant);
                document.getElementById('resumePourcentage').textContent = data.pourcentage_completion + '%';
                
                // Date de mise à jour
                document.getElementById('derniereMaj').textContent = data.derniere_maj;
            })
            .catch(error => {
                console.error('Erreur:', error);
                spinner.classList.add('hidden');
                btn.disabled = false;
                alert('Erreur lors de l\'actualisation des données');
            });
        }

        // Charger les données au démarrage
        document.addEventListener('DOMContentLoaded', actualiser);
    </script>
<?php include __DIR__ . '/../../public/include/Footer.php'; ?>
</body>
</html>
