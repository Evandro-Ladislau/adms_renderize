<?php
if (!isset($seguranca)) {
    exit;
}

require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//esse id é resgatado ao clicar no botão pela url
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!empty($id)) {

    //Verificar se há nivel de acesso cadastrado no nivel de acesso ele não poderá ser excluído.
    $result_niv_ac_ver = $pdo->verificarNivelMenuCadastrado($id);
    if ($result_niv_ac_ver) {
        $_SESSION['msg'] = "<div class='alert alert-danger'> O menu não pode ser apagado, há níveis de acesso cadastrado nesse menu!</div>";
        $url_destino = pg . '/listar/list_menu';
        header("Location: $url_destino");

    } else {
        //Não há nnhum nivel de acesso cadastrado nesse menu
        //Pesquisa no banco de dado se há menu com ordem acima do qual será apagado
        $result_men_ver = $pdo->buscarOrdemMenuCadastrado($id);
        //$_SESSION['resultado'] = $result_men_ver;

        //Apaga Menu 
        $result_men_del = $pdo->deletarMenu($id);
        if ($result_men_del) {
            //Alterar a sequencia da ordem para não deixar nenhum número da ordem vazio
            if ($result_men_ver) {
                for ($i = 0; $i < count($result_men_ver); $i++) {
                    //esse if pega o nivel de acesso após o que foi apagado e diminiu 1 corrigindo a ordem no nivel anterior
                    $result_men_ver[$i]['ordem_result'] = $result_men_ver[$i]['ordem_result'] - 1;
                    $atualizarOrdemMenu = $pdo->atualizaOrdemMenu($result_men_ver[$i]['ordem_result'], $result_men_ver[$i]['id']);
                }
            }


            $_SESSION['msg'] = "<div class='alert alert-success'> Menu apagado com sucesso! </div>";
            $url_destino = pg . '/listar/list_menu';
            header("Location: $url_destino");
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> ERRO: O Menu de acesso não foi apagado! </div>";
            $url_destino = pg . '/listar/list_menu';
            header("Location: $url_destino");
        }
    }
} else {
    $_SESSION['msg'] = "<div class='alert alert-danger'> Pagina não encontrada! </div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
