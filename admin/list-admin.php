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

$recupInfoAdmin = $pdo->prepare("SELECT id_membre,pseudo,nom,prenom,email,statut FROM membre");
$recupInfoAdmin->execute();
$recupInfoAdmin->bindColumn('pseudo', $pseudo, PDO::PARAM_STR);
$recupInfoAdmin->bindColumn('nom', $nom, PDO::PARAM_STR);
$recupInfoAdmin->bindColumn('prenom', $prenom, PDO::PARAM_STR);
$recupInfoAdmin->bindColumn('email', $email, PDO::PARAM_STR);
$recupInfoAdmin->bindColumn('statut', $statut, PDO::PARAM_STR);
$recupInfoAdmin->bindColumn('id_membre', $idAdmin, PDO::PARAM_STR);
while($recupInfoAdmin->fetch())
{
    $affichage .= "<tr>
                    <td>{$pseudo}</td>
                    <td>{$nom}</td>
                    <td>{$prenom}</td>
                    <td>{$email}</td>
                    <td>{$statut}</td>
                    <td>";
    if(isset($statut) AND !($statut == 2)){
        $affichage .= "<a href='?page=admin&action=suppr&id_membre={$idAdmin}'><i class='far fa-trash-alt'></i></a>";
    }
    else{
        $affichage .= "<i class='fas fa-minus-circle'></i></td>";
    }

    if(isset($_SESSION['membre']) AND $_SESSION['membre']['statut'] > 1){
        $affichage .= "<td><a href='modif-admin?id={$idAdmin}'><i class='far fa-edit'></i></a></td>";   
    }

    $affichage .="</tr>";
}

if(isset($_GET['action']) AND $_GET['action'] == "suppr"){
    if($_SESSION['membre']['id_membre'] == $_GET['id_membre']){
        $erreur .= "<div class='erreur'>Vous ne pouvez pas vous auto supprimer !</div>";
    }
    else{
        $pdo->query('DELETE FROM `membre` WHERE id_membre = "' . $_GET['id_membre'] . '"');
        header('location:list-admin');
    }
}


?>
<section id="evenement" class="evenement-e">
    <div class="contenaire-event" id="contenaire-event">
        <h1 class="red">Liste des administrateurs</h1>
        <div class="scroll">
        <table id="resTable">
            <thead>
                <tr>
                    <th>Pseudo</th>
                    <th>Nom</th>
                    <th>Prenom</th>
                    <th>email</th>
                    <th>Statut</th>
                    <th>Supprimer</th>
                    <?php
    if(isset($_SESSION['membre']) AND ($_SESSION['membre']['statut'] > 1)){
        echo '<th>Modification</th>';
    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?= $affichage ?>
            </tbody>
        </table>
        </div>
        <a href="https://www.bnkcommunity.com/admin/admin_office" class="return-admin-office">Retour au back-office</a>
    </div>
</section>
<?php
    require '../asset/module/footer.php';