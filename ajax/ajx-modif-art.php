<?php
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
                <img src='asset/img/captcha.php'>
                <input type='captcha' id='captcha' class='form-control' name='captcha' placeholder='Entrer le Captcha'>
            </div>
            <input type='hidden' name='id_article' value='{$idArticle}' style='display:none'>
                        <div class='contenair-partage'>
                <input type='submit' name='modificationArticle' class='partager-seul' value='Envoyer' id='submit-modif'>
            </div>";

        }
    }
}
?>