<?php

require '../inc/init.inc.php';


if(isset($_GET['action']) AND $_GET['action'] == "suppr"){

    $pdo->query('DELETE FROM `article` WHERE id_article = "' . $_GET['id_article'] . '"');

    chmod("./asset/a_img/", 0755);
    unlink("./asset/a_img/".$images);


    $erreur .= "<div class='success'>L'article à bien été supprimé !</div>";

}

echo '<h1>Actualité</h1>
        <div id="container-alert"></div>';

echo $erreur;

$recupArticle = $pdo->prepare("SELECT id_article,titre,contenu,image FROM article ORDER BY `article`.`id_article` DESC");
$recupArticle->execute();
$recupArticle->bindColumn('image', $images, PDO::PARAM_LOB);
$recupArticle->bindColumn('contenu', $contenu, PDO::PARAM_STR);
$recupArticle->bindColumn('titre', $titre, PDO::PARAM_STR);
$recupArticle->bindColumn('id_article', $idArticle);
$i = 0;
while($recupArticle->fetch())
{
    echo "<div class='contenu-article'>
            <img src='./asset/a_img/{$images}' 'alt='' class='img-actu'>
            <div class='contenaire-text'>
                <div class='titre-actu'>{$titre}</div>
                <div class='text-actu'>";
    echo rogneArticle($contenu,550);


    echo "</div>";

    if(isset($_SESSION['membre']) AND ($_SESSION['membre']['statut'] >= 1)){

        echo "<div class='croix-suppr-article'>
        <a href='' supp=".$idArticle." class='croix'>❌</a> 

        </div>";
    }

    // ?page=admin&action=suppr&id_article={$idArticle}

    echo "</div>";
    echo "</div>";

    $i++;


    if($i > 3){

        echo "<a href='?page=article' ><div class='aff-plus-art'>Cliquez ici pour afficher plus d'article</div></a>";

        break;
    }
}


