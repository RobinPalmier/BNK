<?php
ob_start();
require 'asset/module/header.php';

//echo '<pre>';
//print_r($_SESSION);
//echo '</pre>';

// Cookie

// [ Conversion base de données en fichier Json
/* |  */   $recupBDDNl = $pdo->prepare('SELECT * FROM newsletter');
/* |  */   $recupBDDNl->execute();
/* |  */   $recupDonneesNl = $recupBDDNl->fetchAll(); 
/* |  */   $recupBDDNl->closeCursor();
/* |  */   $encodageJsonNl = json_encode($recupDonneesNl);
/* |  */   $fileJsonNl = 'json/bdd-nl-verif.json';
/* |  */   $fichierNl = fopen($fileJsonNl, 'w+');
/* |  */   fwrite($fichierNl, $encodageJsonNl);
/* |  */   fclose($fichierNl); 
// Fin de la conversion de la base de données en fichier Json ]

$recupPhpSessId = $pdo->prepare("SELECT PHPSESSID FROM newsletter");
$recupPhpSessId->execute();
$recupPhpSessId->bindColumn('PHPSESSID', $phpSessId, PDO::PARAM_STR);
while($recupPhpSessId->fetch())
{

    $sessionLog[] = $phpSessId;

}

global $sessionLog;

if(!in_array($_COOKIE['PHPSESSID'], $sessionLog)){

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
                        else{
                            $emailVerifySQL = $pdo->prepare("SELECT email FROM `newsletter` WHERE email='{$_POST['email']}'");
                            $emailVerifySQL->execute();
                            $emailExist = $emailVerifySQL->RowCount();

                            if($emailExist != 0){
                                $erreurNl .= "<div class='erreur'>L'adresse email est déjà abonné à la newsletter</div>";
                                break;
                            }

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
            $message_html = "<body style='padding:0px;margin:0px;'><div style='width:100%;height:100px;background:black;'><img src='https://www.bnkcommunity.com/asset/img/bnk.png' style='width:80px;margin: 0 auto;display:block;padding-top:10px;' alt=''></div><div style='background:white;width:100%;height:100%;padding-top:50px;'><div style='background:white;height:80%;width:80%;display:block;margin:0 auto;border:1px solid #E2E2E2E2'><p style='font-family:sans-serif;text-align:center'>Félicitations, vous êtes bien inscrit à la newsletter de BNK !</p><hr><p style='font-family:sans-serif;margin-left:10px;text-align:center;margin-top:30px;'>Vous recevrez prochainement l'actualité de BNK dans votre messagerie.</p><a href='https://www.bnkcommunity.com/' style='font-family:sans-serif;margin-left:10px;text-decoration:none;color:#cb2025;display:block;text-align:center;font-size:1em;padding-bottom:20px;'>Accéder au site</a><hr>
<p style='font-size:0.7em;color:#a5a5a5;text-align:center;width:70%;margin:0 auto;margin-bottom:10px;'>Vous recevez ce message car vous êtes inscrits à la Newsletter du site <a href='https://www.bnkcommunity.com/'>www.bnkcommunity.com</a>.<br><br>Les données à caractère personnel, recueillies par l'intermédiaire du formulaire d'inscription de la newsletter, sont traitées en conformité avec la loi modifiée du 2 août 2002 relative à la protection des personnes à l'égard du traitement des données à caractère personnel.<br><br>Les données ne sont traitées que dans le but de vous permettre de recevoir la newsletter de ce portail. Elles sont toutefois susceptibles d'être traitées à des fins historiques, statistiques ou scientifiques.<br><br>Conformément à cette loi, vous avez le droit d'accéder, de modifier ou de vous opposer à tout traitement de données qui vous concernent. Pour faire usage de ces droits, vous pouvez nous contacter : <a href='mailto:bnk.association@gmail.com'>bnk.association@gmail.com</a> en indiquant comme objet : Désabonnement Newsletter BNK.</p></div></div></body></html>";

            //=====Création de la boundary
            $boundary = "-----=".md5(rand());

            //=====Définition du sujet
            $sujet = "BNK - Confirmation d'inscription à la newsletter";

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
}
















// [ Conversion base de données en fichier Json
/* |  */   $recupBDDArt = $pdo->prepare('SELECT * FROM article');
/* |  */   $recupBDDArt->execute();
/* |  */   $recupDonneesArt = $recupBDDArt->fetchAll(); 
/* |  */   $recupBDDArt->closeCursor();
/* |  */   $encodageJsonArt = json_encode($recupDonneesArt);
/* |  */   $fileJsonArt = 'json/bdd-art-affichage.json';
/* |  */   $fichierArt = fopen($fileJsonArt, 'w+');
/* |  */   fwrite($fichierArt, $encodageJsonArt);
/* |  */   fclose($fichierArt); 
// Fin de la conversion de la base de données en fichier Json ]


$recupArticle = $pdo->prepare("SELECT id_article,titre,contenu,image FROM article ORDER BY `article`.`id_article` DESC");
$recupArticle->execute();
$recupArticle->bindColumn('image', $images, PDO::PARAM_LOB);
$recupArticle->bindColumn('contenu', $contenu, PDO::PARAM_STR);
$recupArticle->bindColumn('titre', $titre, PDO::PARAM_STR);
$recupArticle->bindColumn('id_article', $idArticle);
$i = 0;
while($recupArticle->fetch())
{
    $i++;

    if($i === 1){
        $affichageA .= "<div class='article-first' onclick='openArt(this);' data-art=".$idArticle.">
                    <img src='./asset/a_img/{$images}' alt='{$titre}'>
                    <article>
                    <h2>{$titre}</h2>";
        $affichageA .= str_replace(array("\r\n","\n"),'<br />',rogneArticle($contenu,353));
        $affichageA .= "</article>";
        $affichageA .= "</div>";
    }
    else{

        $affichageAelse .= "<li class='contenu-article' data-art=".$idArticle.">
            <img src='./asset/a_img/{$images}' alt='{$titre}' onclick='openArt(this);'>
            <div class='content-text'>
        <article>
<h2>{$titre}</h2>";
        $affichageAelse .= str_replace(array("\r\n","\n"),'<br />',rogneArticle($contenu,350));
        $affichageAelse .= "</article>";
        $affichageAelse .= "</div></li>";

    }



    if($i > 3){

        $affichageAelse .= "<li><a href='?page=article' class='pad-i'>Cliquez ici pour afficher plus d'article</a></li>";


        break;
    }
}


$recupEvent = $pdo->prepare("SELECT event_id,nom_event,partenaire_event,date_event,prix_event FROM event ORDER BY `event`.`event_id` DESC");
$recupEvent->execute();
$recupEvent->bindColumn('nom_event', $nomEvent, PDO::PARAM_STR);
$recupEvent->bindColumn('partenaire_event', $partEvent, PDO::PARAM_STR);
$recupEvent->bindColumn('date_event', $dateEvent, PDO::PARAM_STR);
$recupEvent->bindColumn('prix_event', $prixEvent, PDO::PARAM_STR);
$recupEvent->bindColumn('event_id', $idEvent);
$u = 0;
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

    $u++;


    if($u > 2){

        $affichageE .= "<a href='?page=evenement'><div class='aff-plus-ev'>Cliquez ici pour afficher plus d'évenement</div></a>";

        break;
    }

}

?>

<section id="accueil">
    <div class='contenaire-bnk'>
        <?= $erreurDeux ?>

        <h1 class='bnk-index' data-text="BNK">BNK</h1>
    </div>
</section>
<section id="actualite">
    <div class="contenaire-actu" id="contenaire-actu">
        <h1>Suivez l'actualité de BNK</h1>
        <h2 class="chapo-actu-h2">Retrouver l'actualité des événements, des concerts et des artistes de la  scène Techno</h2>
        <div id="container-alert"></div>
        <?= $erreur; ?>

        <div class="row">

            <div class="col-6">
                <?= $affichageA; ?>
            </div>


            <div class="col-6 menu">
                <ul>
                    <?= $affichageAelse; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="bcg-modal-art"></div>
    <div class='modal-art' id='jquery-select-modal-art'>
        <div class='modal-content-art' id='select-content-art'>
            <div class='lock-button-art'>
                <i class='far fa-times-circle close-button-art'></i>
            </div>
            <div class='image-art'>
                <img id='expandedImgArt' src='./asset/a_img/' alt=''>
            </div>
            <hr>
            <div class='texte-Article'>
                <div class='titre-modal-art' id="ArticleImgTitre"></div>
                <div class='content-modal-art' id="ArticleImgText"></div>
            </div>
        </div>
    </div>

</section>
<section id="evenement">
    <div class="contenaire-event" id="contenaire-event">
        <h1>Les évenements de BNK</h1>
        <h2>Suivez les événements Techo organisé par BNK</h2>
        <?= $erreur; ?>
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
        <?= $affichageE; ?>
    </div>
</section>
<section id="qui-sommes-nous">
    <video autoplay muted loop class="video-qsn">
        <source src="./asset/img/bnk_qui_sommes_nous.gif.mp4" type="video/mp4">
    </video>
    <div class="contenaire-qsn">
        <h1>Qu'est ce que BNK ?</h1>
        <div class="text-qsn">
            <h2>Nous créons des évènements technos à Paris et sa banlieue dans des lieux atypiques.</h2>
            <br>
            <h2>En vous proposant des évènements Technos de qualité avec artistes confirmés comme Nuages, Perc, Randomer, DJ Pierre, Blush Response, JoeFarr, Thomas P. Heckmann ... et promouvant la scène techno locale.</h2>
        </div>
    </div>
</section>

<div class="bcg-modal-nl"></div>
<div class="modal-newsletter" id="modal-newsletter">
    <div class="modalheader-newsletter">
        <div class="title-newsletter">Bienvenue sur le site de <span>BNK</span>, souhaitez-vous vous inscrire à la newsletter ?</div>
    </div>
    <?= $erreurNl ?>
    <div class="modalcontent-newsletter">
        <form method="post">
            <input type="email" name="email" placeholder="Adresse Email">
            <input type="text" name="nom" placeholder="Votre nom">
            <input type="text" name="prenom" placeholder="Votre prenom">
            <div class="captcha-display">
                <img src="asset/img/captcha.php" alt="">
                <input type="text" id="captcha-i" class="form-control" name="captcha">
            </div>
            <div class="button-nl">
                <input type="submit" name="submit-nl" class="partager-nl" value="Abonnement">
                <input type="button" name="later-nl" class="partager-xx" value="Plus tard">
            </div>
        </form>
    </div>
</div> 

<?php
    require 'asset/module/footer.php';
