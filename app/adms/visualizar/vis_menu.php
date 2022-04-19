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
            $resultado_MenuCadastrado = $pdo->pesquisarMenuCadastrado($id);

            if ($resultado_MenuCadastrado) {
            ?>
                <div class="content p-1">
                    <div class="list-group-item">
                        <div class="d-flex">
                            <div class="mr-auto p-2">
                                <h2 class="display-4 titulo">Detalhes Do Menu</h2>
                            </div>
                            <div class="p-2">
                                <?php
                                for ($i = 0; $i < count($resultado_MenuCadastrado); $i++) {
                                ?>
                                    <span class="d-none d-md-block">
                                        <?php
                                        //BOTAO VISUALIZAE
                                        $btn_list = $pdo->carregarBtn('listar/list_menu');

                                        if ($btn_list) {
                                            echo "<a href='" . pg . "/listar/list_menu?id=" . $resultado_MenuCadastrado[$i]['id'] . "' class='btn btn-outline-info btn-sm'>Listar</a>";
                                        }

                                        //BOTAR EDITAR
                                        $btn_edit = $pdo->carregarBtn('editar/edit_menu');

                                        if ($btn_edit) {
                                            echo " <a href='" . pg . "/editar/edit_menu?id=" . $resultado_MenuCadastrado[$i]['id'] . "' class='btn btn-outline-warning btn-sm'>Editar</a>";
                                        }

                                        //BOTAO PAGAR
                                        $btn_apagar = $pdo->carregarBtn('processa/apagar_menu');

                                        if ($btn_apagar) {
                                            echo " <a href='" . pg . "/processa/apagar_menu?id=" . $resultado_MenuCadastrado[$i]['id'] . "' class='btn btn-outline-danger btn-sm' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
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
                                                echo "<a class='dropdown-item' href='" . pg . "/listar/list_menu?id=" . $resultado_MenuCadastrado[$i]['id'] . "'>Listar</a>";
                                            }

                                            if ($btn_edit) {
                                                echo "<a class='dropdown-item' href='" . pg . "/editar/edit_menu?id=" . $resultado_MenuCadastrado[$i]['id'] . "'>Editar</a>";
                                            }

                                            if ($btn_apagar) {
                                                echo "<a class='dropdown-item' href='" . pg . "/processa/apagar_menu?id=" . $resultado_MenuCadastrado[$i]['id'] . "' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
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
                        for ($i=0; $i <count($resultado_MenuCadastrado) ; $i++) { 
                            
                        ?>        
                        <dl class="row">
                            <dt class="col-sm-3">ID</dt>
                            <dd class="col-sm-9"><?php echo $resultado_MenuCadastrado[$i]['id']; ?></dd>

                            <dt class="col-sm-3">Nome Página</dt>
                            <dd class="col-sm-9"><?php echo $resultado_MenuCadastrado[$i]['nome']; ?></dd>

                            <dt class="col-sm-3">Icone</dt>
                            <dd class="col-sm-9">
                                <?php
                                if(!empty($resultado_MenuCadastrado[$i]['icone'])){

                                echo "<i class='".$resultado_MenuCadastrado[$i]['icone']."'></i> : " . $resultado_MenuCadastrado[$i]['icone'];

                                }else{
                                    echo "VAZIO";
                                }
                                 ?>
                            </dd>

                            <dt class="col-sm-3">Ordem</dt>
                            <dd class="col-sm-9"><?php echo $resultado_MenuCadastrado[$i]['ordem']; ?></dd>

                            <dt class="col-sm-3">Situação</dt>
                            <dd class="col-sm-9">
                            <?php 
                                echo "<span class='badge bg-".$resultado_MenuCadastrado[$i]['cor_cores']."'> ".$resultado_MenuCadastrado[$i]['nome_sit']."</span>"; 
                            ?>
                            </dd>

                            <dt class="col-sm-3 text-truncate">Data Cadastro</dt>
                            <dd class="col-sm-9"><?php echo date('d/m/y H:i:s', strtotime($resultado_MenuCadastrado[$i]['created'])) ?></dd>

                            <dt class="col-sm-3 text-truncate">Data Edição</dt>
                            <dd class="col-sm-9"><?php
                                                    //verifica se a variavel data existe.
                                                    //usuei o date e formatei para ficar mais amigavel
                                                    if (!empty($resultado_MenuCadastrado[$i]['modified'])) {
                                                        echo date('d/m/y H:i:s', strtotime($resultado_MenuCadastrado[$i]['modified']));
                                                    }

                                                    ?>
                            </dd>

                        </dl>
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
                $_SESSION['msg'] = "<div class='alert alert-danger'> Menu não encontrado! </div>";
                $url_destino = pg . '/listar/list_menu';
                header("Location: $url_destino");
            }
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! </div>";
            $url_destino = pg . '/acesso/login';
            header("Location: $url_destino");
        }
