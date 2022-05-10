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
                        <h2 class="display-4 titulo">Listar Produtos</h2>
                    </div>
                    <div class="p-2">
                        <?php

                        $btn_cad = $pdo->carregarBtn('cadastrar/cad_produto');

                        if ($btn_cad) {
                            echo "<a href='" . pg . "/cadastrar/cad_produto' class='btn btn-outline-success btn-sm'> Cadastar </a>";
                        }

                        //Receber o número da página
                        $pagina_atual = filter_input(INPUT_GET, 'pagina', FILTER_SANITIZE_NUMBER_INT);
                        $pagina = (!empty($pagina_atual)) ? $pagina_atual : 1;

                        //Setar a quantidade de itens por página
                        $qnt_result_pg = 40;

                        //Calcular o inicio da visualização
                        $inicio = ($qnt_result_pg * $pagina) - $qnt_result_pg;

                        $result_produtos = $pdo->buscarProdutos($inicio, $qnt_result_pg);
                        


                        ?>

                    </div>

                </div>

                <?php
                //se cadastrar com sucesso impresse essa variavel global
                if (isset($_SESSION['msg'])) {
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                    
                }

                ?>
                <?php
                if ($result_produtos) {
                ?>
                    <div class="table-responsive">
                        <table class="table table table-bordered table-striped table-hover ">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descricão</th>
                                    <th class="d-none d-sm-table-cell">UN</th>
                                    <th class="d-none d-sm-table-cell">Estoque</th>
                                    <th class="d-none d-sm-table-cell">Custo</th>
                                    <th class="d-none d-sm-table-cell">Venda</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $qnt_linhas_exe = 1;
                                for ($i = 0; $i < count($result_produtos); $i++) {
                                ?>

                                    <tr>
                                        <td><?php echo $result_produtos[$i]['id']; ?></td>
                                        <td>
                                        <?php echo $result_produtos[$i]['descricao']; ?></td>
                                        </td>
                                        <td class="d-none d-sm-table-cell">
                                            <?php echo $result_produtos[$i]['nome']; ?>
                                        </td>
                                        <td class="d-none d-sm-table-cell">
                                            <?php echo $result_produtos[$i]['estoque']; ?>
                                        </td>
                                        <td>
                                        <?php echo $result_produtos[$i]['preco_custo']; ?></td>
                                        </td>
                                        <td>
                                        <?php echo $result_produtos[$i]['preco_venda']; ?></td>
                                        </td>
                                        <td class="text-center">
                                            <span class="d-none d-md-block">
                                                <?php
                                                $qnt_linhas_exe++;

                                                //BOTAO VISUALIZAE
                                                $btn_vis = $pdo->carregarBtn('visualizar/vis_produto');

                                                if ($btn_vis) {
                                                    echo " <a href='" . pg . "/visualizar/vis_produto?id=" . $result_produtos[$i]['id'] . "' class='btn btn-outline-primary btn-sm'> Visualizar </a>";
                                                }

                                                //BOTAR EDITAR
                                                $btn_edit = $pdo->carregarBtn('editar/edit_produto');

                                                if ($btn_edit) {
                                                    echo " <a href='" . pg . "/editar/edit_produto?id=" . $result_produtos[$i]['id'] . "' class='btn btn-outline-warning btn-sm' > Editar </a>";
                                                }

                                                //BOTAO PAGAR
                                                $btn_apagar = $pdo->carregarBtn('processa/apagar_produto');

                                                if ($btn_apagar) {
                                                    echo " <a href='" . pg . "/processa/apagar_produto?id=" . $result_produtos[$i]['id'] . "' class='btn btn-outline-danger btn-sm' data-confirm='Tem Certeza que deseja excluir o item?'> Apagar </a>";
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
                                                        echo "<a class='dropdown-item' href='" . pg . "/visualizar/vis_produto?id=" . $result_produtos[$i]['id'] . "'>Visualizar</a>";
                                                    }

                                                    if ($btn_edit) {
                                                        echo "<a class='dropdown-item' href='" . pg . "/editar/edit_produto?id=" . $result_produtos[$i]['id'] . "'>Editar</a>";
                                                    }

                                                    if ($btn_apagar) {
                                                        echo "<a class='dropdown-item' href='" . pg . "/processa/apagar_produto?id=" . $result_produtos[$i]['id'] . "' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
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
                        $result_pg = $pdo->paginacao_menu();
                        for ($i = 0; $i < count($result_pg); $i++) {
                            //quantidade de pagina
                            $quantidade_pg = ceil($result_pg[$i]['num_result'] / $qnt_result_pg);
                            //limitar os links antes    
                            $max_links = 2;
                            echo "<nav aria-label='paginacao'>";
                            echo "<ul class='pagination pagination-sm justify-content-center'>";
                            echo "<li class='page-item'>";
                            echo "<a class='page-link' href='" . pg . "/listar/list_produto?pagina=1' tabindex='-1'>Primeira</a>";
                            echo "</li>";

                            //mostra a pagina anterior a pagina que o usuario esta
                            //o if é para a pagina ser mostrada só se o valor for maior que 1
                            for ($pag_ant = $pagina - $max_links; $pag_ant <= $pagina - 1; $pag_ant++) {
                                if ($pag_ant >= 1) {
                                    echo "<li class='page-item'><a class='page-link' href='" . pg . "/listar/list_produto?pagina=$pag_ant'>$pag_ant</a></li>";
                                }
                            }

                            //pagina onde o usuario esta (pagina_atual)    
                            echo "<li class='page-item active'>";
                            echo "<a class='page-link' href='#'>$pagina</a>";
                            echo "</li>";


                            //mostra pagina posterior de onde o usuario esta
                            for ($pag_dep = $pagina + 1; $pag_dep <= $pagina + $max_links; $pag_dep++) {
                                if ($pag_dep <= $quantidade_pg) {
                                    echo "<li class='page-item'><a class='page-link' href='" . pg . "/listar/list_produto?pagina=$pag_dep'>$pag_dep</a></li>";
                                }
                            }

                            //limita o link depois
                            echo "<li class='page-item'>";
                            echo "<a class='page-link' href='" . pg . "/listar/list_produto?pagina=$quantidade_pg'>Ultima</a>";
                            echo "</li>";
                            echo "</ul>";
                            echo "</nav>";
                        }
                        ?>

                    <?php
                } else {
                    ?>

                        <div class="alert alert-danger" role="alert">
                            Nenhuma registro encontrado!
                        </div>
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