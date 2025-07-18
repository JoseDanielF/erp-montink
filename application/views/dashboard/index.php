<div class="container mt-5">
    <div class="jumbotron">
        <h1 class="display-4">Bem-vindo ao Mini ERP!</h1>
        <p class="lead">Selecione uma das opções abaixo para começar a gerenciar sua loja.</p>
        <hr class="my-4">
        <div class="row">
            <div class="col-md-4 mb-3">
                <a class="btn btn-primary btn-lg btn-block" href="<?php echo site_url('produtos'); ?>" role="button">
                    📦 Gerenciar Produtos
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a class="btn btn-secondary btn-lg btn-block" href="<?php echo site_url('cupons'); ?>" role="button">
                    🎟️ Gerenciar Cupons
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a class="btn btn-info btn-lg btn-block" href="<?php echo site_url('pedidos/listar'); ?>" role="button">
                    📋 Listar Pedidos
                </a>
            </div>
        </div>
    </div>
</div>
