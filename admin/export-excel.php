<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="Liste_Abonne_Newsletter.csv"');
try{
    $pdoCSV = new PDO('mysql:host=db736416818.db.1and1.com;dbname=db736416818','dbo736416818','Robin123$');
    $pdoCSV->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
    $pdoCSV->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_OBJ);
}catch(PDOException $e){
    echo 'Connexion impossible';
}

$req = $pdoCSV->prepare('SELECT nom,prenom,email FROM newsletter');
$req->execute();
$data = $req->fetchAll();

//echo '<pre>';
//print_r($data);
//echo '</pre>';

?>"Nom";"Prenom";"Adresse e-mail"<?php


foreach($data as $d){
    echo "\n{$d->nom};{$d->prenom};{$d->email};";
}?>