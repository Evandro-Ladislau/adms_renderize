<?php
if(!isset($seguranca)){
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once './index.php';

$result_dados_usuarios = $pdo->buscarDadosUsuarios();


?>

<nav class="navbar navbar-expand navbar-dark bg-primary">
    <a class="sidebar-toggle text-light mr-3"><span class="navbar-toggler-icon"></span></a>
    <a class="navbar-brand" href="#">Renderize - ADM</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">

                <?php
                if ($result_dados_usuarios) {
                    for ($i = 0; $i < count($result_dados_usuarios); $i++) {
                ?>

                        <a class="nav-link dropdown-toggle menu-header" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown">
                            <?php
                            if (!empty($result_dados_usuarios[$i]['imagem'])) {
                                echo "<img class='rounded-circle' src='" . pg . "/assets/imagens/usuario/" . $result_dados_usuarios[$i]['id'] . "/" . $result_dados_usuarios[$i]['imagem'] . "' width='20' height='20'> &nbsp; <span class='d-none d-sm-inline'>";
                            } else {
                                echo "<img class='rounded-circle' src='" . pg . "/assets/imagens/usuario/usuario.ico' width='20' height='20'> &nbsp; <span class='d-none d-sm-inline'>";
                            }
                            //trás somente o primeiro nome cadastrado no banco de dados.
                            $nome = explode(" ", $result_dados_usuarios[$i]['nome']);
                            $primeiro_nome = $nome[0];
                            echo  $primeiro_nome;
                            ?>
                            </span>
                        </a>

                <?php
                    }
                }
                ?>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown" id="navbarDropdownMenuLink">
                    <a class="dropdown-item" href=""><i class="fas fa-user"></i> Perfil</a>
                    <a class="dropdown-item" href="<?php echo pg; ?>/acesso/sair"><i class="fas fa-sign-out-alt"></i> Sair</a>
                </div>
            </li>
        </ul>
    </div>
</nav>