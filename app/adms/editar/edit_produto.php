<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';


//pega o id passado pela url
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
//verificar a existencia do id na URL

if (!empty($id)) {
    $result_edit_produto = $pdo->VerificarProdutosCadastrados($id);

    //verificar se encontrou a página no banco de dados
    if ($result_edit_produto) {

        include_once 'app/adms/include/head.php';
?>

        <body>
            <?php
            unset($_SESSION['dados']);
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
                                <h2 class="display-4 titulo">Editar Produto</h2>
                            </div>
                            <div class="p-2">
                                <span class="d-none d-md-block">
                                    <?php

                                    for ($i = 0; $i < count($result_edit_produto); $i++) {
                                        //BOTAO LISTAR
                                        $btn_list = $pdo->carregarBtn('listar/list_produto');

                                        if ($btn_list) {
                                            echo "<a href='" . pg . "/listar/list_produto?id=" . $result_edit_produto[$i]['id'] . "' class='btn btn-outline-info btn-sm'>Listar</a>";
                                        }

                                        //BOTAR EDITAR
                                        $btn_vis = $pdo->carregarBtn('visualizar/vis_produto');

                                        if ($btn_vis) {
                                            echo " <a href='" . pg . "/visualizar/vis_produto?id=" . $result_edit_produto[$i]['id'] . "' class='btn btn-outline-primary btn-sm'>visualizar</a>";
                                        }

                                        //BOTAO PAGAR
                                        $btn_apagar = $pdo->carregarBtn('processa/apagar_produto');

                                        if ($btn_apagar) {
                                            echo " <a href='" . pg . "/processa/apagar_produto?id=" . $result_edit_produto[$i]['id'] . "' class='btn btn-outline-danger btn-sm' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
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
                                            echo "<a class='dropdown-item' href='" . pg . "/listar/list_produto?id=" . $result_edit_produto[$i]['id'] . "'>Listar</a>";
                                        }

                                        if ($btn_vis) {
                                            echo "<a class='dropdown-item' href='" . pg . "visualizar/vis_produto?id=" . $result_edit_produto[$i]['id'] . "'>visualizar</a>";
                                        }

                                        if ($btn_apagar) {
                                            echo "<a class='dropdown-item' href='" . pg . "/processa/apagar_produto?id=" . $_SESSION['id'] . "' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
                                        }


                                        ?>
                                    </div>
                                <?php
                                    }
                                ?>
                                </div>
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
                        <form method="POST" action="<?php echo pg; ?>/processa/proc_edit_produto">

                            <?php
                            for ($c = 0; $c < count($result_edit_produto); $c++) {
                                # code...
                            ?>
                                <input type="hidden" name="id" value="<?php if (isset($result_edit_produto[$c]['id'])) {
                                                                            echo $result_edit_produto[$c]['id'];
                                                                        } ?>">

                                <div class="form-group">
                                    <label>
                                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Descricao do produto">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                        <span class="text-danger">*</span> Descricao
                                    </label>
                                    <input name="descricao" type="text" class="form-control" placeholder="descricao do Produto" value="<?php if (isset($_SESSION['dados']['descricao'])) {
                                                                                                                                        echo  $_SESSION['dados']['descricao'];
                                                                                                                                    } elseif (isset($result_edit_produto[$c]['descricao'])) {
                                                                                                                                        echo $result_edit_produto[$c]['descricao'];
                                                                                                                                    }

                                                                                                                                    ?>">
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label>
                                            <span tabindex="0" data-placement="top" data-toggle="tooltip" data-html="true" title="Quantidade de estoque do produto">
                                                <i class="fas fa-question-circle"></i>
                                            </span>
                                            <span class="text-danger">*</span> estoque
                                        </label>
                                        <input name="estoque" type="number" step="0.010" class="form-control" placeholder="0.00" value="<?php if (isset($_SESSION['dados']['estoque'])) {
                                                                                                                                        echo  $_SESSION['dados']['estoque'];
                                                                                                                                    } elseif (isset($result_edit_produto[$c]['estoque'])) {
                                                                                                                                        echo $result_edit_produto[$c]['estoque'];
                                                                                                                                    }
                                                                                                                                    ?>">
                                    </div>

                                    <div class="form-group">
                                    <label>
                                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Selecionar a unidade de medida.">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                        <span class="text-danger">*</span>UN
                                    </label>
                                    <select name="adms_unidade_id" id="adms_unidade_id" class="form-control">
                                        <option selected>Selecione</option>
                                        <?php
                                        $resultado_unidades = $pdo->listarUnidades();
                                        for ($un = 0; $i < count($resultado_unidades); $i++) {
                                            if (isset($_SESSION['dados']['adms_unidade_id']) and $_SESSION['dados']['adms_unidade_id'] == $resultado_unidades[$i]['id']) {

                                                echo "<option value='" . $resultado_unidades[$i]['id'] . "' selected>" . $resultado_unidades[$i]['nome'] . "</option>";

                                                //Preencher com informações do banco de dados caso não tenha nenhum valor na sessão $_SESSION['dados]
                                            } elseif (!isset($_SESSION['dados']['adms_unidade_id']) and (isset($result_edit_produto[$c]['adms_unidade_id'])) and $result_edit_produto[$c]['adms_unidade_id'] ==  $resultado_unidades[$i]['id']) {
                                                echo "<option value='" . $resultado_unidades[$i]['id'] . "' selected>" . $resultado_unidades[$i]['nome'] . "</option>";
                                            } else {
                                                echo "<option value='" . $resultado_unidades[$i]['id'] . "'> " . $resultado_unidades[$i]['nome'] . "</option>";
                                            }
                                        }

                                        ?>
                                    </select>
                                </div>
                                    <div class="form-group col-md-3">
                                        <label>
                                            <span tabindex="0" data-placement="top" data-toggle="tooltip" data-html="true" title="Preço de custo do produto">
                                                <i class="fas fa-question-circle"></i>
                                            </span>
                                            <span class="text-danger">*</span> Preco de Custo
                                        </label>
                                        <input name="preco_custo" type="number" step="0.010" class="form-control" placeholder="0.00" value="<?php if (isset($_SESSION['dados']['preco_custo'])) {
                                                                                                                                        echo  $_SESSION['dados']['preco_custo'];
                                                                                                                                    } elseif (isset($result_edit_produto[$c]['preco_custo'])) {
                                                                                                                                        echo $result_edit_produto[$c]['preco_custo'];
                                                                                                                                    }
                                                                                                                                    ?>">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>
                                            <span tabindex="0" data-placement="top" data-toggle="tooltip" data-html="true" title="Preço de Venda do Produto">
                                                <i class="fas fa-question-circle"></i>
                                            </span>
                                            <span class="text-danger">*</span> Preco de Venda
                                        </label>
                                        <input name="preco_venda" type="number" step="0.010" class="form-control" placeholder="0.00" value="<?php if (isset($_SESSION['dados']['preco_venda'])) {
                                                                                                                                        echo  $_SESSION['dados']['preco_venda'];
                                                                                                                                    } elseif (isset($result_edit_produto[$c]['preco_venda'])) {
                                                                                                                                        echo $result_edit_produto[$c]['preco_venda'];
                                                                                                                                    }
                                                                                                                                    ?>">
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label>
                                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Selecionar a situação do Produto.">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                        <span class="text-danger">*</span>Situação
                                    </label>
                                    <select name="adms_sit_id" id="adms_sit_id" class="form-control">
                                        <option selected>Selecione</option>
                                        <?php
                                        $resultado_situacao = $pdo->listarSituacaoMenu();
                                        for ($i = 0; $i < count($resultado_situacao); $i++) {
                                            if (isset($_SESSION['dados']['adms_sit_id']) and $_SESSION['dados']['adms_sit_id'] == $resultado_situacao[$i]['id']) {

                                                echo "<option value='" . $resultado_situacao[$i]['id'] . "' selected>" . $resultado_situacao[$i]['nome'] . "</option>";

                                                //Preencher com informações do banco de dados caso não tenha nenhum valor na sessão $_SESSION['dados]
                                            } elseif (!isset($_SESSION['dados']['adms_sit_id']) and (isset($result_edit_produto[$c]['adms_sit_id'])) and $result_edit_produto[$c]['adms_sit_id'] ==  $resultado_situacao[$i]['id']) {
                                                echo "<option value='" . $resultado_situacao[$i]['id'] . "' selected>" . $resultado_situacao[$i]['nome'] . "</option>";
                                            } else {
                                                echo "<option value='" . $resultado_situacao[$i]['id'] . "'> " . $resultado_situacao[$i]['nome'] . "</option>";
                                            }
                                        }

                                        ?>
                                    </select>
                                </div>

                                <p><span class="text-danger">*</span> Campo Obrogatório</p>
                                <input name="SendEditProduto" type="submit" class="btn btn-warning" value="Salvar">
                        </form>
                    <?php
                            }
                    ?>
                    </div>
                </div>
            </div>

            <?php

            include_once 'app/adms/include/rodape_lib.php';
            ?>
            </div>
            <script>
        
        
        document.getElementById("input").addEventListener("change", function(){
   this.value = parseFloat(this.value).toFixed(2);
});
    </script>
        </body>

<?php
    } else {
        $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! </div>";
        $url_destino = pg . '/listar/list_menu';
        header("Location: $url_destino");
    }
} else {
    $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! </div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
