<?php
require 'asset/module/header.php';

$recupEvent = $pdo->prepare("SELECT event_id,nom_event,partenaire_event,orga_event,date_event,prix_event FROM event ORDER BY `event`.`event_id` DESC");
$recupEvent->execute();
$recupEvent->bindColumn('nom_event', $nomEvent, PDO::PARAM_STR);
$recupEvent->bindColumn('partenaire_event', $partEvent, PDO::PARAM_STR);
$recupEvent->bindColumn('date_event', $dateEvent, PDO::PARAM_STR);
$recupEvent->bindColumn('prix_event', $prixEvent, PDO::PARAM_STR);
$recupEvent->bindColumn('event_id', $idEvent);
while($recupEvent->fetch())
{
    $affichageB .=  "<tr>
                    <td><img src='./asset/img/bnk.png' alt=''></td>
                    <td>{$nomEvent}</td>
                    <td>{$partEvent}</td>
                    <td>{$dateEvent}</td>
                    <td>{$prixEvent}</td>";
    if(isset($_SESSION['membre']) AND ($_SESSION['membre']['statut'] >= 1)){
        $affichageB .=  " <td><a href='' suppr=".$idEvent." class='croix-e'>❌</a></td>";
    }
    $affichageB .= "</tr>";


}

?>
<section id="evenement" class="evenement-e">
    <div class="contenaire-event" id="contenaire-event">
        <h1 class="red">Les évenements de BNK</h1>
        <?= $erreur; ?> 
        <div class="scroll">
        <table id="resTable">
            <thead>
                <tr>
                    <th>Organisateur</th>
                    <th>Nom de l'évenment</th>
                    <th>Partenaire</th>
                    <th>Date</th>
                    <th>Prix</th>

                    <?php
                    if(isset($_SESSION['membre']) AND ($_SESSION['membre']['statut'] >= 1)){
                        echo "<th>Suppr.</th>";
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?= $affichageB; ?>
            </tbody>
        </table>
        </div>
        <?= $affichageE; ?>
    </div>
</section>
<?php
require 'asset/module/footer.php';
