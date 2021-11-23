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
                        <h2 class="display-4 titulo">Cadastrar Página</h2>
                    </div>
                    <div class="p-2">
                        <?php
                        //BOTAO VISUALIZAE
                        $btn_list = $pdo->carregarBtn('listar/list_pagina');

                        if ($btn_list) {
                            echo "<a href='" . pg . "/listar/list_pagina?id=" . $_SESSION['id'] . "' class='btn btn-outline-info btn-sm'>Listar</a>";
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
                <form method="POST" action="<?php echo pg; ?>/processa/proc_cad_pagina">
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label><span class="text-danger">*</span> Nome</label>
                            <input name="nome_pagina" type="text" class="form-control" placeholder="Nome da pagina" id="nome">
                        </div>
                        <div class="form-group col-md-4">
                            <label><span class="text-danger">*</span> Endereço</label>
                            <input name="endereco" type="text" class="form-control" placeholder="Endereço da página, ex: listar/list_pagina" id="email">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Ícone</label>
                            <input name="icone" type="text" class="form-control" placeholder="Ícone da página" id="email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Observação</label>
                        <textarea name="obs" class="form-control"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label><span class="text-danger">*</span> Palavra Chave</label>
                            <input name="keywords" type="text" class="form-control" placeholder="Palavra Chave" id="nome">
                        </div>
                        <div class="form-group col-md-4">
                            <label><span class="text-danger">*</span> Descrição</label>
                            <input name="description" type="text" class="form-control" placeholder=" Descrição da página" id="email">
                        </div>

                        <div class="form-group col-md-3">
                            <label><span class="text-danger">*</span>Autor</label>
                            <input name="author" type="text" class="form-control" placeholder="Desenvolvedor" id="email">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label><span class="text-danger">*</span> Indexar</label>
                            <select name="adms_robot_id" id="adms_robot_id" class="form-control">
                                <option selected>Selecione</option>
                                <?php
                                $resultado_robots = $pdo->listarRobots();
                                for ($i = 0; $i < count($resultado_robots); $i++) {
                                    echo "<option value='" . $resultado_robots[$i]['id'] . "'>" . $resultado_robots[$i]['nome'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label><span class="text-danger">*</span> Página pública</label>
                            <select name="lib_pub" id="lib_pub" class="form-control">
                                <option selected>Selecione</option>
                                <option value="1">Sim</option>
                                <option value="2">Não</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label><span class="text-danger">*</span>Página Dependente</label>
                            <select name="depend_pg" id="depend_pg" class="form-control">
                                <option selected>Selecione</option>
                                <?php
                                $resultado_dependentes = $pdo->listarDependentes();
                                for ($i = 0; $i < count($resultado_dependentes); $i++) {
                                    echo "<option value='" . $resultado_dependentes[$i]['id'] . "'>" . $resultado_dependentes[$i]['nome_pagina'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label><span class="text-danger">*</span> Grupo</label>
                            <select name="adms_grps_pg_id" id="adms_grps_pg_id" class="form-control">
                                <option selected>Selecione</option>
                                <?php
                                $resultado_grupos = $pdo->listarGrupos();
                                for ($i = 0; $i < count($resultado_grupos); $i++) {
                                    echo "<option value='" . $resultado_grupos[$i]['id'] . "'>" . $resultado_grupos[$i]['nome'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label><span class="text-danger">*</span> Tipo</label>
                            <select name="adms_tps_pg_id" id="adms_tps_pg_id" class="form-control">
                                <option selected>Selecione</option>
                                <?php
                                $resultado_tipos_paginas = $pdo->listarTiposPaginas();
                                for ($i = 0; $i < count($resultado_tipos_paginas); $i++) {
                                    echo "<option value='" . $resultado_tipos_paginas[$i]['id'] . "'>" . $resultado_tipos_paginas[$i]['tipo'] . " - ".$resultado_tipos_paginas[$i]['nome']."</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label><span class="text-danger">*</span>Situação</label>
                            <select name="adms_sits_pg_id" id="adms_sits_pg_id" class="form-control">
                                <option selected>Selecione</option>
                                <?php
                                $resultado_situacao_paginas = $pdo->listarSituacaoPaginas();
                                for ($i = 0; $i < count($resultado_situacao_paginas); $i++) {
                                    echo "<option value='" . $resultado_situacao_paginas[$i]['id'] . "'> ".$resultado_situacao_paginas[$i]['nome']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <p><span class="text-danger">*</span> Campo Obrogatório</p>
                    <input name="SendCadPg" type="submit" class="btn btn-success" value="Cadastrar">
                </form>

            </div>
        </div>
    </div>
    <?php

    include_once 'app/adms/include/rodape_lib.php';
    ?>
    </div>
</body>