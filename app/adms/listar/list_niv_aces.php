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
                        <h2 class="display-4 titulo">Listar Nivel de Acesso</h2>
                    </div>
                    <a href="cadastrar.html">
                        <div class="p-2">
                            <button class="btn btn-outline-success btn-sm">
                                Cadastar
                            </button>
                        </div>
                    </a>
                </div>
                <div class="alert alert-success" role="alert">
                    Usuario Apagado com Sucesso!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php
                //recebe o numero da pagina que o usuario esta
                $pagina_atual = filter_input(INPUT_GET, 'pagina', FILTER_SANITIZE_NUMBER_INT);
                $pagina = (!empty($pagina_atual)) ? $pagina_atual : 1;

                //setar a quantidade de itens por pagina
                $qnt_result_pg = 4;

                //calcular o inicio visualização
                $inicio = ($qnt_result_pg * $pagina) - $qnt_result_pg;

                //chamei a funcao que busca a paginacao conforme nivel de acesso.
                $result_paginacaoNivelAcesso = $pdo->paginacaoNivelAcesso($inicio, $qnt_result_pg);
                if ($result_paginacaoNivelAcesso) {
                ?>
                    <div class="table-responsive">
                        <table class="table table table-bordered table-striped table-hover ">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th class="d-none d-sm-table-cell">Ordem</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                //pegando as informações do nivel de acesso cadastradas no banco de dados.
                                for ($i=0; $i <count($result_paginacaoNivelAcesso) ; $i++) { 
                                    ?>
                                    <tr>
                                    <th><?php echo $result_paginacaoNivelAcesso[$i]['id']?></th>
                                    <td><?php echo $result_paginacaoNivelAcesso[$i]['nome']?></td>
                                    <td class="d-none d-sm-table-cell"><?php echo $result_paginacaoNivelAcesso[$i]['ordem']?></td>
                                    <td class="text-center">
                                        <span class="d-none d-md-block">
                                            <a href="visualizar.html" class="btn btn-outline-primary btn-sm">Visualizar</a>
                                            <a href="editar.html" class="btn btn-outline-success btn-sm">Editar</a>
                                            <a href="apagar.html" data-toggle="modal" data-target="#apagarRegistro" class="btn btn-outline-danger btn-sm">Apagar</a>
                                        </span>
                                        <div class="dropdown d-block d-md-none">
                                            <button class="btn btn-primary dropdown-toggle btn-sm" type="button" id="acoeslistar" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Ações
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="acoeslistar">
                                                <a class="dropdown-item" href="visualizar.html">Visualizar</a>
                                                <a class="dropdown-item" href="editar.html">Editar</a>
                                                <a class="dropdown-item" data-toggle="modal" data-target="#apagarRegistro" href="apagar.html">Apagar</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                    <?php
                                }
                                ?>
                                
                            </tbody>
                        </table>
                        <nav aria-label="paginacao">
                            <ul class="pagination pagination-sm justify-content-center">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Primeira</a>
                                </li>
                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item active">
                                    <a class="page-link" href="#">3</a>
                                </li>
                                <li class="page-item"><a class="page-link" href="#">4</a></li>
                                <li class="page-item"><a class="page-link" href="#">5</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Ultima</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                <?php
                } else {
                ?>

                    <div class="alert alert-danger" role="alert">
                        Nenhuma registro encontrado!
                    </div>
                <?php

                }

                ?>

            </div>
        </div>
        <?php

        include_once 'app/adms/include/rodape_lib.php';
        ?>
    </div>
</body>