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
    $result_edit_pg = $pdo->VerificarPaginasCadastradasNoBanco($id);

    //verificar se encontrou a página no banco de dados
    if ($result_edit_pg) {
        
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
                                <h2 class="display-4 titulo">Editar Página</h2>
                            </div>
                            <div class="p-2">
                            <span class="d-none d-md-block">
                                    <?php

                                    for ($i=0; $i <count($result_edit_pg) ; $i++) { 
                                       //BOTAO LISTAR
                                    $btn_list = $pdo->carregarBtn('listar/list_pagina');

                                    if ($btn_list) {
                                        echo "<a href='" . pg . "/listar/list_pagina?id=" . $result_edit_pg[$i]['id'] . "' class='btn btn-outline-info btn-sm'>Listar</a>";
                                    }

                                    //BOTAR EDITAR
                                    $btn_vis = $pdo->carregarBtn('visualizar/vis_pagina');

                                    if ($btn_vis) {
                                        echo " <a href='" . pg . "/visualizar/vis_pagina?id=" . $result_edit_pg[$i]['id'] . "' class='btn btn-outline-primary btn-sm'>visualizar</a>";
                                    }

                                    //BOTAO PAGAR
                                    $btn_apagar = $pdo->carregarBtn('processa/apagar_pagina');

                                    if ($btn_apagar) {
                                        echo " <a href='" . pg . "/processa/apagar_pagina?id=" . $result_edit_pg[$i]['id'] . "' class='btn btn-outline-danger btn-sm' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
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
                                            echo "<a class='dropdown-item' href='" . pg . "/listar/list_pagina?id=" . $result_edit_pg[$i]['id'] . "'>Listar</a>";
                                        }

                                        if ($btn_vis) {
                                            echo "<a class='dropdown-item' href='" . pg . "visualizar/vis_pagina?id=" . $result_edit_pg[$i]['id'] . "'>visualizar</a>";
                                        }

                                        if ($btn_apagar) {
                                            echo "<a class='dropdown-item' href='" . pg . "/processa/apagar_pagina?id=" . $_SESSION['id'] . "' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
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
                        <form method="POST" action="<?php echo pg; ?>/processa/proc_edit_pagina">
                        
                        <?php
                        for ($c=0; $c <count($result_edit_pg) ; $c++) { 
                            # code...
                        ?>
                        <input type="hidden" name="id" value="<?php if (isset($result_edit_pg[$c]['id'])) {
                                                                            echo $result_edit_pg[$c]['id'];
                                                                    } ?>">
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label>
                                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Nome da página a ser apresentada no menu ou listar página">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                        <span class="text-danger">*</span> Nome
                                    </label>
                                    <input name="nome_pagina" type="text" class="form-control" placeholder="Nome da pagina" id="nome" value="
                                    <?php 
                                    if(isset($_SESSION['dados']['nome_pagina'])){
                                        echo  $_SESSION['dados']['nome_pagina'];

                                        }elseif(isset($result_edit_pg[$c]['nome_pagina'])){
                                            echo $result_edit_pg[$c]['nome_pagina'];
                                        }

                                    ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label><span class="text-danger">*</span> Endereço</label>
                                    <input name="endereco" type="text" class="form-control" placeholder="Endereço da página, ex: listar/list_pagina" id="email" value="<?php if (isset($_SESSION['dados']['endereco'])) {
                                                                                                                                                                            echo  $_SESSION['dados']['endereco'];
                                                                                                                                                                        }elseif(isset($result_edit_pg[$c]['endereco'])){
                                                                                                                                                                            echo $result_edit_pg[$c]['endereco'];
                                                                                                                                                                        }
                                                                                                                                                                         ?>">
                                </div>

                                <div class="form-group col-md-3">
                                    <label>
                                        <span tabindex="0" data-placement="top" data-toggle="tooltip" data-html="true" title="Página de ícone: <a href='https://fontawesome.com/v5.15/icons?d=gallery&p=2' target='_blank' >fontawesome</a>. Somente inserir o nome, Ex: fas fa-volume-up">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                        Ícone
                                    </label>
                                    <input name="icone" type="text" class="form-control" placeholder="Ícone da página" id="email" value="<?php if (isset($_SESSION['dados']['icone'])) {
                                                                                                                                                echo  $_SESSION['dados']['icone'];
                                                                                                                                            }elseif(isset($result_edit_pg[$c]['icone'])){
                                                                                                                                                echo $result_edit_pg[$c]['icone'];
                                                                                                                                            }
                                                                                                                                             ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Observação</label>
                                <textarea name="obs" class="form-control"><?php if (isset($_SESSION['dados']['obs'])) {
                                                                                echo  $_SESSION['dados']['obs'];
                                                                            }elseif(isset($result_edit_pg[$c]['obs'])){
                                                                                echo $result_edit_pg[$c]['obs'];
                                                                            }
                                                                             ?></textarea>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label>
                                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Principais palavras que indicam a função da página, por exemplo na página login: pagina de login, login. Máximo 180 letras.">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                        <span class="text-danger">*</span> Palavra Chave
                                    </label>
                                    <input name="keywords" type="text" class="form-control" placeholder="Palavra Chave" id="nome" value="<?php if (isset($_SESSION['dados']['keywords'])) {
                                                                                                                                                echo  $_SESSION['dados']['keywords'];
                                                                                                                                            }elseif(isset($result_edit_pg[$c]['keywords'])){
                                                                                                                                                echo $result_edit_pg[$c]['keywords'];
                                                                                                                                            }
                                                                                                                                             ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>
                                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Resumo do principal objetivo da página, máximo 180 letras.">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                        <span class="text-danger">*</span> Descrição
                                    </label>
                                    <input name="description" type="text" class="form-control" placeholder=" Descrição da página" id="email" value="<?php if (isset($_SESSION['dados']['description'])) {
                                                                                                                                                        echo  $_SESSION['dados']['description'];
                                                                                                                                                    }elseif(isset($result_edit_pg[$c]['descriptio'])){
                                                                                                                                                        echo $result_edit_pg[$c]['descriptio'];
                                                                                                                                                    }
                                                                                                                                                     ?>">
                                </div>

                                <div class="form-group col-md-3">
                                    <label>
                                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Desenvolvedor responsável pela criação da página.">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                        <span class="text-danger">*</span>Autor
                                    </label>
                                    <input name="author" type="text" class="form-control" placeholder="Desenvolvedor" id="email" value="<?php if (isset($_SESSION['dados']['author'])) {
                                                                                                                                            echo  $_SESSION['dados']['author'];
                                                                                                                                        }elseif(isset($result_edit_pg[$c]['author'])){
                                                                                                                                            echo $result_edit_pg[$c]['author'];
                                                                                                                                        }
                                                                                                                                         ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>
                                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Informa ao navegador se deve indexar seguir, não indexar, não seguir... a página. ">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                        <span class="text-danger">*</span> Indexar
                                    </label>
                                    <select name="adms_robot_id" id="adms_robot_id" class="form-control">
                                        <option value=" ">Selecione</option>
                                        <?php
                                        $resultado_robots = $pdo->listarRobots();
                                        for ($i = 0; $i < count($resultado_robots); $i++) {
                                            if (isset($_SESSION['dados']['adms_robot_id']) and $_SESSION['dados']['adms_robot_id'] == $resultado_robots[$i]['id']) {
                                                echo "<option value='" . $resultado_robots[$i]['id'] . "' selected>" . $resultado_robots[$i]['nome'] . "</option>";
                                                //Preencher com informações do banco de dados caso não tenha nenhum valor na sessão $_SESSION['dados]
                                            }elseif (!isset($_SESSION['dados']['adms_robot_id']) AND (isset($result_edit_pg[$c]['adms_robot_id'])) AND $result_edit_pg[$c]['adms_robot_id'] ==  $resultado_robots[$i]['id']) {
                                                echo "<option value='" . $resultado_robots[$i]['id'] . "' selected>" . $resultado_robots[$i]['nome'] . "</option>";
                                                }
                                            else {
                                                echo "<option value='" . $resultado_robots[$i]['id'] . "'>" . $resultado_robots[$i]['nome'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>
                                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Página pública significa que para acessar a página não é necessário fazer login ou estar logado no adminstrativo">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                        <span class="text-danger">*</span> Página pública
                                    </label>
                                    <select name="lib_pub" id="lib_pub" class="form-control">
                                        <?php
                                        if ((isset($_SESSION['dados']['lib_pub']) AND ($_SESSION['dados']['lib_pub'] == 1) OR (isset($result_edit_pg[$c]['lib_pub']) AND ($result_edit_pg[$c]['lib_pub'] == 1) ))) {
                                            echo "<option value=' ' >Selecione</option>";
                                            echo "<option value='1' selected>Sim</option>";
                                            echo "<option value='2'>Não</option>";
                                        } elseif ((isset($_SESSION['dados']['lib_pub']) AND ($_SESSION['dados']['lib_pub'] == 2)) OR (isset($result_edit_pg[$c]['lib_pub']) AND ($result_edit_pg[$c]['lib_pub'] == 2))) {
                                            echo "<option value=' ' >Selecione</option>";
                                            echo "<option value='1' >Sim</option>";
                                            echo "<option value='2' selected>Não</option>";
                                        } else {
                                            echo "<option value=' ' selected>Selecione</option>";
                                            echo "<option value='1'>Sim</option>";
                                            echo "<option value='2'>Não</option>";
                                        }
                                        ?>

                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>
                                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Quando a página é dependente, por exemplo 'processa/proc_cad_usuario' é dependente da página 'cadastrar/cad_usuario', ao liberar a página 'cadastrar/cad_usuario' é liberado automaticamente a página 'processa/proc_cad_usuario'. ">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                        <span class="text-danger">*</span>Página Dependente
                                    </label>
                                    <select name="depend_pg" id="depend_pg" class="form-control">
                                        <option value=" ">Selecione</option>
                                        <?php
                                        $resultado_dependentes = $pdo->listarDependentes();
                                        if ((isset($_SESSION['dados']['depend_pg']) and $_SESSION['dados']['depend_pg'] == 0) OR (isset($result_edit_pg[$c]['depend_pg']) AND ($result_edit_pg[$c]['depend_pg']==0))) {
                                            echo "<option value='0'selected>Não depende de outra página</option>";
                                        } else {
                                            echo "<option value='0'>Não depende de outra página</option>";
                                        }
                                        for ($i = 0; $i < count($resultado_dependentes); $i++) {


                                            if (isset($_SESSION['dados']['depend_pg']) and $_SESSION['dados']['depend_pg'] == $resultado_dependentes[$i]['id']) {

                                                echo "<option value='" . $resultado_dependentes[$i]['id'] . "'selected>" . $resultado_dependentes[$i]['nome_pagina'] . "</option>";
                                                //Preencher com informações do banco de dados caso não tenha nenhum valor na sessão $_SESSION['dados]
                                        $resultado_situacao_paginas = $pdo->listarSituacaoPaginas();
                                    }elseif (!isset($_SESSION['dados']['depend_pg']) AND (isset($result_edit_pg[$c]['depend_pg'])) AND $result_edit_pg[$c]['depend_pg'] ==  $resultado_dependentes[$i]['id']) {
                                        echo "<option value='" . $resultado_dependentes[$i]['id'] . "' selected>" . $resultado_dependentes[$i]['nome_pagina'] . "</option>";
                                            } else {
                                                echo "<option value='" . $resultado_dependentes[$i]['id'] . "'>" . $resultado_dependentes[$i]['nome_pagina'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>
                                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Grupo que a página pertence">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                        <span class="text-danger">*</span> Grupo
                                    </label>
                                    <select name="adms_grps_pg_id" id="adms_grps_pg_id" class="form-control">
                                        <option selected>Selecione</option>
                                        <?php
                                        $resultado_grupos = $pdo->listarGrupos();
                                        for ($i = 0; $i < count($resultado_grupos); $i++) {


                                            if (isset($_SESSION['dados']['adms_grps_pg_id']) and $_SESSION['dados']['adms_grps_pg_id'] == $resultado_grupos[$i]['id']) {

                                                echo "<option value='" . $resultado_grupos[$i]['id'] . "'selected>" . $resultado_grupos[$i]['nome'] . "</option>";
                                                //Preencher com informações do banco de dados caso não tenha nenhum valor na sessão $_SESSION['dados]
                                        $resultado_situacao_paginas = $pdo->listarSituacaoPaginas();
                                    }elseif (!isset($_SESSION['dados']['adms_grps_pg_id']) AND (isset($result_edit_pg[$c]['adms_grps_pg_id'])) AND $result_edit_pg[$c]['adms_grps_pg_id'] ==  $resultado_grupos[$i]['id']) {
                                        echo "<option value='" . $resultado_grupos[$i]['id'] . "' selected>" . $resultado_grupos[$i]['nome'] . "</option>";
                                            } else {
                                                echo "<option value='" . $resultado_grupos[$i]['id'] . "'>" . $resultado_grupos[$i]['nome'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>
                                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Tipo neste caso é o projeto em que a página pertence">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                        <span class="text-danger">*</span> Tipo
                                    </label>
                                    <select name="adms_tps_pg_id" id="adms_tps_pg_id" class="form-control">
                                        <option selected>Selecione</option>
                                        <?php
                                        $resultado_tipos_paginas = $pdo->listarTiposPaginas();
                                        for ($i = 0; $i < count($resultado_tipos_paginas); $i++) {
                                            if (isset($_SESSION['dados']['adms_tps_pg_id']) and $_SESSION['dados']['adms_tps_pg_id'] == $resultado_tipos_paginas[$i]['id']) {
                                                echo "<option value='" . $resultado_tipos_paginas[$i]['id'] . "'selected>" . $resultado_tipos_paginas[$i]['tipo'] . " - " . $resultado_tipos_paginas[$i]['nome'] . "</option>";
                                                //Preencher com informações do banco de dados caso não tenha nenhum valor na sessão $_SESSION['dados]
                                        $resultado_situacao_paginas = $pdo->listarSituacaoPaginas();
                                    }elseif (!isset($_SESSION['dados']['adms_tps_pg_id']) AND (isset($result_edit_pg[$c]['adms_tps_pg_id'])) AND $result_edit_pg[$c]['adms_tps_pg_id'] ==  $resultado_tipos_paginas[$i]['id']) {
                                        echo "<option value='" . $resultado_tipos_paginas[$i]['id'] . "' selected>" . $resultado_tipos_paginas[$i]['nome'] . "</option>";
                                            } else {
                                                echo "<option value='" . $resultado_tipos_paginas[$i]['id'] . "'>" . $resultado_tipos_paginas[$i]['tipo'] . " - " . $resultado_tipos_paginas[$i]['nome'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>
                                        <span tabindex="0" data-placement="top" data-toggle="tooltip" title="Situação da página cadastrada">
                                            <i class="fas fa-question-circle"></i>
                                        </span>
                                        <span class="text-danger">*</span>Situação
                                    </label>
                                    <select name="adms_sits_pg_id" id="adms_sits_pg_id" class="form-control">
                                        <option selected>Selecione</option>
                                        <?php
                                        $resultado_situacao_paginas = $pdo->listarSituacaoPaginas();
                                        for ($i = 0; $i < count($resultado_situacao_paginas); $i++) {
                                            if (isset($_SESSION['dados']['adms_sits_pg_id']) and $_SESSION['dados']['adms_sits_pg_id'] == $resultado_situacao_paginas[$i]['id']) {

                                                echo "<option value='" . $resultado_situacao_paginas[$i]['id'] . "' selected>" . $resultado_situacao_paginas[$i]['nome'] . "</option>";
                                                //Preencher com informações do banco de dados caso não tenha nenhum valor na sessão $_SESSION['dados]
                                        $resultado_situacao_paginas = $pdo->listarSituacaoPaginas();
                                            }elseif (!isset($_SESSION['dados']['adms_sits_pg_id']) AND (isset($result_edit_pg[$c]['adms_sits_pg_id'])) AND $result_edit_pg[$c]['adms_sits_pg_id'] ==  $resultado_situacao_paginas[$i]['id']) {
                                                echo "<option value='" . $resultado_situacao_paginas[$i]['id'] . "' selected>" . $resultado_situacao_paginas[$i]['nome'] . "</option>";
                                            } else {
                                                echo "<option value='" . $resultado_situacao_paginas[$i]['id'] . "'> " . $resultado_situacao_paginas[$i]['nome'] . "</option>";
                                            }
                                        }

                                        ?>
                                    </select>
                                </div>
                            </div>
                            <p><span class="text-danger">*</span> Campo Obrogatório</p>
                            <input name="SendEditPg" type="submit" class="btn btn-warning" value="Salvar">
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
        $url_destino = pg . '/listar/list_pagina';
        header("Location: $url_destino");
    }
} else {
    $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! </div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
