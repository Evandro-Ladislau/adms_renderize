<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//Pesquisar os niveis de acesso;
$result_niv_acesso = $pdo->PesquisarNiveisAcesso();
for ($i=0; $i <count($result_niv_acesso) ; $i++) { 
    
    $resultado_paginas = $pdo->pesquisarPaginasSincrono();
    for ($c=0; $c <count($resultado_paginas) ; $c++) { 
        //pesquisar se o nivel de acesso possuiu a inscrição na página na tabela adms_nivacs_pgs
        $resultado_niv_ac_pg = $pdo->pesquisarInscricaoNivac($result_niv_acesso[$i]['id'], $resultado_paginas[$c]['id']);
        
        //verificar se não encontrou a página cadastrada para o nível de acesso em questão
        for ($x=0; $x <count($resultado_niv_ac_pg) ; $x++) { 
            if ($resultado_niv_ac_pg[$x]['id'] != 0 ) {
            
                //determinar 1 na permissão caso seja superadminstrador e para outros niveis 2: 1 = liberado, 2 = bloqueado
                if ($result_niv_acesso[$i]['id'] == 1) {
                    $permissao = 1;
                } else {
                    if ($resultado_paginas[$c]['lib_pub'] == 1) {
                        $permissao = 1;
                    } else {
                        $permissao = 2;
                    }
                    
                }
    
                //pesquisar o maior número da ordem na tabela adms_nivacs_pgs para o nível em execução
                $result_maior_ordem = $pdo->pesquisarMaiorOrdemSincrono($result_niv_acesso[$i]['id']);
    
                
                for ($cont=0; $cont <count($result_maior_ordem) ; $cont++) { 
                    $ordem = $result_maior_ordem[$cont]['ordem'] + 1;
                }
                
    
                //pesquisar se página está cadastrada para outro nível e para qual item de menu pertence
                $resultado_item_menu = $pdo->pesquisarPaginaItemMenu($resultado_paginas[$c]['id']);
                if ($resultado_item_menu) {
                   for ($vet=0; $vet <count($resultado_item_menu) ; $vet++) { 
                    $item_men = $resultado_item_menu[$vet]['adms_menu_id'];
                   }
                   
                }else{
                    $item_men = 3;
                }
    
                //Cadastrar no banco de dados a permissão de acessar a página na tabela adms_nivacs_pgs
                $result_cad_pagina = $pdo->cadastrarPermisaoAcessarAdmsNivac($permissao,  $ordem,  $item_men, $result_niv_acesso[$i]['id'], 
                $resultado_paginas[$c]['id'] );
    
                
            }else{
                echo $resultado_niv_ac_pg[$x]['id']."<br/>";
            }
        }
        
    }


}
