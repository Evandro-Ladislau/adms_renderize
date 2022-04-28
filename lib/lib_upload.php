<?php
if(!isset($seguranca)){
    exit;
}

function upload($foto, $destino, $largura, $altura){
    mkdir($destino, 0755);
    switch($foto['type']){
        case 'image/png';
        case 'image/x-png';
            $imagem_temporaria = imagecreatefrompng($foto['tmp_name']);

             $img_redimensionada =  redimensionarImg($imagem_temporaria, $largura, $altura);

             imagepng($img_redimensionada, $destino. $foto['name']);
           break;
        case 'image/jpeg';

        case 'image/pjpeg';
            $imagem_temporaria = imagecreatefromjpeg($foto['tmp_name']);

             $img_redimensionada =  redimensionarImg($imagem_temporaria, $largura, $altura);

             imagejpeg($img_redimensionada, $destino. $foto['name']);
           break;
    }
   
}

function redimensionarImg($imagem_temporaria, $largura, $altura){
    $largura_original = imagesx($imagem_temporaria);
    $altura_original = imagesy($imagem_temporaria);

    $nova_largura = $largura ? $largura : floor (($largura_original / $altura_original) * $altura );
    
    $nova_altura = $altura ? $altura : floor(($altura_original / $largura_original) * $largura);

    $imagem_redimensionada = imagecreatetruecolor($nova_largura, $nova_altura);

    imagecopyresampled($imagem_redimensionada, $imagem_temporaria, 0, 0, 0, 0, $nova_largura, $nova_altura, $largura_original, $altura_original);

    return $imagem_redimensionada;

}