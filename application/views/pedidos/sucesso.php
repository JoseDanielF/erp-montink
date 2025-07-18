<div class="container mt-5 text-center">
    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading">Pedido Realizado!</h4>
        <p><?php echo $this->session->flashdata('success_message'); ?></p>
        <hr>
        <p class="mb-0">Agradecemos a sua preferência. Você receberá um e-mail com os detalhes da sua compra em breve.</p>
    </div>
    <a href="<?php echo site_url('produtos'); ?>" class="btn btn-primary mt-3">Continuar Comprando</a>
</div>
