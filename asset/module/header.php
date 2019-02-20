<?php

$adresse = $_SERVER['PHP_SELF'];
$search = '/admin/';
$hta = '/error/';
$matches = array();

if(preg_match($search, $adresse, $matches)){
    require '../inc/init.inc.php';
    $css = '../';
}
else{
    if(preg_match($hta, $adresse, $matches)){
        require '../inc/init.inc.php';
        $css = '../';
        $linkCss = "<link rel='stylesheet' href='{$css}asset/css/errors.css'>";
    }
    else{
        require 'inc/init.inc.php';
        $css = './';
    }
}

if($_GET){

    if(isset($_GET['page']) AND ($_GET['page'] == 'index')){
        header('location:' . URL);
    }

    if(isset($_GET['page']) AND file_exists($_GET['page'] . '.php')){
        header('location:' .  $_GET['page']);
    }

    else if(isset($_GET['page']) AND file_exists('admin/' . $_GET['page'] . '.php')){
        header('admin/' . $_GET['pageAdmin'] . '.php');    
    }


    else{
        //Si la page n'existe pas, un message apparaît : 
        $affichage .= "<div class='erreur-access'> La demande n'a pas pu aboutir</div>";
    }
}

// Formulaire de déconnexion

if(isset($_GET['action']) AND ($_GET['action'] == "deconnexion")){
    session_destroy();
    header('location:' . URL);
}

?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="description" content="Créateur d'événements Technos à Paris et sa banlieue dans des lieux atypiques. 
                                          BNK vous propose des événements Technos de qualité avec artistes confirmés.">
        <meta name="robots" content="index, follow">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta property="og:locale" content="fr_FR">
        <meta property="og:type" content="website">
        <meta property="og:title" content="BNK | Créateur d'événements Techno à Paris et sa banlieue">
        <meta property="og:url" content="https://www.bnkcommunity.com/">
        <meta property="og:site_name" content="BNK | Créateur d'événements Techno à Paris et sa banlieue">
        <meta property="og:image" content="https://www.bnkcommunity.com/asset/img/bnk-soiree-techno.png">
        <link rel="stylesheet" href="<?=$css?>asset/css/style-zw.css">
        <link rel="stylesheet" href="<?=$css?>asset/css/scroll-barb.css">
        <link rel="stylesheet" href="<?=$css?>asset/css/font-a.css">
        <link rel="stylesheet" href="<?=$css?>asset/css/qsn-o.css">
        <link rel="stylesheet" href="<?=$css?>asset/css/articles-p.css">
        <link rel="stylesheet" href="<?=$css?>asset/css/article-i.css">
        <link rel="stylesheet" href="<?=$css?>asset/css/jquery-ui.css">
        <?= $linkCss; ?>
        <link rel="shortcut icon" href="<?=$css?>asset/module/bnk_fav.png" type="image/png">
        <title>BNK | Créateur d'événements Techno à Paris et sa banlieue</title>
    </head>
    <body>
        <noscript>Veuillez activer le Javascript de votre navigateur !
        </noscript>
        <header>
            <div class="content-header">
                <div id="burger">
                    <div id="trait1"></div>
                    <div id="trait2"></div>
                    <div id="trait3"></div>
                </div>
                <div class="bnk-header"><a href="<?= URL ?>#accueil">BNK</a></div>
            </div>


            <nav>
                <ul class="navigation-left">
                    <li><a class='burger-nav' href="<?= URL ?>#accueil">Accueil</a></li>
                    <li><a class='burger-nav' href="<?= URL ?>#actualite">Actualité</a></li>
                    <li><a class='burger-nav' href="<?= URL ?>#evenement">Nos événements</a></li>
                    <li><a class='burger-nav' href="<?= URL ?>#qui-sommes-nous">Qui sommes nous ?</a></li>
                    <li><a class='burger-nav' href="<?= URL ?>?page=contact">Nous contacter</a></li>
                    <li><a class='burger-nav' href="<?= URL ?>?page=galerie">Galerie</a></li>
                    <?php 
    // S'il s'agit d'un admin

    // intérieur d'un <li> on met un <ul> pour les sous menus
    // Quand on clique il s'affiche, sinon display none;
    if(isset($_SESSION['membre']) AND ($_SESSION['membre']['statut'] >= 1)) : ?>
                    <li><a class='burger-nav' href="<?= URL ?>?page=admin/admin_office">Back Office</a></li>
                    <?php endif ; ?>
                    <?php
                    if(isset($_SESSION['membre'])) : ?>
                    <li><a class='burger-nav' href="<?= URL ?>?action=deconnexion">Déconnexion</a></li>
                    <?php endif ; ?>
                </ul>
            </nav>
        </header>