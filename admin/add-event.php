<?php
ob_start();
require '../asset/module/header.php';

if($_SESSION['membre'] == '')
{
    header('location:' . URL . '/error/403');
}

if(isset($_POST['ajouter']) and $_POST['ajouter'] == 'Envoyer'){

    extract($_POST);

    // echo '<pre>';
    // print_r($_POST);
    // echo '</pre>';


    if(empty($_POST['nomEvent'])){
        $erreur .= "<div class='erreur'>Veuillez indiquer un titre à votre évenement.</div>";
    }
    else{
        if(strlen($_POST['nomEvent']) > 100){
            $erreur .= "<div class='erreur'>Votre titre contient plus de 100 caractères.</div>";
        }
    }

    if(empty($_POST['partEvent'])){
        $erreur .= "<div class='erreur'>Veuillez indiquer au moins un partenaire à votre évenement.</div>";
    }

    if(empty($_POST['dateEvent'])){
        $erreur .= "<div class='erreur'>Veuillez indiquer une date à votre évenement.</div>";
    }
    else{
        if(!preg_match('#^[0-9-]+$#',$_POST['dateEvent'])){
            $erreur .= "<div class='erreur'>Veuillez ecrire la date au format : dd/mm/aaaa</div>";
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


        $insertionEvent = $pdo->prepare("INSERT INTO event(nom_event,partenaire_event,orga_event,date_event,prix_event)VALUES(:nom_event,:partenaire_event,:orga_event,:date_event,:prix_event)");
        $insertionEvent->execute([
            ':nom_event'            => htmlspecialchars($_POST['nomEvent'], ENT_QUOTES),
            ':partenaire_event'     => htmlspecialchars($_POST['partEvent'], ENT_QUOTES),
            ':date_event'           => htmlspecialchars($_POST['dateEvent'], ENT_QUOTES),
            ':prix_event'           => htmlspecialchars($_POST['prixEvent'], ENT_QUOTES)
        ]);

        $erreur .= "<div class='success'>Félicitation ! L'image à bien été publié.</div>";
    }
}


/*if(isset($_POST['ajouter']) AND $_POST['ajouter'] == 'Envoyer'){
    extract($_POST);
    extract($_FILES);

    echo '<pre>';
    print_r($_FILES);
    echo '</pre>';



}

*/


?>

<section class="galerie">
    <div class="position-H1">
        <h1>Ajouter un évenement</h1>
    </div>

    <?= $erreur;?>
    <div class='g-article'>
        <form method='post' enctype='multipart/form-data'>
            <input type="text" name="nomEvent" placeholder="Titre de l'évenement">
            <input type="text" name="partEvent" placeholder="Partenaires">
            <input type="text" name="dateEvent" id="datepicker" placeholder="date de l'évenement">
            <input type="text" name="prixEvent" placeholder="Prix d'entrée" maxlength="8">

            <div class='captcha-display'>
                <img src='../asset/img/captcha.php' alt=''>
                <input type='text' id='captcha' class='form-control' name='captcha'>
            </div>
            <div class='contenair-partage'>
                <input type='submit' name='ajouter' class='partager-seul' value='Envoyer'>
            </div>
        </form>
        <a href="https://www.bnkcommunity.com/admin/admin_office" class="return-admin-office">Retour au back-office</a>
    </div>
</section>


<?php
require '../asset/module/footer.php';