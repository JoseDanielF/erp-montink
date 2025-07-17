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
        $data['itens_carrinho'] = $this->cart->contents();

        $subtotal = $this->cart->total();

        if ($subtotal >= 52.00 && $subtotal <= 166.59) {
            $data['frete'] = 15.00;
        } elseif ($subtotal > 200.00) {
            $data['frete'] = 0.00;
        } else {
            $data['frete'] = 20.00;
        }

        $data['cupom_desconto'] = $this->session->userdata('cupom_desconto') ? $this->session->userdata('cupom_desconto') : 0;

        $this->load->view('carrinho/index', $data);
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
            $this->update_cart_count();
        }
        redirect('produtos');
    }

    public function aplicar_cupom()
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

    private function update_cart_count()
    {
        $this->session->set_userdata('cart_count', $this->cart->total_items());
    }

    public function consultar_cep($cep)
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
