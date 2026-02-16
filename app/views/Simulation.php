<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulation de Dispatch - BNGRC</title>
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
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .message.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .btn-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            padding: 15px 40px;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin: 0 10px;
            transition: all 0.3s;
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .btn-simuler {
            background-color: #17a2b8;
            color: white;
        }
        .btn-simuler:hover:not(:disabled) {
            background-color: #138496;
        }
        .btn-valider {
            background-color: #28a745;
            color: white;
        }
        .btn-valider:hover:not(:disabled) {
            background-color: #1e7e34;
        }
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
            vertical-align: middle;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .hidden {
            display: none;
        }
        .result-section {
            margin-top: 30px;
        }
        .summary-cards {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            min-width: 200px;
        }
        .card.blue { border-top: 4px solid #007bff; }
        .card.green { border-top: 4px solid #28a745; }
        .card.orange { border-top: 4px solid #fd7e14; }
        .card h3 {
            margin: 0 0 10px 0;
            color: #666;
            font-size: 14px;
        }
        .card .value {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }
        .card.blue .value { color: #007bff; }
        .card.green .value { color: #28a745; }
        .card.orange .value { color: #fd7e14; }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #17a2b8;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .data-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .data-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .data-card h3 {
            background: #007bff;
            color: white;
            margin: 0;
            padding: 15px;
        }
        .data-card.dons h3 {
            background: #28a745;
        }
        .data-card table {
            margin: 0;
            box-shadow: none;
        }
        .data-card th {
            background-color: #f8f9fa;
            color: #333;
        }
        @media (max-width: 768px) {
            .data-section {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <h1>Simulation de Dispatch des Dons</h1>

    <div class="nav">
        <a href="/">Saisie</a>
        <a href="/dashboard">Tableau de Bord</a>
        <a href="/achats">Achats</a>
        <a href="/simulation" class="active">Simulation</a>
        <a href="/recap">Récapitulation</a>
    </div>

    <div id="messageContainer"></div>

    <!-- Données disponibles -->
    <div class="data-section">
        <div class="data-card">
            <h3>📋 Besoins Non Satisfaits (<?= count($besoins ?? []) ?>)</h3>
            <div style="max-height: 300px; overflow-y: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Ville</th>
                            <th>Type</th>
                            <th>Demandé</th>
                            <th>Satisfait</th>
                            <th>Reste</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($besoins) && !empty($besoins)): ?>
                            <?php foreach($besoins as $besoin): ?>
                                <tr>
                                    <td><?= htmlspecialchars($besoin['ville_nom']) ?></td>
                                    <td><?= htmlspecialchars($besoin['type_nom']) ?></td>
                                    <td><?= $besoin['quantite_demandee'] ?></td>
                                    <td><?= $besoin['quantite_satisfaite'] ?></td>
                                    <td><strong><?= $besoin['quantite_demandee'] - $besoin['quantite_satisfaite'] ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center;">Aucun besoin en attente</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="data-card dons">
            <h3>🎁 Dons Disponibles (<?= count($dons ?? []) ?>)</h3>
            <div style="max-height: 300px; overflow-y: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Donateur</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Disponible</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($dons) && !empty($dons)): ?>
                            <?php foreach($dons as $don): ?>
                                <tr>
                                    <td><?= htmlspecialchars($don['donateur'] ?? 'Anonyme') ?></td>
                                    <td><?= htmlspecialchars($don['type_nom']) ?></td>
                                    <td><?= $don['quantite_donnee'] ?></td>
                                    <td><strong><?= $don['quantite_disponible'] ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center;">Aucun don disponible</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="btn-container">
        <button id="btnSimuler" class="btn btn-simuler" onclick="simuler()">
            <span id="spinnerSimuler" class="spinner hidden"></span>
            🔍 Simuler le Dispatch
        </button>
        <button id="btnValider" class="btn btn-valider" onclick="valider()" disabled>
            <span id="spinnerValider" class="spinner hidden"></span>
            ✅ Valider le Dispatch
        </button>
    </div>

    <!-- Résultats de simulation -->
    <div id="resultSection" class="result-section hidden">
        <h2>Résultat de la Simulation</h2>
        
        <div class="summary-cards">
            <div class="card blue">
                <h3>Nombre d'Affectations</h3>
                <div class="value" id="totalAffectations">0</div>
            </div>
            <div class="card green">
                <h3>Valeur Totale Affectée</h3>
                <div class="value" id="totalValeur">0 Ar</div>
            </div>
            <div class="card orange">
                <h3>Besoins Satisfaits</h3>
                <div class="value" id="besoinsSatisfaits">0</div>
            </div>
        </div>

        <h3>Détail des Affectations Simulées</h3>
        <table>
            <thead>
                <tr>
                    <th>Donateur</th>
                    <th>Type</th>
                    <th>Ville Destinataire</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Valeur</th>
                </tr>
            </thead>
            <tbody id="simulationTable">
            </tbody>
        </table>
    </div>

    <script>
        function formatNumber(num) {
            return new Intl.NumberFormat('fr-FR').format(num);
        }

        function showMessage(text, type) {
            const container = document.getElementById('messageContainer');
            container.innerHTML = `<div class="message ${type}">${text}</div>`;
            setTimeout(() => {
                container.innerHTML = '';
            }, 5000);
        }

        function simuler() {
            const btn = document.getElementById('btnSimuler');
            const spinner = document.getElementById('spinnerSimuler');
            
            btn.disabled = true;
            spinner.classList.remove('hidden');
            
            fetch('/simulation/simuler', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                spinner.classList.add('hidden');
                btn.disabled = false;
                
                if (data.success) {
                    // Afficher les résultats
                    document.getElementById('resultSection').classList.remove('hidden');
                    document.getElementById('totalAffectations').textContent = data.total_affectations;
                    document.getElementById('totalValeur').textContent = formatNumber(data.total_valeur) + ' Ar';
                    document.getElementById('besoinsSatisfaits').textContent = data.besoins_satisfaits;
                    
                    // Remplir le tableau
                    const tbody = document.getElementById('simulationTable');
                    tbody.innerHTML = '';
                    
                    if (data.simulation.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Aucune affectation possible</td></tr>';
                        document.getElementById('btnValider').disabled = true;
                    } else {
                        data.simulation.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${item.donateur}</td>
                                <td>${item.type_nom}</td>
                                <td>${item.ville_nom}</td>
                                <td>${item.quantite_affectee}</td>
                                <td>${formatNumber(item.prix_unitaire)} Ar</td>
                                <td><strong>${formatNumber(item.valeur_affectee)} Ar</strong></td>
                            `;
                            tbody.appendChild(row);
                        });
                        
                        // Activer le bouton valider
                        document.getElementById('btnValider').disabled = false;
                    }
                    
                    showMessage(`Simulation terminée : ${data.total_affectations} affectation(s) possible(s)`, 'info');
                } else {
                    showMessage('Erreur lors de la simulation', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                spinner.classList.add('hidden');
                btn.disabled = false;
                showMessage('Erreur de connexion au serveur', 'error');
            });
        }

        function valider() {
            if (!confirm('Êtes-vous sûr de vouloir valider ce dispatch ?\nCette action va enregistrer les affectations en base de données.')) {
                return;
            }
            
            const btn = document.getElementById('btnValider');
            const spinner = document.getElementById('spinnerValider');
            
            btn.disabled = true;
            spinner.classList.remove('hidden');
            
            fetch('/simulation/valider', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                spinner.classList.add('hidden');
                
                if (data.success) {
                    showMessage(`${data.message} - ${data.total_affectations} affectation(s) pour une valeur de ${formatNumber(data.total_valeur)} Ar`, 'success');
                    
                    // Recharger la page après 2 secondes
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    btn.disabled = false;
                    showMessage('Erreur lors de la validation', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                spinner.classList.add('hidden');
                btn.disabled = false;
                showMessage('Erreur de connexion au serveur', 'error');
            });
        }
    </script>
</body>
</html>
