<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//pessa o valor do id passado pela url
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

//se o valor pego for diferente de vazio ele acessa o if
//senao ele faz o redirecionamento
if (!empty($id)) {
    //pesquisar o nível de acesso atual a ser movido para cima
    $resultado_niv_atual = $pdo->alterarOrdemNivelAcesso($id);
    $_SESSION['resut'] = $resultado_niv_atual;
    if ($resultado_niv_atual) {
        for ($i=0; $i <count($resultado_niv_atual) ; $i++) { 
           //verificar se a ordem é maior em relação a ordem do usuario logado
           if ($resultado_niv_atual[$i]['ordem'] > $_SESSION['ordem'] + 1) {
              $ordem = $resultado_niv_atual[$i]['ordem']; 

              //pesquisar o id do nivel de acesso a ser movido para baixo
              $ordem_super = $ordem - 1;
              $result_niv_super = $pdo->pesquisarIdNivelAcessoMovido($ordem_super);

              //Alterar a ordem para o numero ser maior
              $result_niv_mv_baixo = $pdo->altualizaOrdemNivelAcessoMaior($ordem, $result_niv_super[$i]['id']);

              //alterar a ordem para o número ser maior
              $result_niv_mv_super = $pdo->altualizaOrdemNivelAcessoMenor($ordem_super, $resultado_niv_atual[$i]['id']);
              
              //redirecionar conforme a situação do alterar: sucesso ou erro
              if ($result_niv_mv_super) {
                $_SESSION['msg'] = "<div class='alert alert-success'> Ordem do Nível de Acesso editado com sucesso! </div>";
                $url_destino = pg . '/listar/list_niv_aces';
                header("Location: $url_destino");
              }else{
                $_SESSION['msg'] = "<div class='alert alert-danger'> Erro ao editar a ordem do Nível de Acesso! </div>";
                $url_destino = pg . '/listar/list_niv_aces';
                header("Location: $url_destino");
              }


           }else{
            $_SESSION['msg'] = "<div class='alert alert-danger'> Nível de Acesso não pode ser alterado! </div>";
            $url_destino = pg . '/listar/list_niv_aces';
            header("Location: $url_destino");
           }
        }
    }else{
        $_SESSION['msg'] = "<div class='alert alert-danger'> Nível de Acesso não encontrado! </div>";
    $url_destino = pg . '/listar/list_niv_aces';
    header("Location: $url_destino");
    }
} else {
    //variavel global para criar uma mensagem de alerta.
    $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! </div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
