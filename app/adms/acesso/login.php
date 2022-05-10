<?php
if (!isset($seguranca)) {
    exit;
}

include_once 'app/adms/include/head_login.php';
?>

<body class="text-center">
    <form class="form-signin" method="POST" action="<?php echo pg.'/acesso/valida'; ?>">
        <img class="mb-4" src="<?php echo pg;?>/assets/imagens/logo_login/icone_white.png" alt="Evandro Ladislau" width="72" height="72">
        <h1 class="h3 mb-3 font-weight-normal">Área Restrita</h1>
        <pre>
        <?php
        if(isset($_SESSION['msg'])){
            echo $_SESSION['msg'];
            unset($_SESSION['msg']);
            
           
        }
        ?>
        </pre>
        <div class="form-group">
            <label>Usuário</label>
            <input name="usuario" type="text" class="form-control" placeholder="Digite o Usuario" required>

        </div>
        <div class="form-group">
            <label>Senha</label>
            <input name="senha" type="password" class="form-control" placeholder="Digite a Senha" required>
        </div>
        <input class="btn btn-lg btn-primary btn-block" type="submit" value="Acessar" name="SendLogin"></input>
        <p class="text-center">Esqueceu a Senha?</p>
    </form>
</body>