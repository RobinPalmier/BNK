<?php
ob_start();
require 'asset/module/header.php';

if(isset($_SESSION['membre'])){
    header('location:' . URL);
}

if(isset($_POST['connexion'])){
    extract($_POST);

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";

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
        }
    }

    if(empty($_POST['mdp'])){
        $erreur .= "<div class='erreur'>Vous n'avez pas indiqué de mot de passe !</div>";
    }
    else{
        if(strlen($_POST['mdp']) > 20 || strlen($_POST['mdp']) < 4){
            $erreur .= "<div class='erreur'>Votre mot de passe doit contenir entre 4 et 20 caractères. </div>";
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

        $recupSession = $pdo->prepare('SELECT id_membre,nom,prenom,email,pseudo,statut,mdp FROM membre WHERE email = :email');
        $recupSession->execute(array(
            'email' => $email,


        ));
        $connexion = $recupSession->fetch();

        $verifMdp = password_verify($_POST['mdp'],$connexion['mdp']);

        if(!$connexion){
            $erreur .= "<div class='erreur'>Erreur d'authentification.</div>";
        }
        else{
            if($verifMdp){
                $_SESSION['membre']['id_membre'] = $connexion['id_membre'];
                $_SESSION['membre']['nom'] = $connexion['nom'];
                $_SESSION['membre']['prenom'] = $connexion['prenom'];
                $_SESSION['membre']['email'] = $connexion['email'];
                $_SESSION['membre']['pseudo'] = $connexion['pseudo'];
                $_SESSION['membre']['statut'] = $connexion['statut'];

                header('location:https://www.bnkcommunity.com/admin/admin_office');
            }

            else{
                $erreur .= "<div class='erreur'>Erreur d'authentification.</div>";
            }
        }

    }

}
?>

<section class="galerie">
    <div class="position-H1">
        <h1>Connexion</h1>
    </div>
    <?= $erreur;?>
    <div class='g-article'>
        <form method='post'>
            <input type='email' name='email' placeholder='adresse e-mail'>
            <input type='password' name='mdp' placeholder='mot de passe'>
            <div class='captcha-display'>
                <img src='asset/img/captcha.php' alt=''>
                <input type='text' id='captcha' class='form-control' name='captcha'>
            </div>
            <div class='contenair-partage'>
                <input type='submit' name='connexion' class='partager-seul' value='Envoyer'>
            </div>
        </form>
    </div>
</section>



<?php
require 'asset/module/footer.php';
