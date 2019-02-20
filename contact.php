<?php
require 'asset/module/header.php';
if(isset($_POST['envoyer']) AND $_POST['envoyer'] == 'Envoyer')
{
    extract($_POST);

    $email_expediteur = $_POST['email'];
    $titreMessage = $_POST['titreContact'];
    $message = $_POST['message'];

    if(empty($email_expediteur)){
        $erreur .= "<div class='erreur'>Vous n'avez pas indiqué d'email !</div>";
    }
    else{
        if(!preg_match('/^[^0-9][_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',$email_expediteur)){
            $erreur .= "<div class='erreur'>Le format de votre adresse email n'est pas valide.</div>";
        }
        else{
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreur .= "<div class='erreur'>Le format de votre adresse email est incorrect.</div>";
            }
        }
    }

    if(empty($titreMessage)){
        $erreur .= "<div class='erreur'>Vous n'avez pas indiqué de titre à votre message !</div>";
    }
    else{
        if(strlen($titreMessage) > 100){
            $erreur .= "<div class='erreur'>Votre titre contient plus de 100 caractères.</div>";
        }else{
            if(strlen($titreMessage) < 5){
                $erreur .= "<div class='erreur'>Votre titre doit contenir 5 caractères minimum.</div>";
            }
        }
    }

    if(empty($message)){
        $erreur .= "<div class='erreur'>Vous n'avez pas entré de message.</div>";
    }
    else{
        if(strlen($message) < 10){
            $erreur .= "<div class='erreur'>Votre message doit contenir 10 caractères minimum.</div>";
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
        // Déclaration de l'adresse de destination
        $mail = 'robinpalmier98@gmail.com';
        $day = date('d-m-y');
        $hour = date('h:i');

        // O,n filtre les serveurs qui bugs
        if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) 
        {
            $passage_ligne = "\r\n";
        }
        else
        {
            $passage_ligne = "\n";
        }

        //=====Déclaration des messages au format texte et au format HTML
        $message_html = "<body style='padding:0px;margin:0px;'><div style='width:100%;height:100px;background:black;'><img src='https://www.bnkcommunity.com/asset/img/bnk.png' style='width:80px;margin: 0 auto;display:block;padding-top:10px;' alt=''></div><div style='background:white;width:100%;height:100%;padding-top:50px;'><div style='background:white;height:80%;width:80%;display:block;margin:0 auto;border:1px solid #E2E2E2E2'><p style='font-family:sans-serif;text-align:center'><b>{$titreMessage}</b></p><hr><p style='font-family:sans-serif;margin-left:10px'><b>Expéditeur :</b> {$email_expediteur}</p><p style='font-family:sans-serif;margin-left:10px'><b>Date d'envoie :</b> {$day} à {$hour}</p><p style='font-family:sans-serif;margin-left:10px'><b>Message :</b> <br> {$message}</p></div></div></body></html>";

        //=====Création de la boundary
        $boundary = "-----=".md5(rand());

        //=====Définition du sujet
        $sujet = "BNK - Un nouveau message à été envoyé depuis le www.bnkcommunity.com !";

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


        if(!mail($mail,$sujet,$message,$header)){
            $erreur .= "<div class='erreur'>Problème lors de l'envoi de l'email.</div>";
        }

        else{
            $erreur .= "<div class='success center'>L'email à bien été envoyé !</div>";
        }
    }
}

?>

<section class="galerie">
    <div class="position-H1">
        <h1>Nous contacter</h1>
    </div>
    <?= $erreur;?>
    <div class='g-article'>
        <form method='post'>
            <input type='email' name='email' placeholder='adresse e-mail'>
            <input type='text' name='titreContact' placeholder='Titre du message'>
            <textarea placeholder='Ecrivez-ici ...' name='message'></textarea>

            <div class='captcha-display'>
                <img src='asset/img/captcha.php' alt=''>
                <input type='text' id='captcha' class='form-control' name='captcha'>
            </div>

            <div class='contenair-partage'>
                <input type='submit' name='envoyer' class='partager-seul' value='Envoyer'>
            </div>
        </form>
    </div>
</section>


<?php
require 'asset/module/footer.php';