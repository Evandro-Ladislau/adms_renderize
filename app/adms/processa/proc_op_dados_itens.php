<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

$SendopProdItens = filter_input(INPUT_POST, 'SendopProdItens', FILTER_SANITIZE_STRING);
if ($SendopProdItens) {
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

        
        //verificar se o item já existe na operação
        $result_itens_exist = $pdo->VerificarItem($adms_operacao_id['id'], $dados_validos['adms_produto_id']);
        if ($result_itens_exist) {
            
            //atualizar quantidade quando o produto ja esta listado nos itens da operacao
            foreach ($result_itens_exist as  $value) {
                $quantidade = $value;
            }
            $quantidade_total = $quantidade['quantidade'] + $dados_validos['quantidade'];
            $atualizarQuanditade = $pdo->AtualizarQuantidade($quantidade_total, $adms_operacao_id['id'], $dados_validos['adms_produto_id']);
            
            
            
        }else{
            //atualizar a quantidade na listagem da operacao
            $result_Itens_Operacao = $pdo->cadastrarItensOperacao($adms_operacao_id['id'], 
            $dados_validos['adms_produto_id'], 
            $dados['quantidade'], 
            $dados['adms_unidade_id']);
        }


       
        if ($atualizarQuanditade) {
            $_SESSION['msg'] = "<div class='alert alert-success'> Quantidade Atualizada, produo ja existia na listagem! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
            </button> </div>";
            $url_destino = pg . '/cadastrar/cad_op_estoque';
            header("Location: $url_destino");

        }elseif ($result_Itens_Operacao) {
            $_SESSION['msg'] = "<div class='alert alert-success'> Produto inserido com sucesso! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
            </button> </div>";
            $url_destino = pg . '/cadastrar/cad_op_estoque';
            header("Location: $url_destino");
        } else {
            $_SESSION['msg'] = "<div class='alert alert-danger'> Erro ao inserir o Produto! <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
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
