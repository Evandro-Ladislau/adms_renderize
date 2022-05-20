<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

$SendopFinalizarOperacao = filter_input(INPUT_POST, 'SendopFinalizarOperacao', FILTER_SANITIZE_STRING);
if ($SendopFinalizarOperacao) {
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    //var_dump($dados);
    //validar nenhum campo vazio
    $erro = false;
    include_once 'lib/lib_vazio.php';
    $dados_validos = vazio($dados);

    if (!$dados_validos) {
        $erro = true;
        $_SESSION['msg'] = "<div class='alert alert-danger'> Necessário preencher todos os campos paraseguir com a operação!</div>";
    }

    //Se houver erro em algum campo será redirecionado para o login
    //mão há erro no formulário tenta cadastrar no banco
    if ($erro) {
        $url_destino = pg . '/cadastrar/cad_op_estoque';
        header("Location: $url_destino");
    } else {



        //pega o ultimo id da operacao que esta sendo executada.
        $result_Operacao_id = $pdo->OperacaoID();
        foreach ($result_Operacao_id as $value) {
            $adms_operacao_id = $value;
        }

        //cadastrar na tabela operacao de estoque incluindo o id da tabela operacao
        $cadastrar_operacao_estoque = $pdo->cadastrarOperacaoEstoque(
            $adms_operacao_id['id'],
            $dados_validos['adms_usuario_id'],
            $dados_validos['tipo_operacao'],
            $dados_validos['obs']

        );

        //Buscar os itens da operacao para alterar o estoque.
        $itensAtualizarEstoque = $pdo->BuscarItensOperacaoParaAtualizarEstoque($adms_operacao_id['id']);
        //Buscar o estoque atual dos itens da operacao na tebale de produtos
        $estoqueAtual = $pdo->EstoqueAtual($adms_operacao_id['id']);


        if ($dados_validos['tipo_operacao'] == 1) {
            //atualizar a tabela de produtos conforme a operacao de estoque
            for ($i = 0; $i < count($itensAtualizarEstoque); $i++) {
                 $atualizaEstoque = $pdo->AtualizarEstoqueOperacao($estoqueAtual[$i]['estoque'] + $itensAtualizarEstoque[$i]['quantidade'], $itensAtualizarEstoque[$i]['adms_produto_id']);
                
            }
        } else {
            
            //atualizar a tabela de produtos conforme a operacao de estoque
            for ($i = 0; $i < count($itensAtualizarEstoque); $i++) {
                    $atualizaEstoque = $pdo->AtualizarEstoqueOperacao($estoqueAtual[$i]['estoque'] - $itensAtualizarEstoque[$i]['quantidade'], $itensAtualizarEstoque[$i]['adms_produto_id']);
                
            }
        }

        //inserir um proximo registro para tabela operacao que servirá de codigo para a proxima operacao de esstoque
        if ($cadastrar_operacao_estoque) {
            $prox_cod_operacao_estoque = $pdo->OperacaoInsertId($adms_operacao_id['id'] + 1);
        } else {
            $_SESSION['msg'] = "<div class='alert alert-success'> Erro ao Processar Operacao de Estoque, Verifique! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
            </button> </div>";
            $url_destino = pg . '/cadastrar/cad_op_estoque';
            header("Location: $url_destino");
        }


        //se cadastrar no banco de dados acessa esse if
        if ($prox_cod_operacao_estoque) {
            unset($_SESSION['adms_operacao_id']);
            $_SESSION['msg'] = "<div class='alert alert-success'> Operacao de Estoque concluída com Sucesso! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
            </button> </div>";
            $url_destino = pg . '/cadastrar/cad_op_estoque';
            header("Location: $url_destino");
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> Erro ao Efetuar a Operacao, Verifique! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
        </button></div>";
            $url_destino = pg . '/cadastrar/cad_op_estoque';
            header("Location: $url_destino");
        }
    }
} else {
    //variavel global para criar uma mensagem de alerta.
    $_SESSION['msg'] = "<div class='alert alert-danger'> Página não encontrada! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
    <span aria-hidden='true'>&times;</span>
</button></div>";
    $url_destino = pg . '/acesso/login';
    header("Location: $url_destino");
}
