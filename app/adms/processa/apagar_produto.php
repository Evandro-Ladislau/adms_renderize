<?php
if (!isset($seguranca)) {
    exit;
}

require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//esse id é resgatado ao clicar no botão pela url
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!empty($id)) {

    //Verificar se o produto esta relacionado em outra tabela, se sim ele não podera ser excluido.
    $result_Prod_relacionado = $pdo->verificarProdutoRelacionado($id);
    if ($result_Prod_relacionado) {
        $_SESSION['msg'] = "<div class='alert alert-danger'> O Produto não pode ser apagado, existem operações vinculadas a ele!</div>";
        $url_destino = pg . '/listar/list_produto';
        header("Location: $url_destino");

    } else {
        //Não há vinculo com outras tabelas
        //Apaga Menu 
        $result_del_produto = $pdo->deletarProduto($id);
        if ($result_del_produto) {
            $_SESSION['msg'] = "<div class='alert alert-success'> Produto Apagado com Sucesso! </div>";
            $url_destino = pg . '/listar/list_produto';
            header("Location: $url_destino");
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> ERRO: O Produto não foi apagado! </div>";
            $url_destino = pg . '/listar/list_produto';
            header("Location: $url_destino");
        }
    }
} else {
    $_SESSION['msg'] = "<div class='alert alert-danger'> Pagina não encontrada! </div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
