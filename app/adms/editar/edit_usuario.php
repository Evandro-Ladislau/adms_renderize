<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';


//pega o id passado pela url
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
//verificar a existencia do id na URL

if (!empty($id)) {
    $result_edit_usuario = $pdo->VerificarUsuariosCadastradosNoBanco($id);

    //verificar se encontrou a página no banco de dados
    if ($result_edit_usuario) {

        include_once 'app/adms/include/head.php';
?>

        <body>
            <?php
            unset($_SESSION['dados']);
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
                                <h2 class="display-4 titulo">Editar Usuario</h2>
                            </div>
                            <div class="p-2">
                                <span class="d-none d-md-block">
                                    <?php

                                    for ($i = 0; $i < count($result_edit_usuario); $i++) {
                                        //BOTAO LISTAR
                                        $btn_list = $pdo->carregarBtn('listar/list_usuario');

                                        if ($btn_list) {
                                            echo "<a href='" . pg . "/listar/list_usuario?id=" . $result_edit_usuario[$i]['id'] . "' class='btn btn-outline-info btn-sm'>Listar</a>";
                                        }

                                        //BOTAR EDITAR
                                        $btn_vis = $pdo->carregarBtn('visualizar/vis_usuario');

                                        if ($btn_vis) {
                                            echo " <a href='" . pg . "/visualizar/vis_usuario?id=" . $result_edit_usuario[$i]['id'] . "' class='btn btn-outline-primary btn-sm'>visualizar</a>";
                                        }

                                        //BOTAO PAGAR
                                        $btn_apagar = $pdo->carregarBtn('processa/apagar_usuario');

                                        if ($btn_apagar) {
                                            echo " <a href='" . pg . "/processa/apagar_usuario?id=" . $result_edit_usuario[$i]['id'] . "' class='btn btn-outline-danger btn-sm' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
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
                                            echo "<a class='dropdown-item' href='" . pg . "/listar/list_usuario?id=" . $result_edit_usuario[$i]['id'] . "'>Listar</a>";
                                        }

                                        if ($btn_vis) {
                                            echo "<a class='dropdown-item' href='" . pg . "visualizar/vis_usuario?id=" . $result_edit_usuario[$i]['id'] . "'>visualizar</a>";
                                        }

                                        if ($btn_apagar) {
                                            echo "<a class='dropdown-item' href='" . pg . "/processa/apagar_usuario?id=" . $_SESSION['id'] . "' data-confirm='Tem Certeza que deseja excluir o item?'>Apagar</a>";
                                        }


                                        ?>
                                    </div>
                                <?php
                                    }
                                ?>
                                </div>
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
                        <form method="POST" action="<?php echo pg; ?>/processa/proc_edit_usuario" enctype="multipart/form-data">
                        <?php
                        for ($c=0; $c <count($result_edit_usuario) ; $c++) { 
                            
                        
                        ?>
                        <input type="hidden" name="id" value="<?php if (isset($result_edit_usuario[$c]['id'])) {
                                                                            echo $result_edit_usuario[$c]['id'];
                                                                        } ?>">
                <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>
                            
                                <span class="text-danger">*</span> Nome
                            </label>
                            <input name="nome" type="text" class="form-control" placeholder="Nome do Usuário Completo" id="nome" value="
                            <?php 
                            if (isset( $_SESSION['dados']['nome'])){
                                echo  $_SESSION['dados']['nome'];
                            }elseif (isset($result_edit_usuario[$c]['nome'])) {
                                echo $result_edit_usuario[$c]['nome'];
                            }
                            ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label><span class="text-danger">*</span> E-mail</label>
                            <input name="email" type="email" class="form-control" placeholder="Seu Melhor Email de usuário" id="email" value="
                            <?php 
                            if (isset( $_SESSION['dados']['email'])){
                                echo  $_SESSION['dados']['email'];
                            }elseif (isset($result_edit_usuario[$c]['email'])) {
                                echo $result_edit_usuario[$c]['email'];
                            }
                            ?>">
                        </div>

                        
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label>
                            
                                <span class="text-danger">*</span> Usuário
                            </label>
                            <input name="usuario" type="text" class="form-control" placeholder="Nome de usuário para login" id="usuario" value="
                            <?php if (isset( $_SESSION['dados']['usuario'])){
                                echo  $_SESSION['dados']['usuario'];
                            }elseif ($result_edit_usuario[$c]['usuario']) {
                                echo $result_edit_usuario[$c]['usuario'];
                            }
                            ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label><span class="text-danger">*</span> Senha</label>
                            <input name="senha" type="password" class="form-control" placeholder="A senha deve ter 6 caracteres" id="Senha" value="<?php if (isset( $_SESSION['dados']['Senha'])){echo  $_SESSION['dados']['Senha'];}?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>
                                Apelido
                            </label>
                            <input name="apelido" type="text" class="form-control" placeholder="Apelido" id="apelido" value="<?php if (isset( $_SESSION['dados']['apelido'])){echo  $_SESSION['dados']['apelido'];}elseif ($result_edit_usuario[$c]['apelido']) {
                                echo $result_edit_usuario[$c]['apelido'];
                            }?>">
                        </div>
                    </div>

                    <div class="form-row">
                    
                        <div class="form-group col-md-6">
                        <label>
                                <span class="text-danger">*</span>Nível de Acesso
                            </label>
                            <select name="adms_niveis_acesso_id" id="adms_niveis_acesso_id" class="form-control">
                                <option selected>Selecione</option>
                                <?php
                                $resultado_niv_ac = $pdo->BuscarNiveisAcessosCadastrados();
                                for ($i = 0; $i < count($resultado_niv_ac); $i++) {
                                    if (isset($_SESSION['dados']['adms_niveis_acesso_id']) AND $_SESSION['dados']['adms_niveis_acesso_id']==$resultado_niv_ac[$i]['id'] ) {

                                        echo "<option value='" . $resultado_niv_ac[$i]['id'] . "' selected>".$resultado_niv_ac[$i]['nome']."</option>";

                                    }elseif (!isset($_SESSION['dados']['adms_niveis_acesso_id']) AND (isset($result_edit_usuario[$c]['adms_niveis_acesso_id'])) AND $result_edit_usuario[$c]['adms_niveis_acesso_id'] ==  $resultado_niv_ac[$i]['id']) {
                                        echo "<option value='" . $resultado_niv_ac[$i]['id'] . "' selected>" . $resultado_niv_ac[$i]['nome'] . "</option>";
                                        }
                                    else{
                                        echo "<option value='" . $resultado_niv_ac[$i]['id'] . "'> ".$resultado_niv_ac[$i]['nome']."</option>";
                                    }
                                    
                                }
                                
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                        <label>
                                <span class="text-danger">*</span>Situação do Usuário
                            </label>
                            <select name="adms_sits_usuario_id" id="adms_sits_usuario_id" class="form-control">
                                <option selected>Selecione</option>
                                <?php
                                $resultado_sit_user = $pdo->BuscarSituacaoUsuarioCadastrado();
                                for ($i = 0; $i < count($resultado_sit_user); $i++) {
                                    if (isset($_SESSION['dados']['adms_sits_usuario_id']) AND $_SESSION['dados']['adms_sits_usuario_id']==$resultado_sit_user[$i]['id'] ) {

                                        echo "<option value='" . $resultado_sit_user[$i]['id'] . "' selected>".$resultado_sit_user[$i]['nome']."</option>";

                                    }elseif (!isset($_SESSION['dados']['adms_sits_usuario_id']) AND (isset($result_edit_usuario[$c]['adms_sits_usuario_id'])) AND $result_edit_usuario[$c]['adms_niveis_acesso_id'] ==  $resultado_sit_user[$i]['id']) {
                                        echo "<option value='" . $resultado_sit_user[$i]['id'] . "' selected>" . $resultado_sit_user[$i]['nome'] . "</option>";
                                        }
                                    else{
                                        echo "<option value='" . $resultado_sit_user[$i]['id'] . "'> ".$resultado_sit_user[$i]['nome']."</option>";
                                    }
                                    
                                }
                                
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        
                    <div class="form-group col-md-6">
                            <label>
                             Foto
                             <input type="file" name="imagem" onchange="previewImagem()">
                            </label>
                        </div>
                        <div class="form-group col-md-6">
                            <?php
                            if (isset($result_edit_usuario[$c]['imagem'])) {
                                $imagem_antiga = pg.'/assets/imagens/usuario/'.$result_edit_usuario[$c]['id'].'/'.$result_edit_usuario[$c]['imagem'];
                            }else {
                                $imagem_antiga = pg.'/assets/imagens/usuario/preview_img.png';
                            }
                            ?>
                            <img class='rounded-circle' src="<?php echo $imagem_antiga; ?>" id="preview-user" class="img=thumbnail" style="width: 150px; height: 150px;" >
                        </div>
                        
                    <input name="SendEditUsuario" type="submit" class="btn btn-warning" value="Editar">
                    
                </form>
                <?php
                }
                ?>

                    </div>
                </div>
            </div>

            <?php

            include_once 'app/adms/include/rodape_lib.php';
            ?>
            <script>
        function previewImagem(){
            var imagem = document.querySelector('input[name=imagem]').files[0];
            var preview = document.querySelector('#preview-user');

            var reader = new FileReader();

            reader.onloadend = function(){
                preview.src = reader.result;
            }

            if (imagem) {
                reader.readAsDataURL(imagem);
            }else{
                preview.src = ""; 
            }
        }
    </script>
            </div>
        </body>

<?php
    } else {
        $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! </div>";
        $url_destino = pg . '/listar/list_menu';
        header("Location: $url_destino");
    }
} else {
    $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! </div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
