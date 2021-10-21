<?php
if(!isset($seguranca)){
    exit;
}
require_once './app/adms/models/Conexao.php';
require_once './app/adms/models/Pessoa.php';

class ValidarUsuario extends Pessoa {
    private $senha;
    private $confirmarSenha;
    private $recuperarSenha;
    private $chaveDescadastro;
    private $imagem;
    private $admsNiveisAcesso_id;
    private $admsNiveisUsuario_id;
    private $exemplo;
    private $created;
    private $modified;

    public function getSenha(){
        return $this->senha;
    }

    public function setSenha($senha){
        $this->senha = $senha;
    }

    public function getConfirmarSenha(){
        return $this->confirmarSenha;
    }

    public function setConfirmaSenha($confirmarSenha){
        $this->confirmarSenha = $confirmarSenha;
    }

    function __construct($usuario, $senha)
    {
        $this->usuario = $usuario;
        $this->senha = $senha;
    }

    
    
}