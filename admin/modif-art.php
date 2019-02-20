<?php
ob_start();
require '../asset/module/header.php';

if($_SESSION['membre'] == '')
{
    header('location:' . URL . '/error/403');
}

// [ Conversion base de données en fichier Json
/* |  */   $recupBDD = $pdo->prepare('SELECT * FROM article');
/* |  */   $recupBDD->execute();
/* |  */   $recupDonnees = $recupBDD->fetchAll(); 
/* |  */   $recupBDD->closeCursor();
/* |  */   $encodageJson = json_encode($recupDonnees);
/* |  */   $fileJson = '../json/bdd-art.json';
/* |  */   $fichier = fopen($fileJson, 'w+');
/* |  */   fwrite($fichier, $encodageJson);
/* |  */   fclose($fichier); 
// Fin de la conversion de la base de données en fichier Json ]


$fichierJson = file_get_contents('../json/bdd-art.json');
$json = json_decode($fichierJson,true);

// echo '<pre>';
// print_r ($json);
// echo '</pre>';


foreach($json as $valeurs){
    $affichageB .= "<option value='$valeurs[id_article]'>$valeurs[titre]</option>";
}

if(isset($_POST['modif'])){
    $select_val = $_POST['select'];

    if($select_val == 'none'){
        $erreurDeux .= "<div class='erreur' style='margin-top:78px;'>Veuillez séléctionner un article !</div>";
    }
    else{

        $recupModifArticle = $pdo->prepare("SELECT * FROM article WHERE id_article = {$select_val}");
        $recupModifArticle->execute();
        $recupModifArticle->bindColumn('titre', $titreArticle, PDO::PARAM_STR);
        $recupModifArticle->bindColumn('contenu', $contenuArticle, PDO::PARAM_STR);
        $recupModifArticle->bindColumn('id_article', $idArticle);
        while($recupModifArticle->fetch()){

            $affichageA .= "<input type='text' name='titre' placeholder='Titre de l'article' value='{$titreArticle}'>
            <textarea placeholder='Ecrivez-ici ...' name='contenu'>{$contenuArticle}</textarea>
                        <div class='captcha-display'>
                <img src='../asset/img/captcha.php' alt=''>
                <input type='text' id='captcha' class='form-control' name='captcha'>
            </div>
            <input type='hidden' name='id_article' value='{$idArticle}' style='display:none'>
                        <div class='contenair-partage'>
                <input type='submit' name='modificationArticle' class='partager-seul' value='Envoyer' id='submit-modif'>
            </div>";

        }
    }
}

if(isset($_POST['modificationArticle']) and $_POST['modificationArticle'] == 'Envoyer'){
    extract($_POST);

    //   echo '<pre>';
    //    print_r($_POST);
    //    echo '</pre>';

    $modifTitre = $_POST['titre'];
    $modifContenu = $_POST['contenu'];
    $idArticle = $_POST['id_article'];



    if(empty($_POST['titre'])){
        $erreur .= "<div class='erreur'>Veuillez indiquer un titre à votre article.</div>";
    }
    else{
        if(strlen($_POST['titre']) > 200){
            $erreur .= "<div class='erreur'>Votre titre contient plus de 200 caractères.</div>";
        }
    }

    if(empty($_POST['contenu'])){
        $erreur .= "<div class='erreur'>Veuillez indiquer un message à votre article.</div>";
    }
    else{
        if(strlen($_POST['contenu']) < 10){
            $erreur .= "<div class='erreur'>Votre article doit contenir au moins 10 caractères.</div>";
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

        $sql = 'UPDATE article SET titre = "' . htmlspecialchars($modifTitre, ENT_QUOTES) . '", contenu = "' . htmlspecialchars($modifContenu, ENT_QUOTES) . '" WHERE id_article = "' . $idArticle . '"';

        $validModifArticle = $pdo->query($sql);


        $erreur .= "<div class='success'>Félicitation ! L'article à bien été modifié.</div>";

    }
}




?>

<section class="galerie">
    <div class="position-H1">
        <h1>Modifier un article</h1>
    </div>
    <?= $erreur;?>
    <form method="post" id="form-select-modif-article">
        <select name='select' id='select-titre'>
            <option value="none">Choisir un article</option>
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
