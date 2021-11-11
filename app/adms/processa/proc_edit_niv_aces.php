<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

$sendEditNivAC = filter_input(INPUT_POST, 'SendEditNivAC', FILTER_SANITIZE_STRING);
if ($sendEditNivAC) {
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    //retirar os espacos em branco
    $dados_id = trim($dados['id']);
    //var_dump($dados);
    //validar nenhum campo vazio
    $erro = false;
    include_once 'lib/lib_vazio.php';
    $dados_validos = vazio($dados);

    if (!$dados_validos) {
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'> Necessário preencher todos os campos para  editar nível de acesso!</div>";
    }

    //Se houver erro em algum campo será redirecionado para o login
    //mão há erro no formulário tenta cadastrar no banco
    if ($erro) {
        $url_destino = pg . '/editar/edit_niv_aces?id='.$dados_id['id'];
        var_dump($url_destino);
        header("Location: $url_destino");
    } else {
        //Pesquisar todas as ordem de nivel cadastrado  e incrementa + 1 
        $resultOrdemCadNivAc = $pdo->ordemCadastrarNivAc();
        $ordem = count($resultOrdemCadNivAc) + 1;


        //chama a funcao que faz o update no banco de dados
        $resultEditNivAc = $pdo->alterarNivelAcesso($dados_validos['nome'], $dados_validos['id']);
        //se cadastrar no banco de dados acessa esse if
        if ($resultEditNivAc) {
            $_SESSION['msg'] = "<div class='alert alert-success'> Nível de acesso Editado com sucesso! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
        </button> </div>";
            $url_destino = pg . '/listar/list_niv_aces';
            header("Location: $url_destino");
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> Erro a Editar o nível de acesso! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
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
