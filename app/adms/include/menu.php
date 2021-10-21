<?php
if (!isset($seguranca)) {
    exit;
}
?>

<nav class="sidebar">
    <ul class="list-unstyled">
        <?php
        //chama a funcao que busca os botoes do meno cadastrados no banco.
        $result_niveis_acessos_pgs = $pdo->buscarBotoesMenu();
        for ($i = 0; $i < count($result_niveis_acessos_pgs); $i++) {
            //echo "ID : ".$result_niveis_acessos_pgs[$i]['id'];
            //echo "<i class='".$result_niveis_acessos_pgs[$i]['icone']."'></i>".$result_niveis_acessos_pgs[$i]['nome']."<br>";
            //aqui foi implementado o dropdown. se dropdown for igul a 1 ele impreme o menu e sub menu
            //se nao ele imprime somente o menu.
            if ($result_niveis_acessos_pgs[$i]['dropdown'] == 1) {
        ?>
                <li>
                    <a href="#submenu1" data-toggle="collapse">
                        <i class="fas fa-user"></i> Usuários
                    </a>
                    <ul class="list-unstyled collapse" id="submenu1">
                        <li><a href="listar.html"><i class="fas fa-users"></i> Usuários </a> </li>
                        <li><a href="#"><i class="fas fa-key"></i> Niveis de Acesso </a> </li>
                    </ul>
                </li>
        <?php
            } else {
                echo "<li><a href='" . pg . "/" . $result_niveis_acessos_pgs[$i]['endereco'] . "'><i class='" . $result_niveis_acessos_pgs[$i]['icone'] . "'></i> " . $result_niveis_acessos_pgs[$i]['nome'] . "</a></li>";
            }
        }
        ?>
        <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li>
            <a href="#submenu1" data-toggle="collapse">
                <i class="fas fa-user"></i> Usuários
            </a>
            <ul class="list-unstyled collapse" id="submenu1">
                <li><a href="listar.html"><i class="fas fa-users"></i> Usuários </a> </li>
                <li><a href="#"><i class="fas fa-key"></i> Niveis de Acesso </a> </li>
            </ul>
        </li>
        <li><a href="#submenu2" data-toggle="collapse"><i class="fas fa-list"></i> Menu</a>
            <ul class="list-unstyled collapse" id="submenu2">
                <li><a href="#"><i class="fas fa-file-alt"></i> Páginas </a> </li>
                <li><a href="#"><i class="fab fa-elementor"></i> Item de Menu </a> </li>
            </ul>
        </li>
        <li><a href="#">Item 1</a></li>
        <li><a href="#">Item 2</a></li>
        <li><a href="#">Item 3</a></li>
        <li class="active"><a href="#">Item 4</a></li>
        <li><a href="#"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
    </ul>
</nav>