<?php
if (!isset($seguranca)) {
    exit;
}

require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//esse id é resgatado ao clicar no botão pela url
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!empty($id)) {
    //Apagar a página
    $result_apagar_pagina = $pdo->apagarPagina($id);
    if ($result_apagar_pagina) {
        //APAGAR AS PERMISSÕES DE ACESSO A PÁGINA NA TABELA adms_nivacs_pgs
        $result_apagar_permissao_Nivacs = $pdo->apagarNivAcessoPagina($id);

        $_SESSION['msg'] = "<div class='alert alert-success'> Pagina apagada com sucesso! </div>";
        $url_destino = pg . '/listar/list_pagina';
        header("Location: $url_destino");
    } else {
        $_SESSION['msg'] = "<div class='alert alert-danger'> ERRO: A página não foi apagada! </div>";
        $url_destino = pg . '/listar/list_pagina';
        header("Location: $url_destino");
    }
} else {
    $_SESSION['msg'] = "<div class='alert alert-danger'> Pagina não encontrada! </div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
