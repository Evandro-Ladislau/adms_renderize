<?php
if (!isset($seguranca)) {
    exit;
}
//Esse arquivo destroi a sessão e desloga o usuario, sendo necessario informar o usuario e senha novamente
//para acessar o site.
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

unset($_SESSION['id'], $_SESSION['nome'],  $_SESSION['email'], $_SESSION['adms_niveis_acesso_id'], $_SESSION['ordem']);
$_SESSION['msg'] = "<div class='alert alert-danger'> Deslogado com Sucesso! </div>";
$url_destino = pg . '/acesso/login';
header("Location: $url_destino");
