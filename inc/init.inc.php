<?php
/* session : */
session_start();
date_default_timezone_set ("Europe/Berlin");
//echo "<pre>";
//print_r($_SESSION);
//echo "</pre>";

//echo phpinfo() <- Affiche la version du PHP

// Sert à afficher toutes les erreurs. A commenter en prod.
 //error_reporting(E_ALL);

//Forcer l'affichage des erreurs. Important pour contourner la configuration du fichier php.ini
ini_set('display_errors', 'Off'); 


/* Connexion à la BDD : */

// Configuration de base :
$bddOptions = array(
    PDO::MYSQL_ATTR_INIT_COMMAND    => "SET NAMES utf8", // On force l'encodage en utf8.
    PDO::ATTR_DEFAULT_FETCH_MODE     => PDO::FETCH_ASSOC, // On récupère les résultats sous forme de tableau associatif.
    PDO::ATTR_ERRMODE               => PDO::ERRMODE_WARNING // On affiche les erreurs de type warning. Cette instruction sera à commenter en prod (mise en ligne).
);

// Le champs de droite est à modifé !
define('TYPEBDD','mysql'); // Type de la base de donné. (à modifier : 'mysql').
define('HOST', 'localhost'); // Domaine du serveur.
define('USER','root'); // Nom de l'utilisateur.
define('PASSWORD',''); // Mot de passe.
define('DBNAME','bnk'); // Nom de la base de donné.

try{ // On essai de se connecter à la base de donné.
    $pdo = new PDO(TYPEBDD . ':host=' . HOST . ';dbname=' . DBNAME,USER,PASSWORD,$bddOptions);
}
catch(Exception $e){ // Sinon on fait ce code.
    die('Error BDD : ' . $e ->getMessage()); // Indique une erreur si on ne peut pas se connecter à la base de donné.
}



/* Constantes : */ 
// echo '<pre>';
// print_r($_SERVER);
// echo '</pre>';

// echo __DIR__; //Afficher la localisation du fichier.

define('URL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']);  
// Adapte le HTTP de façon automatique. 
/* '/PHP/site_dynamique/' <- A commenter lors de la mise en ligne.*/


/* Variable d'affichage : */
$affichage = "";
$affichageA = "";
$affichageE = "";
$affichageB = "";
$erreur = "";
$erreurNl = "";
$erreurDeux = "";


/* fonctions : */
require 'fonction.inc.php';




?>