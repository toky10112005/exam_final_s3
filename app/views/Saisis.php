<?php
$ds = DIRECTORY_SEPARATOR;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie - BNGRC</title>
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
        form {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        select, input[type="number"], input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #1e7e34;
        }
        fieldset {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        legend {
            font-weight: bold;
            color: #007bff;
            padding: 0 10px;
        }
    </style>
</head>
<body>
    <h1>Saisie des Besoins et Dons - BNGRC</h1>

    <div class="nav">
        <a href="/">Saisie</a>
        <a href="/dashboard">Tableau de Bord</a>
        <a href="/achats" style="background-color: #17a2b8;">Achats</a>
        <a href="/simulation" style="background-color: #6f42c1;">Simulation</a>
        <a href="/recap" style="background-color: #fd7e14;">Récapitulation</a>

        <a href="/reset" style="background-color: #dc3545;" onclick="return confirm('Voulez-vous vraiment réinitialiser toutes les données ?');">Reset</a>
    </div>

    <?php if(isset($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form action="/Donne" method="get">
        <fieldset>
            <legend>Besoin</legend>
            
            <div class="form-group">
                <label for="Ville">Ville</label>
                <select name="Ville" id="Ville">
                    <?php foreach($listVille as $ville): ?>
                        <option value="<?= $ville['id'] ?>"><?= $ville['nom'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="TypeBesoin">Type de Besoin</label>
                <select name="TypeBesoin" id="TypeBesoin">
                    <?php foreach($listTypeBesoin as $typeBesoin): ?>
                        <option value="<?= $typeBesoin['id'] ?>"><?= $typeBesoin['nom'] ?> (<?= $typeBesoin['categorie'] ?? '' ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="Besoin">Quantité Demandée</label>
                <input type="number" id="Besoin" name="Besoin" placeholder="Entrez la quantité demandée" required min="1">
            </div>
        </fieldset>

        <fieldset>
            <legend>Don</legend>
            
            <div class="form-group">
                <label for="QuantiteDonnee">Quantité Donnée</label>
                <input type="number" id="QuantiteDonnee" name="QuantiteDonnee" placeholder="Entrez la quantité donnée" required min="1">
            </div>

            <div class="form-group">
                <label for="Donateur">Nom du Donateur</label>
                <input type="text" placeholder="Entrer le nom du donateur" id="Donateur" name="Donateur" required>
            </div>
        </fieldset>
        
        <button type="submit">Soumettre</button>
    </form>
    <!-- //Tsy mety ilay include -->
<!-- ?php include_once '../../public/include/Footer.php'; ?> -->