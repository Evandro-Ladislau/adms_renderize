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
    //pesquisar o odem do menu atual a ser movido para cima
    $resultado_menu_atual = $pdo->alterarOrdemMenu($id);
   //$_SESSION['resut'] = $resultado_niv_atual;
    if ($resultado_menu_atual) {
        for ($i=0; $i <count($resultado_menu_atual) ; $i++) { 
           
              $ordem = $resultado_menu_atual[$i]['ordem']; 

              //pesquisar o id do Menu a ser movido para baixo
              $ordem_super = $ordem - 1;
              $result_menu_super = $pdo->pesquisarIdMenuAcessoMovido($ordem_super);

              //Alterar a ordem para o numero ser maior
              $result_menu_mv_baixo = $pdo->altualizaOrdemMenuAcessoMaior($ordem, $result_menu_super[$i]['id']);

              //alterar a ordem para o número ser maior
              $result_menu_mv_super = $pdo->altualizaOrdemMenuAcessoMenor($ordem_super, $resultado_menu_atual[$i]['id']);
              
              //redirecionar conforme a situação do alterar: sucesso ou erro
              if ($result_menu_mv_super) {
                $_SESSION['msg'] = "<div class='alert alert-success'> Ordem do Menu editado com sucesso! </div>";
                $url_destino = pg . '/listar/list_menu';
                header("Location: $url_destino");
              }else{
                $_SESSION['msg'] = "<div class='alert alert-danger'> Erro ao editar a ordem do Menu! </div>";
                $url_destino = pg . '/listar/list_menu';
                header("Location: $url_destino");
              }


           }
    }else{
        $_SESSION['msg'] = "<div class='alert alert-danger'> Menu não encontrado! </div>";
    $url_destino = pg . '/listar/list_menu';
    header("Location: $url_destino");
    }
} else {
    //variavel global para criar uma mensagem de alerta.
    $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! </div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
