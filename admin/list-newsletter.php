<?php
ob_start();
require '../asset/module/header.php';

if($_SESSION['membre'] == '')
{
    header('location:' . URL . '/error/403');
}

//echo '<pre>';
//print_r($_SESSION);
//echo '</pre>';

$recupInfoAdmin = $pdo->prepare("SELECT id_newsletter,nom,prenom,email FROM newsletter");
$recupInfoAdmin->execute();
$recupInfoAdmin->bindColumn('nom', $nom, PDO::PARAM_STR);
$recupInfoAdmin->bindColumn('prenom', $prenom, PDO::PARAM_STR);
$recupInfoAdmin->bindColumn('email', $email, PDO::PARAM_STR);
$recupInfoAdmin->bindColumn('id_newsletter', $idNl, PDO::PARAM_STR);
while($recupInfoAdmin->fetch())
{
    $affichage .= "<tr>
                    <td>{$nom}</td>
                    <td>{$prenom}</td>
                    <td>{$email}</td>
                    <td>";
    if(isset($_SESSION['membre']) AND ($_SESSION['membre']['statut'] == 2)){
        $affichage .= "<a href='?page=admin&action=suppr&id_newsletter={$idNl}'><i class='far fa-trash-alt'></i></a></td>";
    }
    else{
        $affichage .= "<i class='fas fa-minus-circle'></i></td>";
    }

    $affichage .="</tr>";
}

if(isset($_GET['action']) AND $_GET['action'] == "suppr"){
    $pdo->query('DELETE FROM `newsletter` WHERE id_newsletter = "' . $_GET['id_newsletter'] . '"');
    header('location:list-newsletter');
}


?>
<section id="evenement" class="evenement-e">
    <div class="contenaire-event" id="contenaire-event">
        <h1 class="red">Liste des abonnés</h1>
        <?= $erreur; ?>
        <div class="scroll">
            <table id="resTable">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prenom</th>
                        <th>email</th>
                        <?php
                        if(isset($_SESSION['membre']) AND ($_SESSION['membre']['statut'] > 1)){
                            echo '<th>Supprimer</th>';
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?= $affichage ?>
                </tbody>
            </table>
        </div>
        <div class="align-abonne-nl">
            <a href="https://www.bnkcommunity.com/admin/admin_office" class="return-admin-office-nl">Retour au back-office</a>
            <a href="export-excel" class="export-excel-button">Exporter en excel</a>
        </div>
    </div>
</section>

<?php
    require '../asset/module/footer.php';