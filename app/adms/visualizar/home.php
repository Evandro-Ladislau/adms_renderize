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
    
        echo "Bem Vindo HOME <br>";
        echo "<a href='" . pg . "/acesso/sair'>Sair</a> <br>";

        
        //chama a funcao que busca os botoes do meno cadastrados no banco.
            $result_niveis_acessos_pgs = $pdo->buscarBotoesMenu();
            for ($i=0; $i <count($result_niveis_acessos_pgs) ; $i++) { 
                //echo "ID : ".$result_niveis_acessos_pgs[$i]['id'];
                echo "Nome icone: ".$result_niveis_acessos_pgs[$i]['icone']." - ".$result_niveis_acessos_pgs[$i]['nome']."<br>";
               
            }        
        include_once 'app/adms/include/rodape_lib.php';
        ?>
    </div>
</body>