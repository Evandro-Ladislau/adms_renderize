<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//receber o valor do campo input com nome SendCadPg do formulario no arquivo cad_pagina
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

//se essa variavel tiver valor significa que o usuario clicou no botão então ele entra nesse if
if (!empty($id)) {
    //quando for administrado cai nesse if
    if ($_SESSION['adms_niveis_acesso_id'] == 1) {
        //Pesquisar os dados da tabela adms_nivacs_pgs
        $result_niv_ac_pg = $pdo->PesuisaDadosNiveisAcessoPaginasADM($id);
        var_dump($result_niv_ac_pg);
    } else {
        //quando não for administrador cai nesse else
        //Pesquisar os dados da tabela adms_nivacs_pgs
        $result_niv_ac_pg = $pdo->PesuisaDadosNiveisAcessoPaginas($id, $_SESSION['ordem']);
    }

    //Verificar se retornou algum valor do banco de dados. IF se sim, Else se não.
    if ($result_niv_ac_pg) {

        foreach ($result_niv_ac_pg as $key => $value) {
            $veter_result_niv_ac_pg = $value;
        }

        if ($veter_result_niv_ac_pg['lib_menu'] == 1) {
            $status = 2;
        } else {
            $status = 1;
        }

        $result_niv_pg_update = $pdo->atualizarLiberarMenu($status, $id);

        if ($result_niv_pg_update) {
            $_SESSION['msg'] = "<div class='alert alert-success'> Situação do menu editado com sucesso!</div>";
            $url_destino = pg . '/listar/list_permissao?id='. $veter_result_niv_ac_pg['adms_niveis_acesso_id'];
            header("Location: $url_destino");
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> Erro: A situação do menu não foi alterada! </div>";
            $url_destino = pg . '/listar/list_permissao?id='. $veter_result_niv_ac_pg['adms_niveis_acesso_id'];
            header("Location: $url_destino");
        }
    } else {
        $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada!</div>";
        $url_destino = pg . '/listar/list_niv_aces';
        header("Location: $url_destino");
    }
} else {

    $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada!</div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
