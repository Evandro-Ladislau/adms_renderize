<?php
if(!isset($seguranca)){
    exit;
}

function validarExtensao($foto){

    switch($foto){
        case 'image/png';
        case 'image/x-pgn';
            return true;
        case 'image/jpeg';
        case 'image/pjpeg';   
            return true; 
            break;
        default:
            return false; 
    }
   
}
