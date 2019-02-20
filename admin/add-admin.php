<?php
ob_start();
require '../asset/module/header.php';

if($_SESSION['membre'] == '')
{
    header('location:' . URL . '/error/403');
}

if(isset($_POST['ajouter']) AND $_POST['ajouter'] == 'Envoyer'){
    extract($_POST);

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

    if(empty($_POST['email'])){
        $erreur .= "<div class='erreur'>Vous n'avez pas indiqué d'email !</div>";
    }
    else{
        if(!preg_match('/^[^0-9][_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',$_POST['email'])){
            $erreur .= "<div class='erreur'>Le format de votre adresse email n'est pas valide.</div>";
        }
        else{
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreur .= "<div class='erreur'>Le format de votre adresse email est incorrect.</div>";
            }
            else{
                foreach($invalidMail as $value){
                    if(stripos($_POST['email'], $value)){
                        $erreur .= "<div class='erreur'>Vous ne pouvez pas vous inscrire avec cette adresse email !</div>";
                    }
                    else{
                        $emailVerifSQL = $pdo->prepare("SELECT email FROM `membre` WHERE email='{$_POST['email']}'");
                        $emailVerifSQL->execute();
                        $emailExist = $emailVerifSQL->RowCount();

                        if($emailExist != 0){
                            $erreur .= "<div class='erreur'>L'adresse email est déjà utilisé par un administrateur.</div>";
                            break;
                        }
                    }
                }
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

        $mdp = password_hash($string,PASSWORD_DEFAULT);

        $insertionInscription = $pdo->prepare("INSERT INTO membre(pseudo,nom,prenom,email,token,mdp) VALUES(:pseudo,:nom,:prenom,:email,:token,:mdp)");
        $insertionInscription->execute([
            ':pseudo'       => htmlspecialchars($pseudo, ENT_QUOTES),
            ':nom'          => htmlspecialchars($nom, ENT_QUOTES),
            ':prenom'       => htmlspecialchars($prenom, ENT_QUOTES),
            ':email'        => htmlspecialchars($email, ENT_QUOTES),
            ':token'        => token(),
            ':mdp'          => htmlspecialchars($mdp, ENT_QUOTES)
        ]);

        $admin = $_SESSION['membre']['pseudo'];
        $email_reception = $_POST['email'];
        $day = date('d-m-y');
        $hour = date('h:i');

        // O,n filtre les serveurs qui bugs
        if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $email_reception)) 
        {
            $passage_ligne = "\r\n";
        }
        else
        {
            $passage_ligne = "\n";
        }
        $message_html = "<body style='padding:0px;margin:0px;'><div style='width:100%;height:100px;background:black;'><img src='https://www.bnkcommunity.com/asset/img/bnk.png' style='width:80px;margin: 0 auto;display:block;padding-top:10px;' alt=''></div><div style='background:white;width:100%;height:100%;padding-top:50px;'><div style='background:white;height:80%;width:80%;display:block;margin:0 auto;border:1px solid #E2E2E2E2'><p style='font-family:sans-serif;text-align:center'>Vous avez été désigné comme administrateur par l'adminstrateur {$admin}.</p><hr><p style='font-family:sans-serif;margin-left:10px;text-align:center'>Veuillez trouver ci-dessous vos identifiants de connexion.</p><p style='font-family:sans-serif;margin-left:10px'>E-mail : {$email_reception}</p><p style='font-family:sans-serif;margin-left:10px;'>Mot de passe : {$string}</p><a href='https://www.bnkcommunity.com/bnk_login' style='font-family:sans-serif;margin-left:10px;text-decoration:none;color:#cb2025;display:block;text-align:center;font-size:1em;padding-bottom:20px;'>Cliquez ici pour accéder au back-office</a></div></div></body>";

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


        if(!mail($email_reception,$sujet,$message,$header)){
            $erreur .= "<div class='erreur'>Problème lors de l'envoi de l'email.</div>";
        }

        else{
            $erreur .= "<div class='success'>Félicitation, vous êtes inscrit ! Un email contenant le mot de passe à été envoyé à {$_POST['email']}</div>";
        }
    }

}

?>

<section class="galerie">
    <div class="position-H1">
        <h1>Ajouter un administrateur</h1>
    </div>

    <?= $erreur;?>
    <div class='g-article'>
        <form method='post'>
            <input type="text" name="pseudo" placeholder="Pseudo">
            <input type="text" name="nom" placeholder="Nom">
            <input type="text" name="prenom" placeholder="prenom">
            <input type='email' name='email' placeholder='adresse e-mail'>
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