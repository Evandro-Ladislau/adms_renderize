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
                        <h2 class="display-4 titulo">Cadastrar Usuário</h2>
                    </div>
                    <div class="p-2">
                        <?php
                        //BOTAO VISUALIZAE
                        $btn_list = $pdo->carregarBtn('listar/list_usuario');

                        if ($btn_list) {
                            echo "<a href='" . pg . "/listar/list_usuario?id=" . $_SESSION['id'] . "' class='btn btn-outline-info btn-sm'>Listar</a>";
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
                <form method="POST" action="<?php echo pg; ?>/processa/proc_cad_usuario" enctype="multipart/form-data">
                <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>
                            
                                <span class="text-danger">*</span> Nome
                            </label>
                            <input name="nome" type="text" class="form-control" placeholder="Nome do Usuário Completo" id="nome" value="<?php if (isset( $_SESSION['dados']['nome'])){echo  $_SESSION['dados']['nome'];}?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label><span class="text-danger">*</span> E-mail</label>
                            <input name="email" type="email" class="form-control" placeholder="Seu Melhor Email de usuário" id="email" value="<?php if (isset( $_SESSION['dados']['email'])){echo  $_SESSION['dados']['email'];}?>">
                        </div>

                        
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label>
                            
                                <span class="text-danger">*</span> Usuário
                            </label>
                            <input name="usuario" type="text" class="form-control" placeholder="Nome de usuário para login" id="usuario" value="<?php if (isset( $_SESSION['dados']['usuario'])){echo  $_SESSION['dados']['usuario'];}?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label><span class="text-danger">*</span> Senha</label>
                            <input name="senha" type="password" class="form-control" placeholder="A senha deve ter 6 caracteres" id="Senha" value="<?php if (isset( $_SESSION['dados']['Senha'])){echo  $_SESSION['dados']['Senha'];}?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label>
                                Apelido
                            </label>
                            <input name="apelido" type="text" class="form-control" placeholder="Apelido" id="apelido" value="<?php if (isset( $_SESSION['dados']['apelido'])){echo  $_SESSION['dados']['apelido'];}?>">
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
                                    }else{
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
                                    }else{
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
                            <img src="<?php echo pg.'/assets/imagens/usuario/preview_img.png';?>" id="prevuew-user" class="img=thumbnail" style="width: 150px; height: 150px;" >
                        </div>
                        
                    <p><span class="text-danger">*</span> Campo Obrogatório</p>
                    <input name="SendCadUser" type="submit" class="btn btn-success" value="Cadastrar">
                </form>

            </div>
        </div>
    </div>
    <?php
    unset($_SESSION['dados']);
    include_once 'app/adms/include/rodape_lib.php';
    ?>
    <script>
        function previewImagem(){
            var imagem = document.querySelector('input[name=imagem]').files[0];
            var preview = document.querySelector('#prevuew-user');

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