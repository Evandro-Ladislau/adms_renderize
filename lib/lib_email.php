<?php
if(!isset($seguranca)){
    exit;
}

function validarEmail($email){
   if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    }
    else {
        return false;
    }
}