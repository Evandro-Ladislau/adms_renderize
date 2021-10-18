<?php
if (!isset($seguranca)) {
    exit;
}

$url_host = filter_input(INPUT_SERVER, 'HTTP_HOST');
define('pg', "http://$url_host/adm");

// se caso o servidor nao suportar o HTTP_HOST usar dessa forma define('pg', "http://meudominio.com.br/adm");