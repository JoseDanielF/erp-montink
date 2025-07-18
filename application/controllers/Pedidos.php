<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pedidos extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pedido_model');
        $this->load->model('produto_model');
        $this->load->library('cart');
        $this->load->library('session');
        $this->load->library('form_validation');
    }

    public function listar()
    {
        $data['title'] = "Lista de Pedidos";
        $data['pedidos'] = $this->pedido_model->getAllPedidos();

        $this->load->view('templates/header', $data);
        $this->load->view('pedidos/listar', $data);
        $this->load->view('templates/footer');
    }

    public function detalhes($pedido_id)
    {
        $data['title'] = "Detalhes do Pedido #" . $pedido_id;
        $data['pedido'] = $this->pedido_model->getDetalhesPedido($pedido_id);
        $data['itens'] = $this->pedido_model->getItensPedido($pedido_id);

        if (empty($data['pedido'])) {
            show_404();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('pedidos/detalhes', $data);
        $this->load->view('templates/footer');
    }


    public function finalizar()
    {
        $this->form_validation->set_rules('nome_cliente', 'Nome Completo', 'required|trim');
        $this->form_validation->set_rules('email_cliente', 'E-mail', 'required|trim|valid_email');
        $this->form_validation->set_rules('cep', 'CEP', 'required|trim');
        $this->form_validation->set_rules('logradouro', 'Logradouro', 'required|trim');
        $this->form_validation->set_rules('numero', 'Número', 'required|trim');
        $this->form_validation->set_rules('bairro', 'Bairro', 'required|trim');
        $this->form_validation->set_rules('cidade', 'Cidade', 'required|trim');
        $this->form_validation->set_rules('uf', 'Estado', 'required|trim|max_length[2]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('compra_error', validation_errors('<p class="mb-0">', '</p>'));
            redirect('carrinho');
            return;
        }

        foreach ($this->cart->contents() as $item) {
            $estoque_atual = $this->produto_model->getEstoquePorIDVariacao($item['id']);
            if ($item['qty'] > $estoque_atual) {
                $this->session->set_flashdata('compra_error', "Desculpe, o produto '{$item['name']}' não possui estoque suficiente. ({$estoque_atual} disponíveis)");
                redirect('carrinho');
                return;
            }
        }

        $endereco_completo = $this->input->post('logradouro') . ', ' . $this->input->post('numero');
        if ($this->input->post('complemento')) {
            $endereco_completo .= ' - ' . $this->input->post('complemento');
        }
        $endereco_completo .= ' - ' . $this->input->post('bairro') . ', ' . $this->input->post('cidade') . '/' . $this->input->post('uf');

        $subtotal = $this->cart->total();
        $frete = 20.00;
        if ($subtotal >= 52.00 && $subtotal <= 166.59) {
            $frete = 15.00;
        } elseif ($subtotal > 200.00) {
            $frete = 0.00;
        }

        $desconto = $this->session->userdata('cupom_desconto') ? $this->session->userdata('cupom_desconto') : 0;
        $total = $subtotal - $desconto + $frete;

        $dados_pedido = [
            'cliente_nome' => $this->input->post('nome_cliente'),
            'cliente_email' => $this->input->post('email_cliente'),
            'cep' => $this->input->post('cep'),
            'endereco' => $endereco_completo,
            'subtotal' => $subtotal,
            'valor_frete' => $frete,
            'valor_desconto' => $desconto,
            'cupom_id' => $this->session->userdata('cupom_id'),
            'valor_total' => $total
        ];

        $itens_pedido = [];
        foreach ($this->cart->contents() as $item) {
            $itens_pedido[] = [
                'produto_variacao_id' => $item['id'],
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
            $this->enviarEmailConfirmacao($pedido_id, $dados_pedido, $itens_pedido);
            $this->cart->destroy();
            $this->session->unset_userdata(['cupom_id', 'cupom_desconto', 'cart_count']);
            $this->session->set_flashdata('success_message', "Pedido #{$pedido_id} finalizado com sucesso!");
            redirect('pedidos/sucesso');
        } else {
            $this->session->set_flashdata('compra_error', "Ocorreu um erro ao processar seu pedido. Tente novamente.");
            redirect('carrinho');
        }
    }

    public function sucesso()
    {
        $data['title'] = "Pedido Finalizado";
        $this->load->view('templates/header', $data);
        $this->load->view('pedidos/sucesso');
        $this->load->view('templates/footer');
    }

    private function enviarEmailConfirmacao($pedido_id, $pedido_data, $itens_pedido)
    {
        $this->load->library('email');

        $config['protocol']    = 'smtp';
        $config['smtp_host']   = 'ssl://smtp.googlemail.com';
        $config['smtp_port']   = 465;
        $config['smtp_user']   = 'danielduartefilho.df@gmail.com';
        $config['smtp_pass']   = 'npkr zpjy lqod rfpl';
        $config['mailtype']    = 'html';
        $config['charset']     = 'utf-8';
        $config['newline']     = "\r\n";
        $config['crlf']        = "\r\n";

        $this->email->initialize($config);

        $this->email->from($config['smtp_user'], 'Sua Loja');
        $this->email->to($pedido_data['cliente_email']);
        $this->email->subject('Confirmação do Pedido #' . $pedido_id);

        $itens_html = '<h3>Itens do seu pedido:</h3>
        <table border="1" cellpadding="10" cellspacing="0" style="width:100%; border-collapse: collapse;">
            <thead style="background-color:#f2f2f2;">
                <tr>
                    <th>Produto</th>
                    <th style="text-align:center;">Qtd.</th>
                    <th style="text-align:right;">Preço Unit.</th>
                    <th style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>';
        foreach ($itens_pedido as $item) {
            $itens_html .= '<tr>';
            $itens_html .= '<td>' . html_escape($item['produto_nome']) . '<br><small>(' . html_escape($item['variacao_nome']) . ')</small></td>';
            $itens_html .= '<td style="text-align:center;">' . $item['quantidade'] . '</td>';
            $itens_html .= '<td style="text-align:right;">R$ ' . number_format($item['preco_unitario'], 2, ',', '.') . '</td>';
            $itens_html .= '<td style="text-align:right;">R$ ' . number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') . '</td>';
            $itens_html .= '</tr>';
        }
        $itens_html .= '</tbody></table>';

        $mensagem = "<h1>Olá, {$pedido_data['cliente_nome']}!</h1>";
        $mensagem .= "<p>Seu pedido #{$pedido_id} foi recebido com sucesso e já está sendo processado.</p>";
        $mensagem .= $itens_html;
        $mensagem .= "<hr>";
        $mensagem .= "<p><b>Endereço de entrega:</b><br>" . html_escape($pedido_data['endereco']) . "</p>";
        $mensagem .= "<h3><b>Valor Total: R$ " . number_format($pedido_data['valor_total'], 2, ',', '.') . "</b></h3>";

        $this->email->message($mensagem);

        if (!$this->email->send()) {
            log_message('error', $this->email->print_debugger());
        }
    }
}
