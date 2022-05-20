<?php
if (!isset($seguranca)) {
    exit;
}

require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//esse id é resgatado ao clicar no botão pela url
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!empty($id)) {
        $result_Operacao_id = $pdo->OperacaoID();
        foreach ($result_Operacao_id as  $value) {
            $adms_operacao_id = $value;
        }
        $result_del_produto = $pdo->deletarItem($id, $adms_operacao_id['id']);
        if ($result_del_produto) {
            $_SESSION['msg'] = "<div class='alert alert-success'> Produto Apagado com Sucesso! </div>";
            $url_destino = pg . '/cadastrar/cad_op_estoque';
            header("Location: $url_destino");
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> ERRO: O Produto não foi apagado! </div>";
            $url_destino = pg . '/cadastrar/cad_op_estoque';
            header("Location: $url_destino");
        }
    
} else {
    $_SESSION['msg'] = "<div class='alert alert-danger'> Pagina não encontrada! </div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
