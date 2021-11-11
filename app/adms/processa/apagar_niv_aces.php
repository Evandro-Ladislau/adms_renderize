<?php
if (!isset($seguranca)) {
    exit;
}

require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!empty($id)) {
    //Pesquisa no vanco de dado se há nível com ordem acima do qual será apagado
    $result_niv_aces = $pdo->buscarOrdemDoNivelDeletado($id);
    
    $result_niv_aces_del = $pdo->deletarNivelAcesso($id);
    if ($result_niv_aces_del) {
        //Alterar a sequencia da ordem para não deixar nenhum número da ordem vazio
        if ($result_niv_aces) {
            for ($i=0; $i <count($result_niv_aces) ; $i++) { 
               $atualizado = $result_niv_aces[$i]['ordem_result '] = $result_niv_aces[$i]['ordem_result '] - 1;
            }

            $atualizar_niv_ac = $pdo->atualizaOrdem(5, $id);
        }


        $_SESSION['msg'] = "<div class='alert alert-success'> Nível de acesso apagado com sucesso! </div>";
        $url_destino = pg . '/listar/list_niv_aces';
        header("Location: $url_destino");
    } else {
        $_SESSION['msg'] = "<div class='alert alert-danger'> ERRO: O nível de acesso não foi apagado! </div>";
        $url_destino = pg . '/listar/list_niv_aces';
        header("Location: $url_destino");
    }
} else {
    $_SESSION['msg'] = "<div class='alert alert-danger'> Pagina não encontrada! </div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
