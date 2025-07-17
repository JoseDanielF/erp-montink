<!DOCTYPE html>
<html>

<head>
    <title>Gerenciar Cupons de Desconto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Gerenciar Cupons</h1>
        <a href="<?php echo site_url('produtos'); ?>">Voltar para Produtos</a>
        <hr>

        <h3>Criar Novo Cupom</h3>
        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
        <form action="<?php echo site_url('cupons/store'); ?>" method="post" class="p-3 mb-4" style="background-color: #f8f9fa; border-radius: 5px;">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Código do Cupom</label>
                    <input type="text" name="codigo" class="form-control" value="<?php echo set_value('codigo'); ?>" required>
                </div>
                <div class="form-group col-md-2">
                    <label>Tipo de Desconto</label>
                    <select name="tipo_desconto" class="form-control">
                        <option value="fixo">Fixo (R$)</option>
                        <option value="percentual">Percentual (%)</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Valor do Desconto</label>
                    <input type="text" name="valor_desconto" class="form-control" value="<?php echo set_value('valor_desconto'); ?>" required>
                </div>
                <div class="form-group col-md-2">
                    <label>Valor Mínimo do Pedido (R$)</label>
                    <input type="text" name="valor_minimo_pedido" class="form-control" placeholder="0.00">
                </div>
                <div class="form-group col-md-2">
                    <label>Data de Validade</label>
                    <input type="date" name="data_validade" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Cupom</button>
        </form>

        <hr>
        <h3>Cupons Cadastrados</h3>
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Valor Mínimo</th>
                    <th>Validade</th>
                    <th>Status</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cupons as $cupom): ?>
                    <tr>
                        <td><?php echo $cupom['codigo']; ?></td>
                        <td><?php echo ucfirst($cupom['tipo_desconto']); ?></td>
                        <td><?php echo ($cupom['tipo_desconto'] == 'fixo') ? 'R$ ' . $cupom['valor_desconto'] : $cupom['valor_desconto'] . ' %'; ?></td>
                        <td>R$ <?php echo number_format($cupom['valor_minimo_pedido'], 2, ',', '.'); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($cupom['data_validade'])); ?></td>
                        <td>
                            <?php if (strtotime($cupom['data_validade']) < time()): ?>
                                <span class="badge badge-secondary">Expirado</span>
                            <?php elseif ($cupom['ativo']): ?>
                                <span class="badge badge-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo site_url('cupons/altera_status_cupom/' . $cupom['id']); ?>" class="btn btn-sm <?php echo $cupom['ativo'] ? 'btn-warning' : 'btn-info'; ?>">
                                <?php echo $cupom['ativo'] ? 'Desativar' : 'Ativar'; ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>