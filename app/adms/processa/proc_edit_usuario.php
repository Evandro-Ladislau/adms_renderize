<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//receber o valor do campo input com nome SendCadPg do formulario no arquivo cad_pagina
$SendEditUsuario = filter_input(INPUT_POST, 'SendEditUsuario', FILTER_SANITIZE_STRING);

//se essa variavel tiver valor significa que o usuario clicou no botão então ele entra nesse if
if ($SendEditUsuario) {
    //receber os dados do formulario e atribuir para a variavel $dados
    //dados recebidos como string usando o metodo post e passando um filtro nos dados.
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    //retirar campo da validação
    $dados_apelido = $dados['apelido'];
    $dados_senha = $dados['senha'];
    $dados_imagem_antiga = $dados['imagem_antiga'];
    unset($dados['apelido'],$dados['senha'],$dados['imagem_antiga']);
    
    //validar nenhum campo vazio
    $erro = false;
    include_once 'lib/lib_vazio.php';
    include_once 'lib/lib_email.php';
    $dados_validos = vazio($dados);

    //validar campos vazios
    if (!$dados_validos) {
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'> Necessário preencher todos os campos para editar o Usuario!</div>";

    }//validar email
    elseif (!validarEmail($dados_validos['email'])) {
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'>E-mail inválido!</div>";

      
    }//validar usuario  
    elseif (stristr($dados_validos['usuario'], "")) {
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'> Caracter (') utilizado no usuário inválido!</div>";

    }
    elseif ((strlen($dados_validos['usuario'])) < 5) {
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'> O usuário deve ter no mínimo 5 caracteres!</div>";

    }else {
        //Proibir o cadatro de email e senha duplicado
        $resultado_user_duplicado = $pdo->validarCadUsuarioDuplicadoNoEditar($dados_validos['email'], $dados_validos['usuario'], $dados_validos['id']);
        
        if ($resultado_user_duplicado) {
            $erro = true;
            $_SESSION['msg'] = "<div class='alert alert-danger'> Este email ou usuário já esta cadastrado!</div>";
        }
        
    }
    //vallidar senha
    if (empty($dados_senha)) {
        $campo_senha = "";
        $valor_senha = "";
    }else{
        if ((strlen($dados_senha)) < 6) {
            $erro = true;
            $_SESSION['msg'] = "<div class='alert alert-danger'> A senha deve ter no mínimo 6 caracteres!</div>";

        }elseif (stristr($dados_senha, "'")) {
            $erro = true;
            $_SESSION['msg'] = "<div class='alert alert-danger'> Caracter (') utilizado na senha inválido!</div>";

        }else{
            $senha_cript = password_hash($dados_senha, PASSWORD_DEFAULT);
            $campo_senha = "senha = ";
            $valor_senha = $senha_cript.",";
        }
    }

    //criar vampo apelido
    if (empty($dados_apelido)) {
        $campo_apelido = "";
        $valor_apelido = "";
    }else {
        $campo_apelido = "apelido = ";
        $valor_apelido = $dados_apelido.",";
    }

    //criar as variaveis da foto quando a mesma não está sendo cadastrada
    if (empty($_FILES['imagem']['name'])) {
        $campo_foto = "";
        $valor_foto = "";

    }else {
        //validar extensao da imagem
        $foto = $_FILES['imagem'];
        include_once 'lib/lib_val_img_ext.php';

        if (!validarExtensao($foto['type'])) {
            $erro = true;
            $_SESSION['msg'] = "<div class='alert alert-danger'> Extensão da imagem inválida!</div>";
        }else{
            include_once 'lib/lib_caracter_esp.php';
            $foto['name'] = caracterEspecial($foto['name']);
            $campo_foto = "imagem,";
            $valor_foto = "".$foto['name']."";
        }
    }

    //HOUVE ERRO EM ALGUM CAMPO SERÁ REDIRECIONADO PARA O CADASTRAR USUARIO.
    if ($erro) {
        $dados['senha'] = $dados_senha;
        $dados['apelido'] = $dados_apelido;
        $_SESSION['dados'] = $dados;
        $url_destino = pg . '/editar/edit_usuario?id='.$dados['id'];
        header("Location: $url_destino");

        //NÃO HÁ ERRO NO FORMULÁRIO TENTA CADASTRAR NO BANCO
    }else {
       $result_edit_menu = $pdo->EditarMenu($dados_validos['nome'], $dados_validos['icone'], $dados_validos['adms_sit_id'], $dados_validos['id']);

        
        if ($result_edit_menu) {
            unset($_SESSION['dados']);

            $_SESSION['msg'] = "<div class='alert alert-success'> Menu editado com sucesso! </div>";
            $url_destino = pg . '/listar/list_usuario';
            header("Location: $url_destino");
        } else {
            
            $_SESSION['msg'] = "<div class='alert alert-danger'> Menu não editada! </div>";
            $url_destino = pg . '/editar/edit_usuario?id='.$dados['id'];
            header("Location: $url_destino");
        } 
    }
} else {
    //se o usuario tentar entrar na pagina sem clicar no botão.
    //variavel global para criar uma mensagem de alerta.
    $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    <span aria-hidden='true'>&times;</span>
</button></div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
