<?php
if (!isset($seguranca)) {
    exit;
}

//pega o id passado pela url
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!empty($id)) {


    require_once './app/adms/models/Conexao.php';
    require_once '../adm/index.php';
    include_once 'app/adms/include/head.php';

    //chama a funcao que verifica se existe id no bd
    $result_edit_pg = $pdo->editarPaginaPermissao($id);

    //se existir ele acessa esse if e impreme a pagina
    if ($result_edit_pg) {


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
                                <h2 class="display-4 titulo">Editar Permissão</h2>
                            </div>
                            <div class="p-2">
                                <span class="d-none d-md-block">
                                    <?php

                                    for ($i = 0; $i < count($result_edit_pg); $i++) {
                                        //BOTAO LISTAR
                                        $btn_list = $pdo->carregarBtn('listar/list_permissao');

                                        if ($btn_list) {
                                            echo "<a href='" . pg . "/listar/list_permissao?id=" . $result_edit_pg[$i]['adms_niveis_acesso_id'] . "' class='btn btn-outline-info btn-sm'>Listar</a>";
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
                                            echo "<a class='dropdown-item' href='" . pg . "/listar/list_permissao?id=" . $result_edit_pg[$i]['adms_niveis_acesso_id'] . "'>Listar</a>";
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
                        <form method="POST" action="<?php echo pg; ?>/processa/proc_edit_permissao">
                            <input type="hidden" name="id" value="<?php echo $result_edit_pg[$i]['id']; ?>">
                            <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>
                                    <span tabindex="0" data-placement="top" data-toggle="tooltip" data-html="true" title="Página de ícone: <a href='https://fontawesome.com/v5.15/icons?d=gallery&p=2' target='_blank' >fontawesome</a>. Somente inserir o nome, Ex: fas fa-volume-up">
                                        <i class="fas fa-question-circle"></i>
                                    </span> Ícone
                                </label>
                                <input name="icone" type="text" class="form-control" placeholder="Página de ícone" value="<?php
                                                                                                                            for ($j = 0; $j < count($result_edit_pg); $j++) {
                                                                                                                                if (isset($_SESSION['dados']['icone'])) {
                                                                                                                                    echo $_SESSION['dados']['icone'];
                                                                                                                                } elseif (isset($result_edit_pg[$j]['icone'])) {
                                                                                                                                    echo $result_edit_pg[$j]['icone'];
                                                                                                                                }
                                                                                                                            }
                                                                                                                            ?>
                                ">
                            </div>

                            <div class="form-group col-md-6">
                                <?php
                                $result_menus = $pdo->resultadoMenus();
                                ?>
                                <label>
                                    <span tabindex="0" data-placement="top" data-toggle="tooltip" data-html="true" title="Selecionar para qual item de menu pertence a página">
                                        <i class="fas fa-question-circle"></i>
                                    </span><span class="text-danger">*</span> Item Menu
                                </label>
                                <select name="adms_menu_id" id="adms_menu_id" class="form-control">
                                    <option value="">Selecione</option>
                                    <?php
                                    for ($c = 0; $c < count($result_menus); $c++) {
                                        if (isset($_SESSION['dados']['adms_menu_id']) and $_SESSION['dados']['adms_menu_id'] == $result_menus[$c]['id']) {
                                            echo "<option value='" . $result_menus[$c]['id'] . "'selected>" . $result_menus[$c]['nome'] . "</option>";
                                        }
                                        //Preencher com informações do banco de dados caso não tenha nenhum valor salvo na sessão $_SESSION['dados']
                                        elseif (!isset($_SESSION['dados']['adms_menu_id']) and isset($result_menus['adms_menu_id']) and ($result_menus['adms_menu_id'] == $result_menus['id'])) {
                                            echo "<option value='" . $result_menus[$c]['id'] . "' selected>" . $result_menus[$c]['nome'] . "</option>";
                                        } else {
                                            echo "<option value='" . $result_menu[$c]['id'] . "'>" . $result_menus[$c]['nome'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            </div>
                            <p><span class="text-danger">*</span> Campo Obrigatório</p>
                            <input name="SendEditNivAC" type="submit" class="btn btn-warning" value="Salvar">
                        </form>

                    </div>
                </div>
            </div>
            <?php

            include_once 'app/adms/include/rodape_lib.php';
            ?>
            </div>
        </body>
<?php
        //se nao redireciona para a pagina listar
    } else {
        $_SESSION['msg'] = "<div class='alert alert-danger'> Nível de acesso Não encontrado! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    <span aria-hidden='true'>&times;</span>
</button> </div>";
        $url_destino = pg . '/listar/list_niv_aces';
        header("Location: $url_destino");
    }
} else {
    $_SESSION['msg'] = "<div class='alert alert-danger'> Nível de acesso Não encontrado! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
        </button> </div>";
    $url_destino = pg . '/listar/list_niv_aces';
    header("Location: $url_destino");
}
