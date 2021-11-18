<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

$sendCadNivAc = filter_input(INPUT_POST, 'SendCadNivAc', FILTER_SANITIZE_STRING);
if ($sendCadNivAc) {
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    //var_dump($dados);
    //validar nenhum campo vazio
    $erro = false;
    include_once 'lib/lib_vazio.php';
    $dados_validos = vazio($dados);

    if (!$dados_validos) {
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'> Necessário preencher todos os campos para cadastrar nível de acesso!</div>";
    }

    //Se houver erro em algum campo será redirecionado para o login
    //mão há erro no formulário tenta cadastrar no banco
    if ($erro) {
        $url_destino = pg . '/cadastrar/cad_niv_aces';
        header("Location: $url_destino");
    } else {
        //Pesquisar todas as ordem de nivel cadastrado  e incrementa + 1 
        $resultOrdemCadNivAc = $pdo->ordemCadastrarNivAc();
        $ordem = count($resultOrdemCadNivAc) + 1;



        $resultCadNivAc = $pdo->cadastrarNivelAcesso($dados_validos['nome'], $ordem);
        //se cadastrar no banco de dados acessa esse if
        if ($resultCadNivAc) {
            $_SESSION['msg'] = "<div class='alert alert-success'> Nível de acesso inserido com sucesso! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
        </button> </div>";
            $url_destino = pg . '/listar/list_niv_aces';
            header("Location: $url_destino");
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> Erro a inserir o nível de acesso! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
        </button></div>";
            $url_destino = pg . '/cadastrar/cad_niv_aces';
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
