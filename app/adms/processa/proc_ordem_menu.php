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
       $result_niv_ac_acoes = $pdo->pesquisarAcessoPaginaAcoesADM($id);
    } else {
        //quando não for administrador cai nesse else
        //Pesquisar os dados da tabela adms_nivacs_pgs
        $result_niv_ac_acoes = $pdo->pesquisarAcessoPaginaAcoes($id, $_SESSION['ordem']);
    }

    //Verificar se retornou algum valor do banco de dados. IF se sim, Else se não.
    if ($result_niv_ac_acoes) {
        //passar os valores da matriz para o vetor
        foreach ($result_niv_ac_acoes as $key => $value) {
            $vetor_result_niv_ac_acoes = $value;
        }
        //pesquisar o ID do adms_nivacs_pgs a ser movido para baixo
        $ordem_num_menor = $vetor_result_niv_ac_acoes['ordem'] - 1;
        $result_niv_num_men = $pdo->pesquisarIdParaSerMovido($ordem_num_menor, $vetor_result_niv_ac_acoes['adms_niveis_acesso_id'] ); 
        foreach ($result_niv_num_men as $key => $value) {
            $vetor_result_niv_num_men = $value;
        }
        //var_dump($result_niv_num_men);

        //alterar a ordem do numero menor para o maior
        $resultado_inserir_num_maior = $pdo->alterarOrdemMenorParaMaior($vetor_result_niv_ac_acoes['ordem'], $vetor_result_niv_num_men['id']);

        //alterar a ordem do número maior para o número menor
        $resultado_inserir_num_menor = $pdo->alterarOrdemMaiorParaMenor($ordem_num_menor, $vetor_result_niv_ac_acoes['id']);

        //redirecionar conforme a situação do alterar: sucesso ou erro.


        if ($resultado_inserir_num_menor) {
            $_SESSION['msg'] = "<div class='alert alert-success'> Ordem do menu editado com sucesso!</div>";
            $url_destino = pg . '/listar/list_permissao?id='. $vetor_result_niv_ac_acoes['adms_niveis_acesso_id'];
            header("Location: $url_destino");
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> Erro: A ordem do menu não foi alterada com sucesso! </div>";
            $url_destino = pg . '/listar/list_permissao?id='. $vetor_result_niv_ac_acoes['adms_niveis_acesso_id'];
            header("Location: $url_destino");
        }
        

    
        
    } else {
        $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! oi</div>";
        $url_destino = pg . '/listar/list_niv_aces';
        header("Location: $url_destino");
    }
} else {

    $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada!</div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
