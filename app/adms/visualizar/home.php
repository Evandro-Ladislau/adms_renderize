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
                        <h2 class="display-4 titulo">Dashboard</h2>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-3 col-sm-6">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <i class="fas fa-users fa-3x"></i>
                                <h6 class="card-title">Usuarios</h6>
                                <h2 class="lead">
                                    <?php
                                     $qnt_user = $pdo->NumeroDeUsuarios() ;
                                     foreach ($qnt_user as  $value) {
                                    $qnt = $value;
                                } 
                                echo $qnt['count(id)'];
                                ?>
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card bg-info  text-white">
                            <div class="card-body">
                                <i class="fas fa-box fa-3x"></i>
                                <h6 class="card-title">Produtos</h6>
                                <h2 class="lead">
                                    <?php 
                                    $produtos = $pdo->NumeroDeProdutosCadastrados();
                                    foreach ($produtos as  $value) {
                                        $qnt_produtos = $value;
                                    }
                                    echo $qnt_produtos['count(id)'];
                                    ?>
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <i class="fas fa-truck fa-3x"></i>
                                <h6 class="card-title">Op. Concluídas</h6>
                                <h2 class="lead">
                                    <?php
                                     $operacoes = $pdo->NumeroDeOperacoesEstoque();
                                     foreach ($operacoes as  $value) {
                                         $qnt_operacoes = $value;
                                     }
                                     echo $qnt_operacoes['count(id)'];
                                    ?>
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <i class="fas fa-comments fa-3x"></i>
                                <h6 class="card-title">Op. Canceladas</h6>
                                <h2 class="lead">17</h2>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <?php

        include_once 'app/adms/include/rodape_lib.php';
        ?>
    </div>
</body>