<?php
if (!isset($seguranca)) {
    exit;
}

//pega o id passado pela url
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if ($id) {


    require_once './app/adms/models/Conexao.php';
    require_once '../adm/index.php';
    include_once 'app/adms/include/head.php';

    //chama a funcao que verifica se existe id no bd
    $resultID = $pdo->verificarId($id);

    //se existir ele acessa esse if e impreme a pagina
    if ($resultID) {


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
                                <h2 class="display-4 titulo">Editar Nível de Acesso</h2>
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
                        <?php
                        //IMPRIME MENSAGEM GLOBAL QUANDO NÃO FOR POSSIVEL CADASTRAR O NIVEL DE ACESSO
                        if (isset($_SESSION['msg'])) {
                            echo $_SESSION['msg'];
                            unset($_SESSION['msg']);
                        }
                        ?>
                        <form method="POST" action="<?php echo pg; ?>/processa/proc_edit_niv_aces">
                            <input type="hidden" name="id" value="<?php if (isset($resultID)) {
                                                                        for ($i = 0; $i < count($resultID); $i++) {
                                                                            echo $resultID[$i]['id'];
                                                                        }
                                                                    } ?>">
                            <div class="form-group">
                                <label><span class="text-danger">*</span> Nome</label>
                                <input name="nome" type="text" class="form-control" placeholder="Nome do Nível de Acesso" value="<?php
                                                                                                                                    if (isset($resultID)) {
                                                                                                                                        for ($i = 0; $i < count($resultID); $i++) {
                                                                                                                                            echo $resultID[$i]['nome'];
                                                                                                                                        }
                                                                                                                                    }

                                                                                                                                    ?>
                                ">
                            </div>
                            <p><span class="text-danger">*</span> Campo Obrogatório</p>
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
