<h1 class="mb-4">Histórico de Pedidos</h1>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th>ID do Pedido</th>
                <th>Cliente</th>
                <th>E-mail</th>
                <th>Valor Total</th>
                <th>Status</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($pedidos)): ?>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td>#<?php echo $pedido['id']; ?></td>
                        <td><?php echo html_escape($pedido['cliente_nome']); ?></td>
                        <td><?php echo html_escape($pedido['cliente_email']); ?></td>
                        <td>R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></td>
                        <td>
                            <?php
                            $status_class = 'badge-info';
                            if ($pedido['status'] === 'Entregue') {
                                $status_class = 'badge-success';
                            }
                            ?>
                            <span class="badge <?php echo $status_class; ?>"><?php echo html_escape($pedido['status']); ?></span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($pedido['criado_em'])); ?></td>
                        <td>
                            <a href="<?php echo site_url('pedidos/detalhes/' . $pedido['id']); ?>" class="btn btn-sm btn-primary">Detalhes</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Nenhum pedido encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
