<?php
if (!isset($seguranca)) {
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once '../adm/index.php';

//receber o valor do campo input com nome SendCadPg do formulario no arquivo cad_pagina
$SendCadPg = filter_input(INPUT_POST, 'SendCadPg', FILTER_SANITIZE_STRING);

//se essa variavel tiver valor significa que o usuario clicou no botão então ele entra nesse if
if ($SendCadPg) {
    //receber os dados do formulario e atribuir para a variavel $dados
    //dados recebidos como string usando o metodo post e passando um filtro nos dados.
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    //var_dump($dados); esse vardum mostra que na variavel dados existe um array com 14 posições, cada uma delas é os campos do formulario cadastrar
    //contendo o nome da tabela do banco + o botao cadastrar.


    //retirar campo que não são orbrigatórios do array $dados citado anteriormente
    $dados_ob = $dados['obs']; //campo observação
    $dados_icone = $dados['icone']; //campo icone

    //destruir as posições do array $dados
    unset($dados['obs'], $dados['icone']);
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
        $_SESSION['msg'] = "<div class='alert alert-danger'> Necessário preencher todos os campos para cadastrar a página!</div>";
    } else {
        //Proibir o cadatro de página duplicado
        $resultado_pagina_duplicada = $pdo->validarCadPaginaDuplicada($dados_validos['endereco'], $dados_validos['adms_tps_pg_id'], $id);

        if ($resultado_pagina_duplicada) {
            $erro = true;
            $_SESSION['msg'] = "<div class='alert alert-danger'> Este endereço já esta cadastrado!</div>";
        }
    }


    //HOUVE ERRO EM ALGUM CAMPO SERÁ REDIRECIONADO PARA O CADASTRAR PAGINA.
    if ($erro) {
        $dados['obs'] = trim($dados_ob); //campo observação
        $dados['icone'] = $dados_icone; //campo icone
        $_SESSION['dados'] = $dados;
        //se o usuario tentar entrar na pagina sem clicar no botão.
        $url_destino = pg . '/cadastrar/cad_pagina';
        header("Location: $url_destino");

        //NÃO HÁ ERRO NO FORMULÁRIO TENTA CADASTRAR NO BANCO
    } else {
        $result_cad_pagina = $pdo->cadastrarPagina(
            $dados_validos['nome_pagina'],
            $dados_validos['endereco'],
            $dados_ob,
            $dados_validos['keywords'],
            $dados_validos['description'],
            $dados_validos['author'],
            $dados_validos['lib_pub'],
            $dados_icone,
            $dados_validos['depend_pg'],
            $dados_validos['adms_grps_pg_id'],
            $dados_validos['adms_tps_pg_id'],
            $dados_validos['adms_robot_id'],
            $dados_validos['adms_sits_pg_id']
        );


        if ($result_cad_pagina) {
            unset($_SESSION['dados']);

            //Inicio inserir na tabela adms_nivacs_pgs

            //neste caso ele vai retornar o id da ultima pagina cadastrada isso pq eu usei o parametro
            //pdo->lastInsertId(); dentro da funcao que insere os dados e retornei ele mesmo sendo assim apos a chamada da funcao
            //ea retorna o valor do id da ultima pagina cadastrada.
            $pagina_id = $result_cad_pagina; 

            //Pesquisar os niveis de acesso
            $result_niv_acesso = $pdo->pesquisarIdNivelAcessoCadastrados(); 
            

            for ($i=0; $i < count($result_niv_acesso) ; $i++) { 
                //Determinar 1 na permissão caso seja superadministrador e para outros niveis 2:  1 Liberado, 2 - BLoqueado
                if ($result_niv_acesso[$i]['id'] == 1) {
                    $permissao = 1;
                }else{
                    $permissao = 2;
                }
                
                //Pesquisar o maior numero da ordem na tabela  adms_nivacs_pgs (para o nivel de acesso em eecução, é realmente necessario verificar se o nivel de acesso é igual o nive do usuario em execucao?)
                $result_maior_ordem = $pdo->maiorNumeroOrdemAdmsNivAcs();
                $ordem = $result_maior_ordem[0]['ordem'] + 1;
                
                

                $result_cad_nivacs_pg = $pdo->cadastrarPermissaoAcesso($permissao, $ordem, $result_niv_acesso[$i]['id'], $pagina_id);
            
            }
            
            $_SESSION['msg'] = "<div class='alert alert-success'> Página cadastrada! </div>";
            $url_destino = pg . '/listar/list_pagina';
            header("Location: $url_destino");
        } else {
            $dados['obs'] = trim($dados_ob); //campo observação
            $dados['icone'] = $dados_icone; //campo icone
            $_SESSION['dados'] = $dados;
            
            $_SESSION['msg'] = "<div class='alert alert-danger'> Página não cadastrada! </div>";
            $url_destino = pg . '/cadastrar/cad_pagina';
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
