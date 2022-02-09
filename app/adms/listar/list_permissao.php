<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//receber o id do nivel de acesso passado pela URL ao clicar no botão permissão passando um filtro.
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);


if (!empty($id)) {
    //receber o número da página
    $pagina_atual = filter_input(INPUT_GET, 'pagina', FILTER_SANITIZE_NUMBER_INT);

    //determinar em qual página o usuário esta
    $pagina = (!empty($pagina_atual) ? $pagina_atual : 1);

    //setar a quantidade de resultado por página
    $qnt_result_pg = 50;

    //Calcular o inicio da visualização

    $inicio = ($qnt_result_pg * $pagina) - $qnt_result_pg;

    if ($_SESSION['adms_niveis_acesso_id'] == 1) {
        $result_niv_ac = $pdo->permissaoSuperAdministrador($id, $inicio, $qnt_result_pg);
    } else {
        
    }

    
    //verificar se ele encontrar algum cadastro.
    if ($result_niv_ac) {

        include_once 'app/adms/include/head.php';
?>

        <body>

            <?php
            include_once 'app/adms/include/header.php';
            ?>

            <div class="d-flex">
                <?php
                include_once 'app/adms/include/menu.php';

                echo "Listar Páginas";

                ?>

                
                    <?php

                    include_once 'app/adms/include/rodape_lib.php';
                    ?>
                
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