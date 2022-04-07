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
                        <h2 class="display-4 titulo">Cadastrar Menu</h2>
                    </div>
                    <div class="p-2">
                        <?php
                        //BOTAO VISUALIZAE
                        $btn_list = $pdo->carregarBtn('listar/list_menu');

                        if ($btn_list) {
                            echo "<a href='" . pg . "/listar/list_menu?id=" . $_SESSION['id'] . "' class='btn btn-outline-info btn-sm'>Listar</a>";
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
                <form method="POST" action="<?php echo pg; ?>/processa/proc_cad_menu">
                    <div class="form-group">
                        <label>
                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Nome do item de menu a ser apresentado no menu">
                            <i class="fas fa-question-circle"></i>       
                        </span>
                            <span class="text-danger">*</span> Nome
                        </label>
                        <input name="nome" type="text" class="form-control" placeholder="Nome do item de menu" value="<?php if (isset( $_SESSION['dados']['nome'])){echo  $_SESSION['dados']['nome'];}?>">
                    </div>

                    <div class="form-group">
                        <label>
                        <span tabindex="0" data-placement="top" data-toggle="tooltip" data-html="true" title="Página de ícone: <a href='https://fontawesome.com/v5.15/icons?d=gallery&p=2' target='_blank' >fontawesome</a>. Somente inserir o nome, Ex: fas fa-volume-up">
                            <i class="fas fa-question-circle"></i>       
                        </span>
                            <span class="text-danger">*</span> Ícone
                        </label>
                        <input name="icone" type="text" class="form-control" placeholder="Ícone da página" value="<?php if (isset( $_SESSION['dados']['icone'])){echo  $_SESSION['dados']['icone'];}?>">
                    </div>

                    <div class="form-group ">
                            <label>
                            <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Selecionar a situação do Menu.">
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
                    <input name="SendCadMenu" type="submit" class="btn btn-success" value="Cadastrar">
                </form>

            </div>
        </div>
    </div>
    <?php
    unset($_SESSION['dados']);
    include_once 'app/adms/include/rodape_lib.php';
    ?>
    </div>
</body>