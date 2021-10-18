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
    // e o adms_niveis_acesso_id chave estrageira da tabela adms_niveis_acessos tem que ser igual ao adms_niveis_acesso_id
    // que esta cadastrado no usuario.
    
    public function paginasCadastradas($url){
        $result = array();
        
        if (isset($_SESSION['adms_niveis_acesso_id'])) {
            $adms_niveis_acesso_id = $_SESSION['adms_niveis_acesso_id'];
        }else{
            $adms_niveis_acesso_id = 0;
        }

        $cmd = $this->pdo->prepare("SELECT pg.tp_pagina, pg.endereco 
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

    public function validarLogin(){
        $result = array();
        $cmd = $this->pdo->query("SELECT id, nome, email, senha , adms_niveis_acesso_id FROM adms_usuarios  WHERE usuario=usuario LIMIT 1");
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function buscarOrdemNivelAcesso($adms_niv_ac_id){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT ordem FROM adms_niveis_acessos WHERE id=:adms_niv_ac_id LIMIT 1");
        $cmd->bindParam(":adms_niv_ac_id", $adms_niv_ac_id);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
}