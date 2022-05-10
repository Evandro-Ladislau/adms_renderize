<?php
if(!isset($seguranca)){
    exit;
}

function caracterEspecial($nome_imagem){
   //Substituir os caracteres especiais
   $original = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:,\\\'<>°ºª';
   $substituir = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                ';

   $nome_imagem_es = strtr(utf8_decode($nome_imagem), utf8_decode($original), $substituir);

   //Substituir o espaco em branco pelo traco
   $nome_imagem_br = str_replace(' ', '-', $nome_imagem_es);

   $nome_imagem_tr = str_replace(array('----', '---', '--'), '-', $nome_imagem_br);

   //Converter para minusculo
   $nome_imagem_mi = strtolower($nome_imagem_tr);

   return $nome_imagem_mi;

}