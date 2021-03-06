<?php
if (!isset($seguranca)) {
    exit;
}
//classe de conexão com o banco de dados.
class Conexao
{
    private $pdo; // atributo privado só é usado dentro da classe.

    //CONEXÃO COM BANCO DE DADOS

    function __construct($dbname, $host, $user, $senha)
    {

        try {
            $this->pdo = new PDO("mysql:dbname=" . $dbname . ";host=" . $host, $user, $senha);
        } catch (PDOException $e) {
            echo "Erro na conexão com banco de dados:" . $e->getMessage();
            exit();
        } catch (Exception $e) {
            echo "Erro generico" . $e->getMessage();
            exit();
        }
    }

    //Essa função busca as paginas cadastradas no banco em que o adms_pagina_id que é chave estrangeira da 
    //tabela adms_nivacs_pgs
    //seja igual ao id da tabela adms_pagina E
    //endereco cadastrado seja igual ao passado pela url
    // e o adms_niveis_acesso_id chave estrangeira da tabela adms_niveis_acessos tem que ser igual ao adms_niveis_acesso_id
    // que esta cadastrado no usuario.

    public function paginasCadastradas($url)
    {
        $result = array();

        if (isset($_SESSION['adms_niveis_acesso_id'])) {
            $adms_niveis_acesso_id = $_SESSION['adms_niveis_acesso_id'];
        } else {
            $adms_niveis_acesso_id = 0;
        }

        $cmd = $this->pdo->prepare("SELECT pg.id, pg.endereco ,
        tpg.tipo
        FROM adms_paginas pg
        LEFT JOIN adms_nivacs_pgs nivpg ON nivpg.adms_pagina_id=pg.id
        INNER JOIN adms_tps_pg tpg ON tpg.id=pg.adms_tps_pg_id
        WHERE pg.endereco=:endereco
        AND (pg.adms_sits_pg_id=1
        AND (nivpg.adms_niveis_acesso_id=:adms_niveis_acesso_id	
        AND nivpg.permissao=1) OR (pg.lib_pub=1))
         LIMIT 1");

        $cmd->bindValue(":endereco", $url, PDO::PARAM_STR);
        $cmd->bindValue(":adms_niveis_acesso_id", $adms_niveis_acesso_id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    //essa funcao PEGA AS INFORMACOES DO BANO DE DADOS PAR SER COLOCADA NA vARIAVEL GLOBAL
    public function validarLogin()
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT id, nome, email, usuario, senha , adms_niveis_acesso_id 
        FROM adms_usuarios  WHERE usuario=usuario ");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //Essa função buscar a ordem do nivel de acesso do usuario
    public function buscarOrdemNivelAcesso($adms_niv_ac_id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT ordem FROM adms_niveis_acessos 
        WHERE id=:adms_niv_ac_id LIMIT 1");
        $cmd->bindParam(":adms_niv_ac_id", $adms_niv_ac_id);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //Essa função busca os dados do usuario no banco de dados.
    public function buscarDadosUsuarios($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id, nome, imagem FROM adms_usuarios 
        WHERE id=:id LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    //essa funcao pega todos os valores da tabela adms_nivacs_pgs onde o adms_niveis_cesso_id é igual
    // ao do usuario logado tambem a coluna permissao seja igual a 1 e tambem a lib_menu tem que estar liberada .
    public function buscarBotoesMenu( $adms_niveis_acesso_id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT nivpg.dropdown,
        men.id, men.nome nomemen, men.icone iconmen,
        pg.id id_pg_menu, pg.nome_pagina nomepg, pg.endereco, pg.icone iconpg
        FROM adms_nivacs_pgs nivpg
        INNER JOIN adms_menus men ON men.id=nivpg.adms_menu_id
        INNER JOIN adms_paginas pg ON pg.id=nivpg.adms_pagina_id
        WHERE nivpg.adms_niveis_acesso_id=:adms_niveis_acesso_id
            AND nivpg.permissao=1
            AND nivpg.lib_menu=1 
            ORDER BY men.ordem, nivpg.ordem ASC");
        $cmd->bindValue(":adms_niveis_acesso_id", $adms_niveis_acesso_id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //Essa pega as informacoes da tabela adms_niveis_acesso onde a ordem do usuario logado seja igual ou menos que os outros usuarios
    // e limita o resultado do inicio da pagina que ele esta ao valor final de paginas.
    public function paginacaoNivelAcesso($inicio, $qnt_result_pg)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT * FROM adms_niveis_acessos  
        ORDER BY ordem 
        ASC LIMIT :inicio, :qnt_result_pg");
        $cmd->bindValue(":inicio", $inicio, PDO::PARAM_INT);
        $cmd->bindParam(":qnt_result_pg", $qnt_result_pg, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    //Essa pega as informacoes da tabela adms_niveis_acesso onde a ordem do usuario logado seja igual ou menos que os outros usuarios
    // e limita o resultado do inicio da pagina que ele esta ao valor final de paginas. LIMITANDO CONFORME NIVEL DE ACESSO
    public function paginacaoNivelAcessoLimitado($inicio, $qnt_result_pg, $ordem)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT * FROM adms_niveis_acessos 
        WHERE ordem >= :ordem 
        ORDER BY ordem 
        ASC LIMIT :inicio, :qnt_result_pg");
        $cmd->bindValue(":inicio", $inicio, PDO::PARAM_INT);
        $cmd->bindParam(":qnt_result_pg", $qnt_result_pg, PDO::PARAM_INT);
        $cmd->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    //funcao count conta a coluna id e o resultado é atribuido para um apelido(num_result).
    public function paginacao()
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT COUNT(id) AS num_result FROM adms_niveis_acessos WHERE ordem >= ordem");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function carregarBtn($endereco)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT pg.id, pg.endereco 
        FROM adms_paginas pg
        LEFT JOIN adms_nivacs_pgs nivpg ON nivpg.adms_pagina_id=pg.id
        WHERE pg.endereco=:endereco
        AND (pg.adms_sits_pg_id=1
        AND (nivpg.adms_niveis_acesso_id=:adms_niveis_acesso_id	
        AND nivpg.permissao=1))
         LIMIT 1");

        $cmd->bindValue(":adms_niveis_acesso_id", $_SESSION['adms_niveis_acesso_id'], PDO::PARAM_INT);
        $cmd->bindValue(":endereco", $endereco, PDO::PARAM_STR);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    //essa funcao buscar no banco os detalhes do nivel de acesso cadastrado
    //com a condicao da ordem for maior ou igual a do usuario e 
    // o id por igual ao id do usuario colocando em ordem ascendente.
    public function buscarDadosNivelAcesso($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT * FROM adms_niveis_acessos WHERE id=:id ORDER BY ordem ASC LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    //essa funcao buscar no banco os detalhes do nivel de acesso cadastrado
    //com a condicao da ordem for maior ou igual a do usuario e 
    // o id por igual ao id do usuario colocando em ordem ascendente.
    //LIMITADA
    public function buscarDadosNivelAcessoLimitada($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT * FROM adms_niveis_acessos WHERE ordem >= :ordem 
        AND id=:id ORDER BY ordem ASC LIMIT 1");

        $cmd->bindValue(":ordem", $_SESSION['ordem'], PDO::PARAM_INT);
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //BUSCAR A ORDEM DOS NIVEIS DE ACESSO CADASTRADOS
    public function ordemCadastrarNivAc()
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT ordem FROM adms_niveis_acessos");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //CADASTRAR NIVEL DE ACESSO
    public function cadastrarNivelAcesso($dados_validos, $ordem)
    {
        $cmd = $this->pdo->prepare("INSERT INTO adms_niveis_acessos (nome, ordem, created) 
        VALUES (:dados, :ordem, NOW()) ");

        $cmd->bindValue(":dados", $dados_validos, PDO::PARAM_STR);
        $cmd->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    //busca o id do usuario logado que seja igual ao passad pela url
    public function verificarId($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id, nome FROM adms_niveis_acessos WHERE ordem > :ordem AND id=:id LIMIT 1");
        $cmd->bindValue(":ordem", $_SESSION['adms_niveis_acesso_id'], PDO::PARAM_INT);
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function alterarNivelAcesso($nome, $id)
    {

        $cmd = $this->pdo->prepare("UPDATE adms_niveis_acessos SET nome=:nome, modified=NOW() 
        WHERE id=:id ");
        $cmd->bindValue(":nome", $nome, PDO::PARAM_STR);
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function buscarOrdemDoNivelDeletado($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id, ordem AS ordem_result 
        FROM adms_niveis_acessos 
        WHERE ordem > (SELECT ordem FROM adms_niveis_acessos WHERE id=:id) ORDER BY ordem ASC");
        $cmd->bindValue(":id", $id);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function deletarNivelAcesso($id)
    {
        $cmd = $this->pdo->prepare("DELETE FROM adms_niveis_acessos WHERE id=:id AND ordem > :ordem ");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->bindValue(":ordem", $_SESSION['ordem'], PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function atualizaOrdem($ordem, $id)
    {
        $cmd = $this->pdo->prepare("UPDATE  adms_niveis_acessos SET 
        ordem=:ordem, modified=NOW()
        WHERE id=:id");
        $cmd->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function verificarNivelCadastradoUsuario($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id FROM adms_usuarios WHERE adms_niveis_acesso_id=:id LIMIT 1 ");
        $cmd->bindValue("id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function alterarOrdemNivelAcesso($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id, ordem FROM adms_niveis_acessos WHERE id=:id LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function pesquisarIdNivelAcessoMovido($ordem_super)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id, ordem FROM adms_niveis_acessos WHERE ordem=:ordem_super LIMIT 1");
        $cmd->bindValue(":ordem_super", $ordem_super, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function altualizaOrdemNivelAcessoMaior($ordem, $niv_super)
    {
        $cmd = $this->pdo->prepare("UPDATE adms_niveis_acessos SET ordem=:ordem, modified=NOW() 
        WHERE id=:niv_super");
        $cmd->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $cmd->bindValue(":niv_super", $niv_super, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }


    public function altualizaOrdemNivelAcessoMenor($ordem_super, $ordem_niv_atual)
    {
        $cmd = $this->pdo->prepare("UPDATE adms_niveis_acessos SET ordem=:ordem_super, modified=NOW() 
        WHERE id=:ordem_niv_atual");
        $cmd->bindValue(":ordem_super", $ordem_super, PDO::PARAM_INT);
        $cmd->bindValue(":ordem_niv_atual", $ordem_niv_atual, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function paginacaoNivelAcessoPaginas($inicio, $qnt_result_pg)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT pg.id, pg.nome_pagina, pg.endereco,
        tpg.tipo
        FROM adms_paginas pg
        INNER JOIN adms_tps_pg tpg ON tpg.id=adms_tps_pg_id
        ORDER BY id 
        ASC LIMIT :inicio, :qnt_result_pg");
        $cmd->bindValue(":inicio", $inicio, PDO::PARAM_INT);
        $cmd->bindParam(":qnt_result_pg", $qnt_result_pg, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function paginacaoNivelAcessoPermissao($inicio, $qnt_result_pg,  $ordem)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT pg.id, pg.nome_pagina, pg.endereco, tpg.tipo
        FROM adms_paginas pg 
        INNER JOIN adms_nivacs_pgs nivac ON nivac.adms_pagina_id=pg.id 
        INNER JOIN adms_niveis_acessos nivacess ON nivacess.id = nivac.adms_niveis_acesso_id
        INNER JOIN adms_tps_pg tpg ON tpg.id=pg.adms_tps_pg_id
        WHERE nivac.adms_niveis_acesso_id > :ordem
        ORDER BY id
        ASC LIMIT :inicio, :qnt_result_pg");
        $cmd->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $cmd->bindValue(":inicio", $inicio, PDO::PARAM_INT);
        $cmd->bindParam(":qnt_result_pg", $qnt_result_pg, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //funcao count conta a coluna id e o resultado é atribuido para um apelido(num_result).
    public function paginacaoPaginas()
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT COUNT(id) AS num_result FROM adms_paginas");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //listar opçoes buscadores no cadatro de pagina "Indexar"
    public function listarRobots()
    {

        $result = array();
        $cmd = $this->pdo->query("SELECT id, nome FROM adms_robots ");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    //listar opçoes dependentes no cadatro de pagina "Dependentes"
    public function listarDependentes()
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT id, nome_pagina FROM adms_paginas ORDER BY nome_pagina ASC");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //listar opçoes grupos no cadatro de pagina "Grupo"
    public function listarGrupos()
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT id, nome FROM adms_grps_pgs ORDER BY nome ASC");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //listar opçoes tipos de paginas no cadatro de pagina "Tipos"
    public function listarTiposPaginas()
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT id, tipo, nome FROM  adms_tps_pg ORDER BY nome ASC");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //listar opçoes tipos de paginas no cadatro de pagina "Tipos"
    public function listarSituacaoPaginas()
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT id, nome FROM  adms_sits_pgs ORDER BY nome ASC");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //Cadastrar pagina no banco de dados.

    public function cadastrarPagina(
        $nome_pagina,
        $endereco,
        $obs,
        $keywords,
        $descriptio,
        $author,
        $lib_pub,
        $icone,
        $depend_pg,
        $adms_grps_pg_id,
        $adms_tps_pg_id,
        $adms_robot_id,
        $adms_sits_pg_id
    ) {

        $cmd = $this->pdo->prepare("INSERT INTO  adms_paginas 
        (nome_pagina, endereco, obs, keywords, descriptio, author, 
        lib_pub, icone, depend_pg, adms_grps_pg_id, adms_tps_pg_id, 
        adms_robot_id, adms_sits_pg_id, created )

        VALUES (:nome_pagina, :endereco, :obs, :keywords, :descriptio, :author, 
        :lib_pub, :icone, :depend_pg, :adms_grps_pg_id, :adms_tps_pg_id, :adms_robot_id, :adms_sits_pg_id, NOW()) ");

        $cmd->bindValue(":nome_pagina", $nome_pagina, PDO::PARAM_STR);
        $cmd->bindValue(":endereco", $endereco, PDO::PARAM_STR);
        $cmd->bindValue(":obs", $obs, PDO::PARAM_STR);
        $cmd->bindValue(":keywords", $keywords, PDO::PARAM_STR);
        $cmd->bindValue(":descriptio", $descriptio, PDO::PARAM_STR);
        $cmd->bindValue(":author", $author, PDO::PARAM_STR);

        $cmd->bindValue(":lib_pub", $lib_pub, PDO::PARAM_INT);
        $cmd->bindValue(":icone", $icone, PDO::PARAM_STR);
        $cmd->bindValue(":depend_pg", $depend_pg, PDO::PARAM_INT);
        $cmd->bindValue(":adms_grps_pg_id", $adms_grps_pg_id, PDO::PARAM_INT);
        $cmd->bindValue(":adms_tps_pg_id", $adms_tps_pg_id, PDO::PARAM_INT);

        $cmd->bindValue(":adms_robot_id", $adms_robot_id, PDO::PARAM_INT);
        $cmd->bindValue(":adms_sits_pg_id", $adms_sits_pg_id, PDO::PARAM_INT);
        $cmd->execute();
        //apos a execucao da query passei o parametro para pegar o valor do ultimo id cadastrado
        // e retornei ele mesmo
        $cmd = $this->pdo->lastInsertId();
        return $cmd;
    }

    public function validarCadPaginaDuplicada($endereco, $adms_tps_pg_id, $id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id FROM adms_paginas 
        WHERE endereco=:endereco 
        AND adms_tps_pg_id=:adms_tps_pg_id AND id <> :id");
        $cmd->bindValue(":endereco", $endereco);
        $cmd->bindValue(":adms_tps_pg_id", $adms_tps_pg_id);
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function pesquisarIdNivelAcessoCadastrados()
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT id, nome FROM adms_niveis_acessos");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function maiorNumeroOrdemAdmsNivAcs($result_niv_acesso)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT ordem FROM adms_nivacs_pgs 
        WHERE adms_niveis_acesso_id=:result_niv_acesso
        ORDER BY ordem DESC LIMIT 1 ");
        $cmd->bindValue(":result_niv_acesso", $result_niv_acesso, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //CADASTRAR no banco de dados a permissão de acessar a página na tabela adms_nivacs_pgs

    public function cadastrarPermissaoAcesso(
        $permissao,
        $ordem,
        $result_niv_acesso_id,
        $pagina_id
    ) {

        $cmd = $this->pdo->prepare("INSERT INTO adms_nivacs_pgs 
        (	permissao, 
            ordem, 
            dropdown, 
            lib_menu, 
            adms_menu_id, 
            adms_niveis_acesso_id, 
            adms_pagina_id, 
            created	) 
            VALUES (
                :permissao, 
                :ordem, 
                1, 
                2, 
                3, 
                :adms_niveis_acesso_id, 
                :adms_pagina_id, 
                NOW())");

        $cmd->bindValue(":permissao", $permissao, PDO::PARAM_INT);
        $cmd->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $cmd->bindValue(":adms_niveis_acesso_id", $result_niv_acesso_id, PDO::PARAM_INT);
        $cmd->bindValue(":adms_pagina_id", $pagina_id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }


    public function pesquisarPaginasCadastradas($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT pg.*, 
        grpg.nome nome_grpg,
        tppg.tipo tipo_tppg ,tppg.nome nome_tppg,
        rb.tipo tipo_rb, rb.nome nome_rb,
        sitpg.nome nome_sitpg, sitpg.cor,
        depg.id id_depg, depg.nome_pagina nome_depg
        FROM adms_paginas pg
        LEFT JOIN adms_grps_pgs grpg ON grpg.id=pg.adms_grps_pg_id
        LEFT JOIN adms_tps_pg tppg ON tppg.id=pg.adms_tps_pg_id
        LEFT JOIN adms_robots rb ON rb.id=pg.adms_robot_id
        INNER JOIN adms_sits_pgs sitpg ON sitpg.id=pg.adms_sits_pg_id
        LEFT JOIN adms_paginas depg ON depg.id=pg.depend_pg
        WHERE pg.id=:id LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function VerificarPaginasCadastradasNoBanco($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT * FROM adms_paginas WHERE id=:id LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function EditarPagina(
        $nome_pagina,
        $endereco,
        $obs,
        $keywords,
        $descriptio,
        $author,
        $lib_pub,
        $icone,
        $depend_pg,
        $adms_grps_pg_id,
        $adms_tps_pg_id,
        $adms_robot_id,
        $adms_sits_pg_id,
        $id
    ) {
        $cmd = $this->pdo->prepare("UPDATE adms_paginas SET nome_pagina=:nome_pagina,
        endereco=:endereco,
        obs=:obs,
        keywords=:keywords,
        descriptio=:descriptio,
        author=:author,
        lib_pub=:lib_pub,
        icone=:icone,
        depend_pg=:depend_pg,
        adms_grps_pg_id=:adms_grps_pg_id,
        adms_tps_pg_id=:adms_tps_pg_id,
        adms_robot_id=:adms_robot_id,
        adms_sits_pg_id=:adms_sits_pg_id,
        modified=NOW() WHERE id=:id");

        $cmd->bindValue(":nome_pagina", $nome_pagina, PDO::PARAM_STR);
        $cmd->bindValue(":endereco", $endereco, PDO::PARAM_STR);
        $cmd->bindValue(":obs", $obs, PDO::PARAM_STR);
        $cmd->bindValue(":keywords", $keywords, PDO::PARAM_STR);
        $cmd->bindValue(":descriptio", $descriptio, PDO::PARAM_STR);
        $cmd->bindValue(":author", $author, PDO::PARAM_STR);
        $cmd->bindValue(":lib_pub", $lib_pub, PDO::PARAM_INT);
        $cmd->bindValue(":icone", $icone, PDO::PARAM_STR);
        $cmd->bindValue(":depend_pg", $depend_pg, PDO::PARAM_INT);
        $cmd->bindValue(":adms_grps_pg_id", $adms_grps_pg_id, PDO::PARAM_INT);
        $cmd->bindValue(":adms_tps_pg_id", $adms_tps_pg_id, PDO::PARAM_INT);
        $cmd->bindValue(":adms_robot_id", $adms_robot_id, PDO::PARAM_INT);
        $cmd->bindValue(":adms_sits_pg_id", $adms_sits_pg_id, PDO::PARAM_INT);
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function apagarPagina($id)
    {

        $cmd = $this->pdo->prepare("DELETE FROM adms_paginas WHERE id=:id ");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    //apaga o nível de acesso da página que for apagada.

    public function apagarNivAcessoPagina($id){
        $cmd = $this->pdo->prepare("DELETE FROM adms_nivacs_pgs WHERE adms_pagina_id=:id");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function permissaoSuperAdministrador($id, $inicio, $qnt_result_pg){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT nivpg.* ,
        pg.nome_pagina, pg.obs
        FROM adms_nivacs_pgs nivpg
        INNER JOIN adms_paginas pg ON pg.id=nivpg.adms_pagina_id
        WHERE nivpg.adms_niveis_acesso_id=:id AND pg.depend_pg=0
        ORDER BY nivpg.ordem ASC 
        LIMIT :inicio, :qnt_result_pg");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->bindValue(":inicio", $inicio, PDO::PARAM_INT);
        $cmd->bindValue(":qnt_result_pg", $qnt_result_pg, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
        
    }

    public function nomeNivelAcesso($id){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT nome FROM adms_niveis_acessos  WHERE id=:id LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function paginacaoPermissao($id)
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT COUNT(id) AS num_result FROM adms_nivacs_pgs WHERE adms_niveis_acesso_id=$id");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function buscarPaginaAlterarPermissao(){
        $result = array();
        $cmd = $this->pdo->query("SELECT * FROM adms_paginas");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
        
    }

    public function BuscarNiveisAcessoPaginas($id, $ordem){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT nivacpg.permissao, nivacpg.adms_niveis_acesso_id, nivacpg.adms_pagina_id
        FROM adms_nivacs_pgs nivacpg
        INNER JOIN adms_niveis_acessos nivac ON nivac.id=nivacpg.adms_niveis_acesso_id
        WHERE nivacpg.id=:id AND nivac.ordem > :ordem LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;

    }

    public function AtualizarNivelAcesso($status, $id){
        $cmd = $this->pdo->prepare("UPDATE adms_nivacs_pgs SET permissao=:estatus, modified=NOW()
        WHERE id=:id");
        $cmd->bindValue(":estatus", $status, PDO::PARAM_INT);
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function PesquisarPaginasDependentes($adms_pagina_id, $adms_niveis_acesso_id){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT nivacpg.id
        FROM adms_paginas pg
        LEFT JOIN adms_nivacs_pgs nivacpg ON nivacpg.adms_pagina_id=pg.id
        WHERE pg.depend_pg=:adms_pagina_id AND nivacpg.adms_niveis_acesso_id=:adms_niveis_acesso_id");
        $cmd->bindValue(":adms_pagina_id", $adms_pagina_id, PDO::PARAM_INT);
        $cmd->bindValue(":adms_niveis_acesso_id", $adms_niveis_acesso_id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;

    }

    public function AtualizarNivelAcessoDependente($status, $id){
        $cmd = $this->pdo->prepare("UPDATE adms_nivacs_pgs SET permissao=:estatus, modified=NOW()
        WHERE id=:id");
        $cmd->bindValue(":estatus", $status, PDO::PARAM_INT);
        $cmd->bindValue("id", $id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function PesquisaDadosNiveisAcessoPaginasADM($id){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT nivacpg.dropdown, nivacpg.lib_menu, nivacpg.adms_niveis_acesso_id
        FROM adms_nivacs_pgs nivacpg
        WHERE nivacpg.id=:id LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function PesquisaDadosNiveisAcessoPaginas($id, $ordem){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT nivacpg.dropdown, nivacpg.lib_menu, nivacpg.adms_niveis_acesso_id
        FROM adms_nivacs_pgs nivacpg
        INNER JOIN adms_niveis_acessos nivac ON nivac.id=nivacpg.adms_niveis_acesso_id
        WHERE nivacpg.id=:id AND nivac.ordem > :ordem LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->bindValue("ordem", $ordem);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function atualizarLiberarMenu($status,$id){
        $cmd = $this->pdo->prepare("UPDATE adms_nivacs_pgs SET lib_menu=:estatus, modified=NOW()
        WHERE id=:id");
        $cmd->bindValue(":estatus", $status, PDO::PARAM_INT);
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function atualizarLiberMenuDropdown($status,$id){
        $cmd = $this->pdo->prepare("UPDATE adms_nivacs_pgs SET dropdown=:estatus, modified=NOW()
        WHERE id=:id");
        $cmd->bindValue(":estatus", $status, PDO::PARAM_INT);
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function pesquisarAcessoPaginaAcoesADM($id){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id, ordem, adms_niveis_acesso_id		
        FROM adms_nivacs_pgs
        WHERE id=:id LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function pesquisarAcessoPaginaAcoes($id,$ordem){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT nivacpg.id, nivacpg.ordem, nivacpg.adms_niveis_acesso_id
        FROM adms_nivacs_pgs nivacpg
        INNER JOIN adms_niveis_acessos nivac ON nivac.id=nivacpg.adms_niveis_acesso_id
        WHERE nivacpg.id=:id AND nivac.ordem > :ordem LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;

    }

    public function pesquisarIdParaSerMovido($ordem, $adms_niveis_acesso_id){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id, ordem FROM adms_nivacs_pgs
        WHERE ordem=:ordem AND adms_niveis_acesso_id=:adms_niveis_acesso_id LIMIT 1");
        $cmd->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $cmd->bindValue(":adms_niveis_acesso_id", $adms_niveis_acesso_id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function alterarOrdemMaiorParaMenor($ordem_num_menor, $id){
        $cmd = $this->pdo->prepare("UPDATE adms_nivacs_pgs SET ordem=:ordem_num_menor, modified=NOW()
        WHERE id=:id ");
        $cmd->bindValue(":ordem_num_menor", $ordem_num_menor, PDO::PARAM_INT);
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function alterarOrdemMenorParaMaior($ordem, $id){
        $cmd = $this->pdo->prepare("UPDATE adms_nivacs_pgs SET ordem=:ordem, modified=NOW()
        WHERE id=:id");
        $cmd->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function PesquisarNiveisAcesso(){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id, nome FROM adms_niveis_acessos");
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function pesquisarPaginasSincrono(){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id, lib_pub FROM adms_paginas");
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function pesquisarInscricaoNivac($id,$paginas_id){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id, adms_niveis_acesso_id FROM adms_nivacs_pgs
        WHERE adms_niveis_acesso_id=:id AND adms_pagina_id=:paginas_id ORDER BY id ASC LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->bindValue(":paginas_id", $paginas_id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function pesquisarMaiorOrdemSincrono($id){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT ordem FROM adms_nivacs_pgs
        WHERE adms_niveis_acesso_id=:id
        ORDER BY id DESC LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function pesquisarPaginaItemMenu($id){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT adms_menu_id FROM adms_nivacs_pgs
        WHERE adms_pagina_id=:id ORDER BY id DESC LIMIT 1");
        $cmd->bindValue(":id", $id);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function cadastrarPermissaoAcessoUsuario(
        $permissao,
        $ordem,
        $item_menu,
        $result_niv_acesso_id,
        $pagina_id
    ) {

        $cmd = $this->pdo->prepare("INSERT INTO adms_nivacs_pgs 
        (	permissao, 
            ordem, 
            dropdown, 
            lib_menu, 
            adms_menu_id, 
            adms_niveis_acesso_id, 
            adms_pagina_id, 
            created	) 
            VALUES (
                :permissao, 
                :ordem, 
                1, 
                2, 
                :adms_menu_id, 
                :adms_niveis_acesso_id, 
                :adms_pagina_id, 
                NOW())");

        $cmd->bindValue(":permissao", $permissao, PDO::PARAM_INT);
        $cmd->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $cmd->bindValue(":adms_menu_id", $item_menu, PDO::PARAM_INT);
        $cmd->bindValue(":adms_niveis_acesso_id", $result_niv_acesso_id, PDO::PARAM_INT);
        $cmd->bindValue(":adms_pagina_id", $pagina_id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }


    public function editarPaginaPermissao($id){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT pg.icone, nivpg.id, nivpg.adms_menu_id, nivpg.adms_niveis_acesso_id
        FROM adms_paginas pg
        INNER JOIN adms_nivacs_pgs nivpg ON nivpg.adms_pagina_id=pg.id
        WHERE nivpg.id=:id LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function resultadoMenus(){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT * FROM adms_menus");
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function AlterMenu($dados_validos_adms_menu_id, $dados_validos_id){
        $cmd = $this->pdo->prepare("UPDATE adms_nivacs_pgs SET 
        adms_menu_id=:dados_validos_adms_menu_id, modified=NOW()
        WHERE id=:dados_validos_id");
        $cmd->bindValue(":dados_validos_adms_menu_id", $dados_validos_adms_menu_id, PDO::PARAM_INT);
        $cmd->bindValue(":dados_validos_id", $dados_validos_id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function PesquisarIdNivelAc($dados_validos_id){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT adms_niveis_acesso_id, adms_pagina_id 
        FROM adms_nivacs_pgs 
        WHERE id=:dados_validos_id 
        LIMIT 1");
        $cmd->bindValue(":dados_validos_id", $dados_validos_id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function AlterarIcone($dados_icone, $vetor_resultado_nivpg_adms_pagina_id){
        $cmd = $this->pdo->prepare("UPDATE adms_paginas SET 
        icone=:dados_icone
        modified=NOW()
        WHERE id=:vetor_resultado_nivpg_adms_pagina_id");
        $cmd->bindValue(":dados_icone", $dados_icone, PDO::PARAM_STR);
        $cmd->bindValue(":vetor_resultado_nivpg_adms_pagina_id", $vetor_resultado_nivpg_adms_pagina_id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function buscarDadosMenu($inicio,  $qnt_result_pg){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT * FROM adms_menus ORDER BY ordem ASC LIMIT :inicio, :qnt_result_pg ");
        $cmd->bindValue(":inicio", $inicio, PDO::PARAM_INT);
        $cmd->bindValue(":qnt_result_pg", $qnt_result_pg, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function paginacao_menu()
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT COUNT(id) AS num_result FROM adms_menus");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    

    public function listarSituacaoMenu()
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT id, nome FROM  adms_sits ORDER BY nome ASC");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function MaiorOrdemMenu(){
        $result = array();
        $cmd = $this->pdo->query("SELECT ordem FROM adms_menus ");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function CadastrarMenu(
        $nome,
        $icone,
        $ordem,
        $adms_sit_id
        )
        {
        $cmd = $this->pdo->prepare("INSERT INTO adms_menus 
        (
            nome,
            icone,
            ordem,
            adms_sit_id,
            created
            )
            VALUES 
            ( 
                :nome,
                :icone,
                :ordem,
                :adms_sit_id,
                NOW()
            )
             ");
        $cmd->bindValue(":nome", $nome, PDO::PARAM_STR);
        $cmd->bindValue(":icone", $icone, PDO::PARAM_STR);
        $cmd->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $cmd->bindValue(":adms_sit_id", $adms_sit_id, PDO::PARAM_INT);
        $cmd->execute();
        return true;     
    }

    public function VerificarMenusCadastradosNoBanco($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT * FROM adms_menus WHERE id=:id LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function EditarMenu(
        $nome,
        $icone,
        $adms_sit_id,
        $id
    ) {
        $cmd = $this->pdo->prepare("UPDATE adms_menus SET 
        nome=:nome,
        icone=:icone,
        adms_sit_id=:adms_sit_id,
        modified=NOW() WHERE id=:id");

        $cmd->bindValue(":nome", $nome, PDO::PARAM_STR);
        $cmd->bindValue(":icone", $icone, PDO::PARAM_STR);
        $cmd->bindValue(":adms_sit_id", $adms_sit_id, PDO::PARAM_INT);
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function pesquisarMenuCadastrado($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT men.* ,
        sit.nome nome_sit,
        cors.cor cor_cores
        FROM adms_menus men
        INNER JOIN adms_sits sit ON sit.id=men.adms_sit_id
        INNER JOIN adms_cors cors	ON cors.id=sit.adms_cor_id
        WHERE men.id=:id LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function verificarNivelMenuCadastrado($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id FROM adms_nivacs_pgs WHERE adms_menu_id=:id LIMIT 1 ");
        $cmd->bindValue("id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function buscarOrdemMenuCadastrado($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id, ordem AS ordem_result 
        FROM adms_menus 
        WHERE ordem > (SELECT ordem FROM adms_menus WHERE id=:id) ORDER BY ordem ASC");
        $cmd->bindValue(":id", $id);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function deletarMenu($id)
    {
        $cmd = $this->pdo->prepare("DELETE FROM adms_menus WHERE id=:id");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function atualizaOrdemMenu($ordem, $id)
    {
        $cmd = $this->pdo->prepare("UPDATE  adms_menus SET 
        ordem=:ordem, modified=NOW()
        WHERE id=:id");
        $cmd->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function alterarOrdemMenu($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id, ordem FROM adms_menus WHERE id=:id LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function pesquisarIdMenuAcessoMovido($ordem_super)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id, ordem FROM adms_menus WHERE ordem=:ordem_super LIMIT 1");
        $cmd->bindValue(":ordem_super", $ordem_super, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function altualizaOrdemMenuAcessoMaior($ordem, $niv_super)
    {
        $cmd = $this->pdo->prepare("UPDATE adms_menus SET ordem=:ordem, modified=NOW() 
        WHERE id=:niv_super");
        $cmd->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $cmd->bindValue(":niv_super", $niv_super, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function altualizaOrdemMenuAcessoMenor($ordem_super, $ordem_niv_atual)
    {
        $cmd = $this->pdo->prepare("UPDATE adms_menus SET ordem=:ordem_super, modified=NOW() 
        WHERE id=:ordem_niv_atual");
        $cmd->bindValue(":ordem_super", $ordem_super, PDO::PARAM_INT);
        $cmd->bindValue(":ordem_niv_atual", $ordem_niv_atual, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function paginacaoUsuarioSuper($inicio, $qnt_result_pg)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT user.id, user.nome, user.email,
        nivac.nome nome_nivac
        FROM adms_usuarios user
        INNER JOIN adms_niveis_acessos nivac ON nivac.id=user.adms_niveis_acesso_id
        ORDER BY user.id 
        LIMIT :inicio, :qnt_result_pg");
        $cmd->bindValue(":inicio", $inicio, PDO::PARAM_INT);
        $cmd->bindParam(":qnt_result_pg", $qnt_result_pg, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function paginacaoUsuario($inicio, $qnt_result_pg, $ordem)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT user.id, user.nome, user.email,
        nivac.nome nome_nivac, nivac.ordem
        FROM adms_usuarios user
        INNER JOIN adms_niveis_acessos nivac ON nivac.id=user.adms_niveis_acesso_id
        WHERE nivac.ordem >= :ordem
        ORDER BY user.id 
        LIMIT :inicio, :qnt_result_pg");
        $cmd->bindValue(":inicio", $inicio, PDO::PARAM_INT);
        $cmd->bindParam(":qnt_result_pg", $qnt_result_pg, PDO::PARAM_INT);
        $cmd->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function paginacaoSeletorUsuario()
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT COUNT(id) AS num_result FROM adms_usuarios");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function BuscarNiveisAcessosCadastrados()
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT id, nome FROM  adms_niveis_acessos WHERE ordem >= 2 ORDER BY nome ASC");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function BuscarSituacaoUsuarioCadastrado()
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT id, nome FROM  adms_sits_usuarios ORDER BY nome ASC");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public function validarCadUsuarioDuplicado($email, $usuario)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id FROM adms_usuarios 
        WHERE email=:email OR usuario=:usuario ");
        $cmd->bindValue(":email", $email, PDO::PARAM_STR);
        $cmd->bindValue(":usuario", $usuario, PDO::PARAM_STR);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function cadastrarUsuario(
        $nome,
        $email,
        $usuario,
        $senha,
        $valor_foto,
        $adms_niveis_acesso_id,
        $adms_sits_usuario_id
    ) {

        $cmd = $this->pdo->prepare("INSERT INTO  adms_usuarios 
        (nome, email, usuario, senha, imagem, adms_niveis_acesso_id, adms_sits_usuario_id, created )

        VALUES (:nome, :email, :usuario, :senha, :imagem, :adms_niveis_acesso_id, :adms_sits_usuario_id, NOW()) ");

        $cmd->bindValue(":nome", $nome, PDO::PARAM_STR);
        $cmd->bindValue(":email", $email, PDO::PARAM_STR);
        $cmd->bindValue(":usuario", $usuario, PDO::PARAM_STR);
        $cmd->bindValue(":imagem", $valor_foto, PDO::PARAM_STR);
        $cmd->bindValue(":senha", $senha, PDO::PARAM_STR);
        $cmd->bindValue(":adms_niveis_acesso_id", $adms_niveis_acesso_id, PDO::PARAM_INT);
        $cmd->bindValue(":adms_sits_usuario_id", $adms_sits_usuario_id, PDO::PARAM_INT);
        $cmd->execute();
        //apos a execucao da query passei o parametro para pegar o valor do ultimo id cadastrado
        // e retornei ele mesmo
        $cmd = $this->pdo->lastInsertId();
        return $cmd;
    }


    public function pesquisarUsuariosCadastradosSuper($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT user.* ,
        sit.nome nome_sit,
        cors.cor cor_cores,
        nivac.nome nome_nivac
        FROM adms_usuarios user
        INNER JOIN adms_sits_usuarios sit ON sit.id=user.adms_sits_usuario_id
        INNER JOIN adms_cors cors	ON cors.id=sit.adms_cor_id
        INNER JOIN adms_niveis_acessos nivac ON nivac.id=user.adms_niveis_acesso_id
        WHERE user.id=:id LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function pesquisarUsuariosCadastrados($id, $ordem)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT user.* ,
        sit.nome nome_sit,
        cors.cor cor_cores,
        nivac.nome nome_nivac
        FROM adms_usuarios user
        INNER JOIN adms_sits_usuarios sit ON sit.id=user.adms_sits_usuario_id
        INNER JOIN adms_cors cors	ON cors.id=sit.adms_cor_id
        INNER JOIN adms_niveis_acessos nivac ON nivac.id=user.adms_niveis_acesso_id
        WHERE user.id=:id  AND nivac.ordem > :ordem  LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->bindValue(":ordem", $ordem, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function VerificarUsuariosCadastradosNoBanco($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT * FROM adms_usuarios WHERE id=:id LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function validarCadUsuarioDuplicadoNoEditar($email, $usuario, $dados_validos_id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id FROM adms_usuarios 
        WHERE email=:email OR usuario=:usuario AND id <> :id  ");
        $cmd->bindValue(":email", $email, PDO::PARAM_STR);
        $cmd->bindValue(":usuario", $usuario, PDO::PARAM_STR);
        $cmd->bindValue(":id", $dados_validos_id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function buscarProdutos($inicio,  $qnt_result_pg){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT produtos.*,
        unidade.nome 
        FROM adms_produtos produtos 
        INNER JOIN adms_unidades unidade ON unidade.id=adms_unidade_id
        ORDER BY produtos.id ASC LIMIT :inicio, :qnt_result_pg ");
        $cmd->bindValue(":inicio", $inicio, PDO::PARAM_INT);
        $cmd->bindValue(":qnt_result_pg", $qnt_result_pg, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function listarUnidades()
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT id, nome FROM  adms_unidades ORDER BY nome ASC");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public function CadastrarProduto(
        $descricao,
        $adms_unidade_id,
        $estoque,
        $preco_custo,
        $preco_venda,
        $adms_sit_id
        )
        {
        $cmd = $this->pdo->prepare("INSERT INTO adms_produtos
        (
            descricao,
            adms_unidade_id,
            estoque,
            preco_custo,
            preco_venda,
            adms_sit_id,
            created
            )
            VALUES 
            ( 
                :descricao,
                :adms_unidade_id,
                :estoque,
                :preco_custo,
                :preco_venda,
                :adms_sit_id,
                NOW()
            )
             ");
        $cmd->bindValue(":descricao", $descricao, PDO::PARAM_STR);
        $cmd->bindValue(":adms_unidade_id", $adms_unidade_id, PDO::PARAM_INT);
        $cmd->bindValue(":estoque", $estoque);
        $cmd->bindValue(":preco_custo", $preco_custo);
        $cmd->bindValue(":preco_venda", $preco_venda);
        $cmd->bindValue(":adms_sit_id", $adms_sit_id, PDO::PARAM_INT);
        $cmd->execute();
        return true;     
    }

    
/**
 * public function VerificarProdutosCadastrados($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT produto.*, 
        unidade.id id_un,  
        unidade.nome
        FROM adms_produtos produto
        INNER JOIN adms_unidades unidade ON unidade.id=produto.adms_unidade_id
        WHERE produto.id=:id LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
 */
    public function VerificarProdutosCadastrados($id){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT * FROM adms_produtos WHERE id=:id LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function EditarProduto(
        $descricao, 
        $estoque, 
        $adms_unidade_id, 
        $preco_custo, 
        $preco_venda, 
        $adms_sit_id, 
        $id

        ) { 

        $cmd = $this->pdo->prepare("UPDATE adms_produtos SET 
        descricao=:descricao, 
        estoque=:estoque, 
        adms_unidade_id=:adms_unidade_id, 
        preco_custo=:preco_custo,
        preco_venda=:preco_venda,
        adms_sit_id=:adms_sit_id,
        modified=NOW() WHERE id=:id");

        $cmd->bindValue(":descricao", $descricao);
        $cmd->bindValue(":estoque", $estoque);
        $cmd->bindValue(":adms_unidade_id", $adms_unidade_id);
        $cmd->bindValue(":preco_custo", $preco_custo);
        $cmd->bindValue(":preco_venda", $preco_venda);
        $cmd->bindValue(":adms_sit_id", $adms_sit_id);
        $cmd->bindValue(":id", $id);
        $cmd->execute();
        return true;
    }

    public function verificarProdutoRelacionado($id)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id FROM adms_movestoque_itens WHERE adms_produto_id=:id LIMIT 1 ");
        $cmd->bindValue("id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function deletarProduto($id)
    {
        $cmd = $this->pdo->prepare("DELETE FROM adms_produtos WHERE id=:id");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function listarTiposOperacoes()
    {
        $result = array();
        $cmd = $this->pdo->query("SELECT id, nome FROM adms_tipos_operacoes ORDER BY nome ASC");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function listarUsuariosOp(){
        $result = array();
        $cmd = $this->pdo->query("SELECT * FROM adms_usuarios");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

   public function listarProdutosOperacao(){
       $result = array();
       $cmd = $this->pdo->query("SELECT * FROM adms_produtos");
       $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
       return $result;
   }


   //verificar se existe o item na operação
   public function VerificarItem($adms_operacao_id, $adms_produto_id){
       $result = array();
       $cmd = $this->pdo->prepare("SELECT quantidade FROM adms_movestoque_itens WHERE adms_operacao_id=:adms_operacao_id AND adms_produto_id=:adms_produto_id");
       $cmd->bindValue(":adms_operacao_id", $adms_operacao_id, PDO::PARAM_INT);
       $cmd->bindValue(":adms_produto_id", $adms_produto_id, PDO::PARAM_INT);
       $cmd->execute();
       $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
       return $result;
   }

   
    //Atualizar Caso ja tenha o produto na tabela itens.
    public function AtualizarQuantidade($quantidade_total, $adms_operacao_id, $adms_produto_id ){
        $cmd = $this->pdo->prepare("UPDATE adms_movestoque_itens SET 
        quantidade=:quantidade,
        modified=NOW() WHERE adms_operacao_id=:adms_operacao_id 
        AND adms_produto_id=:adms_produto_id");
        $cmd->bindValue(":quantidade", $quantidade_total);
        $cmd->bindValue(":adms_operacao_id", $adms_operacao_id, PDO::PARAM_INT);
        $cmd->bindValue(":adms_produto_id", $adms_produto_id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

   //CADASTRAR Itens da Operacao
   public function cadastrarItensOperacao(

   $adms_operacao_id, 
   $adms_produto_id, 
   $quantidade, 
   $adms_unidade_id)

   {
       $cmd = $this->pdo->prepare("INSERT INTO adms_movestoque_itens (
           adms_operacao_id, 
           adms_produto_id, 
           quantidade, 
           adms_unidade_id, 
           created)
            
       VALUES (:adms_operacao_id, 
                :adms_produto_id, 
                :quantidade, 
                :adms_unidade_id,   
                NOW()) ");

       $cmd->bindValue(":adms_operacao_id", $adms_operacao_id, PDO::PARAM_STR);
       $cmd->bindValue(":adms_produto_id", $adms_produto_id, PDO::PARAM_INT);
       $cmd->bindValue(":quantidade", $quantidade, PDO::PARAM_INT);
       $cmd->bindValue(":adms_unidade_id", $adms_unidade_id, PDO::PARAM_INT);
       $cmd->execute();
       return true;
   }

   public function OperacaoID(){
       $cmd = $this->pdo->prepare("SELECT * FROM adms_operacao ORDER BY id DESC LIMIT 1");
       $cmd->execute();
       $cmd = $cmd->fetchAll(PDO::FETCH_ASSOC);
       return $cmd;

   }

   public function BuscarItensOperacao($adms_operacao_id){
       $result = array();
       $cmd = $this->pdo->prepare("SELECT mov_itens.adms_produto_id prod_id, 
       prod.descricao prod_desc, 
       unidade.nome un_nome, 
       mov_itens.quantidade mov_quantidade
       FROM adms_movestoque_itens mov_itens
       INNER JOIN adms_produtos prod ON prod.id=mov_itens.adms_produto_id
       INNER JOIN adms_unidades unidade ON unidade.id=mov_itens.adms_unidade_id
       WHERE mov_itens.adms_operacao_id=:adms_operacao_id");
       $cmd->bindValue(":adms_operacao_id", $adms_operacao_id, PDO::PARAM_INT);
       $cmd->execute();
       $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
       return $result;
       
       
   }

   


//Cadastrar Operacao de estoque
public function cadastrarOperacaoEstoque(
    $adms_operacao_id,
    $adms_usuario_id,
    $adms_tipo_operacao_id,
    $obs)

{
     $cmd = $this->pdo->prepare("INSERT INTO adms_operacoes_estoque (
         adms_operacao_id, 
         adms_usuario_id, 
         adms_tipo_operacao_id, 
         obs,
         created) 

     VALUES (:adms_operacao_id,
             :adms_usuario_id,
             :adms_tipo_operacao_id,
             :obs,
             NOW()) ");
     $cmd->bindValue(":adms_operacao_id", $adms_operacao_id, PDO::PARAM_INT );
     $cmd->bindValue(":adms_usuario_id", $adms_usuario_id, PDO::PARAM_INT );
     $cmd->bindValue(":adms_tipo_operacao_id", $adms_tipo_operacao_id, PDO::PARAM_INT );
     $cmd->bindValue(":obs", $obs, PDO::PARAM_STR );
     $cmd->execute();
     return true;

}

//FINALIZAR OPERACAO DE ESTOQUE
public function OperacaoInsertId($id){
    $cmd = $this->pdo->prepare("INSERT INTO adms_operacao (id, created) VALUES (:id, NOW()) ");
    $cmd->bindValue(":id", $id, PDO::PARAM_INT);
    $cmd->execute();
    return true;

}

//Deletar item da operação de estoque pegando o id do produto e o codigo da operacao de estoque no item setado
public function deletarItem($id, $adms_operacao_id)
    {
        $cmd = $this->pdo->prepare("DELETE FROM 
        adms_movestoque_itens 
        WHERE adms_produto_id=:id 
        AND adms_operacao_id=:adms_operacao_id 
        ORDER BY adms_operacao_id DESC LIMIT 1");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->bindValue(":adms_operacao_id", $adms_operacao_id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

    public function BuscarItensOperacaoParaAtualizarEstoque($adms_operacao_id){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT 
        itens.adms_operacao_id, 
        itens.adms_produto_id, 
        itens.quantidade,
        op_estoque.adms_tipo_operacao_id 
        FROM adms_movestoque_itens itens
        INNER JOIN adms_operacoes_estoque op_estoque ON op_estoque.adms_operacao_id=itens.adms_operacao_id
        WHERE itens.adms_operacao_id=:adms_operacao_id");
        $cmd->bindValue(":adms_operacao_id", $adms_operacao_id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function EstoqueAtual($adms_operacao_id){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT produtos.id, produtos.estoque 
        FROM adms_produtos produtos
        INNER JOIN adms_movestoque_itens itens ON itens.adms_produto_id=produtos.id
        WHERE produtos.id=itens.adms_produto_id AND itens.adms_operacao_id=:adms_operacao_id");
        $cmd->bindValue(":adms_operacao_id", $adms_operacao_id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function AtualizarEstoqueOperacao($estoque, $adms_produto_id){
        $cmd = $this->pdo->prepare("UPDATE adms_produtos 
        SET estoque=:estoque 
        WHERE id=:adms_produto_id");
        $cmd->bindValue(":estoque", $estoque);
        $cmd->bindValue(":adms_produto_id", $adms_produto_id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return true;

    }

    //Listar Operações de Estoque
    public function ListarOperacaoEstoque(){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT op_estoque.adms_operacao_id, 
        op_estoque.obs, 
        tp_operacao.nome tp_nome, 
        usuario.nome, 
        op_estoque.created 
        FROM adms_operacoes_estoque op_estoque
        INNER JOIN adms_usuarios usuario ON usuario.id=op_estoque.adms_usuario_id
        INNER JOIN adms_tipos_operacoes tp_operacao ON tp_operacao.id=op_estoque.adms_tipo_operacao_id");
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //vISUALIZAR OPERACAO DE ESTOQUE
    public function VisualizarOperacaoEstoque($id){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT op_estoque.id, op_estoque.adms_operacao_id, 
        op_estoque.obs, 
        tp_operacao.nome tp_nome, 
        usuario.nome, 
        op_estoque.created 
        FROM adms_operacoes_estoque op_estoque
        INNER JOIN adms_usuarios usuario ON usuario.id=op_estoque.adms_usuario_id
        INNER JOIN adms_tipos_operacoes tp_operacao ON tp_operacao.id=op_estoque.adms_tipo_operacao_id
        WHERE op_estoque.adms_operacao_id=:id ");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //Apagar Operação de estoque


    public function ApagarOperacaoEstoque($id){
        $cmd = $this->pdo->prepare("DELETE FROM adms_operacoes_estoque WHERE adms_operacao_id=:id");
        $cmd->bindValue(":id", $id, PDO::PARAM_INT);
        $cmd->execute();
        return true;
    }

   //Apagar Itens
   public function ApagarItensOperação($id){
       $cmd = $this->pdo->prepare("DELETE FROM adms_movestoque_itens WHERE adms_operacao_id=:id");
       $cmd->bindValue(":id", $id, PDO::PARAM_INT);
       $cmd->execute();
       return true;

   }

   //Deash Boardes
   public function NumeroDeUsuarios(){
       $result = array();
       $cmd = $this->pdo->prepare("SELECT count(id) FROM adms_usuarios");
       $cmd->execute();
       $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
       return $result;

   }
   public function NumeroDeProdutosCadastrados(){
    $result = array();
    $cmd = $this->pdo->prepare("SELECT count(id) FROM adms_produtos");
    $cmd->execute();
    $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
    return $result;

}

public function NumeroDeOperacoesEstoque(){
    $result = array();
    $cmd = $this->pdo->prepare("SELECT count(id) FROM adms_operacoes_estoque");
    $cmd->execute();
    $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}
   
    
}


