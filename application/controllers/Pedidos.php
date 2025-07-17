<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pedidos extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pedido_model');
        $this->load->library('cart');
        $this->load->library('session');
    }

    public function finalizar()
    {
        $subtotal = $this->cart->total();
        $frete = 0;
        if ($subtotal >= 52.00 && $subtotal <= 166.59) {
            $frete = 15.00;
        } elseif ($subtotal <= 200.00) {
            $frete = 20.00;
        }
        $desconto = ($this->session->userdata('cupom_desconto') !== false) ? $this->session->userdata('cupom_desconto') : 0;
        $total = $subtotal - $desconto + $frete;

        $dados_pedido = [
            'cliente_nome' => $this->input->post('nome_cliente'),
            'cliente_email' => $this->input->post('email_cliente'),
            'cep' => $this->input->post('cep'),
            'endereco' => $this->input->post('endereco'),
            'subtotal' => $subtotal,
            'valor_frete' => $frete,
            'valor_desconto' => $desconto,
            'cupom_id' => $this->session->userdata('cupom_id'),
            'valor_total' => $total
        ];

        $itens_pedido = [];
        foreach ($this->cart->contents() as $item) {
            $itens_pedido[] = [
                'variacao_id_ref' => $item['id'],
                'produto_nome' => $item['name'],
                'variacao_nome' => $item['options']['variacao_nome'],
                'quantidade' => $item['qty'],
                'preco_unitario' => $item['price']
            ];
        }

        $data_to_create = [
            'pedido' => $dados_pedido,
            'itens' => $itens_pedido
        ];

        $pedido_id = $this->pedido_model->create($data_to_create);

        if ($pedido_id) {
            $this->enviarEmailConfirmacao($pedido_id, $dados_pedido);
            $this->cart->destroy();
            $this->session->unset_userdata(['cupom_id', 'cupom_desconto', 'cart_count']);
            echo "<h1>Pedido #{$pedido_id} finalizado com sucesso!</h1>";
        } else {
            echo "<h1>Erro ao finalizar o pedido.</h1>";
        }
    }

    private function enviarEmailConfirmacao($pedido_id, $pedido_data)
    {
        $this->load->library('email');
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.googlemail.com';
        $config['smtp_port'] = 465;
        $config['smtp_user'] = '';
        $config['smtp_pass'] = '';
        $config['mailtype'] = 'html';
        $config['charset'] = 'utf-8';
        $this->email->initialize($config);

        $this->email->from('', 'Sua Loja');
        $this->email->to($pedido_data['cliente_email']);
        $this->email->subject('Confirmação do Pedido #' . $pedido_id);

        $mensagem = "<h1>Olá, {$pedido_data['cliente_nome']}!</h1>";
        $mensagem .= "<p>Seu pedido #{$pedido_id} foi recebido com sucesso.</p>";
        $mensagem .= "<p>Endereço de entrega: {$pedido_data['endereco']}</p>";
        $mensagem .= "<p>Valor Total: R$ " . number_format($pedido_data['valor_total'], 2, ',', '.') . "</p>";

        $this->email->message($mensagem);
        $this->email->send();
    }
}
