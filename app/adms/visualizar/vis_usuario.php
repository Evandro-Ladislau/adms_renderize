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
            if ($_SESSION['adms_niveis_acesso_id'] == 1) {
            $resultado_UsuariosCadastrados = $pdo->pesquisarUsuariosCadastradosSuper($id);
            }else{
                
            }

            if ($resultado_UsuariosCadastrados) {
            ?>
                <div class="content p-1">
                    <div class="list-group-item">
                        <div class="d-flex">
                            <div class="mr-auto p-2">
                                <h2 class="display-4 titulo">Detalhes Usuarios</h2>
                            </div>
                            <div class="p-2">
                                <?php
                                for ($i = 0; $i < count($resultado_UsuariosCadastrados); $i++) {
                                ?>
                                    <span class="d-none d-md-block">
                                        <?php
                                        //BOTAO VISUALIZAE
                                        $btn_list = $pdo->carregarBtn('listar/list_usuario');

                                        if ($btn_list) {
                                            echo "<a href='" . pg . "/listar/list_usuario?id=" . $resultado_UsuariosCadastrados[$i]['id'] . "' class='btn btn-outline-info btn-sm'>Listar</a>";
                                        }

                                        //BOTAR EDITAR
                                        $btn_edit = $pdo->carregarBtn('editar/edit_usuario');

                                        if ($btn_edit) {
                                            echo " <a href='" . pg . "/editar/edit_usuario?id=" . $resultado_UsuariosCadastrados[$i]['id'] . "' class='btn btn-outline-warning btn-sm'>Editar</a>";
                                        }

                                        //BOTAO PAGAR
                                        $btn_apagar = $pdo->carregarBtn('processa/apagar_usuario');

                                        if ($btn_apagar) {
                                            echo " <a href='" . pg . "/processa/apagar_usuario?id=" . $resultado_UsuariosCadastrados[$i]['id'] . "' class='btn btn-outline-danger btn-sm' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
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
                                                echo "<a class='dropdown-item' href='" . pg . "/listar/list_usuario?id=" . $resultado_UsuariosCadastrados[$i]['id'] . "'>Listar</a>";
                                            }

                                            if ($btn_edit) {
                                                echo "<a class='dropdown-item' href='" . pg . "/editar/edit_usuario?id=" . $resultado_UsuariosCadastrados[$i]['id'] . "'>Editar</a>";
                                            }

                                            if ($btn_apagar) {
                                                echo "<a class='dropdown-item' href='" . pg . "/processa/apagar_usuario?id=" . $resultado_UsuariosCadastrados[$i]['id'] . "' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
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
                        for ($i=0; $i <count($resultado_UsuariosCadastrados) ; $i++) { 
                            
                        ?>        
                        <dl class="row">

                            <dt class="col-sm-3">Imagem</dt>
                            <dd class="col-sm-9">
                                <?php
                                if (!empty($resultado_UsuariosCadastrados[$i]['imagem'])) {
                                    echo "<img class='rounded-circle' src='" . pg . "/assets/imagens/usuario/" . $resultado_UsuariosCadastrados[$i]['id'] . "/" . $resultado_UsuariosCadastrados[$i]['imagem'] . "' width='150' height='150'> &nbsp; <span class='d-none d-sm-inline'>";
                                } 
                                 
                                ?>
                            </dd>


                            <dt class="col-sm-3">ID</dt>
                            <dd class="col-sm-9"><?php echo $resultado_UsuariosCadastrados[$i]['id']; ?></dd>

                            <dt class="col-sm-3">Nome</dt>
                            <dd class="col-sm-9"><?php echo $resultado_UsuariosCadastrados[$i]['nome']; ?></dd>

                            <dt class="col-sm-3">Apelido</dt>
                            <dd class="col-sm-9"><?php echo $resultado_UsuariosCadastrados[$i]['apelido']; ?></dd>

                            <dt class="col-sm-3">E-mail</dt>
                            <dd class="col-sm-9"><?php echo $resultado_UsuariosCadastrados[$i]['email']; ?></dd>

                            <dt class="col-sm-3">Usuário</dt>
                            <dd class="col-sm-9"><?php echo $resultado_UsuariosCadastrados[$i]['usuario']; ?></dd>

                            <dt class="col-sm-3">Nível de Acesso</dt>
                            <dd class="col-sm-9"><?php echo $resultado_UsuariosCadastrados[$i]['nome_nivac']; ?></dd>

                            
                            <dt class="col-sm-3">Situação</dt>
                            <dd class="col-sm-9">
                            <?php 
                                echo "<span class='badge bg-".$resultado_UsuariosCadastrados[$i]['cor_cores']."'> ".$resultado_UsuariosCadastrados[$i]['nome_sit']."</span>"; 
                            ?>
                            </dd>

                            <dt class="col-sm-3 text-truncate">Data Cadastro</dt>
                            <dd class="col-sm-9"><?php echo date('d/m/y H:i:s', strtotime($resultado_UsuariosCadastrados[$i]['created'])) ?></dd>

                            <dt class="col-sm-3 text-truncate">Data Edição</dt>
                            <dd class="col-sm-9"><?php
                                                    //verifica se a variavel data existe.
                                                    //usuei o date e formatei para ficar mais amigavel
                                                    if (!empty($resultado_UsuariosCadastrados[$i]['modified'])) {
                                                        echo date('d/m/y H:i:s', strtotime($resultado_UsuariosCadastrados[$i]['modified']));
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
                $url_destino = pg . '/listar/list_usuario';
                header("Location: $url_destino");
            }
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! </div>";
            $url_destino = pg . '/acesso/login';
            header("Location: $url_destino");
        }
