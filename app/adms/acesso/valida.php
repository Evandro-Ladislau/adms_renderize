<?php
if(!isset($seguranca)){
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';
//receber o que foi passado no formulario pelo input de nome SendLogin.
$SendLogin = filter_input(INPUT_POST, 'SendLogin', FILTER_SANITIZE_STRING);
if ($SendLogin) {
    // se tiver valor ao clicar no botão ele acessa esse if.
    $usuario_rc = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
    $usuario = str_ireplace(" ", "", $usuario_rc); // retirando o espaço em brando

    $senha_rc = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
    $senha = str_ireplace(" ", "", $senha_rc);

    if (!empty($usuario) and (!empty($senha))) {
        // imprime a senha cripitografada echo password_hash($senha, PASSWORD_DEFAULT);
        //chamda a função que valida o usuario.
        $result_login = $pdo->validarLogin($usuario);
        
        //iterei o array colocando os valores nas variaveis globais.
        if ($result_login) {

            for ($i = 0; $i < count($result_login); $i++) {

                //Com esse for pego o resultado de cada um dessas colunas e coloco dentro da variavel global.
                $_SESSION['id'] = $result_login[$i]['id'];
                $_SESSION['nome'] = $result_login[$i]['nome'];
                $_SESSION['email'] = $result_login[$i]['email'];
                $_SESSION['senha'] = $result_login[$i]['senha'];
                $_SESSION['adms_niveis_acesso_id'] = $result_login[$i]['adms_niveis_acesso_id'];
            }

            //aqui chamei a funcao que busca a coluna ordem do nivel de acesso e coloquei o valor dentro da variavel global.
            $result_ordem_niv_ac = $pdo->buscarOrdemNivelAcesso($_SESSION['adms_niveis_acesso_id']);
            for ($i = 0; $i < count($result_ordem_niv_ac); $i++) {
                $_SESSION['ordem'] = $result_ordem_niv_ac[$i]['ordem'];
            }
        }


        if (password_verify($senha, $_SESSION['senha'])) {
            //Redireciono para a pagina home apos a validação do login pela senha.

            $url_destino = pg . '/visualizar/home';
            header("Location: $url_destino");
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> Login ou Senha incorreto! </div>";
            $url_destino = pg . '/acesso/login';
            header("Location: $url_destino");
        }
    } else {
        $_SESSION['msg'] = "<div class='alert alert-danger'> Login ou Senha incorreto! </div>";
        $url_destino = pg . '/acesso/login';
        header("Location: $url_destino");
    }
} else {
    //esse erro acontece caso haja uma tentativa de acessar o arquivo sem clicar no botão.

    //variavel global para criar uma mensagem de alerta.
    $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada!</div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
