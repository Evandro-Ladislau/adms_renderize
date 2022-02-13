<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//Pesquisar os niveis de acesso;
$result_niv_acesso = $pdo->PesquisarNiveisAcesso();
for ($i = 0; $i < count($result_niv_acesso); $i++) {

    $resultado_paginas = $pdo->pesquisarPaginasSincrono();
    for ($c = 0; $c < count($resultado_paginas); $c++) {
        //pesquisar se o nivel de acesso possuiu a inscrição na página na tabela adms_nivacs_pgs
        $resultado_niv_ac_pg = $pdo->pesquisarInscricaoNivac($result_niv_acesso[$i]['id'], $resultado_paginas[$c]['id']);

        if ($resultado_niv_ac_pg == null) {
            //DETERMINAR 1 NA PERMISSÃO CASO SEJA SUPERADMINISTRADOR E PARA OUTRO NIVEIS 2: 1= liberado e 2 = bloqueado
            if ($result_niv_acesso[$i]['id'] == 1) {
                $permisao = 1;
                
            } else {
                if ($resultado_paginas[$c]['lib_pub'] == 1) {
                    $permissao = 1;
                    
                } else {
                    $permissão = 2;
                    
                }
            }

            //Pesquisar o maior numero da ordem na tabela adms_nivacs_pgs para o nível em execução
            $result_maior_ordem = $pdo->pesquisarMaiorOrdemSincrono($result_niv_acesso[$i]['id']);

            $ordem =  $result_maior_ordem[0]['ordem'] + 1;
            
            //Pesquisar se a pagina esta cadastrada para outro nivel e para qual item de menu pertence.

            $resultado_item_men = $pdo->pesquisarPaginaItemMenu($resultado_paginas[$c]['id']);
            if ($resultado_item_men) {

                $item_men = $resultado_item_men[0]['adms_menu_id'];
                

            } else {

                $item_men = 3;
               
            }

            

            $resultado_cad_permissao = $pdo->cadastrarPermissaoAcessoUsuario(
            $permisao, 
            $ordem,
            $item_men,
            $result_niv_acesso[$i]['id'],
            $resultado_paginas[$c]['id']

        );

        
        }
    }
}
$_SESSION['msg'] = "<div class='alert alert-success'> Permissão editada com sucesso! </div>";
        $url_destino = pg . "/listar/list_pagina";
        header("Location: $url_destino");