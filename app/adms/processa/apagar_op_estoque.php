<?php
if (!isset($seguranca)) {
    exit;
}

require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//esse id é resgatado ao clicar no botão pela url
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!empty($id)) {

    //Buscar os itens da operacao para alterar o estoque.
    $itensAtualizarEstoque = $pdo->BuscarItensOperacaoParaAtualizarEstoque($id);
    //Buscar o estoque atual dos itens da operacao na tebale de produtos
    $estoqueAtual = $pdo->EstoqueAtual($id);

    //esse foreach é para pegar o tipo de operacao que esta vinculado aos produtos que serão atualizados no estoque
    $_SESSION['itenstest'] = $itensAtualizarEstoque;
    var_dump($_SESSION['itenstest']);
    foreach ($itensAtualizarEstoque as  $value) {
        $itensAtualizar = $value;
    }
    if ($itensAtualizar['adms_tipo_operacao_id'] == 1) {
        //atualizar a tabela de produtos conforme a operacao de estoque
        for ($i = 0; $i < count($itensAtualizarEstoque); $i++) {
            $atualizaEstoque = $pdo->AtualizarEstoqueOperacao($estoqueAtual[$i]['estoque'] - $itensAtualizarEstoque[$i]['quantidade'], $itensAtualizarEstoque[$i]['adms_produto_id']);
        }
    } elseif ($itensAtualizar['adms_tipo_operacao_id'] == 2) {

        //atualizar a tabela de produtos conforme a operacao de estoque
        for ($i = 0; $i < count($itensAtualizarEstoque); $i++) {
            $atualizaEstoque = $pdo->AtualizarEstoqueOperacao($estoqueAtual[$i]['estoque'] + $itensAtualizarEstoque[$i]['quantidade'], $itensAtualizarEstoque[$i]['adms_produto_id']);
        }
    }
    $apagar_itens_operacoes = $pdo->ApagarItensOperação($id);
    $apagar_op_estoque = $pdo->ApagarOperacaoEstoque($id);
    if ($apagar_op_estoque) {
        $_SESSION['msg'] = "<div class='alert alert-success'> Operacao Apagada com Sucesso! </div>";
        $url_destino = pg . '/listar/list_op_estoque';
        header("Location: $url_destino");
    } else {
        $_SESSION['msg'] = "<div class='alert alert-danger'> ERRO: O Produto não foi apagado! </div>";
        $url_destino = pg . '/listar/list_op_estoque';
        header("Location: $url_destino");
    }
} else {
    $_SESSION['msg'] = "<div class='alert alert-danger'> Pagina não encontrada! </div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
