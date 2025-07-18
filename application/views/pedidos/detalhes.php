<a href="<?php echo site_url('pedidos/listar'); ?>" class="btn btn-secondary mb-3">‹ Voltar para a Lista</a>

<h1 class="mb-4">Detalhes do Pedido #<?php echo $pedido['id']; ?></h1>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Informações do Cliente
            </div>
            <div class="card-body">
                <p><strong>Nome:</strong> <?php echo html_escape($pedido['cliente_nome']); ?></p>
                <p><strong>E-mail:</strong> <?php echo html_escape($pedido['cliente_email']); ?></p>
                <hr>
                <p><strong>Endereço de Entrega:</strong></p>
                <p class="mb-0"><?php echo html_escape($pedido['endereco']); ?></p>
                <p><strong>CEP:</strong> <?php echo html_escape($pedido['cep']); ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Resumo Financeiro
            </div>
            <div class="card-body">
                <p><strong>Subtotal:</strong> R$ <?php echo number_format($pedido['subtotal'], 2, ',', '.'); ?></p>
                <p><strong>Frete:</strong> R$ <?php echo number_format($pedido['valor_frete'], 2, ',', '.'); ?></p>
                <p class="text-success"><strong>Desconto:</strong> - R$ <?php echo number_format($pedido['valor_desconto'], 2, ',', '.'); ?></p>
                <hr>
                <h5 class="font-weight-bold">Total: R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></h5>
                <hr>
                <p><strong>Status do Pedido:</strong> <span class="badge badge-primary" style="font-size: 1rem;"><?php echo html_escape($pedido['status']); ?></span></p>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        Itens do Pedido
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead class="thead-light">
                    <tr>
                        <th>Produto</th>
                        <th>Variação</th>
                        <th class="text-center">Quantidade</th>
                        <th class="text-right">Preço Unitário</th>
                        <th class="text-right">Total do Item</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens as $item): ?>
                        <tr>
                            <td><?php echo html_escape($item['produto_nome']); ?></td>
                            <td><?php echo html_escape($item['variacao_nome']); ?></td>
                            <td class="text-center"><?php echo $item['quantidade']; ?></td>
                            <td class="text-right">R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></td>
                            <td class="text-right">R$ <?php echo number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
