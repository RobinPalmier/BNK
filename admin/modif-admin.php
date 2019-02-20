<?php
ob_start();
require '../asset/module/header.php';

if($_SESSION['membre'] == '')
{
    header('location:' . URL . '/error/403');
}

$reqAdmin = $pdo->prepare("SELECT id_membre FROM membre");
$reqAdmin->execute();
$reqAdmin->bindColumn('id_membre', $idAdminModif);
while($reqAdmin->fetch())
{
    $arrayAdmin[] = $idAdminModif;
}

// echo '<pre>';
// print_r($arrayVerif);
// echo '</pre>';

if(isset($_GET['id']) AND in_array($_GET['id'], $arrayAdmin)){

    $recupAdminModif = $pdo->prepare("SELECT * FROM membre WHERE id_membre='{$_GET['id']}'");
    $recupAdminModif->execute();
    $recupAdminModif->bindColumn('statut', $statutA, PDO::PARAM_LOB);
    $recupAdminModif->bindColumn('nom', $nomA, PDO::PARAM_STR);
    $recupAdminModif->bindColumn('prenom', $prenomA, PDO::PARAM_STR);
    $recupAdminModif->bindColumn('email', $emailA, PDO::PARAM_STR);
    $recupAdminModif->bindColumn('pseudo', $pseudoA, PDO::PARAM_STR);
    $recupAdminModif->bindColumn('token', $tokenA, PDO::PARAM_STR);
    $recupAdminModif->bindColumn('id_membre', $idAdministrateur);
    while($recupAdminModif->fetch())
    {
        $affichageA .= "    <div class='position-H1'>
        <h1>Modification : {$pseudoA}</h1>
    </div>";

        $affichageB .= "<form action='' method='post'>
        <input type='text' name='pseudo' placeholder='Pseudo' value='{$pseudoA}'>
            <input type='text' name='nom' placeholder='Nom' value='{$nomA}'>
            <input type='text' name='prenom' placeholder='prenom' value='{$prenomA}'>
            <input type='email' name='email' placeholder='adresse e-mail' readonly='readonly' value='{$emailA}'>
            <input type='text' name='token' placeholder='token' readonly='readonly' value='$tokenA'>
            <select name='statut'>
    <option value='0'>0 - Membre</option>
    <option value='1'>1 - Administrateur</option>
    <option value='2'>2 - Suprême Admin</option>
</select>

            <div class='captcha-display'>
                <img src='../asset/img/captcha.php' alt=''>
                <input type='text' id='captcha' class='form-control' name='captcha'>
            </div>
            <div class='contenair-partage'>
                <input type='submit' name='modif-admin' class='partager-seul' value='Valider les modifications'>
                <input type='submit' name='reset-mdp' class='partager-seul-orange' value='Renvoyer le mot de passe'>
            </div>
        </form>";
    }
}
else{        

    $erreur .= "<div class='erreur'>Le membre que vous recherchez n'existe pas ou n'existe plus.</div>";
}


if(isset($_POST['modif-admin']) AND $_POST['modif-admin'] == 'Valider les modifications'){

    extract($_POST);

    global $idAdministrateur;
    global $emailA;

    //echo '<pre>';
    //print_r($_POST);
    //echo '</pre>';

    $invalidMail = array('@yopmail.com','@laposte.net','@armyspy.com','@cuvox.de','@dayrep.com','@einrot.com','@fleckens.hu','@gustr.com','@jourrapide.com','@rhyta.com','@superrito.com','@teleworm.us','@boximail.com','@boximail.com','@boximail.com','@boximail.com','@boximail.com','@boximail.com','@getairmail.com','@zetmail.com','@vomoto.com','@clrmail.com','@vmani.com','@emlhub.com','@mozej.com','@carins.io','@goooomail.com','@ziyap.com','@sharklasers.com','@guerrillamail.info','@grr.la','@guerrillamail.biz','@guerrillamail.com','@guerrillamail.de','@guerrillamail.net','@guerrillamail.org','@guerrillamailblock.com','@guerrillamailblock.com','@spam4.me');


    if(empty($_POST['pseudo'])){
        $erreur .= "<div class='erreur'>Vous n'avez pas indiqué de pseudo !</div>";
    }
    else{
        if(strlen($_POST['pseudo']) > 20){
            $erreur .= "<div class='erreur'>Votre pseudo contient plus de 20 caractères.</div>";
        }
        else{
            if(!preg_match('#^[a-zA-Z0-9\'._-]+$#',$_POST['pseudo'])){
                $erreur .= "<div class='erreur'>Erreur de format au niveau du champ pseudo.<br> Les Caractères autorisés : a-zA-Z0-9'._-</div>";
            }
        }
    }

    if(empty($_POST['nom'])){
        $erreur .= "<div class='erreur'>Vous n'avez pas indiqué de nom !</div>";
    }
    else{
        if(strlen($_POST['nom']) > 100){
            $erreur .= "<div class='erreur'>Votre nom contient plus de 100 caractères.</div>";
        }
        else{
            if(!preg_match('#^[a-zA-Z]+$#',$_POST['nom'])){
                $erreur .= "<div class='erreur'>Erreur de format au niveau du champ nom.<br> Les Caractères autorisés : a-zA-Z</div>";
            }
        }
    }

    if(isset($_POST['statut']) && $_POST['statut'] < 0 || $_POST['statut'] > 2){
        $erreur .= "<div class='erreur'>Statut invalide.</div>";
    }


    if(empty($_POST['prenom'])){
        $erreur .= "<div class='erreur'>Vous n'avez pas indiqué de prenom !</div>";
    }
    else{
        if(strlen($_POST['prenom']) > 100){
            $erreur .= "<div class='erreur'>Votre prenom contient plus de 100 caractères.</div>";
        }
        else{
            if(!preg_match('#^[a-zA-Z-]+$#',$_POST['prenom'])){
                $erreur .= "<div class='erreur'>Erreur de format au niveau du champ prenom.<br> Les Caractères autorisés : a-zA-Z</div>";
            }
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

        $validModifArticle = $pdo->query("UPDATE membre SET `pseudo` = '{$_POST['pseudo']}', `nom` = '{$_POST['nom']}', `prenom` = '{$_POST['prenom']}', `statut` = '{$_POST['statut']}' WHERE `id_membre` = '{$idAdministrateur}'");

        $erreur .= "<div class='success'>Félicitation ! Les informations ont bien été modifié.</div>";

        header("refresh:1;url=list-admin"); 
    }
}

if(isset($_POST['reset-mdp']) AND $_POST['reset-mdp'] == 'Renvoyer le mot de passe'){

    $mdpReset = password_hash($string,PASSWORD_DEFAULT);

    $newMdp = $pdo->query("UPDATE membre SET `mdp` = '{$mdpReset}' WHERE `id_membre` = '{$idAdministrateur}'");

    $admin = $_SESSION['membre']['pseudo'];

    global $emailA;
    $day = date('d-m-y');
    $hour = date('h:i');

    // O,n filtre les serveurs qui bugs
    if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $emailA)) 
    {
        $passage_ligne = "\r\n";
    }
    else
    {
        $passage_ligne = "\n";
    }
    $message_html = "<body style='padding:0px;margin:0px;'><div style='width:100%;height:100px;background:black;'><img src='https://www.bnkcommunity.com/asset/img/bnk.png' style='width:80px;margin: 0 auto;display:block;padding-top:10px;' alt=''></div><div style='background:white;width:100%;height:100%;padding-top:50px;'><div style='background:white;height:80%;width:80%;display:block;margin:0 auto;border:1px solid #E2E2E2E2'><p style='font-family:sans-serif;text-align:center'>Votre message a été modifié par l'administrateur {$admin}.</p><hr><p style='font-family:sans-serif;margin-left:10px;text-align:center'>Voici vos nouveaux identifiants :</p><p style='font-family:sans-serif;margin-left:10px'>E-mail : {$emailA}</p><p style='font-family:sans-serif;margin-left:10px;'>Mot de passe : {$string}</p><a href='https://www.bnkcommunity.com/bnk_login' style='font-family:sans-serif;margin-left:10px;text-decoration:none;color:#cb2025;display:block;text-align:center;font-size:1em;padding-bottom:20px;'>Cliquez ici pour accéder au back-office</a></div></div></body>";

    //=====Création de la boundary
    $boundary = "-----=".md5(rand());

    //=====Définition du sujet
    $sujet = "BNK - Votre nouveau mot de passe";

    //=====Création du header de l'E-mail
    $header = "From: {$email_expediteur}".$passage_ligne;
    $header.= "Reply-to: {$email_expediteur}".$passage_ligne;
    $header.= "MIME-Version: 1.0".$passage_ligne;
    $header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;

    //=====Création du message
    $message = $passage_ligne.$boundary.$passage_ligne;

    //=====Ajout du message au format texte
    $message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
    $message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
    $message.= $passage_ligne.$message_txt.$passage_ligne;

    $message.= $passage_ligne."--".$boundary.$passage_ligne;

    //=====Ajout du message au format HTML
    $message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
    $message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
    $message.= $passage_ligne.$message_html.$passage_ligne;

    $message.= $passage_ligne."--".$boundary."--".$passage_ligne;
    $message.= $passage_ligne."--".$boundary."--".$passage_ligne;


    if(!mail($emailA,$sujet,$message,$header)){
        $erreur .= "<div class='erreur'>Problème lors de l'envoi de l'email.</div>";
    }

    else{
        $erreur .= "<div class='success center'>L'email à bien été envoyé !</div>";
    }

   header("refresh:5;url=list-admin"); 
}

?>
<section class='galerie'>
    <?= $affichageA ?>
    <?= $erreur ?>
    <div class='g-article'>
        <?= $affichageB ?>
        <a href="https://www.bnkcommunity.com/admin/admin_office" class="return-admin-office">Retour au back-office</a>
    </div>
</section>



<?php
    require '../asset/module/footer.php';
