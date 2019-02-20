<?php

require '../inc/init.inc.php';
// --------------------------------------------------------------------------------- //
// --------------------------------------EVENT-------------------------------------- //
// --------------------------------------------------------------------------------- //


if(isset($_GET['action']) AND $_GET['action'] == "suppr"){

    $pdo->query('DELETE FROM `event` WHERE event_id = "' . $_GET['event_id'] . '"');

    $erreur .= "<div class='success'>L'évenement à bien été supprimé !</div>";

}

echo "        <h1>Les évenements de BNK</h1>
        <h2>Suivez les événements Techo organisé par BNK</h2>";
echo $erreur;

echo "<table id='resTable'>
            <thead>
                <tr>
                    <th>Organisateur</th>
                    <th>Nom de l'évenment</th>
                    <th>Partenaire</th>
                    <th>Date</th>
                    <th>Prix</th>";

                    if(isset($_SESSION['membre']) AND ($_SESSION['membre']['statut'] >= 1)){
                        echo "<th>Suppr.</th>";
                    }
                echo "</tr>
            </thead>
            <tbody>";


$recupEvent = $pdo->prepare("SELECT event_id,nom_event,partenaire_event,date_event,prix_event FROM event ORDER BY `event`.`event_id` DESC");
$recupEvent->execute();
$recupEvent->bindColumn('nom_event', $nomEvent, PDO::PARAM_STR);
$recupEvent->bindColumn('partenaire_event', $partEvent, PDO::PARAM_STR);
$recupEvent->bindColumn('date_event', $dateEvent, PDO::PARAM_STR);
$recupEvent->bindColumn('prix_event', $prixEvent, PDO::PARAM_STR);
$recupEvent->bindColumn('event_id', $idEvent);
$u = 0;
while($recupEvent->fetch())
{
    echo "<tr>
                    <td><img src='./asset/img/bnk.png' alt=''></td>
                    <td>{$nomEvent}</td>
                    <td>{$partEvent}</td>
                    <td>{$dateEvent}</td>
                    <td>{$prixEvent}</td>";
    if(isset($_SESSION['membre']) AND ($_SESSION['membre']['statut'] >= 1)){
        echo " <td><a href='' suppr=".$idEvent." class='croix-e'>❌</a></td>";
    }
    echo "</tr>";

    $u++;


    if($u > 2){

        echo "<a href='?page=evenement'><div class='aff-plus-ev'>Cliquez ici pour afficher plus d'évenement</div></a>";

        break;
    }

}


echo "</tbody>
        </table>";