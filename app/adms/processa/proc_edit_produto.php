<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//receber o valor do campo input com nome SendCadPg do formulario no arquivo cad_pagina
$SendEditProduto = filter_input(INPUT_POST, 'SendEditProduto', FILTER_SANITIZE_STRING);

//se essa variavel tiver valor significa que o usuario clicou no botão então ele entra nesse if
if ($SendEditProduto) {
    //receber os dados do formulario e atribuir para a variavel $dados
    //dados recebidos como string usando o metodo post e passando um filtro nos dados.
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    //validar se existe algum desses campos obrigatorios vazios (tirando o obs e icone que foram destruidos)
    //inclui o arquivo lib_vazio responsável por validar se existe campo vazio.
    $erro = false;
    include_once 'lib/lib_vazio.php';

    $dados_validos = vazio($dados);

    if (!$dados_validos) {
        //se for diferente de verdadeiro(no caso falso algum campo esta vazio)

        //erro se torna verdadeiro e caiu no if abaixo redirecionando para cadastrar.
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'> Necessário preencher todos os campos para editar a Menu!</div>";
    } 

    //HOUVE ERRO EM ALGUM CAMPO SERÁ REDIRECIONADO PARA O CADASTRAR PAGINA.
    if ($erro) {
        //$_SESSION['dados'] = $dados;
        //se o usuario tentar entrar na pagina sem clicar no botão.
        $url_destino = pg . '/editar/edit_produto?id='.$dados['id'];
        header("Location: $url_destino");

        //NÃO HÁ ERRO NO FORMULÁRIO TENTA CADASTRAR NO BANCO
    } else {
       $result_edit_Produto = $pdo->EditarProduto($dados_validos['descricao'],
                                                $dados_validos['adms_unidade_id'],     
                                                $dados_validos['estoque'], 
                                                $dados_validos['preco_custo'], 
                                                $dados_validos['preco_venda'], 
                                                $dados_validos['adms_sit_id'], 
                                                $dados_validos['id']);

        print_r($dados_validos);

        
        if ($result_edit_Produto) {
            unset($_SESSION['dados']);

            $_SESSION['msg'] = "<div class='alert alert-success'> Produto editado com sucesso! </div>";
           // $url_destino = pg . '/listar/list_produto';
            //header("Location: $url_destino");
        } else {
            
            $_SESSION['msg'] = "<div class='alert alert-danger'> Produto não editada! </div>";
            $url_destino = pg . '/editar/edit_produto?id='.$dados['id'];
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
