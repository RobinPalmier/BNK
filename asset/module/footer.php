<?php

$adresse = $_SERVER['PHP_SELF'];
$search = '/admin/';
$matches = array();

if(preg_match($search, $adresse, $matches)){
    $js = '../';
}
else
{
    $js = '';

}

if(isset($_POST['submit-nl']) AND $_POST['submit-nl'] == "Abonnement"){

    extract($_POST);

    $invalidMail = array('@yopmail.com','@laposte.net','@armyspy.com','@cuvox.de','@dayrep.com','@einrot.com','@fleckens.hu','@gustr.com','@jourrapide.com','@rhyta.com','@superrito.com','@teleworm.us','@boximail.com','@boximail.com','@boximail.com','@boximail.com','@boximail.com','@boximail.com','@getairmail.com','@zetmail.com','@vomoto.com','@clrmail.com','@vmani.com','@emlhub.com','@mozej.com','@carins.io','@goooomail.com','@ziyap.com','@sharklasers.com','@guerrillamail.info','@grr.la','@guerrillamail.biz','@guerrillamail.com','@guerrillamail.de','@guerrillamail.net','@guerrillamail.org','@guerrillamailblock.com','@guerrillamailblock.com','@spam4.me');



    if(empty($_POST['nom'])){
        $erreurNl .= "<div class='erreur'>Vous n'avez pas indiqué de nom !</div>";
    }
    else{
        if(strlen($_POST['nom']) > 100){
            $erreurNl .= "<div class='erreur'>Votre nom contient plus de 100 caractères.</div>";
        }
        else{
            if(!preg_match('#^[a-zA-Z]+$#',$_POST['nom'])){
                $erreurNl .= "<div class='erreur'>Erreur de format au niveau du champ nom.<br> Les Caractères autorisés : a-zA-Z</div>";
            }
        }
    }


    if(empty($_POST['prenom'])){
        $erreurNl .= "<div class='erreur'>Vous n'avez pas indiqué de prenom !</div>";
    }
    else{
        if(strlen($_POST['prenom']) > 100){
            $erreurNl .= "<div class='erreur'>Votre prenom contient plus de 100 caractères.</div>";
        }
        else{
            if(!preg_match('#^[a-zA-Z-]+$#',$_POST['prenom'])){
                $erreurNl .= "<div class='erreur'>Erreur de format au niveau du champ prenom.<br> Les Caractères autorisés : a-zA-Z</div>";
            }
        }
    }

    if(empty($_POST['email'])){
        $erreurNl .= "<div class='erreur'>Vous n'avez pas indiqué d'email !</div>";
    }
    else{
        if(!preg_match('/^[^0-9][_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',$_POST['email'])){
            $erreurNl .= "<div class='erreur'>Le format de votre adresse email n'est pas valide.</div>";
        }
        else{
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreurNl .= "<div class='erreur'>Le format de votre adresse email est incorrect.</div>";
            }
            else{
                foreach($invalidMail as $value){
                    if(stripos($_POST['email'], $value)){
                        $erreurNl .= "<div class='erreur'>Vous ne pouvez pas vous inscrire avec cette adresse email !</div>";
                    }
                }
            }
        }
    }

    if(empty($_POST['captcha'])){
        $erreurNl .= "<div class='erreur'>Veuillez entrer le captcha.</div>";
    }
    else{
        if($_POST['captcha'] != $_SESSION['captcha']){
            $erreurNl .= "<div class='erreur'>Le captcha est invalide.</div>";
        }
    }

    if(empty($erreurNl)){
        $nomNl = $_POST['nom'];
        $prenomNl = $_POST['prenom'];
        $emailNl = $_POST['email'];

        $temps = 365*24*3600;
        setcookie ("nom",$nomNl,time() + $temps);
        setcookie ("prenom",$prenomNl,time() + $temps);
        setcookie ("email",$emailNl,time() + $temps);

        $abonneNewsletter = $pdo->prepare("INSERT INTO newsletter(email,nom,prenom,PHPSESSID) VALUES(:email,:nom,:prenom,:PHPSESSID)");
        $abonneNewsletter->execute([
            ':nom'          => htmlspecialchars($nomNl, ENT_QUOTES),
            ':prenom'       => htmlspecialchars($prenomNl, ENT_QUOTES),
            ':email'        => htmlspecialchars($emailNl, ENT_QUOTES),
            ':PHPSESSID'    => htmlspecialchars($_COOKIE['PHPSESSID'], ENT_QUOTES)
        ]);

        $erreurNl .= "<div class='success'>Félicitations, vous êtes bien inscrit à notre Newsletter.</div>";

        header('Refresh: 2;URL=' . URL);

    }
}


?>
<footer>
    <div class="content-footer">
        <div class="partenaire">
            <h2>Partenaire</h2>
            <h3>Paris La Nuit</h3>
            <h3>Trax Magazine</h3>
            <h3>La carte Son</h3>
            <h3>Radio 2.26 Tours</h3>
        </div>
        <div class="contact-footer">
            <h2>Contact</h2>
            <a href="mailto:bnk.association@gmail.com">bnk.association@gmail.com</a>
            <div id="abonnementNl">S'abonner à la newsletter</div>
        </div>
        <div class="bnk-social">
            <h2>Reseaux Sociaux</h2>
            <div class="icon">
                <a href="https://www.facebook.com/bnkcommunity" class="fb" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.instagram.com/bnkevents" class="ig" target="_blank"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
    <hr>
    <div class="content-footer2">
        <a href="<?= URL ?>/mentions-legales">Mention légales</a>
        <p class="date">©BNK<?= date('Y')?></p>
        <a href="https://www.linkedin.com/in/robinpalmier" class="credits" target="_blank">
            <p> Design by&nbsp;&nbsp;</p>
            <img src="<?=$js?>asset/img/Logo-RobinPalmiern_b.svg" class="robinpalmier" alt="www.robinpalmier.fr">
            <p>&nbsp;&nbsp;Robin Palmier</p>
        </a>
    </div>
</footer>

<div class="bcg-modal-nl-footer"></div>
<div class="modal-newsletter-footer" id="modal-newsletter-footer">
    <div class="modalheader-newsletter-footer">
        <div class="title-newsletter-footer">S'abonner à la newsletter</div>
    </div>
    <?= $erreurNl ?>
    <div class="modalcontent-newsletter-footer">
        <form method="post">
            <input type="email" name="email" placeholder="Adresse Email">
            <input type="text" name="nom" placeholder="Votre nom">
            <input type="text" name="prenom" placeholder="Votre prenom">
            <div class="captcha-display">
                <img src="<?=$js?>asset/img/captcha.php" alt="">
                <input type="text" id="captcha-u" class="form-control" name="captcha">
            </div>
            <div class="button-nl-footer">
                <input type="submit" name="submit-nl" class="partager-nl-footer" value="Abonnement">
                <input type="button" name="later-nl" class="partager-xx-footer" value="Plus tard">
            </div>
        </form>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>
<script src="<?=$js?>asset/js/jquery-ui.min.js"></script>
<script src="<?=$js?>asset/js/app.js"></script>
<script src="<?=$js?>asset/js/ajax.js"></script>
<script src="<?=$js?>asset/js/transition.js"></script>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-119138977-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-119138977-1');
</script>
</body>
</html>
