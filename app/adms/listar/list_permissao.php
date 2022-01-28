<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';
include_once 'app/adms/include/head.php';
//receber o id do nivel de acesso passado pela URL ao clicar no botão permissão passando um filtro.
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);


if (!empty($id)) {
    //receber o número da página
    $pagina_atual = filter_input(INPUT_GET, 'pagina', FILTER_SANITIZE_NUMBER_INT);

    //determinar em qual página o usuário esta
    $pagina = (!empty($pagina_atual) ? $pagina_atual : 1);

    //setar a quantidade de resultado por página
    $qnt_result_pg = 5;

    //Calcular o inicio da visualização

    $inicio = ($qnt_result_pg * $pagina) - $qnt_result_pg;

    if ($_SESSION['adms_niveis_acesso_id'] == 1) {
        $result_niv_ac = $pdo->permissaoSuperAdministrador($id, $inicio, $qnt_result_pg);
    } else {
    }

    //verificar se ele encontrar algum cadastro.
    if ($result_niv_ac) {

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
                                <?php
                                $result_nome_nivel_acesso = $pdo->nomeNivelAcesso($id);
                                ?>
                                <h2 class="display-4 titulo">Listar Permissões - <?php

                                                                                    for ($c = 0; $c < count($result_nome_nivel_acesso); $c++) {
                                                                                        echo $result_nome_nivel_acesso[$c]['nome'];
                                                                                    }


                                                                                    ?>

                                </h2>
                            </div>
                            <div class="p-2">
                                <?php
                                $btn_list = $pdo->carregarBtn('listar/list_niv_aces');

                                if ($btn_list) {
                                    echo "<a href='" . pg . "/listar/list_niv_aces' class='btn btn-outline-info btn-sm'>Listar</a>";
                                }
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

                        <div class="table-responsive">
                            <table class="table table table-bordered table-striped table-hover ">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Página</th>
                                        <th class="d-none d-sm-table-cell">Permissão</th>
                                        <th class="d-none d-sm-table-cell">Menu</th>
                                        <th class="d-none d-sm-table-cell">Dropdown</th>
                                        <th class="d-none d-sm-table-cell">Ordem</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    //pegando as informações do nivel de acesso cadastradas no banco de dados.     
                                    for ($i = 0; $i < count($result_niv_ac); $i++) {
                                    ?>
                                        <tr>
                                            <td><?php echo $result_niv_ac[$i]['id'] ?></td>
                                            <td>
                                                <span tabindex="0" data-placement="top" data-toggle="tooltip" title="<?php echo $result_niv_ac[$i]['obs'] ?>">
                                                    <i class="fas fa-question-circle"></i>
                                                </span>
                                                <?php echo $result_niv_ac[$i]['nome_pagina'] ?>
                                            </td>
                                            <td class="d-none d-sm-table-cell"><?php echo $result_niv_ac[$i]['permissao'] ?></td>
                                            <td class="d-none d-sm-table-cell"><?php echo $result_niv_ac[$i]['lib_menu'] ?></td>
                                            <td class="d-none d-sm-table-cell"><?php echo $result_niv_ac[$i]['dropdown'] ?></td>
                                            <td class="d-none d-sm-table-cell"><?php echo $result_niv_ac[$i]['ordem'] ?></td>
                                            <td>Ações</td>

                                        <?php
                                    }
                                        ?>
                                </tbody>
                            </table>
                            <?php
                            //chama a funcao com a quantidade de registro.
                            $result_pg = $pdo->paginacaoPermissao($id);

                            for ($cont = 0; $cont < count($result_pg); $cont++) {
                                //quantidade de pagina
                                $quantidade_pg = ceil($result_pg[$cont]['num_result'] / $qnt_result_pg);
                                //limitar os links antes    
                                $max_links = 2;
                                echo "<nav aria-label='paginacao'>";
                                echo "<ul class='pagination pagination-sm justify-content-center'>";
                                echo "<li class='page-item'>";
                                echo "<a class='page-link' href='" . pg . "/listar/list_permissao?id=$id&pagina=1' tabindex='-1'>Primeira</a>";
                                echo "</li>";

                                //mostra a pagina anterior a pagina que o usuario esta
                                //o if é para a pagina ser mostrada só se o valor for maior que 1
                                for ($pag_ant = $pagina - $max_links; $pag_ant <= $pagina - 1; $pag_ant++) {
                                    if ($pag_ant >= 1) {
                                        echo "<li class='page-item'><a class='page-link' href='" . pg . "/listar/list_permissao?id=$id&pagina=$pag_ant'>$pag_ant</a></li>";
                                    }
                                }

                                //pagina onde o usuario esta (pagina_atual)    
                                echo "<li class='page-item active'>";
                                echo "<a class='page-link' href='#'>$pagina</a>";
                                echo "</li>";


                                //mostra pagina posterior de onde o usuario esta
                                for ($pag_dep = $pagina + 1; $pag_dep <= $pagina + $max_links; $pag_dep++) {
                                    if ($pag_dep <= $quantidade_pg) {
                                        echo "<li class='page-item'><a class='page-link' href='" . pg . "/listar/list_permissao?id=$id&pagina=$pag_dep'>$pag_dep</a></li>";
                                    }
                                }

                                //limita o link depois
                                echo "<li class='page-item'>";
                                echo "<a class='page-link' href='" . pg . "/listar/list_permissao?id=$id&pagina=$quantidade_pg'>Ultima</a>";
                                echo "</li>";
                                echo "</ul>";
                                echo "</nav>";
                            }
                            ?>

                        </div>
                    </div>
                    <?php

                    include_once 'app/adms/include/rodape_lib.php';
                    ?>
                </div>
            </div>
        </body>
<?php
    } else {
        $_SESSION['msg'] = "<div class='alert alert-danger'> Permissão não encontrada! </div>";
        $url_destino = pg . '/listar/list_niv_aces';
        header("Location: $url_destino");
    }
} else {
    $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! </div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
?>