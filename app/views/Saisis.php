<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisis</title>
</head>
<body>
    <h1>Remplir les champs</h1>


    <form action="">
        
        <select name="Ville" id="">
            <?php foreach($listVille as $ville): ?>
                <option value="<?= $ville['id'] ?>"><?= $ville['nom'] ?></option>
            <?php endforeach; ?>
        </select>    
        <br><br>
        <select name="TypeBesoin" id="">
        
           <?php foreach($listTypeBesoin as $typeBesoin): ?>
                <option value="<?= $typeBesoin['id'] ?>"><?= $typeBesoin['nom'] ?></option>
            <?php endforeach; ?>
        </select>
        input pour les Besoin
        input pour les dons
        
        <button type="submit">Soumettre</button>
    </form>
</body>
</html>