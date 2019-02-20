<?php
ob_start();
require '../asset/module/header.php';

if($_SESSION['membre'] == '')
{
    header('location:' . URL . '/error/403');
}

// [ Conversion base de données en fichier Json
/* |  */   $recupBDD = $pdo->prepare('SELECT * FROM event');
/* |  */   $recupBDD->execute();
/* |  */   $recupDonnees = $recupBDD->fetchAll(); 
/* |  */   $recupBDD->closeCursor();
/* |  */   $encodageJson = json_encode($recupDonnees);
/* |  */   $fileJson = '../json/bdd-event.json';
/* |  */   $fichier = fopen($fileJson, 'w+');
/* |  */   fwrite($fichier, $encodageJson);
/* |  */   fclose($fichier); 
// Fin de la conversion de la base de données en fichier Json ]


$fichierJson = file_get_contents('../json/bdd-event.json');
$json = json_decode($fichierJson,true);

// echo '<pre>';
// print_r ($json);
// echo '</pre>';


foreach($json as $valeurs){
    $affichageB .= "<option value='$valeurs[event_id]'>$valeurs[nom_event]</option>";
}

if(isset($_POST['modif'])){
    $select_val = $_POST['select'];

    if($select_val == 'none'){
        $erreurDeux .= "<div class='erreur' style='margin-top:78px;'>Veuillez séléctionner un article !</div>";
    }
    else{

        $recupModifEvent = $pdo->prepare("SELECT * FROM event WHERE event_id = {$select_val}");
        $recupModifEvent->execute();
        $recupModifEvent->bindColumn('nom_event', $nomEvent, PDO::PARAM_STR);
        $recupModifEvent->bindColumn('partenaire_event', $partEvent, PDO::PARAM_STR);
        $recupModifEvent->bindColumn('orga_event', $orgaEvent, PDO::PARAM_STR);
        $recupModifEvent->bindColumn('date_event', $dateEvent, PDO::PARAM_STR);
        $recupModifEvent->bindColumn('prix_event', $prixEvent, PDO::PARAM_STR);
        $recupModifEvent->bindColumn('event_id', $idEvent);
        while($recupModifEvent->fetch()){

            $affichageA .= "<input type='text' name='nomEvent' placeholder='Titre de l'évenement' value='{$nomEvent}'>
            <input type='text' name='orgaEvent' placeholder='Nom des organisateurs' value='{$orgaEvent}'>
            <input type='text' name='partEvent' placeholder='Partenaires' value='{$partEvent}'>
            <input type='text' name='dateEvent' id='datepicker' placeholder='date de l'évenement' value='{$dateEvent}'>
            <input type='text' name='prixEvent' placeholder='Prix d'entrée' maxlength='8' value='{$prixEvent}'>

            <div class='captcha-display'>
                <img src='../asset/img/captcha.php' alt=''>
                <input type='text' id='captcha' class='form-control' name='captcha'>
            </div>
            <input type='hidden' name='id_event' value='{$idEvent}' style='display:none'>
            <div class='contenair-partage'>
                <input type='submit' name='modificationEvent' class='partager-seul' value='Envoyer'>
            </div>";

        }
    }
}

if(isset($_POST['modificationEvent']) and $_POST['modificationEvent'] == 'Envoyer'){
    extract($_POST);

    $modifNom = $_POST['nomEvent'];
    $modifOrga = $_POST['orgaEvent'];
    $modifPart = $_POST['partEvent'];
    $modifDate = $_POST['dateEvent'];
    $modifPrix = $_POST['prixEvent'];
    $idEvent = $_POST['id_event'];

    if(empty($_POST['nomEvent'])){
        $erreur .= "<div class='erreur'>Veuillez indiquer un titre à votre évenement.</div>";
    }
    else{
        if(strlen($_POST['nomEvent']) > 100){
            $erreur .= "<div class='erreur'>Votre titre contient plus de 100 caractères.</div>";
        }
    }

    if(empty($_POST['orgaEvent'])){
        $erreur .= "<div class='erreur'>Veuillez indiquer au moins un organisateur à votre évenement.</div>";
    }

    if(empty($_POST['partEvent'])){
        $erreur .= "<div class='erreur'>Veuillez indiquer au moins un partenaire à votre évenement.</div>";
    }

    if(empty($_POST['dateEvent'])){
        $erreur .= "<div class='erreur'>Veuillez indiquer une date à votre évenement.</div>";
    }
    else{
        if(!preg_match('#^[0-9-]+$#',$_POST['dateEvent'])){
            $erreur .= "<div class='erreur'>Veuillez ecrire la date au format : dd-mm-aaaa</div>";
        }
    }

    if(empty($_POST['prixEvent'])){
        $erreur .= "<div class='erreur'>Veuillez indiquer une prix à votre évenement.</div>";
    }
    else{
        if(!preg_match('#^[0-9,.€]+$#',$_POST['prixEvent'])){
            $erreur .= "<div class='erreur'>Veuillez ecrire le prix au format : 00,00€ ou 00.00€</div>";
        }
    }

    if(empty($_POST['captcha'])){
        $erreur .= "<div class='erreur'>Veuillez entrer le captcha.</div>";  
    }
    else{
        if($_POST['captcha'] != $_SESSION['captcha']){
            $erreur .= "<div class='erreur'>Le captcha est invalide.</div>";   
        }
    }

    if(empty($erreur)){


        // 'UPDATE article SET titre = '{$_POST['titre']}', contenu = '{$_POST['contenu']}' WHERE id = WHERE id_article = '{$select_val}'

        $validModifArticle = $pdo->query("UPDATE event SET `nom_event` = '" . htmlspecialchars($modifNom, ENT_QUOTES) . "', `partenaire_event` = '" . htmlspecialchars($modifPart, ENT_QUOTES) . "', `orga_event` = '" . htmlspecialchars($modifOrga, ENT_QUOTES) . "', `date_event` = '" . htmlspecialchars($modifDate, ENT_QUOTES) . "', `prix_event` = '" . htmlspecialchars($modifPrix, ENT_QUOTES) . "' WHERE `event_id` = '{$idEvent}'");

        $erreur .= "<div class='success'>Félicitation ! L'évenement a bien été modifié.</div>";
    }
}




?>

<section class="galerie">
    <div class="position-H1">
        <h1>Modifier un évenement</h1>
    </div>
    <?= $erreur;?>
    <form method="post" id="form-select-modif-article">
        <select name='select' id='select-titre'>
            <option value="none">Choisir un évenement</option>
            <?= $affichageB;?>
        </select>
        <button type='submit' name='modif' class='partager-seul' value='Envoyer' id="submit-form-select-modif-article">
            <i class="fas fa-search"></i>
        </button>
    </form>
    <?= $erreurDeux;?>
    <div class='g-article'>
        <form method='post' enctype='multipart/form-data' id="modif-article-form-donnes">
            <?= $affichageA;?>
        </form>
        <div id="post-resultat"></div>
        <a href="https://www.bnkcommunity.com/admin/admin_office" class="return-admin-office">Retour au back-office</a>
    </div>
</section>


<?php
require '../asset/module/footer.php';
