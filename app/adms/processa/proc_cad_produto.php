<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//recebe os valores do formulario
$SendCadProduto = filter_input(INPUT_POST, 'SendCadProduto', FILTER_SANITIZE_STRING);
if ($SendCadProduto) {
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    //var_dump($dados);
    //validar nenhum campo vazio
    $erro = false;
    include_once 'lib/lib_vazio.php';
    $dados_validos = vazio($dados);

    if (!$dados_validos) {
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'> Necessário preencher todos os campos para cadastrar o Produto!</div>";
    }

    // de erro direciona para o cadastrar produto
    if ($erro) {
        $url_destino = pg . '/cadastrar/cad_produto';
        header("Location: $url_destino");
    } else {

        //se nao tenta cadastrar no banco de dados
        $result_cad_Produto = $pdo->CadastrarProduto($dados_validos['descricao'], 
                                                 $dados_validos['adms_unidade_id'], 
                                                 $dados_validos['estoque'],
                                                 $dados_validos['preco_custo'], 
                                                 $dados_validos['preco_venda'], 
                                                 $dados_validos['adms_sit_id']);


        //se cadastrar no banco de dados acessa esse if
        if ($result_cad_Produto == 1) {
            $_SESSION['produto'] = $dados_validos;
            $_SESSION['msg'] = "<div class='alert alert-success'> Produto inserido com sucesso! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
        </button> </div>";
            $url_destino = pg . '/listar/list_produto';
            header("Location: $url_destino");
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> Erro ao inserir o Produto! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
        </button></div>";
            $url_destino = pg . '/cadastrar/cad_produto';
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
