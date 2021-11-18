<?php
if (!isset($seguranca)) {
    exit;
}

require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//esse id é resgatado ao clicar no botão pela url
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!empty($id)) {

    //Verificar se há usuarios cadastrado no nivel de acesso ele não poderá ser excluído.
    $result_nivel_user = $pdo->verificarNivelCadastradoUsuario($id);
    if ($result_nivel_user) {
        $_SESSION['msg'] = "<div class='alert alert-danger'> Nível de acesso relacionado a outra tabela, não pode ser apagado!</div>";
        $url_destino = pg . '/listar/list_niv_aces';
        header("Location: $url_destino");

    } else {
        //Não há nnhum usuário cadastrado nesse nível
        //Pesquisa no banco de dado se há nível com ordem acima do qual será apagado
        $result_niv_aces = $pdo->buscarOrdemDoNivelDeletado($id);
        $_SESSION['resultado'] = $result_niv_aces;
        $result_niv_aces_del = $pdo->deletarNivelAcesso($id);
        if ($result_niv_aces_del) {
            //Alterar a sequencia da ordem para não deixar nenhum número da ordem vazio
            if ($result_niv_aces) {
                for ($i = 0; $i < count($result_niv_aces); $i++) {
                    //esse if pega o nivel de acesso após o que foi apagado e diminiu 1 corrigindo a ordem no nivel anterior
                    $result_niv_aces[$i]['ordem_result'] = $result_niv_aces[$i]['ordem_result'] - 1;
                    $atualizarOrdem = $pdo->atualizaOrdem($result_niv_aces[$i]['ordem_result'], $result_niv_aces[$i]['id']);
                }
            }


            $_SESSION['msg'] = "<div class='alert alert-success'> Nível de acesso apagado com sucesso! </div>";
            $url_destino = pg . '/listar/list_niv_aces';
            header("Location: $url_destino");
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> ERRO: O nível de acesso não foi apagado! </div>";
            $url_destino = pg . '/listar/list_niv_aces';
            header("Location: $url_destino");
        }
    }
} else {
    $_SESSION['msg'] = "<div class='alert alert-danger'> Pagina não encontrada! </div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
