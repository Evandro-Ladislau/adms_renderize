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
                        <h2 class="display-4 titulo">Cadastrar Produto</h2>
                    </div>
                    <div class="p-2">
                        <?php
                        //BOTAO VISUALIZAE
                        $btn_list = $pdo->carregarBtn('listar/list_produto');

                        if ($btn_list) {
                            echo "<a href='" . pg . "/listar/list_produto?id=" . $_SESSION['id'] . "' class='btn btn-outline-info btn-sm'>Listar</a>";
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
                <form method="POST" action="<?php echo pg; ?>/processa/proc_cad_produto">
                    <div class="form-group">
                        <label>
                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Nome do produto">
                            <i class="fas fa-question-circle"></i>       
                        </span>
                            <span class="text-danger">*</span> Descricao
                        </label>
                        <input name="descricao" type="text" class="form-control" placeholder="Nome do produto" value="<?php if (isset( $_SESSION['dados']['descricao'])){echo  $_SESSION['dados']['descricao'];}?>">
                    </div>

                    <div class="row">
                    <div class="form-group col-md-3">
                        <label>
                        <span tabindex="0" data-placement="top" data-toggle="tooltip" data-html="true" title="Quantidade de estoque do produto">
                            <i class="fas fa-question-circle"></i>       
                        </span>
                            <span class="text-danger">*</span> estoque
                        </label>
                        <input name="estoque" type="number" step="0.010" class="form-control" placeholder="0.00" value="<?php if (isset( $_SESSION['dados']['estoque'])){echo  $_SESSION['dados']['estoque'];}?>">
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
                                    if (isset($_SESSION['dados']['adms_unidade_id']) AND $_SESSION['dados']['adms_unidade_id']==$resultado_unidades[$i]['id'] ) {

                                        echo "<option value='" . $resultado_unidades[$i]['id'] . "' selected>".$resultado_unidades[$i]['nome']."</option>";
                                    }else{
                                        echo "<option value='" . $resultado_unidades[$i]['id'] . "'> ".$resultado_unidades[$i]['nome']."</option>";
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
                        <input id="input" name="preco_custo" type="number" step="0.010" class="form-control" placeholder="0.00" value="<?php if (isset( $_SESSION['dados']['estoque'])){echo  $_SESSION['dados']['estoque'];}?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label>
                        <span tabindex="0" data-placement="top" data-toggle="tooltip" data-html="true" title="Preço de venda do produto">
                            <i class="fas fa-question-circle"></i>       
                        </span>
                            <span class="text-danger">*</span> Preco de Venda
                        </label>
                        <input name="preco_venda" type="number" step="0.010" class="form-control" placeholder="0.00" value="<?php if (isset( $_SESSION['dados']['estoque'])){echo  $_SESSION['dados']['estoque'];}?>">
                    </div>
                    
                    </div>

                    <div class="form-group ">
                            <label>
                            <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Selecionar a situação do produto.">
                            <i class="fas fa-question-circle"></i>       
                            </span>
                                <span class="text-danger">*</span>Situação
                            </label>
                            <select name="adms_sit_id" id="adms_sit_id" class="form-control">
                                <option selected>Selecione</option>
                                <?php
                                $resultado_situacao = $pdo->listarSituacaoMenu();
                                for ($i = 0; $i < count($resultado_situacao); $i++) {
                                    if (isset($_SESSION['dados']['adms_sit_id']) AND $_SESSION['dados']['adms_sit_id']==$resultado_situacao[$i]['id'] ) {

                                        echo "<option value='" . $resultado_situacao[$i]['id'] . "' selected>".$resultado_situacao[$i]['nome']."</option>";
                                    }else{
                                        echo "<option value='" . $resultado_situacao[$i]['id'] . "'> ".$resultado_situacao[$i]['nome']."</option>";
                                    }
                                    
                                }
                                
                                ?>
                            </select>
                        </div>

                    
                    <p><span class="text-danger">*</span> Campo Obrogatório</p>
                    <input name="SendCadProduto" type="submit" class="btn btn-success" value="Cadastrar">
                </form>

            </div>
        </div>
    </div>
    <?php
    unset($_SESSION['dados']);
    include_once 'app/adms/include/rodape_lib.php';
    ?>
    <script>
        
        
        document.getElementById("input").addEventListener("change", function(){
   this.value = parseFloat(this.value).toFixed(2);
});
    </script>
    </div>
</body>