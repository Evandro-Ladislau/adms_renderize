<?php
if(!isset($seguranca)){
    exit;
}

function vazio($dados){
   $dados_st = array_map('strip_tags', $dados);
   $dados_tr = array_map('trim', $dados_st);
   if(in_array('', $dados_tr)){
    return false;
   }else{
       return $dados_tr;
   }
}