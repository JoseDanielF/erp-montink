<?php
$old_input = $this->session->flashdata('old_input');
?>

<h1>🛒 Seu Carrinho</h1>
<a href="<?php echo site_url('produtos'); ?>">Continuar Comprando</a>
<hr>

<?php if (!empty($itens_carrinho)): ?>

    <form action="<?php echo site_url('carrinho/update'); ?>" method="post" id="update-cart-form">
        <div class="table-responsive">
            <table class="table">
                <thead class="thead-light">
                    <tr>
                        <th>Produto</th>
                        <th class="text-center">Preço</th>
                        <th class="text-center" style="width: 150px;">Quantidade</th>
                        <th class="text-right">Subtotal</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens_carrinho as $item): ?>
                        <tr>
                            <td>
                                <?php echo $item['name']; ?><br>
                                <small class="text-muted">(<?php echo $item['options']['variacao_nome']; ?>)</small>
                            </td>
                            <td class="text-center">R$ <?php echo number_format($item['price'], 2, ',', '.'); ?></td>
                            <td class="text-center">
                                <input type="number" name="cart[<?php echo $item['rowid']; ?>][qty]"
                                    value="<?php echo $item['qty']; ?>"
                                    min="1"
                                    max="<?php echo $item['stock']; ?>"
                                    class="form-control form-control-sm mx-auto" style="width: 80px;">
                                <small class="text-muted">Estoque: <?php echo $item['stock']; ?></small>
                            </td>
                            <td class="text-right">R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></td>
                            <td class="text-center">
                                <a href="<?php echo site_url('carrinho/remove/' . $item['rowid']); ?>" class="btn btn-sm btn-outline-danger" title="Remover Item">×</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mb-4">
            <button type="submit" class="btn btn-secondary">Atualizar Quantidades</button>
        </div>
    </form>
    <hr>

    <div class="row">
        <div class="col-lg-7">

            <form action="<?php echo site_url('carrinho/aplicarCupom'); ?>" method="post">
                <h4>Cupom de Desconto</h4>
                <div class="form-inline mb-3">
                    <input type="text" name="codigo_cupom" class="form-control mb-2 mr-sm-2" placeholder="Código do cupom">
                    <button type="submit" class="btn btn-outline-secondary mb-2">Aplicar</button>
                </div>
                <?php if ($this->session->flashdata('cupom_success')): ?>
                    <div class="alert alert-success py-2"><?php echo $this->session->flashdata('cupom_success'); ?></div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('cupom_error')): ?>
                    <div class="alert alert-danger py-2"><?php echo $this->session->flashdata('cupom_error'); ?></div>
                <?php endif; ?>
            </form>

            <form action="<?php echo site_url('pedidos/finalizar'); ?>" method="post" id="checkout-form">
                <h4 class="mt-4">Endereço de Entrega</h4>
                <div class="form-inline mb-3">
                    <input type="text" id="cep" class="form-control mb-2 mr-sm-2" placeholder="Digite seu CEP" maxlength="9">
                    <button type="button" id="btn-cep" class="btn btn-info mb-2">Buscar CEP</button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Complete seus Dados</h5>
                        <?php if ($this->session->flashdata('compra_error')): ?>
                            <div class="alert alert-danger"><?php echo $this->session->flashdata('compra_error'); ?></div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="nome_cliente">Nome Completo</label>
                                <input type="text" class="form-control" id="nome_cliente" name="nome_cliente" value="<?php echo isset($old_input['nome_cliente']) ? $old_input['nome_cliente'] : ''; ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="email_cliente">E-mail</label>
                                <input type="email" class="form-control" id="email_cliente" name="email_cliente" value="<?php echo isset($old_input['email_cliente']) ? $old_input['email_cliente'] : ''; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <input type="hidden" id="form-cep" name="cep" value="<?php echo isset($old_input['cep']) ? $old_input['cep'] : ''; ?>">
                            <div class="col-md-8 form-group">
                                <label for="logradouro">Logradouro (Rua, Av.)</label>
                                <input type="text" class="form-control" id="logradouro" name="logradouro" value="<?php echo isset($old_input['logradouro']) ? $old_input['logradouro'] : ''; ?>" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="numero">Número</label>
                                <input type="text" class="form-control" id="numero" name="numero" value="<?php echo isset($old_input['numero']) ? $old_input['numero'] : ''; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="complemento">Complemento (Opcional)</label>
                                <input type="text" class="form-control" id="complemento" name="complemento" value="<?php echo isset($old_input['complemento']) ? $old_input['complemento'] : ''; ?>">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="bairro">Bairro</label>
                                <input type="text" class="form-control" id="bairro" name="bairro" value="<?php echo isset($old_input['bairro']) ? $old_input['bairro'] : ''; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 form-group">
                                <label for="cidade">Cidade</label>
                                <input type="text" class="form-control" id="cidade" name="cidade" value="<?php echo isset($old_input['cidade']) ? $old_input['cidade'] : ''; ?>" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="uf">Estado (UF)</label>
                                <input type="text" class="form-control" id="uf" name="uf" value="<?php echo isset($old_input['uf']) ? $old_input['uf'] : ''; ?>" required maxlength="2">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Resumo do Pedido</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Subtotal
                            <span>R$ <?php echo number_format($this->cart->total(), 2, ',', '.'); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Frete
                            <span>R$ <?php echo number_format($frete, 2, ',', '.'); ?></span>
                        </li>
                        <?php if (isset($cupom_desconto) && $cupom_desconto > 0): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center text-success">
                                Desconto (Cupom)
                                <span>- R$ <?php echo number_format($cupom_desconto, 2, ',', '.'); ?></span>
                            </li>
                        <?php endif; ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center font-weight-bold">
                            <h4>Total</h4>
                            <h4>R$ <?php echo number_format($this->cart->total() - (isset($cupom_desconto) ? $cupom_desconto : 0) + $frete, 2, ',', '.'); ?></h4>
                        </li>
                    </ul>
                    <button type="submit" form="checkout-form" class="btn btn-lg btn-success btn-block mt-3" id="btn-finalizar">Finalizar Pedido</button>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="alert alert-info">Seu carrinho está vazio.</div>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function() {
        $('#update-cart-form').submit(function() {
            var updateForm = $(this);
            updateForm.find('input[type="hidden"][data-from-checkout]').remove();

            updateForm.append($('<input>', {
                type: 'hidden',
                name: 'cep_display',
                value: $('#cep').val(),
                'data-from-checkout': 'true'
            }));

            $('#checkout-form').find('input[name]').each(function() {
                updateForm.append(
                    $('<input>', {
                        type: 'hidden',
                        name: $(this).attr('name'),
                        value: $(this).val(),
                        'data-from-checkout': 'true'
                    })
                );
            });
        });

        function preencherEndereco(data) {
            $('#logradouro').val(data.logradouro);
            $('#bairro').val(data.bairro);
            $('#cidade').val(data.localidade);
            $('#uf').val(data.uf);
            $('#numero').focus();
        }

        $('#btn-cep').click(function() {
            var cep = $('#cep').val().replace(/\D/g, '');
            if (cep.length !== 8) {
                alert('Por favor, digite um CEP válido com 8 dígitos.');
                return;
            }
            $('#form-cep').val(cep);

            var url = "<?php echo site_url('carrinho/consultarCep/'); ?>" + cep;
            $.getJSON(url, function(data) {
                if (!("erro" in data)) {
                    preencherEndereco(data);
                } else {
                    alert('CEP não encontrado. Por favor, preencha o endereço manualmente.');
                }
                $('#btn-finalizar').prop('disabled', false);
            }).fail(function() {
                alert('Não foi possível consultar o CEP. Preencha o endereço manualmente.');
                $('#btn-finalizar').prop('disabled', false);
            });
        });
    });
</script>