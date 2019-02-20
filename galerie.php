<?php
require 'asset/module/header.php';

if(isset($_POST['envoyer']) AND $_POST['envoyer'] == 'Envoyer')
{
    extract($_FILES);

    if(isset($_FILES['img'])){

        $img = $_FILES['img'];

        // echo "<pre>";
        // print_r($_FILES);
        // print_r($_POST);
        // echo "</pre>";

        // On récupère l'extension du fichier.
        // strtolower permet de mettre les extensions en minuscule.
        $extMin = strtolower(substr($img['name'],-3));

        // On stock dans un tableau les extensions autorisé.
        $verifExtension = array('jpg','png','gif','jpeg');

        // On récupère le type d'extension.
        $extension = strrchr($_FILES['img']['type'],'/');

        // On supprime le '/' lors de la récupération de l'extension.
        $extension = substr($extension,1);

        if(!in_array($extension,$verifExtension)){
            $erreur .= "<div class='erreur'>Fichier invalide.</div>";
        }      

        $extension = substr($extension,-3);

        if($_FILES['img']['size'] > 5000000){
            $erreur .= "<div class='erreur'>L'image est supérieur à 5Mo.</div>";
        }

        $photoName = time() . '_' . rand(0,9999) . $_FILES['img']['name'];
        $legende = $_POST['legende'];

        if(strlen($legende) > 250){
            $erreur .= "<div class='erreur'>La légende contient plus de 250 caractères.</div>";
        }
        else{
            if(empty($legende)){
                $erreur .= "<div class='erreur'>Veuillez indiquer une légende à votre image.</div>";
            }   
        }
    }
    else{

        $erreur .= "<div class='erreur'>Vous n'avez pas séléctionné d'image.</div>";
    }

    if(empty($erreur)){

        move_uploaded_file($_FILES['img']['tmp_name'],"./asset/g_img/".$photoName);

        $insertionGalerie = $pdo->prepare("INSERT INTO galerie(image,legende)VALUES(:image,:legende)");
        $insertionGalerie->execute([
            ':image'        => $photoName,
            ':legende'      => htmlspecialchars($legende,ENT_NOQUOTES)
        ]);

        $erreur .= "<div class='success'>Félicitation ! L'image à bien été publié.</div>";
    }
}









$affichage .= '<section><div class="block-center-gallerie">
<h1>Galerie</h1>
<div class="contenair-gallerie">
<div class="erreur-content">'.$erreur.'</div>';

$recupGalerie = $pdo->prepare("SELECT id_galerie,image,legende,date_format(date_publication,'%d/%m/%Y') AS 'dateFr' FROM galerie");
$recupGalerie->execute();
$recupGalerie->bindColumn('image', $image, PDO::PARAM_LOB);
$recupGalerie->bindColumn('legende', $legende, PDO::PARAM_STR);
$recupGalerie->bindColumn('dateFr', $datePubli);
$recupGalerie->bindColumn('id_galerie', $idGalerie);
while($recupGalerie->fetch())
{
    $affichage .= '<div class="block-image">
 <img src="asset/g_img/'.$image.'" alt="'.$legende.'" class="selected-image" onclick="openImg(this);"> ';

    if(isset($_SESSION['membre']) AND ($_SESSION['membre']['statut'] >= 1)){

        $affichage .= "<div class='croix-suppr-gallerie'>
        <a href='?page=galerie&action=suppr&id_galerie={$idGalerie}'>❌</a></div>";
    }
    
    $affichage .= "</div>";
}


// Suppression d'une image
$recupImageSuppr = $pdo->prepare('SELECT id_galerie FROM galerie');
$nbCol = $recupImageSuppr->columnCount(); // Je recupère le nombre de champs en BDD


if(isset($_GET['action']) AND $_GET['action'] == "suppr"){
    global $image;

    $pdo->query('DELETE FROM `galerie` WHERE id_galerie = "' . $_GET['id_galerie'] . '"');
    chmod("./asset/g_img/", 0755);
    unlink("./asset/g_img/".$image);


    $erreur .= "<div class='success'>Félicitation ! L'image à bien été supprimé.</div>";
}

if(isset($_SESSION['membre']) AND ($_SESSION['membre']['statut'] >= 1)){

    $affichage .= '<form method="post" enctype="multipart/form-data">
    <input type="file" name="img">
    <input type="text" name="legende" class="input-galerie" placeholder="Legende de l\'image">
    <input type="submit" name="envoyer" class="partager" value="Envoyer">
</form>';
}
$affichage .= '</div></div></section>';
?>


<div class="modal" id="jquery-select-modal">
    <div class="modal-content" id="select-content" onclick="this.parentElement.style.display='none'">
        <div class="lock-button">
            <i class="far fa-times-circle close-button"></i>
        </div>
        <div class="image-galerie">
            <img id="expandedImg" onclick='openImg(this);' alt="" src="./">
        </div>
        <hr>
        <div class="legende-image-galerie">
            <div id="imgtext"></div>
        </div>
    </div>
</div>


<?php

echo $affichage;
require 'asset/module/footer.php';
