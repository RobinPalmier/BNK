<?php
session_start();

//Stockage de la valeur du captcha
$captcha = rand(100,9999);
$_SESSION['captcha'] = $captcha;
extract($_SESSION);
//print_r($captcha);

//creation de l'image:
//#imagecreatetruecolor() : attend deux argument : la largeur & la hauteur
$dimensionImage = imagecreatetruecolor(50,25);

#imagecolorallocate(): 4 argument : l'image dans laquelle je travaille, la valeur du composant rouge, vert, bleu
$background = imagecolorallocate($dimensionImage,255,255,255);
$texteColor = imagecolorallocate($dimensionImage,229,35,41);

#imagefill() :
imagefill($dimensionImage,0,0,$background);

#imagestring() :
imagestring($dimensionImage,20,5,5,$captcha,$texteColor);
header("Content-type: image/png");
imagepng($dimensionImage);
imagedestroy($dimensionImage);

?>