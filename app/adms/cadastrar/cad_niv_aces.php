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
                        <h2 class="display-4 titulo">Cadastrar Nível de Acesso</h2>
                    </div>
                    <div class="p-2">
                        <?php
                        //BOTAO VISUALIZAE
                        $btn_list = $pdo->carregarBtn('listar/list_niv_aces');

                        if ($btn_list) {
                            echo "<a href='" . pg . "/listar/list_niv_aces?id=" . $_SESSION['id'] . "' class='btn btn-outline-info btn-sm'>Listar</a>";
                        }


                        ?>
                    </div>
                </div>
                <hr>
            <form method="POST" action="<?php echo pg;?>/processa/proc_cad_niv_aces">
          <div class="form-group">
            <label><span class="text-danger">*</span> Nome</label>
            <input name="nome" type="text" class="form-control"  placeholder="Nome do Nível de Acesso">
          </div>
          <p><span class="text-danger">*</span> Campo Obrogatório</p>
          <input name="SendCadNivAc" type="submit" class="btn btn-success" value="Cadastrar">
        </form>

            </div>
        </div>
    </div>
    <?php

    include_once 'app/adms/include/rodape_lib.php';
    ?>
    </div>
</body>