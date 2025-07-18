<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cupons extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('cupom_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['cupons'] = $this->cupom_model->getAllCupons();
        $this->load->view('templates/header', $data);
        $this->load->view('cupons/gerenciar', $data);
        $this->load->view('templates/footer');
    }

    public function store()
    {
        $this->form_validation->set_rules('codigo', 'Código', 'required|is_unique[cupons.codigo]');
        $this->form_validation->set_rules('valor_desconto', 'Valor do Desconto', 'required|numeric');
        $this->form_validation->set_rules('data_validade', 'Data de Validade', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->index();
        } else {
            $data = [
                'codigo' => strtoupper($this->input->post('codigo')),
                'tipo_desconto' => $this->input->post('tipo_desconto'),
                'valor_desconto' => $this->input->post('valor_desconto'),
                'valor_minimo_pedido' => $this->input->post('valor_minimo_pedido') ?: 0,
                'data_validade' => $this->input->post('data_validade'),
                'ativo' => 1
            ];

            $this->cupom_model->create($data);
            redirect('cupons');
        }
    }

    public function alteraStatusCupom($id)
    {
        $this->cupom_model->alteraStatusCupom($id);
        redirect('cupons');
    }
}
