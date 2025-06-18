<?php 

session_start();
require 'fonction.php';

$bd = 'immobilier';
$bdd = dbconnect($bd);

$sql = "SELECT * FROM proprietes";
$result = mysqli_query($bdd,$sql);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des propriétés</title>
    <link rel="stylesheet" href="style.css">
<body>

<header class="site-header">
    <div class="container">
        <h1>Fiche Appartement</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="#">Accueil</a></li>
                <li><a href="page2.php">Fiche Appartement</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="property-list">
    <?php
    if ($result && $result->num_rows > 0) {
    $images = ["image/1.jpeg", "image/2.jpeg", "image/3.jpeg"];
    $i = 0;
    while($row = $result->fetch_assoc()) {
        $photoPath = $images[$i % count($images)];
        $i++;
        echo '<a href="page2.php?id=' . $row["id_propriete"] . '" class="property-card" style="text-decoration:none; color:inherit;">';
        echo '<img src="' . $photoPath . '" alt="Appartement" class="property-image">';
        echo '<div class="property-details">';
        echo '<h3>' . ($row["adresse"]) . '</h3>';
        echo '<p><strong>Ville:</strong> ' . ($row["ville"]) . '</p>';
        echo '<p><strong>Prix:</strong> ' . number_format($row["prix"], 2, ",", " ") . ' €</p>';
        echo '<p><strong>Type de maison:</strong> ' . ($row["type_maison"]) . '</p>';
        echo '</div></a>';
    }
    } 
    else {
        echo '<p>Aucune propriété trouvée.</p>';
    }
    ?>
</div>

<footer class="site-footer">
    <div class="container">
        <p>&copy; 2024 Immobilier. Tous droits réservés.</p>
    </div>
</footer>

</body>
</html>
