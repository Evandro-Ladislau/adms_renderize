<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';
include_once 'app/adms/include/head.php';
?>

<body>

    <?php
    include_once 'app/adms/include/header.php';
    ?>

    <div class="d-flex">
        <?php
        include_once 'app/adms/include/menu.php';
        ?>
        <div class="content p-1">
            <div class="list-group-item">
                <div class="d-flex">
                    <div class="mr-auto p-2">
                        <h2 class="display-4 titulo">Listar Página</h2>
                    </div>
                    <div class="p-2">
                        <?php
                        $btn_cad = $pdo->carregarBtn('cadastrar/cad_pagina');

                        if ($btn_cad) {
                            echo "<a href='" . pg . "/cadastrar/cad_pagina' class='btn btn-outline-success btn-sm'>Cadastar</a>";
                        }
                        ?>

                    </div>
                </div>
                <pre>
                <?php
                //se cadastrar com sucesso impresse essa variavel global
                if (isset($_SESSION['msg'])) {
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                }

                //recebe o numero da pagina que o usuario esta
                $pagina_atual = filter_input(INPUT_GET, 'pagina', FILTER_SANITIZE_NUMBER_INT);
                $pagina = (!empty($pagina_atual)) ? $pagina_atual : 1;

                //setar a quantidade de itens por pagina
                $qnt_result_pg = 50;

                //calcular o inicio visualização
                $inicio = ($qnt_result_pg * $pagina) - $qnt_result_pg;

                if ($_SESSION['adms_niveis_acesso_id'] == 1) {
                    //chamei a funcao que busca a paginacao conforme nivel de acesso.
                    $result_paginacaoNivelAcessoPagina = $pdo->paginacaoNivelAcessoPaginas($inicio, $qnt_result_pg);
                } else {
                    $result_paginacaoNivelAcessoPagina = $pdo->paginacaoNivelAcessoLimitado($inicio, $qnt_result_pg);
                }

                if ($result_paginacaoNivelAcessoPagina) {
                ?>
                </pre>
                    <div class="table-responsive">
                        <table class="table table table-bordered table-striped table-hover ">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th class="d-none d-sm-table-cell">Endereço</th>
                                    <th class="d-none d-sm-table-cell">tipo Página</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                //pegando as informações do nivel de acesso cadastradas no banco de dados.     
                                for ($i = 0; $i < count($result_paginacaoNivelAcessoPagina); $i++) {
                                ?>
                                    <tr>
                                        <th><?php echo $result_paginacaoNivelAcessoPagina[$i]['id'] ?></th>
                                        <td><?php echo $result_paginacaoNivelAcessoPagina[$i]['nome_pagina'] ?></td>
                                        <td class="d-none d-sm-table-cell"><?php echo $result_paginacaoNivelAcessoPagina[$i]['endereco'] ?></td>
                                        <td class="d-none d-sm-table-cell"><?php echo $result_paginacaoNivelAcessoPagina[$i]['tipo'] ?></td>
                                        <td class="text-center">
                                            <span class="d-none d-md-block">
                                                <?php

                                                //BOTAO VISUALIZAr
                                                $btn_vis = $pdo->carregarBtn('visualizar/vis_pagina');

                                                if ($btn_vis) {
                                                    echo " <a href='" . pg . "/visualizar/vis_pagina?id=" . $result_paginacaoNivelAcessoPagina[$i]['id'] . "' class='btn btn-outline-primary btn-sm'> Visualizar </a>";
                                                }

                                                //BOTAR EDITAR
                                                $btn_edit = $pdo->carregarBtn('editar/edit_pagina');

                                                if ($btn_edit) {
                                                    echo " <a href='" . pg . "/editar/edit_pagina?id=" . $result_paginacaoNivelAcessoPagina[$i]['id'] . "' class='btn btn-outline-warning btn-sm' > Editar </a>";
                                                }

                                                //BOTAO PAGAR
                                                $btn_apagar = $pdo->carregarBtn('processa/apagar_pagina');

                                                if ($btn_apagar) {
                                                    echo " <a href='" . pg . "/processa/apagar_pagina?id=" . $result_paginacaoNivelAcessoPagina[$i]['id'] . "' class='btn btn-outline-danger btn-sm' data-confirm='Tem Certeza que deseja excluir o item?'> Apagar </a>";
                                                }
                                                ?>
                                            </span>
                                            <div class="dropdown d-block d-md-none">
                                                <button class="btn btn-primary dropdown-toggle btn-sm" type="button" id="acoeslistar" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Ações
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="acoeslistar">
                                                    <?php
                                                    if ($btn_vis) {
                                                        echo "<a class='dropdown-item' href='" . pg . "/visualizar/vis_pagina?id=" . $result_paginacaoNivelAcessoPagina[$i]['id'] . "'>Visualizar</a>";
                                                    }

                                                    if ($btn_edit) {
                                                        echo "<a class='dropdown-item' href='" . pg . "/editar/edit_pagina?id=" . $result_paginacaoNivelAcessoPagina[$i]['id'] . "'>Editar</a>";
                                                    }

                                                    if ($btn_apagar) {
                                                        echo "<a class='dropdown-item' href='" . pg . "/processa/apagar_pagina?id=" . $result_paginacaoNivelAcessoPagina[$i]['id'] . "' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
                                                    }


                                                    ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                <?php
                                }
                                ?>

                            </tbody>

                        </table>

                        <?php
                        //chama a funcao com a quantidade de registro.
                        $result_pg = $pdo->paginacaoPaginas();
                        for ($i = 0; $i < count($result_pg); $i++) {
                            //quantidade de pagina
                            $quantidade_pg = ceil($result_pg[$i]['num_result'] / $qnt_result_pg);
                            //limitar os links antes    
                            $max_links = 2;
                            echo "<nav aria-label='paginacao'>";
                            echo "<ul class='pagination pagination-sm justify-content-center'>";
                            echo "<li class='page-item'>";
                            echo "<a class='page-link' href='" . pg . "/listar/list_pagina?pagina=1' tabindex='-1'>Primeira</a>";
                            echo "</li>";

                            //mostra a pagina anterior a pagina que o usuario esta
                            //o if é para a pagina ser mostrada só se o valor for maior que 1
                            for ($pag_ant = $pagina - $max_links; $pag_ant <= $pagina - 1; $pag_ant++) {
                                if ($pag_ant >= 1) {
                                    echo "<li class='page-item'><a class='page-link' href='" . pg . "/listar/list_pagina?pagina=$pag_ant'>$pag_ant</a></li>";
                                }
                            }

                            //pagina onde o usuario esta (pagina_atual)    
                            echo "<li class='page-item active'>";
                            echo "<a class='page-link' href='#'>$pagina</a>";
                            echo "</li>";


                            //mostra pagina posterior de onde o usuario esta
                            for ($pag_dep = $pagina + 1; $pag_dep <= $pagina + $max_links; $pag_dep++) {
                                if ($pag_dep <= $quantidade_pg) {
                                    echo "<li class='page-item'><a class='page-link' href='" . pg . "/listar/list_pagina?pagina=$pag_dep'>$pag_dep</a></li>";
                                }
                            }

                            //limita o link depois
                            echo "<li class='page-item'>";
                            echo "<a class='page-link' href='" . pg . "/listar/list_pagina?pagina=$quantidade_pg'>Ultima</a>";
                            echo "</li>";
                            echo "</ul>";
                            echo "</nav>";
                        }
                        ?>




                    <?php
                }
                    ?>

                    </div>


            </div>
            <?php

            include_once 'app/adms/include/rodape_lib.php';
            ?>
        </div>
</body>