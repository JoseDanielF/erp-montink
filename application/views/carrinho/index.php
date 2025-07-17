<!DOCTYPE html>
<html>

<head>
    <title>Carrinho de Compras</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h1>🛒 Seu Carrinho</h1>
        <a href="<?php echo site_url('produtos'); ?>">Continuar Comprando</a>
        <hr>
        <?php if (!empty($itens_carrinho)): ?>
            <table class="table">
                <tbody>
                    <?php
                    $subtotal = 0;
                    foreach ($itens_carrinho as $item):
                        $item_total = $item['price'] * $item['qty'];
                        $subtotal += $item_total;
                    ?>
                        <tr>
                            <td><?php echo $item['name']; ?> (<?php echo $item['options']['variacao_nome']; ?>)</td>
                            <td>R$ <?php echo number_format($item['price'], 2, ',', '.'); ?></td>
                            <td>x <?php echo $item['qty']; ?></td>
                            <td>R$ <?php echo number_format($item_total, 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <hr>

            <div class="row">
                <div class="col-md-6">
                    <h4>Aplicar Cupom</h4>

                    <?php if ($this->session->flashdata('cupom_success')): ?>
                        <div class="alert alert-success"><?php echo $this->session->flashdata('cupom_success'); ?></div>
                    <?php endif; ?>
                    <?php if ($this->session->flashdata('cupom_error')): ?>
                        <div class="alert alert-danger"><?php echo $this->session->flashdata('cupom_error'); ?></div>
                    <?php endif; ?>
                    
                    <form action="<?php echo site_url('carrinho/aplicar_cupom'); ?>" method="post" class="form-inline">
                        <input type="text" name="codigo_cupom" class="form-control mb-2 mr-sm-2" placeholder="Código do cupom">
                        <button type="submit" class="btn btn-secondary mb-2">Aplicar</button>
                    </form>

                    <h4 class="mt-4">Calcular Frete e Endereço</h4>
                    <div class="form-inline">
                        <input type="text" id="cep" class="form-control mb-2 mr-sm-2" placeholder="Digite seu CEP" maxlength="9">
                        <button id="btn-cep" class="btn btn-info mb-2">Buscar</button>
                    </div>
                    <div id="endereco-info" class="mt-2" style="display:none;"></div>

                </div>
                <div class="col-md-6 text-right">
                    <p>Subtotal: <strong>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></strong></p>
                    <?php if ($cupom_desconto > 0): ?>
                        <p class="text-success">Desconto (Cupom): <strong>- R$ <?php echo number_format($cupom_desconto, 2, ',', '.'); ?></strong></p>
                    <?php endif; ?>
                    <p>Frete: <strong>R$ <?php echo number_format($frete, 2, ',', '.'); ?></strong></p>
                    <h3>Total: <strong>R$ <?php echo number_format($subtotal - $cupom_desconto + $frete, 2, ',', '.'); ?></strong></h3>

                    <form action="<?php echo site_url('pedidos/finalizar'); ?>" method="post">
                        <input type="hidden" name="nome_cliente" value="Cliente Teste">
                        <input type="hidden" id="form-cep" name="cep">
                        <input type="hidden" id="form-endereco" name="endereco">
                        <input type="hidden" name="email_cliente" value="cliente@teste.com">
                        <button type="submit" class="btn btn-lg btn-success mt-3" id="btn-finalizar" disabled>Finalizar Pedido</button>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <p>Seu carrinho está vazio.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#btn-cep').click(function() {
                var cep = $('#cep').val();
                var url = "<?php echo site_url('Carrinho/consultar_cep/'); ?>" + cep;

                $.getJSON(url, function(data) {
                    if (!("erro" in data)) {
                        var endereco = data.logradouro + ', ' + data.bairro + ' - ' + data.localidade + '/' + data.uf;
                        $('#endereco-info').html(endereco).show();
                        $('#form-cep').val(cep);
                        $('#form-endereco').val(endereco);
                        $('#btn-finalizar').prop('disabled', false);
                    } else {
                        alert('CEP não encontrado.');
                    }
                }).fail(function() {
                    alert('Não foi possível consultar o CEP. Verifique sua conexão ou tente novamente.');
                });
            });
        });
    </script>

</body>

</html>