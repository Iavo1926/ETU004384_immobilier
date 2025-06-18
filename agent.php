<?php
session_start();
require 'fonction.php';

$bd = 'immobilier';
$bdd = dbconnect($bd);

$agent_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($agent_id > 0) {
    $sql = "SELECT * FROM agents WHERE id_agent = $agent_id";
    $result = mysqli_query($bdd, $sql);
    $agent = $result ? mysqli_fetch_assoc($result) : null;

    if ($agent) {

        $count_sql = "SELECT COUNT(l.id_propriete) AS nb_proprietes
                      FROM listings l
                      WHERE l.id_agent = $agent_id";
        $count_result = mysqli_query($bdd, $count_sql);
        $count_row = $count_result ? mysqli_fetch_assoc($count_result) : ['nb_proprietes' => 0];
        $nb_proprietes = intval($count_row['nb_proprietes']);
        $statut = ($nb_proprietes > 3) ? 'Actif' : 'Inactif';

        $prop_sql = "SELECT p.*, 
                            CASE 
                                WHEN t.id_propriete IS NOT NULL THEN 'Vendu'
                                ELSE 'A acheter'
                            END AS statut_vente
                     FROM proprietes p
                     JOIN listings l ON p.id_propriete = l.id_propriete
                     LEFT JOIN transactions t ON p.id_propriete = t.id_propriete
                     WHERE l.id_agent = $agent_id";
        $prop_result = mysqli_query($bdd, $prop_sql);
        $properties = [];
        if ($prop_result) {
            while ($row = mysqli_fetch_assoc($prop_result)) {
                $properties[] = $row;
            }
        }
    }
} else {
    $agent = null;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Détails de l'agent</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        .agent-detail-card {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .agent-detail-card h2 {
            margin-top: 0;
            font-size: 2em;
            color: #007acc;
        }

        .agent-detail-card p {
            font-size: 1.1em;
            margin: 10px 0;
            color: #333;
        }

        .back-link {
            display: block;
            max-width: 600px;
            margin: 20px auto;
            text-align: center;
            font-size: 1em;
        }

        .back-link a {
            color: #007acc;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <header class="site-header">
        <div class="container">
            <h1>Détails de l'agent</h1>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="page2.php">Fiche Appartement</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <?php if ($agent): ?>
            <section class="agent-detail-card" style="display: flex; gap: 20px; align-items: center;">
                <figure>
                    <img src="image/profil.png" alt="Photo de profil" style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%;" />
                    <figcaption>Photo de profil de l'agent</figcaption>
                </figure>
                <div>
                    <h2><?php echo htmlspecialchars($agent['nom'] . ' ' . $agent['prenom']); ?></h2>
                    <p><strong>Région:</strong> <?php echo htmlspecialchars($agent['region']); ?></p>
                    <p><strong>Statut:</strong> <?php echo $statut; ?> (<?php echo $nb_proprietes; ?> propriétés)</p>
                </div>
            </section>

            <?php if (!empty($properties)): ?>
                <section class="properties-list">
                    <h3>Liste des propriétés de l'agent</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Adresse</th>
                                <th>Ville</th>
                                <th>Prix</th>
                                <th>Type de maison</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($properties as $property): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($property['adresse']); ?></td>
                                    <td><?php echo htmlspecialchars($property['ville']); ?></td>
                                    <td><?php echo number_format($property['prix'], 2, ',', ' '); ?> €</td>
                                    <td><?php echo htmlspecialchars($property['type_maison']); ?></td>
                                    <td><?php echo htmlspecialchars($property['statut_vente']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
            <?php else: ?>
                <p style="text-align: center; margin-top: 20px;">Cet agent n'a pas de propriétés listées.</p>
            <?php endif; ?>

        <?php else: ?>
            <p style="text-align: center; margin-top: 40px;">Agent non trouvé.</p>
        <?php endif; ?>
    </main>

</body>

</html>