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
    public function buscarDadosUsuarios()
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT id, nome, imagem FROM adms_usuarios 
        WHERE id=:id LIMIT 1");
        $cmd->bindValue(":id", $_SESSION['id'], PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    //essa funcao pega todos os valores da tabela adms_nivacs_pgs onde o adms_niveis_cesso_id é igual
    // ao do usuario logado tambem a coluna permissao seja igual a 1 e tambem a lib_menu tem que estar liberada .
    public function buscarBotoesMenu()
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
        $cmd->bindValue(":adms_niveis_acesso_id", $_SESSION['adms_niveis_acesso_id'], PDO::PARAM_INT);
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
    public function paginacaoNivelAcessoLimitado($inicio, $qnt_result_pg)
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT * FROM adms_niveis_acessos 
        WHERE ordem > :ordem 
        ORDER BY ordem 
        ASC LIMIT :inicio, :qnt_result_pg");
        $cmd->bindValue(":ordem", $_SESSION['ordem'], PDO::PARAM_INT);
        $cmd->bindValue(":inicio", $inicio, PDO::PARAM_INT);
        $cmd->bindParam(":qnt_result_pg", $qnt_result_pg, PDO::PARAM_INT);
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
        $cmd = $this->pdo->prepare("SELECT * FROM adms_niveis_acessos WHERE ordem > :ordem 
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

    public function maiorNumeroOrdemAdmsNivAcs()
    {
        $result = array();
        $cmd = $this->pdo->prepare("SELECT ordem FROM adms_nivacs_pgs 
        ORDER BY ordem DESC LIMIT 1 ");
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
        WHERE nivpg.adms_niveis_acesso_id=:id 
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
}
