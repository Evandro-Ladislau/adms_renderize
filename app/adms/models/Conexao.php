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

    //Essa função busca as paginas cadastradas no banco em que o endereco cadastrado seja igual ao passado pela url
    public function paginasCadastradas($url){
        $result = array();
        $cmd = $this->pdo->prepare("SELECT * FROM adms_paginas WHERE endereco=:endereco AND adms_sits_pg_id=1 LIMIT 1");
        $cmd->bindValue(":endereco", $url, PDO::PARAM_STR);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    
}