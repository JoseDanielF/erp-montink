<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Produtos extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('produto_model');
    }

    public function index()
    {
        $data['title'] = "Gerenciar Produtos";
        $data['produtos'] = $this->produto_model->getAllProdutosComEstoque();

        $this->load->view('templates/header', $data);
        $this->load->view('produtos/gerenciar', $data);
        $this->load->view('templates/footer');
    }

    public function edit($variacao_id)
    {
        $data['produto'] = $this->produto_model->getDetalhesProduto($variacao_id);
        $data['produtos'] = $this->produto_model->getAllProdutosComEstoque();
        $this->load->view('templates/header', $data);
        $this->load->view('produtos/gerenciar', $data);
        $this->load->view('templates/footer');
    }

    public function store()
    {
        $data = $this->input->post();
        if ($this->produto_model->createProduto($data)) {
        } else {
        }
        redirect('produtos');
    }

    public function update($variacao_id)
    {
        $data = $this->input->post();
        $this->produto_model->updateProdutoEstoque($variacao_id, $data);
        redirect('produtos');
    }
}
