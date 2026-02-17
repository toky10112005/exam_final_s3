<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - BNGRC</title>
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
        .message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
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
        .nav a.dispatch {
            background-color: #28a745;
        }
        .nav a.dispatch:hover {
            background-color: #1e7e34;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .status-satisfait {
            color: #28a745;
            font-weight: bold;
        }
        .status-partiel {
            color: #ffc107;
            font-weight: bold;
        }
        .status-attente {
            color: #dc3545;
            font-weight: bold;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .summary h3 {
            margin-top: 0;
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Tableau de Bord - Suivi des Collectes et Distributions</h1>

    <div class="nav">
        <a href="/">Saisie</a>
        <a href="/dashboard">Tableau de Bord</a>
        <a href="/achats" style="background-color: #17a2b8;">Achats</a>
        <a href="/simulation" style="background-color: #6f42c1;">Simulation</a>
        <a href="/recap" style="background-color: #fd7e14;">Récapitulation</a>

        <a href="/dispatch" class="dispatch">Dispatcher</a>
        <a href="/dispatch-proportionnel" class="dispatch" style="background-color: #e83e8c;">Dispatch Proportionnel</a>
        <a href="/reset" style="background-color: #dc3545;" onclick="return confirm('Voulez-vous vraiment réinitialiser toutes les données ?');">Reset</a>
    </div>

    <?php if(isset($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <h2>Liste des Villes avec Besoins et Dons Attribués</h2>

    <table>
        <thead>
            <tr>
                <th>Région</th>
                <th>Ville</th>
                <th>Type de Besoin</th>
                <th>Catégorie</th>
                <th>Prix Unitaire</th>
                <th>Quantité Demandée</th>
                <th>Quantité Satisfaite</th>
                <th>Reste à Satisfaire</th>
                <th>Valeur Demandée</th>
                <th>Valeur Satisfaite</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($dashboard) && !empty($dashboard)): ?>
                <?php foreach($dashboard as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['region_nom'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['ville_nom'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['type_nom'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['categorie'] ?? '-') ?></td>
                        <td><?= number_format($row['prix_unitaire'] ?? 0, 2) ?> Ar</td>
                        <td><?= $row['total_demande'] ?? 0 ?></td>
                        <td><?= $row['total_satisfait'] ?? 0 ?></td>
                        <td class="<?= ($row['total_reste'] ?? 0) == 0 ? 'status-satisfait' : (($row['total_satisfait'] ?? 0) > 0 ? 'status-partiel' : 'status-attente') ?>">
                            <?= $row['total_reste'] ?? 0 ?>
                        </td>
                        <td><?= number_format($row['valeur_totale_demande'] ?? 0, 2) ?> Ar</td>
                        <td><?= number_format($row['valeur_totale_satisfaite'] ?? 0, 2) ?> Ar</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" style="text-align: center;">Aucune donnée disponible</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

  
<?php include __DIR__ . '/../../public/include/Footer.php'; ?>
</body>
</html>
