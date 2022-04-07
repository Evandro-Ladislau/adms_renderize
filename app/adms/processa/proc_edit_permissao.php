<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

$SendEditPermissao = filter_input(INPUT_POST, 'SendEditPermissao', FILTER_SANITIZE_STRING);
if ($SendEditPermissao) {
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    //Retirar campo da validação vazio

    $dados_icone = $dados['icone'];
    unset($dados['icone']);

    //validar nenhum campo vazio
    $erro = false;
    include_once 'lib/lib_vazio.php';

    $dados_validos = vazio($dados);

    if (!$dados_validos) {
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'>Necessário preencher todos os campos com <b>*</b> para editar permissão!</div>";
    }

    //Houve erro em algum campo será redirecionado para login, não há erro no formuário tenta cadastrar no banco

    if ($erro) {
        $url_destino = pg . '/editar/edit_permissao?id=' . $dados['id'];
        header("Location: $url_destino");
    } else {
        //Alterar o menu
        $result_nivpg_up = $pdo->AlterMenu($dados_validos['adms_menu_id'], $dados_validos['id']);

        if ($result_nivpg_up) {
            //Pesquisar o ID da página e do nível de acesso
            $resultado_nivpg = $pdo->PesquisarIdNivelAc($dados_validos['id']);
            if ($resultado_nivpg) {
                foreach ($resultado_nivpg as $key => $value) {
                    $vetor_resultado_nivpg = $value;
                }

                //ALTERAR O ICONE
                $result_pg_up = $pdo->AlterarIcone($dados_icone, $vetor_resultado_nivpg['adms_pagina_id']);

                //VERIFICAR SE FOI ALTERADO COM SUCESSO OU NÃO
                if ($result_pg_up) {
                    $_SESSION['msg'] = "<div class='alert alert-danger'> ícone Editado com sucesso! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    <span aria-hidden='true'>&times;</span>
</button></div>";
$url_destino = pg . '/editar/edit_permissao?id='.$vetor_resultado_nivpg['adms_niveis_acesso_id'];
            header("Location: $url_destino");
                }else{
                    $_SESSION['msg'] = "<div class='alert alert-danger'> Erro ao editar a permissão. Erro no campo ícone! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    <span aria-hidden='true'>&times;</span>
</button></div>";
            $url_destino = pg . '/editar/edit_permissao?id='.$dados['id'];
            header("Location: $url_destino");
                }

            }else{
                $_SESSION['msg'] = "<div class='alert alert-danger'> Erro ao editar a permissão. Erro no campo ícone! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    <span aria-hidden='true'>&times;</span>
</button></div>";
            $url_destino = pg . '/editar/edit_permissao?id='.$dados['id'];
            header("Location: $url_destino");
            }
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> Erro ao editar a permissão. Erro no campo menu! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    <span aria-hidden='true'>&times;</span>
</button></div>";
            $url_destino = pg . '/editar/edit_permissao?id='.$dados['id'];
            header("Location: $url_destino");
        }
    }
} else {
    $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    <span aria-hidden='true'>&times;</span>
</button></div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
