<?php
session_start(); //incializando a sessão.
ob_start(); //iniciallizando o buffer de saida.


$seguranca = true; //essa variável de segurança permite que as outras paginas sejam abertas somente se ela for inicializada.

//conexao com banco de dados requerindo o arquivo de conexao
require_once '../adm/app/adms/models/Conexao.php';
include_once './lib/lib_valida.php';

$pdo = new Conexao("adms","localhost","root","root");

//limpando o valor da url e atribuindo a variavel
$url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_STRING);
//passando a url na função limpaurl para tirar espaços em branco e caracteres especiais.
$url_limpa = limparUrl($url);

$result_paginas = $pdo->paginasCadastradas($url_limpa);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Administravido - Renderize</title>   
</head>
<body>
    <?php
        //se existir o arquivo no caminho informado ele inclui no body.
        if($result_paginas){

            //neste caso eu percorri o array matriz e mandei ele trazer o cadastro da pagina na posição do $i/nome da coluna
            for ($i=0; $i < count($result_paginas) ; $i++) { 
                $file = 'app/'.$result_paginas[$i]['tp_pagina'].'/'.$result_paginas[$i]['endereco'].'.php';
            }
           

            if(file_exists($file)){
                include $file;
    
            }else{
                include 'app/adms/visualizar/home.php';
            }
        }else{

            include 'app/adms/visualizar/home.php';
        }
       
    ?>
</body>
</html>