<?php
error_reporting(E_ALL ^ E_NOTICE);
$random_number='WORKS';

$im = @imagecreate(200, 50) or die("Cannot Initialize new GD image stream, this will NOT work!");
$background_color = imagecolorallocate($im, 255, 0, 0);
$text_color = imagecolorallocate($im, 255,255, 255);

for ($i=0;$i<5;$i++)
{
    $display = substr($random_number,$i,1);
    $x = ($i*40) + rand(10,20);
    $y = rand(15,30);
    imagestring($im, 5, $x, $y, $display, $text_color);
}

for ($i=1;$i<100;$i++)
{
    $cor_x = rand(1,200);
    $cor_y = rand(1,50);
    imagesetpixel($im,$cor_x,$cor_y,$text_color);
}

header("Content-type: image/jpeg");
imagejpeg($im);
imagedestroy($im);

?>