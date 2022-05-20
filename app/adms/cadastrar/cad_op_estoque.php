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
                        <h2 class="display-4 titulo">Operacao de Estoque</h2>
                    </div>
                    <div class="p-2">
                        <?php
                        //BOTAO Listar
                        $btn_list = $pdo->carregarBtn('listar/list_op_estoque');

                        if ($btn_list) {
                            echo "<a href='" . pg . "/listar/list_op_estoque?id=" . $_SESSION['id'] . "' class='btn btn-outline-info btn-sm'>Listar</a>";
                        }


                        ?>
                    </div>
                </div>
                <hr>
                <?php
                //IMPRIME MENSAGEM GLOBAL QUANDO NÃO FOR POSSIVEL CADASTRAR O NIVEL DE ACESSO
                if (isset($_SESSION['msg'])) {
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                }
                ?>

                <form method="POST" action="<?php echo pg; ?>/processa/proc_op_dados_itens">
                    

                    <div class="row">

                        <div class="form-group col-md-6">
                            <label>
                                <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Adicionar Produto">
                                    <i class="fas fa-question-circle"></i>
                                </span>
                                <span class="text-danger">*</span> Produto
                            </label>
                            <select name="adms_produto_id" id="adms_produto_id" class="form-control">
                                <option selected>Selecione</option>
                                <?php
                                $resultado_produtos_op = $pdo->listarProdutosOperacao();
                                for ($i = 0; $i < count($resultado_produtos_op); $i++) {
                                    if (isset($_SESSION['dados']['adms_usuario_id']) and $_SESSION['dados']['adms_usuario_id'] == $resultado_produtos_op[$i]['id']) {

                                        echo "<option value='" . $resultado_produtos_op[$i]['id'] . "' selected>" . $resultado_produtos_op[$i]['descricao'] . "</option>";
                                    } else {
                                        echo "<option value='" . $resultado_produtos_op[$i]['id'] . "'> " . $resultado_produtos_op[$i]['descricao'] . "</option>";
                                    }
                                }

                                ?>
                            </select>

                        </div>

                        <div class="form-group col-md-3">
                            <label>
                                <span tabindex="0" data-placement="top" data-toggle="tooltip" data-html="true" title="Quantidade">
                                    <i class="fas fa-question-circle"></i>
                                </span>
                                <span class="text-danger">*</span> Quantidade
                            </label>
                            <input name="quantidade" id="quantidade" type="number" step="0.010" class="form-control" placeholder="0.00" value="<?php if (isset($_SESSION['dados']['quantidade'])) {
                                                                                                                                                    echo  $_SESSION['dados']['quantidade'];
                                                                                                                                                } ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>
                                <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Selecionar a medida do produto.">
                                    <i class="fas fa-question-circle"></i>
                                </span>
                                <span class="text-danger">*</span>UN
                            </label>
                            <select name="adms_unidade_id" id="adms_unidade_id" class="form-control">
                                <option selected>Selecione</option>
                                <?php
                                $resultado_unidades = $pdo->listarUnidades();
                                for ($i = 0; $i < count($resultado_unidades); $i++) {
                                    if (isset($_SESSION['dados']['adms_unidade_id']) and $_SESSION['dados']['adms_unidade_id'] == $resultado_unidades[$i]['id']) {

                                        echo "<option value='" . $resultado_unidades[$i]['id'] . "' selected>" . $resultado_unidades[$i]['nome'] . "</option>";
                                    } else {
                                        echo "<option value='" . $resultado_unidades[$i]['id'] . "'> " . $resultado_unidades[$i]['nome'] . "</option>";
                                    }
                                }

                                ?>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <input name="SendopProdItens" type="submit" class="btn btn-warning btn-sm" value="Adicionar" style="margin-top: 35px;">
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table table-bordered table-striped table-hover ">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Descricão</th>
                                <th class="d-none d-sm-table-cell">Unidade</th>
                                <th class="d-none d-sm-table-cell">Quantidade</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result_Operacao_id = $pdo->OperacaoID();
                            foreach ($result_Operacao_id as $value) {
                                $_SESSION['adms_operacao_id'] = $value;
                            }
                            $itens_operacao = $pdo->BuscarItensOperacao($_SESSION['adms_operacao_id']['id']);
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
                                        <td class="text-center">
                                            <span class="d-none d-md-block">
                                                <?php
                                                
                                                //BOTAO PAGAR
                                                $btn_apagar = $pdo->carregarBtn('processa/apagar_produto_op');

                                                if ($btn_apagar) {
                                                    echo " <a href='" . pg . "/processa/apagar_produto_op?id=" . $itens_operacao[$i]['prod_id'] . "' class='btn btn-outline-danger btn-sm' data-confirm='Tem Certeza que deseja excluir o item?'> Apagar </a>";
                                                }
                                                ?>
                                            </span>
                                            <div class="dropdown d-block d-md-none">
                                                <button class="btn btn-primary dropdown-toggle btn-sm" type="button" id="acoeslistar" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Ações
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="acoeslistar">
                                                    <?php
                                                    
                                                    if ($btn_apagar) {
                                                        echo "<a class='dropdown-item' href='" . pg . "/processa/apagar_produto_op?id=" . $itens_operacao[$i]['id'] . "' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
                                                    }


                                                    ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <form method="POST" action="<?php echo pg; ?>/processa/proc_operacao_estoque">
                <div class="row">
                    <div class="form-group col-md-4">
                    <label>
                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Selecionar o funcionário que esta executando a operação">
                            <i class="fas fa-question-circle"></i>
                        </span>
                        <span class="text-danger">*</span> Funcionário
                    </label>
                    </div>
                
                    <div class="form-group col-md-4">
                    <label>
                        <span class="text-danger">*</span> Tipo de Operação
                    </label>
                    </div>
                    
                 </div>
                    
                    <div class="row">

                        <div class="form-group col-md-4">
                            <select name="adms_usuario_id" id="adms_usuario_id" class="form-control">
                                <option selected>Selecione</option>
                                <?php
                                $resultado_usuarios = $pdo->listarUsuariosOp();
                                for ($i = 0; $i < count($resultado_usuarios); $i++) {
                                    if (isset($_SESSION['dados']['adms_usuario_id']) and $_SESSION['dados']['adms_usuario_id'] == $resultado_usuarios[$i]['id']) {

                                        echo "<option value='" . $resultado_usuarios[$i]['id'] . "' selected>" . $resultado_usuarios[$i]['nome'] . "</option>";
                                    } else {
                                        echo "<option value='" . $resultado_usuarios[$i]['id'] . "'> " . $resultado_usuarios[$i]['nome'] . "</option>";
                                    }
                                }

                                ?>
                            </select>
                        </div>

                        
                        <div class="form-group col-md-4">

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo_operacao" id="inlineRadio1" value="1">
                                <label class="form-check-label" for="inlineRadio1">Entrada</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo_operacao" id="inlineRadio2" value="2">
                                <label class="form-check-label" for="inlineRadio2">Saida</label>
                            </div>
                        </div>
                    
                    </div>


                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>
                                <span tabindex="0" data-placement="top" data-toggle="tooltip" data-html="true" title="Informações sobra a Operação">
                                    <i class="fas fa-question-circle"></i>
                                </span>
                                <span class="text-danger">*</span> Histórico
                            </label>
                            <textarea name="obs" class="form-control"><?php if (isset($_SESSION['dados']['obs'])) {
                                                                            echo  $_SESSION['dados']['obs'];
                                                                        } ?></textarea>
                        </div>
                        <div class="form-group">
                        <input name="SendopFinalizarOperacao" type="submit" class="btn btn-success btn-sm" value="Finalizar Operacao" style="margin-top: 35px;">
                    </div>
                 </div>    
                </form>
            </div>
        </div>
    </div>
    </div>
    <?php
    unset($_SESSION['dados']);
    include_once 'app/adms/include/rodape_lib.php';
    ?>
    <script>
        document.getElementById("input").addEventListener("change", function() {
            this.value = parseFloat(this.value).toFixed(2);
        });
    </script>
    </div>
</body>