<?php
session_start();
require 'fonction.php';

$bd = 'immobilier';
$bdd = dbconnect($bd);

$apartment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$ids = [];
$sql_ids = "SELECT p.id_propriete FROM proprietes p ORDER BY p.id_propriete ASC";
$result_ids = mysqli_query($bdd, $sql_ids);
if ($result_ids) {
    while ($row = mysqli_fetch_assoc($result_ids)) {
        $ids[] = $row['id_propriete'];
    }
}

$current_index = array_search($apartment_id, $ids);
if ($current_index === false) {
    $current_index = 0;
    $apartment_id = $ids[0] ?? 0;
}

$prev_id = $ids[$current_index - 1] ?? null;
$next_id = $ids[$current_index + 1] ?? null;


if ($apartment_id > 0) {
    $sql = "SELECT p.*, a.nom AS agent_nom, a.prenom AS agent_prenom, a.region AS agent_region, a.id_agent
            FROM proprietes p
            JOIN listings l ON p.id_propriete = l.id_propriete
            JOIN agents a ON l.id_agent = a.id_agent
            WHERE p.id_propriete = $apartment_id";
    $result = mysqli_query($bdd, $sql);
    $apartment = $result ? mysqli_fetch_assoc($result) : null;
} else {

    $sql = "SELECT p.*, a.nom AS agent_nom, a.prenom AS agent_prenom, a.region AS agent_region, a.id_agent
            FROM proprietes p
            JOIN listings l ON p.id_propriete = l.id_propriete
            JOIN agents a ON l.id_agent = a.id_agent
            ORDER BY p.id_propriete ASC LIMIT 1";
    $result = mysqli_query($bdd, $sql);
    $apartment = $result ? mysqli_fetch_assoc($result) : null;
}


$agent_properties = [];
if ($apartment) {
    $agent_id = $apartment['id_agent'];
    $sql_agent_props = "SELECT p.*, l.date_debut, l.date_fin
                        FROM proprietes p
                        JOIN listings l ON p.id_propriete = l.id_propriete
                        WHERE l.id_agent = $agent_id";
    $result_agent_props = mysqli_query($bdd, $sql_agent_props);
    if ($result_agent_props) {
        while ($row = mysqli_fetch_assoc($result_agent_props)) {
            $agent_properties[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Fiche Appartement</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<header class="site-header">
    <div class="container">
        <h1>Fiche Appartement</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="#">Fiche Appartement</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container" style="padding: 20px; max-width: 900px; margin: auto;">

<?php if ($apartment): ?>
    <div style="display: flex; gap: 40px; margin-bottom: 30px;">
        <div class="property-card" style="flex: 1;">
            <img src="image/appartement.png" alt="Appartement" class="property-image" />
            <div class="property-details">
                <h2><?php echo htmlspecialchars($apartment['adresse']); ?></h2>
                <p><strong>Ville:</strong> <?php echo htmlspecialchars($apartment['ville']); ?></p>
                <p><strong>Prix:</strong> <?php echo number_format($apartment['prix'], 2, ',', ' '); ?> €</p>
                <p><strong>Type de maison:</strong> <?php echo htmlspecialchars($apartment['type_maison']); ?></p>
            </div>
        </div>

        <div class="agent-info" style="flex: 1; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
            <h3>Agent concerné</h3>
            <div class="agent-card" style="border: 1px solid #e0e0e0; border-radius: 10px; padding: 15px; display: flex; gap: 15px; align-items: center; background-color: #ffffff; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
                <img src="image/profil.png" alt="Photo de profil" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;" />
                <div class="agent-details" style="font-size: 0.95em;">
                    <p><a href="agent.php?id=<?php echo $apartment['id_agent']; ?>" style="font-weight: 700; color: #007acc; text-decoration: none;"><?php echo htmlspecialchars($apartment['agent_nom'] . ' ' . $apartment['agent_prenom']); ?></a></p>
                    <p>Région: <?php echo htmlspecialchars($apartment['agent_region']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="interior-section" style="max-width: 900px; margin: 20px auto; padding: 10px;">
        <h3>Intérieur de la maison</h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
            <img src="image/4.jpeg" alt="Intérieur 1" style="width: 30%; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);" />
            <img src="image/5.jpeg" alt="Intérieur 2" style="width: 30%; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);" />
            <img src="image/7.jpeg" alt="Intérieur 3" style="width: 30%; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);" />
        </div>
    </div>

    <div class="navigation" style="text-align: center; margin: 30px 0;">
        <?php if ($prev_id !== null): ?>
            <a href="page2.php?id=<?php echo $prev_id; ?>" style="font-size: 2em; margin-right: 20px; text-decoration: none;">&#8592;</a>
        <?php endif; ?>
        <?php if ($next_id !== null): ?>
            <a href="page2.php?id=<?php echo $next_id; ?>" style="font-size: 2em; margin-left: 20px; text-decoration: none;">&#8594;</a>
        <?php endif; ?>
    </div>

<?php else: ?>
    <p>Aucune propriété trouvée.</p>
<?php endif; ?>

</div>

<footer class="site-footer">
    <div class="container">
        <p>&copy; 2024 Immobilier. Tous droits réservés.</p>
    </div>
</footer>

</body>
</html>
