<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//receber o valor do campo input com nome SendCadPg do formulario no arquivo cad_pagina
$SendCadUser = filter_input(INPUT_POST, 'SendCadUser', FILTER_SANITIZE_STRING);

//se essa variavel tiver valor significa que o usuario clicou no botão então ele entra nesse if
if ($SendCadUser) {
    //receber os dados do formulario e atribuir para a variavel $dados
    //dados recebidos como string usando o metodo post e passando um filtro nos dados.
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    //var_dump($dados); esse vardum mostra que na variavel dados existe um array com 14 posições, cada uma delas é os campos do formulario cadastrar
    //contendo o nome da tabela do banco + o botao cadastrar.


    //retirar campo que não são orbrigatórios do array $dados citado anteriormente
    $dados_apelido = $dados['apelido']; //campo imagem
    unset($dados['apelido']);

    //$dados_imagem = $dados['imagem']; //campo imagem
   // unset($dados['imagem']);
    // var_dump($dados);

    //validar se existe algum desses campos obrigatorios vazios (tirando o obs e icone que foram destruidos)
    //inclui o arquivo lib_vazio responsável por validar se existe campo vazio.
    $erro = false;
    include_once 'lib/lib_vazio.php';
    include_once 'lib/lib_email.php';

    $dados_validos = vazio($dados);
    //var_dump( $dados_validos);
    if (!$dados_validos) {
        //se for diferente de verdadeiro(no caso falso algum campo esta vazio)

        //erro se torna verdadeiro e caiu no if abaixo redirecionando para cadastrar.
        $erro = true;
        $_SESSION['dados'] = $dados_validos;
        $_SESSION['msg'] = "<div class='alert alert-danger'> Necessário preencher todos os campos para cadastrar o usuário!</div>";

    }elseif (!validarEmail($dados_validos['email'])) {
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'> E-mail inválido!!</div>";
    }
    //Validar senha
    elseif ((strlen($dados_validos['senha'])) < 6) {
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'> A senha deve ter no mínimo 6 caracteres!</div>";
    } elseif (stristr($dados_validos['senha'], "'")) {
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'> Caracter (') utilizado na senha é inváido!</div>";

    }elseif (stristr($dados_validos['usuario'], "'")) {
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'> Caracter (') utilizado no usuário é inváido!</div>";
    }elseif ( (strlen($dados_validos['usuario'])) < 5) {
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'> O usuário deve ter no mínimo 6 caracteres!</div>";
    }
    else {
        //Proibir o cadatro de email e senha duplicado
        $resultado_user_duplicado = $pdo->validarCadUsuarioDuplicado($dados['email'], $dados['usuario']);
        
        if ($resultado_user_duplicado) {
            $erro = true;
            $_SESSION['msg'] = "<div class='alert alert-danger'> Este email ou usuário já esta cadastrado!</div>";
        }
    }


    //HOUVE ERRO EM ALGUM CAMPO SERÁ REDIRECIONADO PARA O CADASTRAR USUARIO.
    if ($erro) {
        $dados['apelido'] = $dados_apelido; //campo icone
        $_SESSION['dados'] = $dados;
        //se o usuario tentar entrar na pagina sem clicar no botão.
        $url_destino = pg . '/cadastrar/cad_usuario';
        header("Location: $url_destino");

        //NÃO HÁ ERRO NO FORMULÁRIO TENTA CADASTRAR NO BANCO
    } else {
        //criando criptografia na senha
        $dados['senha'] = password_hash($dados_validos['senha'], PASSWORD_DEFAULT);
        
        $result_cad_usuario = $pdo->cadastrarUsuario(
        $dados['nome'],
        $dados['email'], 
        $dados['usuario'], 
        $dados['senha'], 
        $dados['adms_niveis_acesso_id'], 
        $dados['adms_sits_usuario_id']
    );


        if ($result_cad_usuario) {
            unset($_SESSION['dados']);
            //var_dump($_FILES['imagem']);
            //neste caso ele vai retornar o id do ultimo usuario cadastrado isso pq eu usei o parametro
            //pdo->lastInsertId(); dentro da funcao que insere os dados e retornei ele mesmo sendo assim apos a chamada da funcao
            
            $usuario_id = $result_cad_usuario; 

            //Redimensionar a imagem e fazer upload
            if (!empty($_FILES['imagem']['name'])) {
                include_once 'lib/lib_upload.php';
                $destino = "assets/imagens/usuario/".$usuario_id."/";
                upload($_FILES['imagem'], $destino, 200, 150);
            }
            
            
         $_SESSION['msg'] = "<div class='alert alert-success'> Usuário cadastrado com Sucesso! </div>";
            $url_destino = pg . '/listar/list_usuario';
         header("Location: $url_destino");
        } else {
            $dados['apelido'] = $dados_apelido; //campo apelido
            $_SESSION['dados'] = $dados;
            
            $_SESSION['msg'] = "<div class='alert alert-danger'>ERRO! O Usuário não foi cadastrado! </div>";
            $url_destino = pg . '/cadastrar/cad_usuario';
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
