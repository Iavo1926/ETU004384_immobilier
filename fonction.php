<?php
function dbconnect($Bd)
{

    if ($bdd = mysqli_connect('localhost', 'root', '', $Bd)) {
        //    echo "Connexion reussie";
    } else {
        die('Erreur');
    }
    return $bdd;

}

?>
