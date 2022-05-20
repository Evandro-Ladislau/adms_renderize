<?php
if (!isset($seguranca)) {
    exit;
}
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!empty($id)) {



    include_once 'app/adms/include/head.php';
?>

    <body>

        <?php
        include_once 'app/adms/include/header.php';
        ?>

        <div class="d-flex">
            <?php
            include_once 'app/adms/include/menu.php';
            $resultado_VisualizarOperacaoEstoque = $pdo->VisualizarOperacaoEstoque($id);

            if ($resultado_VisualizarOperacaoEstoque) {
            ?>
                <div class="content p-1">
                    <div class="list-group-item">
                        <div class="d-flex">
                            <div class="mr-auto p-2">
                                <h2 class="display-4 titulo">Detalhes da Operacao</h2>
                            </div>
                            <div class="p-2">
                                <?php
                                for ($i = 0; $i < count($resultado_VisualizarOperacaoEstoque); $i++) {
                                ?>
                                    <span class="d-none d-md-block">
                                        <?php

                                        //BOTAO Listar
                                        $btn_list = $pdo->carregarBtn('listar/list_op_estoque');

                                        if ($btn_list) {
                                            echo "<a href='" . pg . "/listar/list_op_estoque?id=" . $_SESSION['id'] . "' class='btn btn-outline-info btn-sm'>Listar</a>";
                                        }

                                        //BOTAO PAGAR
                                        $btn_apagar = $pdo->carregarBtn('processa/apagar_op_estoque');

                                        if ($btn_apagar) {
                                            echo " <a href='" . pg . "/processa/apagar_op_estoque?id=" . $resultado_VisualizarOperacaoEstoque[$i]['adms_operacao_id'] . "' class='btn btn-outline-danger btn-sm' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
                                        }
                                        ?>
                                    </span>
                                    <div class="dropdown d-block d-md-none">
                                        <button class="btn btn-primary dropdown-toggle btn-sm" type="button" id="acoeslistar" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Ações
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="acoeslistar">
                                            <?php
                                            if ($btn_list) {
                                                echo "<a class='dropdown-item' href='" . pg . "/listar/list_op_estoque?id=" . $_SESSION['id'] . "'>Listar</a>";
                                            }


                                            if ($btn_apagar) {
                                                echo "<a class='dropdown-item' href='" . pg . "/processa/apagar_op_estoque?id=" . $resultado_paginasCadastradas[$i]['adms_operacao_id'] . "' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
                                            }


                                            ?>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>

                            </div>

                        </div>
                        <hr>
                        <?php
                        for ($i = 0; $i < count($resultado_VisualizarOperacaoEstoque); $i++) {

                        ?>
                            <dl class="row">
                                <dt class="col-sm-3">Código Operação</dt>
                                <dd class="col-sm-9"><?php echo $resultado_VisualizarOperacaoEstoque[$i]['adms_operacao_id']; ?></dd>

                                <dt class="col-sm-3">Tipo</dt>
                                <dd class="col-sm-9"><?php echo $resultado_VisualizarOperacaoEstoque[$i]['tp_nome']; ?></dd>

                                
                                <dt class="col-sm-3">Funcionário</dt>
                                <dd class="col-sm-9"><?php echo $resultado_VisualizarOperacaoEstoque[$i]['nome']; ?></dd>

                                <dt class="col-sm-3">Emissão</dt>
                                <dd class="col-sm-9"><?php echo date('d/m/y H:i:s', strtotime($resultado_VisualizarOperacaoEstoque[$i]['created'])) ?></dd>

                                <dt class="col-sm-3">Histórico</dt>
                                <dd class="col-sm-9"><?php echo $resultado_VisualizarOperacaoEstoque[$i]['obs']; ?></dd>

                                


                            </dl>
                            <div class="table-responsive">
                    <table class="table table table-bordered table-striped table-hover ">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Descricão</th>
                                <th class="d-none d-sm-table-cell">Unidade</th>
                                <th class="d-none d-sm-table-cell">Quantidade</th>
                        </thead>
                        <tbody>
                            <?php
                            
                            $itens_operacao = $pdo->BuscarItensOperacao($resultado_VisualizarOperacaoEstoque[$i]['adms_operacao_id']);
                            if ($itens_operacao) {
                                for ($i = 0; $i < count($itens_operacao); $i++) {
                            ?>

                                    <tr>
                                        <td><?php echo $itens_operacao[$i]['prod_id']; ?></td>
                                        <td>
                                            <?php echo $itens_operacao[$i]['prod_desc']; ?></td>
                                        </td>
                                        <td class="d-none d-sm-table-cell">
                                            <?php echo $itens_operacao[$i]['un_nome']; ?>
                                        </td>
                                        <td class="d-none d-sm-table-cell">
                                            <?php echo $itens_operacao[$i]['mov_quantidade']; ?>
                                        </td>
                                    </tr>

                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                    </div>
                <?php
                        }
                ?>

                </div>

                <?php
                include_once 'app/adms/include/rodape_lib.php';
                ?>
        </div>
    </body>
<?php

            } else {
                $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! </div>";
                $url_destino = pg . '/listar/list_pagina';
                header("Location: $url_destino");
            }
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! </div>";
            $url_destino = pg . '/acesso/login';
            header("Location: $url_destino");
        }
