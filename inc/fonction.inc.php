<?php

function debug($param,$mode = 1)
{
    if($mode == 1)
    {
        echo '<pre>';
        print_r($param);
        echo '</pre>';
    }
    else
    {
        echo '<pre>';
        var_dump($param);
        echo '</pre>';
    }
}

// Miniature galerie

class Img{

    static function creerMin($img,$chemin,$nom,$mlargeur=100,$mhauteur=100){
        // On supprime l'extension du nom
        $nom = substr($nom,0,-4);
        // On récupère les dimensions de l'image
        $dimension=getimagesize($img);
        // On cré une image à partir du fichier récup
        if(substr(strtolower($img),-4)==".jpg"){$image = imagecreatefromjpeg($img); }
        else if(substr(strtolower($img),-4)==".png"){$image = imagecreatefrompng($img); }
        else if(substr(strtolower($img),-4)==".gif"){$image = imagecreatefromgif($img); }
        // L'image ne peut etre redimensionne
        else{return false; }

        // Création des miniatures
        // On cré une image vide de la largeur et hauteur voulue
        $miniature =imagecreatetruecolor ($mlargeur,$mhauteur); 
        // On va gérer la position et le redimensionnement de la grande image
        if($dimension[0]>($mlargeur/$mhauteur)*$dimension[1] ){ $dimY=$mhauteur; $dimX=$mhauteur*$dimension[0]/$dimension[1]; $decalX=-($dimX-$mlargeur)/2; $decalY=0;}
        if($dimension[0]<($mlargeur/$mhauteur)*$dimension[1]){ $dimX=$mlargeur; $dimY=$mlargeur*$dimension[1]/$dimension[0]; $decalY=-($dimY-$mhauteur)/2; $decalX=0;}
        if($dimension[0]==($mlargeur/$mhauteur)*$dimension[1]){ $dimX=$mlargeur; $dimY=$mhauteur; $decalX=0; $decalY=0;}
        // on modifie l'image crée en y plaçant la grande image redimensionné et décalée
        imagecopyresampled($miniature,$image,$decalX,$decalY,0,0,$dimX,$dimY,$dimension[0],$dimension[1]);
        // On sauvegarde le tout
        imagejpeg($miniature,$chemin."/".$nom.".jpg",90);
        return true;
    }

    // Conversion en jpg

    static function convertirJPG($img){

        // On supprime l'extension du nom
        $nom = substr($nom,0,-4);
        // On récupère les dimensions de l'image
        $dimension=getimagesize($img);
        // On cré une image à partir du fichier récup
        if(substr(strtolower($img),-4)==".jpg"){$image = imagecreatefromjpeg($img); }
        else if(substr(strtolower($img),-4)==".png"){$image = imagecreatefrompng($img); }
        else if(substr(strtolower($img),-4)==".gif"){$image = imagecreatefromgif($img); }
        // L'image ne peut etre redimensionne
        else{return false; }
        unlink($img);
        imagejpeg($image,$chemin.substr($img,0,-3)."jpg",90);
        return true;
    }
}


function token(){
    global $affichage;
    $token = "";

    $maj = range('A','Z');
    $min = range('a','z');
    $num = range(0,50);
    $fusion = array_merge($maj,$min,$num); 
    shuffle($fusion);
    foreach($fusion as $valeurs)
    {
        $token .= $valeurs;
    }
    return $token;
}


function rogneArticle($texte,$n)
{
    if (strlen($texte) > $n)
    {    
        $texte = substr($texte, 0, $n);    
        $position_espace = strrpos($texte, " ");    
        $texte = substr($texte, 0, $position_espace);    
        $texte .= '...';
        return $texte;
    }
    else return $texte;
}


function random($car) {
    global $string;
    $string = "";
    $chaine = "abcdefghijklmnpqrstuvwxy0123456789";
    srand((double)microtime()*1000000);
    for($i=0; $i<$car; $i++) {
        $string .= $chaine[rand()%strlen($chaine)];
    }
}
$chaine = random(12);

function getURI(){
    $adresse = $_SERVER['PHP_SELF'];
    $i = 0;
    foreach($_GET as $cle => $valeur){
        $adresse .= ($i == 0 ? '?' : '&').$cle.($valeur ? '='.$valeur : '');
        $i++;
    }
    return $adresse;
}