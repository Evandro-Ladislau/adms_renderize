<?php
if(!isset($seguranca)){
    exit;
}
//classe de conexão com o banco de dados.
class Conexao {
    private $pdo; // atributo privado só é usado dentro da classe.

    //CONEXÃO COM BANCO DE DADOS

    function __construct($dbname, $host, $user, $senha)
    {

        try {
            $this->pdo = new PDO("mysql:dbname=".$dbname.";host=".$host,$user,$senha);
        } catch (PDOException $e) {
            echo "Erro na conexão com banco de dados:".$e->getMessage();
            exit();

        }catch(Exception $e){
            echo "Erro generico".$e->getMessage();
            exit();
        }
    }

    //Essa função busca as paginas cadastradas no banco em que o adms_pagina_id que é chave estrangeira da 
    //tabela adms_nivacs_pgs
    //seja igual ao id da tabela adms_pagina E
    //endereco cadastrado seja igual ao passado pela url
    // e o adms_niveis_acesso_id chave estrangeira da tabela adms_niveis_acessos tem que ser igual ao adms_niveis_acesso_id
    // que esta cadastrado no usuario.
    
    public function paginasCadastradas($url){
        $result = array();
        
        if (isset($_SESSION['adms_niveis_acesso_id'])) {
            $adms_niveis_acesso_id = $_SESSION['adms_niveis_acesso_id'];
        }else{
            $adms_niveis_acesso_id = 0;
        }

        $cmd = $this->pdo->prepare("SELECT pg.id, pg.tp_pagina, pg.endereco 
        FROM adms_paginas pg
        LEFT JOIN adms_nivacs_pgs nivpg ON nivpg.adms_pagina_id=pg.id
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


    //essa funcao valida o login do usuario
    public function validarLogin(){
        $result = array();
        $cmd = $this->pdo->query("SELECT id, nome, email, senha , adms_niveis_acesso_id 
        FROM adms_usuarios  WHERE usuario=usuario LIMIT 1");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //Essa função buscar a ordem do nivel de acesso do usuario
    public function buscarOrdemNivelAcesso($adms_niv_ac_id){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT ordem FROM adms_niveis_acessos 
        WHERE id=:adms_niv_ac_id LIMIT 1");
        $cmd->bindParam(":adms_niv_ac_id", $adms_niv_ac_id);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //Essa função busca os dados do usuario no banco de dados.
    public function buscarDadosUsuarios(){
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
    public function buscarBotoesMenu(){
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
    public function paginacaoNivelAcesso($inicio,$qnt_result_pg){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT * FROM adms_niveis_acessos 
        WHERE ordem >= :ordem 
        ORDER BY ordem 
        ASC LIMIT :inicio, :qnt_result_pg");
        $cmd->bindValue(":ordem",$_SESSION['ordem'], PDO::PARAM_INT);
        $cmd->bindValue(":inicio", $inicio, PDO::PARAM_INT);
        $cmd->bindParam(":qnt_result_pg", $qnt_result_pg, PDO::PARAM_INT);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
}