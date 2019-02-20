<?php
require 'asset/module/header.php';

// echo '<pre>';
// print_r($_GET);
// echo '</pre>';

$verifArticle = $pdo->prepare("SELECT id_article FROM article");
$verifArticle->execute();
$verifArticle->bindColumn('id_article', $idArticleVerif);
while($verifArticle->fetch())
{
    $arrayVerif[] = $idArticleVerif;
}

// echo '<pre>';
// print_r($arrayVerif);
// echo '</pre>';

if(isset($_GET['article']) AND in_array($_GET['article'], $arrayVerif)){

    $recupReadArticle = $pdo->prepare("SELECT id_article,titre,contenu,image FROM article WHERE id_article='{$_GET['article']}'");
    $recupReadArticle->execute();
    $recupReadArticle->bindColumn('image', $imageArt, PDO::PARAM_LOB);
    $recupReadArticle->bindColumn('contenu', $contenuArt, PDO::PARAM_STR);
    $recupReadArticle->bindColumn('titre', $titreArt, PDO::PARAM_STR);
    $recupReadArticle->bindColumn('id_article', $idArticle);
    while($recupReadArticle->fetch())
    {
        $affichageA .= "<div class='contenaire-actu'>
        <h1>{$titreArt}</h1>
    </div>
    <div class='content-ra'>
        <img src='asset/a_img/{$imageArt}' alt=''>
        <div class='texte-ra'>
            <div class='texte-ra-content'>" . str_replace(array("\r\n","\n"),'<br />',$contenuArt) . "</div>
        </div>
            </div>";

    }
}
else{        

    $erreurTitre = "<h1>Oops...</h1>";
    $erreur .= "<div class='erreur'>L'article que vous recherchez n'existe pas ou n'existe plus.</div>";
}



?>

<section class='read-art'>
<?= $erreurTitre; ?>
   <div class="affich-erreur"><?= $erreur; ?></div>
    
    <?= $affichageA; ?>


<a href="article" class="rpa">Retourner à la page article</a>

</section>








<?php
require 'asset/module/footer.php';