<?php
if(!isset($seguranca)){
    exit;
}

function vazio($dados){
   $dados_st = array_map('strip_tags', $dados);
   $dados_tr = array_map('trim', $dados_st);

   //se tiver algum campo vazio ele retorna falso.
   if(in_array('', $dados_tr)){
    return false;
   }else{
       //caso contrário ele retorna o array ja passado o strip_tags que retira as tags
       // e o trim que tira o espaço em branco no inicio e no final do campo
       return $dados_tr;
   }
}