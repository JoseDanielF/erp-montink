<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' | ERP Montink' : 'ERP Montink'; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style> body { background-color: #f4f7f6; } </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?php echo site_url('dashboard'); ?>">🚀 ERP Montink</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo site_url('produtos'); ?>">Produtos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo site_url('cupons'); ?>">Cupons</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo site_url('pedidos/listar'); ?>">Pedidos</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo site_url('carrinho'); ?>">
                        🛒 Carrinho (<?php echo $this->cart->total_items() ? $this->cart->total_items() : 0; ?>)
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>


<main role="main" class="container py-4">