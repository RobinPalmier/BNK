<?php
ob_start();
require 'asset/module/header.php';

$recupArticle = $pdo->prepare("SELECT id_article,titre,contenu,image FROM article ORDER BY `article`.`id_article` DESC");
$recupArticle->execute();
$recupArticle->bindColumn('image', $images, PDO::PARAM_LOB);
$recupArticle->bindColumn('contenu', $contenu, PDO::PARAM_STR);
$recupArticle->bindColumn('titre', $titre, PDO::PARAM_STR);
$recupArticle->bindColumn('id_article', $idArticle);
$i = 0;
while($recupArticle->fetch())
{
    $affichageA .=  "<div class='contenu-article-page'>
            <img src='./asset/a_img/{$images}' alt='' class='img-actu-page'>
            <div class='contenaire-text-page'>
                <h2 class='titre-actu-page'>{$titre}</h2>
                <h3 class='text-actu-page'>";
    $affichageA .= str_replace(array("\r\n","\n"),'<br />',rogneArticle($contenu,550));


    $affichageA .= "</h3>
    ";

    if(isset($_SESSION['membre']) AND ($_SESSION['membre']['statut'] >= 1)){

        $affichageA .= "<div class='croix-suppr-article-page'>
        <a href='?page=admin&action=suppr&id_article={$idArticle}'>❌</a>
        </div>";
    }


    $affichageA .= "<a href='read-art?article={$idArticle}' class='show-article-page'>Afficher l'article</a>";

    $affichageA .= "</div>";
    $affichageA .= "</div>";

    $i++;
}


if(isset($_GET['action']) AND $_GET['action'] == "suppr"){

    global $images;

    $pdo->query('DELETE FROM `article` WHERE id_article = "' . $_GET['id_article'] . '"');

    chmod("./asset/a_img/", 0755);
    unlink("./asset/a_img/".$images);

    $erreur .= "<div class='success'>L'article à bien été supprimé !</div>";

    header('refresh:5;url=article');
}

?>

<section id="actu">
    <div class="contenaire-actu">
        <h1>Actualité</h1>
        <?= $erreur; ?>

        <?= $affichageA; ?>
    </div>
</section>

<?php
require 'asset/module/footer.php';
