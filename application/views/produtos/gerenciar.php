<div class="cart-info">
    <a href="<?php echo site_url('carrinho'); ?>" class="btn btn-info">
        🛒 Carrinho (<?php echo $this->session->userdata('cart_count') ? $this->session->userdata('cart_count') : 0; ?> itens)
    </a>
</div>

<h1>Gerenciar Produtos</h1>
<hr>

<h3><?php echo isset($produto) ? 'Editar Produto' : 'Cadastrar Novo Produto'; ?></h3>
<form action="<?php echo isset($produto) ? site_url('produtos/update/' . $produto['variacao_id']) : site_url('produtos/store'); ?>" method="post">
    <?php if (isset($produto)): ?>
        <input type="hidden" name="produto_id" value="<?php echo $produto['produto_id']; ?>">
    <?php endif; ?>
    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Nome do Produto</label>
            <input type="text" name="nome" class="form-control" value="<?php echo isset($produto['produto_nome']) ? $produto['produto_nome'] : ''; ?>" required>
        </div>
        <div class="form-group col-md-2">
            <label>Preço Base (R$)</label>
            <input type="text" name="preco" class="form-control" value="<?php echo isset($produto['preco_base']) ? $produto['preco_base'] : ''; ?>" required>
        </div>
        <div class="form-group col-md-3">
            <label>Nome da Variação (Ex: Cor, Tamanho)</label>
            <input type="text" name="variacao_nome" class="form-control" value="<?php echo isset($produto['variacao_nome']) ? $produto['variacao_nome'] : 'Padrão'; ?>" required>
        </div>
        <div class="form-group col-md-2">
            <label>Estoque</label>
            <input type="number" name="estoque" class="form-control" value="<?php echo isset($produto['quantidade']) ? $produto['quantidade'] : ''; ?>" required>
        </div>
    </div>
    <button type="submit" class="btn btn-primary"><?php echo isset($produto) ? 'Atualizar' : 'Salvar'; ?></button>
    <?php if (isset($produto)): ?>
        <a href="<?php echo site_url('produtos'); ?>" class="btn btn-secondary">Cancelar Edição</a>
    <?php endif; ?>
</form>

<hr>
<h3>Produtos Cadastrados</h3>
<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Produto</th>
            <th>Variação</th>
            <th>Preço Final</th>
            <th>Estoque</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($produtos as $p): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><?php echo $p['nome']; ?></td>
                <td><?php echo $p['variacao_nome']; ?></td>
                <td>R$ <?php echo number_format($p['preco_base'] + $p['preco_adicional'], 2, ',', '.'); ?></td>
                <td><?php echo $p['quantidade']; ?></td>
                <td>
                    <a href="<?php echo site_url('produtos/edit/' . $p['variacao_id']); ?>" class="btn btn-sm btn-warning">Editar</a>
                    <a href="<?php echo site_url('carrinho/add/' . $p['variacao_id']); ?>" class="btn btn-sm btn-success <?php echo $p['quantidade'] <= 0 ? 'disabled' : ''; ?>">Comprar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>