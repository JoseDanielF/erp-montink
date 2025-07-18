<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Carrinho extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('cart');
        $this->load->library('session');
        $this->load->model(['produto_model', 'cupom_model']);
    }

    public function index()
    {
        $cart_contents = $this->cart->contents();
        $data['itens_carrinho'] = [];

        foreach ($cart_contents as $item) {
            $produto_info = $this->produto_model->getDetalhesProduto($item['id']);
            $item['stock'] = isset($produto_info['quantidade']) ? $produto_info['quantidade'] : 0;
            $data['itens_carrinho'][] = $item;
        }

        $subtotal = $this->cart->total();

        $data['frete'] = 20.00;
        if ($subtotal >= 52.00 && $subtotal <= 166.59) {
            $data['frete'] = 15.00;
        } elseif ($subtotal > 200.00) {
            $data['frete'] = 0.00;
        }

        $data['cupom_desconto'] = $this->session->userdata('cupom_desconto') ? $this->session->userdata('cupom_desconto') : 0;

        $this->load->view('templates/header', $data);
        $this->load->view('carrinho/index', $data);
        $this->load->view('templates/footer');
    }

    public function add($variacao_id)
    {
        $produto = $this->produto_model->getDetalhesProduto($variacao_id);

        if ($produto && $produto['quantidade'] > 0) {
            $data = array(
                'id'      => $produto['variacao_id'],
                'qty'     => 1,
                'price'   => $produto['preco_base'] + $produto['preco_adicional'],
                'name'    => $produto['produto_nome'],
                'options' => ['variacao_nome' => $produto['variacao_nome']]
            );
            $this->cart->insert($data);
            $this->updateCarrinhoContador();
        }
        redirect('produtos');
    }

    public function update()
    {
        $cart_data = $this->input->post('cart');
        $update_data = [];

        foreach ($cart_data as $rowid => $item_data) {
            $item_in_cart = $this->cart->get_item($rowid);
            if (!$item_in_cart) continue;

            $stock = $this->produto_model->getEstoquePorIDVariacao($item_in_cart['id']);
            $new_qty = (int)$item_data['qty'];

            if ($new_qty > $stock) {
                $new_qty = $stock;
                $this->session->set_flashdata('compra_error', "A quantidade do produto '{$item_in_cart['name']}' foi ajustada para o máximo em estoque ({$stock}).");
            }

            if ($new_qty < 1) {
                $new_qty = 1;
            }

            $update_data[] = [
                'rowid' => $rowid,
                'qty' => $new_qty
            ];
        }

        if (!empty($update_data)) {
            $this->cart->update($update_data);
        }

        redirect('carrinho');
    }

    public function remove($rowid)
    {
        $this->cart->remove($rowid);
        redirect('carrinho');
    }

    public function aplicarCupom()
    {
        $codigo = strtoupper($this->input->post('codigo_cupom'));
        $subtotal = $this->cart->total();
        $cupom = $this->cupom_model->getCupomByCodigo($codigo);

        $this->session->unset_userdata(['cupom_id', 'cupom_desconto']);

        if ($cupom) {
            if ($subtotal >= $cupom['valor_minimo_pedido']) {
                $desconto = 0;
                if ($cupom['tipo_desconto'] == 'percentual') {
                    $desconto = ($subtotal * $cupom['valor_desconto']) / 100;
                } else {
                    $desconto = $cupom['valor_desconto'];
                }
                $this->session->set_userdata('cupom_id', $cupom['id']);
                $this->session->set_userdata('cupom_desconto', $desconto);
                $this->session->set_flashdata('cupom_success', 'Cupom aplicado com sucesso!');
            } else {
                $this->session->set_flashdata('cupom_error', 'O valor mínimo para este cupom é de R$ ' . number_format($cupom['valor_minimo_pedido'], 2, ',', '.'));
            }
        } else {
            $this->session->set_flashdata('cupom_error', 'Cupom inválido ou expirado.');
        }
        redirect('carrinho');
    }

    private function updateCarrinhoContador()
    {
        $this->session->set_userdata('cart_count', $this->cart->total_items());
    }

    public function consultarCep($cep)
    {
        header('Content-Type: application/json');

        $cep = preg_replace("/[^0-9]/", "", $cep);
        $url = "https://viacep.com.br/ws/{$cep}/json/";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        echo $response;
    }
}
