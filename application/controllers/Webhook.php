<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Webhook extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('pedido_model');
    }

    public function pedidoStatus()
    {
        $json_data = $this->input->raw_input_stream;
        $data = json_decode($json_data, true);

        $pedido_id = isset($data['id']) ? $data['id'] : null;
        $status = isset($data['status']) ? $data['status'] : null;

        if (!$pedido_id || !$status) {
            $this->output->set_status_header(400);
            return;
        }

        if (strtolower($status) === 'Cancelado') {
            $this->pedido_model->deletePedido($pedido_id);
        } else {
            $this->pedido_model->updateStatus($pedido_id, $status);
        }

        $this->output->set_status_header(200)->set_output('Status atualizado com sucesso!');
    }
}
