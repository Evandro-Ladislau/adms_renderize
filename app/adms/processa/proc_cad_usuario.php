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
    $dados_apelido = $dados['apelido']; //campo observação
    unset($dados['apelido']);
    // var_dump($dados);

    //validar se existe algum desses campos obrigatorios vazios (tirando o obs e icone que foram destruidos)
    //inclui o arquivo lib_vazio responsável por validar se existe campo vazio.
    $erro = false;
    include_once 'lib/lib_vazio.php';

    $dados_validos = vazio($dados);
    //var_dump( $dados_validos);
    if (!$dados_validos) {
        //se for diferente de verdadeiro(no caso falso algum campo esta vazio)

        //erro se torna verdadeiro e caiu no if abaixo redirecionando para cadastrar.
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'> Necessário preencher todos os campos para cadastrar o usuário!</div>";
    } else {
        //Proibir o cadatro de página duplicado
        $resultado_user_duplicado = $pdo->validarCadUsuarioDuplicado($dados['email']);
        
        if ($resultado_user_duplicado) {
            $erro = true;
            $_SESSION['msg'] = "<div class='alert alert-danger'> Este email já esta cadastrado!</div>";
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
