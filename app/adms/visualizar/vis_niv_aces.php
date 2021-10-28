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
        //pegar o id pega url
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        //chama a funcao buscarDadosNivelAcesso
        $result_niv_aces = $pdo->buscarDadosNivelAcesso($id);

        ?>
        <div class="content p-1">
            <div class="list-group-item">
                <div class="d-flex">
                    <div class="mr-auto p-2">
                        <h2 class="display-4 titulo">Detalhes Nivel de Acesso</h2>
                    </div>
                    <div class="p-2">
                        <?php
                        //BOTAO APAGAR
                        $btn_list = $pdo->carregarBtn('listar/list_niv_aces');

                        if ($btn_list) {
                            echo "<a href='" . pg . "/listar/list_niv_aces' class='btn btn-outline-info btn-sm'>Listar</a> ";
                        }

                        //BOTAO EDITAR
                        $btn_edit = $pdo->carregarBtn('editar/edit_niv_aces');

                        if ($btn_edit) {
                            echo "<a href='" . pg . "/editar/edit_niv_aces?id=1' class='btn btn-outline-warning btn-sm'>Editar</a> ";
                        }

                        //BOTAO PAGAR
                        $btn_apagar = $pdo->carregarBtn('processa/apagar_niv_aces');

                        if ($btn_apagar) {
                            echo "<a href='" . pg . "/processa/apagar_niv_aces' class='btn btn-outline-danger btn-sm' data-toggle='modal' data-target='#apagarRegistro'>Apagar</a>";
                        }
                        ?>

                    </div>

                </div><hr>
                <?php
                //SE EXISTIR RESULTADO ELE LISTAR OS DATALHES DO NIVEL DE ACESSO
                if ($result_niv_aces) {
                    for ($i = 0; $i < count($result_niv_aces); $i++) {
                ?>
                        <dl class="row">
                            <dt class="col-sm-3">ID</dt>
                            <dd class="col-sm-9"><?php echo $result_niv_aces[$i]['id'];?></dd>

                            <dt class="col-sm-3">Nome</dt>
                            <dd class="col-sm-9"><?php echo $result_niv_aces[$i]['nome'];?></dd>

                            <dt class="col-sm-3">Ordem</dt>
                            <dd class="col-sm-9"><?php echo $result_niv_aces[$i]['ordem'];?></dd>

                            <dt class="col-sm-3 text-truncate">Data Cadastro</dt>
                            <dd class="col-sm-9"><?php echo date('d/m/y H:i:s', strtotime($result_niv_aces[$i]['created']))?></dd>

                            <dt class="col-sm-3 text-truncate">Data Modificacao</dt>
                            <dd class="col-sm-9"><?php
                            //verifica se a variavel data existe.
                            //usuei o date e formatei para ficar mais amigavel
                            if (!empty($result_niv_aces[$i]['modified'])) {
                                echo date('d/m/y H:i:s', strtotime($result_niv_aces[$i]['modified']));
                            }
                            
                             ?></dd>

                        </dl>
                    <?php
                    }
                } else {
                    ?>
                    <div class="alert alert-danger" role="alert">
                        Nenhuma registro encontrado!
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <?php

        include_once 'app/adms/include/rodape_lib.php';
        ?>
    </div>
</body>