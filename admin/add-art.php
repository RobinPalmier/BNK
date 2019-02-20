<?php
ob_start();
require '../asset/module/header.php';

if($_SESSION['membre'] == '')
{
    header('location:' . URL . '/error/403');
}
if(isset($_POST['ajouter']) and $_POST['ajouter'] == 'Envoyer'){

    extract($_POST);

    // echo '<pre>';
    //  print_r($_POST);
    //  print_r($_FILES);
    //  echo '</pre>';

    if(isset($_FILES['image']) AND $_FILES['image']['error'] == 3){

        $erreur .= "<div class='erreur'>Le fichier n'a été que partiellement téléchargé.</div>";

    }
    elseif($_FILES['image']['error'] == 4){

        $erreur .= "<div class='erreur'>Aucun fichier n'a été téléchargé.</div>";
    }
    else{

        // On récupère l'extension du fichier.
        // strtolower permet de mettre les extensions en minuscule.
        $extMin = strtolower(substr($_FILES['image']['name'],-3));

        // On stock dans un tableau les extensions autorisé.
        $verifExtension = array('jpg','png','gif','jpeg');

        // On récupère le type d'extension.
        $extension = strrchr($_FILES['image']['type'],'/');

        // On supprime le '/' lors de la récupération de l'extension.
        $extension = substr($extension,1);

        if(!in_array($extension,$verifExtension)){
            $erreur .= "<div class='erreur'>Fichier invalide.</div>";
        }      

        $extension = substr($extension,-3);

        if($_FILES['image']['size'] > 5000000){
            $erreur .= "<div class='erreur'>L'image est supérieur à 5Mo.</div>";
        }

        $photoArticle = time() . '_' . rand(0,9999) . $_FILES['image']['name'];
    }

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

        move_uploaded_file($_FILES['image']['tmp_name'],"../asset/a_img/".$photoArticle);

        $insertionArticle = $pdo->prepare("INSERT INTO article(titre,contenu,image)VALUES(:titre,:contenu,:image)");
        $insertionArticle->execute([
            ':titre'        => htmlspecialchars($_POST['titre'], ENT_QUOTES),
            ':contenu'      => htmlspecialchars($_POST['contenu'], ENT_QUOTES),
            ':image'        => htmlspecialchars($photoArticle, ENT_QUOTES)
        ]);

        $erreur .= "<div class='success'>Félicitation ! L'image à bien été publié.</div>";
    }
}


/*if(isset($_POST['ajouter']) AND $_POST['ajouter'] == 'Envoyer'){
    extract($_POST);
    extract($_FILES);

    echo '<pre>';
    print_r($_FILES);
    echo '</pre>';



}

*/


?>

<section class="galerie">
    <div class="position-H1">
        <h1>Ajouter un article</h1>
    </div>

    <?= $erreur;?>
    <div class='g-article'>
        <form method='post' enctype='multipart/form-data'>
            <input type="text" name="titre" placeholder="Titre de l'article">
            <textarea placeholder='Ecrivez-ici ...' name='contenu'></textarea>
            <div class='captcha-display'>
                <img src='../asset/img/captcha.php' alt=''>
                <input type='text' id='captcha' class='form-control' name='captcha'>
            </div>
            <input type="file" id="image" name="image" class="input-file">
            <div class='contenair-partage'>
                <input type='submit' name='ajouter' class='partager-seul' value='Envoyer'>
            </div>
        </form>
        <a href="https://www.bnkcommunity.com/admin/admin_office" class="return-admin-office">Retour au back-office</a>
    </div>
</section>


<?php
require '../asset/module/footer.php';