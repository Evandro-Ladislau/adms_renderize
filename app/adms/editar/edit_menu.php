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
    $result_edit_menu = $pdo->VerificarMenusCadastradosNoBanco($id);

    //verificar se encontrou a página no banco de dados
    if ($result_edit_menu) {

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
                                <h2 class="display-4 titulo">Editar Menu</h2>
                            </div>
                            <div class="p-2">
                                <span class="d-none d-md-block">
                                    <?php

                                    for ($i = 0; $i < count($result_edit_menu); $i++) {
                                        //BOTAO LISTAR
                                        $btn_list = $pdo->carregarBtn('listar/list_menu');

                                        if ($btn_list) {
                                            echo "<a href='" . pg . "/listar/list_menu?id=" . $result_edit_menu[$i]['id'] . "' class='btn btn-outline-info btn-sm'>Listar</a>";
                                        }

                                        //BOTAR EDITAR
                                        $btn_vis = $pdo->carregarBtn('visualizar/vis_menu');

                                        if ($btn_vis) {
                                            echo " <a href='" . pg . "/visualizar/vis_menu?id=" . $result_edit_menu[$i]['id'] . "' class='btn btn-outline-primary btn-sm'>visualizar</a>";
                                        }

                                        //BOTAO PAGAR
                                        $btn_apagar = $pdo->carregarBtn('processa/apagar_menu');

                                        if ($btn_apagar) {
                                            echo " <a href='" . pg . "/processa/apagar_menu?id=" . $result_edit_menu[$i]['id'] . "' class='btn btn-outline-danger btn-sm' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
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
                                            echo "<a class='dropdown-item' href='" . pg . "/listar/list_menu?id=" . $result_edit_menu[$i]['id'] . "'>Listar</a>";
                                        }

                                        if ($btn_vis) {
                                            echo "<a class='dropdown-item' href='" . pg . "visualizar/vis_menu?id=" . $result_edit_menu[$i]['id'] . "'>visualizar</a>";
                                        }

                                        if ($btn_apagar) {
                                            echo "<a class='dropdown-item' href='" . pg . "/processa/apagar_menu?id=" . $_SESSION['id'] . "' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
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
                        <form method="POST" action="<?php echo pg; ?>/processa/proc_edit_menu">

                            <?php
                            for ($c = 0; $c < count($result_edit_menu); $c++) {
                                # code...
                            ?>
                                <input type="hidden" name="id" value="<?php if (isset($result_edit_menu[$c]['id'])) {
                                                                            echo $result_edit_menu[$c]['id'];
                                                                        } ?>">

                                <div class="form-group">
                                    <label>
                                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Nome do item de menu a ser apresentado no menu">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                        <span class="text-danger">*</span> Nome
                                    </label>
                                    <input name="nome" type="text" class="form-control" placeholder="Nome do item de menu" value="<?php if (isset($_SESSION['dados']['nome'])) {
                                                                                                                                        echo  $_SESSION['dados']['nome'];
                                                                                                                                    } elseif(isset($result_edit_menu[$c]['nome'])){
                                                                                                                                        echo $result_edit_menu[$c]['nome'];
                                                                                                                                    }
                                                                                            
                                                                                                                                    ?>">
                                </div>

                                <div class="form-group">
                                    <label>
                                        <span tabindex="0" data-placement="top" data-toggle="tooltip" data-html="true" title="Página de ícone: <a href='https://fontawesome.com/v5.15/icons?d=gallery&p=2' target='_blank' >fontawesome</a>. Somente inserir o nome, Ex: fas fa-volume-up">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                        <span class="text-danger">*</span> Ícone
                                    </label>
                                    <input name="icone" type="text" class="form-control" placeholder="Ícone da página" value="<?php if (isset($_SESSION['dados']['icone'])) {
                                                                                                                                    echo  $_SESSION['dados']['icone'];
                                                                                                                                } elseif(isset($result_edit_menu[$c]['icone'])){
                                                                                                                                    echo $result_edit_menu[$c]['icone'];
                                                                                                                                }
                                                                                                                                ?>">
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
                                            if (isset($_SESSION['dados']['adms_sit_id']) and $_SESSION['dados']['adms_sit_id'] == $resultado_situacao[$i]['id']) {

                                                echo "<option value='" . $resultado_situacao[$i]['id'] . "' selected>" . $resultado_situacao[$i]['nome'] . "</option>";
                                                
                                            //Preencher com informações do banco de dados caso não tenha nenhum valor na sessão $_SESSION['dados]
                                        }elseif (!isset($_SESSION['dados']['adms_sit_id']) AND (isset($result_edit_menu[$c]['adms_sit_id'])) AND $result_edit_menu[$c]['adms_sit_id'] ==  $resultado_situacao[$i]['id']) {
                                            echo "<option value='" . $resultado_situacao[$i]['id'] . "' selected>" . $resultado_situacao[$i]['nome'] . "</option>";
                                            }
                                             else {
                                                echo "<option value='" . $resultado_situacao[$i]['id'] . "'> " . $resultado_situacao[$i]['nome'] . "</option>";
                                            }
                                        }

                                        ?>
                                    </select>
                                </div>

                                <p><span class="text-danger">*</span> Campo Obrogatório</p>
                                <input name="SendEditMenu" type="submit" class="btn btn-warning" value="Salvar">
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
