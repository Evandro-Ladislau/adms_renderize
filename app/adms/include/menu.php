<?php
if (!isset($seguranca)) {
    exit;
}
require_once '../adm/index.php';
?>
<div class="d-flex">
    <nav class="sidebar">
        <ul class="list-unstyled">
            <?php
            //chama a funcao que busca os botoes do meno cadastrados no banco.
            $result_niveis_acessos_pgs = $pdo->buscarBotoesMenu($_SESSION['adms_niveis_acesso_id']);
            
            //essas variaveis são para fazer a logica da impressao do dropdown.
            $cont_drop_fech = 0;
            $cont_drop = 0;
            for ($i = 0; $i < count($result_niveis_acessos_pgs); $i++) {
                //echo "ID : ".$result_niveis_acessos_pgs[$i]['id'];
                //echo "<i class='".$result_niveis_acessos_pgs[$i]['icone']."'></i>".$result_niveis_acessos_pgs[$i]['nome']."<br>";
                //aqui foi implementado o dropdown. se dropdown for igul a 1 ele impreme o menu e sub menu
                //se nao ele imprime somente o menu.

                //esse if verifica qual pagina esta ativa no momento
                //usei o for para percorrer o array de result_paginas.
                /* */
                for ($c = 0; $c < count($result_paginas); $c++) {
                    if ($result_paginas[$c]['id'] == $result_niveis_acessos_pgs[$i]['id_pg_menu']) {
                        $menu_ativado = "active";
                    } else {
                        $menu_ativado = "";
                    }
                }


                //implementado o dropbown
                if ($result_niveis_acessos_pgs[$i]['dropdown'] == 1) {
                    if ($cont_drop != $result_niveis_acessos_pgs[$i]['id']) {

                        if (($cont_drop_fech == 1) and ($cont_drop != 0)) {
                            echo "</ul>";
                            echo "</li>";
                            $cont_drop_fech == 0;
                        }
                        echo "<li>";
                        echo "<a href='#submenu" . $result_niveis_acessos_pgs[$i]['id'] . "' data-toggle='collapse'>";
                        echo    "<i class='" . $result_niveis_acessos_pgs[$i]['iconmen'] . "'></i> " . $result_niveis_acessos_pgs[$i]['nomemen'] . "";
                        echo "</a>";
                        echo "<ul class='list-unstyled collapse' id='submenu" . $result_niveis_acessos_pgs[$i]['id'] . "'>";
                        $cont_drop = $result_niveis_acessos_pgs[$i]['id'];
                    }
                    echo "<li class='$menu_ativado'><a href='" . pg . "/" . $result_niveis_acessos_pgs[$i]['endereco'] . "'><i class='" . $result_niveis_acessos_pgs[$i]['iconpg'] . "'></i> " . $result_niveis_acessos_pgs[$i]['nomepg'] . "</a></li>";

                    $cont_drop_fech = 1;
                } else {
                    if ($cont_drop_fech == 1) {
                        echo "</ul>";
                        echo "</li>";
                        $cont_drop_fech == 0;
                    }
                    echo "<li  class='$menu_ativado'><a href='" . pg . "/" . $result_niveis_acessos_pgs[$i]['endereco'] . "'><i class='" . $result_niveis_acessos_pgs[$i]['iconmen'] . "'></i> " . $result_niveis_acessos_pgs[$i]['nomemen'] . "</a></li>";
                }
            }

            if ($cont_drop_fech == 1) {
                echo "</ul>";
                echo "</li>";
                $cont_drop_fech == 0;
            }
            ?>


        </ul>
    </nav>
</div>