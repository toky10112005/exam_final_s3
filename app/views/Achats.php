<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achats - BNGRC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        h1, h2 {
            color: #333;
            text-align: center;
        }
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
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
        .nav a.achats {
            background-color: #17a2b8;
        }
        .nav a.achats:hover {
            background-color: #138496;
        }
        .info-box {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
        }
        .info-card {
            background-color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .info-card h3 {
            margin: 0 0 5px 0;
            color: #666;
            font-size: 14px;
        }
        .info-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .info-card .value.warning {
            color: #ffc107;
        }
        .config-form {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            text-align: center;
        }
        .config-form input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 80px;
        }
        .config-form button {
            padding: 8px 16px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .config-form button:hover {
            background-color: #5a6268;
        }
        .filter-form {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            text-align: center;
        }
        .filter-form select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-width: 200px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        th.achat-header {
            background-color: #17a2b8;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn-acheter {
            padding: 6px 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-acheter:hover {
            background-color: #1e7e34;
        }
        .form-achat {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .form-achat input {
            width: 80px;
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 1200px) {
            .two-columns {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <h1>Gestion des Achats - BNGRC</h1>

    <div class="nav">
        <a href="/">Saisie</a>
        <a href="/dashboard">Tableau de Bord</a>
        <a href="/achats" class="achats">Achats</a>
        <a href="/simulation" style="background-color: #6f42c1;">Simulation</a>
        <a href="/recap" style="background-color: #fd7e14;">Récapitulation</a>
    </div>

    <?php if(isset($message)): ?>
        <div class="message <?= isset($success) && $success ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="info-box">
        <div class="info-card">
            <h3>Solde Argent Disponible</h3>
            <div class="value <?= ($soldeArgent ?? 0) < 100 ? 'warning' : '' ?>">
                <?= number_format($soldeArgent ?? 0, 2) ?> Ar
            </div>
        </div>
        <div class="info-card">
            <h3>Frais d'Achat Actuels</h3>
            <div class="value"><?= $fraisAchat ?? 10 ?>%</div>
        </div>
    </div>

    <div class="config-form">
        <form action="/config-frais" method="GET">
            <label><strong>Modifier les frais d'achat:</strong></label>
            <input type="number" name="frais" value="<?= $fraisAchat ?? 10 ?>" step="0.1" min="0" max="100">
            <span>%</span>
            <button type="submit">Mettre à jour</button>
        </form>
    </div>

    <div class="two-columns">
        <div>
            <h2>Besoins Restants (Nature & Matériaux)</h2>
            <p style="text-align: center; color: #666;">Utilisez l'argent des dons pour acheter ces besoins</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Ville</th>
                        <th>Type</th>
                        <th>Catégorie</th>
                        <th>Prix Unit.</th>
                        <th>Reste</th>
                        <th>Coût Total (+ <?= $fraisAchat ?? 10 ?>%)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($besoinsRestants) && !empty($besoinsRestants)): ?>
                        <?php foreach($besoinsRestants as $besoin): ?>
                            <?php 
                                $coutAvecFrais = $besoin['reste'] * $besoin['prix_unitaire'] * (1 + ($fraisAchat ?? 10) / 100);
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($besoin['ville_nom']) ?></td>
                                <td><?= htmlspecialchars($besoin['type_nom']) ?></td>
                                <td><?= htmlspecialchars($besoin['categorie']) ?></td>
                                <td><?= number_format($besoin['prix_unitaire'], 2) ?> Ar</td>
                                <td><?= $besoin['reste'] ?></td>
                                <td><?= number_format($coutAvecFrais, 2) ?> Ar</td>
                                <td>
                                    <form action="/effectuer-achat" method="GET" class="form-achat">
                                        <input type="hidden" name="besoin_id" value="<?= $besoin['id'] ?>">
                                        <input type="number" name="quantite" value="<?= $besoin['reste'] ?>" min="1" max="<?= $besoin['reste'] ?>">
                                        <button type="submit" class="btn-acheter">Acheter</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Aucun besoin restant à acheter</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div>
            <h2>Liste des Achats Effectués</h2>
            
            <div class="filter-form">
                <form action="/achats" method="GET">
                    <label><strong>Filtrer par ville:</strong></label>
                    <select name="ville_id" onchange="this.form.submit()">
                        <option value="">-- Toutes les villes --</option>
                        <?php if(isset($villes)): ?>
                            <?php foreach($villes as $ville): ?>
                                <option value="<?= $ville['id'] ?>" <?= (isset($ville_id_filtre) && $ville_id_filtre == $ville['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ville['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th class="achat-header">Date</th>
                        <th class="achat-header">Ville</th>
                        <th class="achat-header">Type</th>
                        <th class="achat-header">Qté</th>
                        <th class="achat-header">Prix Unit.</th>
                        <th class="achat-header">Frais</th>
                        <th class="achat-header">Montant Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($achats) && !empty($achats)): ?>
                        <?php $totalAchats = 0; ?>
                        <?php foreach($achats as $achat): ?>
                            <?php $totalAchats += $achat['montant_total']; ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($achat['date_achat'])) ?></td>
                                <td><?= htmlspecialchars($achat['ville_nom']) ?></td>
                                <td><?= htmlspecialchars($achat['type_nom']) ?></td>
                                <td><?= $achat['quantite_achetee'] ?></td>
                                <td><?= number_format($achat['prix_unitaire'], 2) ?> Ar</td>
                                <td><?= $achat['frais_pourcent'] ?>%</td>
                                <td><?= number_format($achat['montant_total'], 2) ?> Ar</td>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="background-color: #e9ecef; font-weight: bold;">
                            <td colspan="6" style="text-align: right;">Total des achats:</td>
                            <td><?= number_format($totalAchats, 2) ?> Ar</td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Aucun achat effectué</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
