<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

$SendCadMenu = filter_input(INPUT_POST, 'SendCadMenu', FILTER_SANITIZE_STRING);
if ($SendCadMenu) {
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    //var_dump($dados);
    //validar nenhum campo vazio
    $erro = false;
    include_once 'lib/lib_vazio.php';
    $dados_validos = vazio($dados);

    if (!$dados_validos) {
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'> Necessário preencher todos os campos para cadastrar o menu!</div>";
    }

    //Se houver erro em algum campo será redirecionado para o login
    //mão há erro no formulário tenta cadastrar no banco
    if ($erro) {
        $url_destino = pg . '/cadastrar/cad_menu';
        header("Location: $url_destino");
    } else {
        
        //Pesquiar o maior número da ordem na tabela adms_menus
        $result_maior_ordem = $pdo->MaiorOrdemMenu();
        $ordem = count($result_maior_ordem) + 1;

        $result_cad_menu = 0;
        $result_cad_menu = $pdo->CadastrarMenu($dados_validos['nome'], $dados_validos['icone'], $ordem, $dados_validos['adms_sit_id']);


        //se cadastrar no banco de dados acessa esse if
        if ($result_cad_menu == 1) {
            $_SESSION['msg'] = "<div class='alert alert-success'> Menu inserido com sucesso! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
        </button> </div>";
            $url_destino = pg . '/listar/list_menu';
            header("Location: $url_destino");
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> Erro ao inserir o menu! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
        </button></div>";
            $url_destino = pg . '/cadastrar/cad_menu';
            header("Location: $url_destino");
        }
    }
} else {
    //variavel global para criar uma mensagem de alerta.
    $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    <span aria-hidden='true'>&times;</span>
</button></div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
