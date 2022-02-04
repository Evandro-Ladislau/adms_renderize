<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//receber o valor do campo input com nome SendCadPg do formulario no arquivo cad_pagina
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

//se essa variavel tiver valor significa que o usuario clicou no botão então ele entra nesse if
if (!empty($id)) {
    //PESQUISAR OS DADOS DA TABELA adms_nivcas_pgs
    $result_niv_ac_pg = $pdo->BuscarNiveisAcessoPaginas($id, $_SESSION['ordem']);

    //SE RETORNAR ALGUM VALOR DO BANCO, ACESSO O IF SE NÃO O ELSE;

    if ($result_niv_ac_pg) {
        
        //neste foreach eu pego o valor permissão que esta na matriz result_niv_ac_pg
        foreach ($result_niv_ac_pg as $key => $value) {
            $vetor_result_niv_ac_pg = $value;
        }

        //verificar o status da página e atribuir o inverso na variável status
       if($vetor_result_niv_ac_pg['permissao'] == 1){
            $status = 2;
       }else{
            $status = 1;
       }

       //Liberar o acesso a página
       $result_niv_pg_update = $pdo->AtualizarNivelAcesso($status, $id);

       if ($result_niv_pg_update) {
           $alteracao = true;
       }else{
           $alteracao = false;
       }

       //Pesquisar as páginas dependentes
       $result_pginas_dependentes = $pdo->PesquisarPaginasDependentes($vetor_result_niv_ac_pg['adms_pagina_id'], $vetor_result_niv_ac_pg['adms_niveis_acesso_id']);

       if ($result_pginas_dependentes) {
           for ($i=0; $i <count($result_pginas_dependentes) ; $i++) { 
               //liberar o acesso para as páginas dependentes
               $result_niv_pg_update = $pdo->AtualizarNivelAcessoDependente($status,$result_pginas_dependentes[$i]['id'] );
           }
           if ($result_niv_pg_update) {
            $alteracao = true;
        }else{
            $alteracao = false;
        }
       }


       //Redirecionar o usuário

       if ($alteracao) {
        $_SESSION['msg'] = "<div class='alert alert-success'> Permissão editada com sucesso! </div>";
        $url_destino = pg . '/listar/list_permissao?id='.$vetor_result_niv_ac_pg['adms_niveis_acesso_id'];
        header("Location: $url_destino");
       }else{
        $_SESSION['msg'] = "<div class='alert alert-danger'> Erro ao editar permissão! </div>";
        $url_destino = pg . '/listar/list_niv_aces';
        header("Location: $url_destino");
       }

    } else {
        $_SESSION['msg'] = "<div class='alert alert-danger'> Nível de acesso não encontrado! </div>";
        $url_destino = pg . '/listar/list_niv_aces';
        header("Location: $url_destino");
    }
} else {

    $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    <span aria-hidden='true'>&times;</span>
</button></div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
