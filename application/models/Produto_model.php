<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Produto_model extends CI_Model
{

    public function getAllProdutosComEstoque()
    {
        $this->db->select('p.id, p.nome, p.preco_base, pv.id as variacao_id, pv.nome as variacao_nome, pv.preco_adicional, e.quantidade');
        $this->db->from('produtos p');
        $this->db->join('produto_variacoes pv', 'p.id = pv.produto_id');
        $this->db->join('estoque e', 'pv.id = e.variacao_id');
        $this->db->order_by('p.nome, pv.nome');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getDetalhesProduto($variacao_id)
    {
        $this->db->select('p.id as produto_id, p.nome as produto_nome, p.preco_base, pv.id as variacao_id, pv.nome as variacao_nome, pv.preco_adicional, e.quantidade');
        $this->db->from('produto_variacoes pv');
        $this->db->join('produtos p', 'pv.produto_id = p.id');
        $this->db->join('estoque e', 'pv.id = e.variacao_id');
        $this->db->where('pv.id', $variacao_id);
        return $this->db->get()->row_array();
    }

    public function createProduto($data)
    {
        $this->db->trans_start();

        $produto_data = ['nome' => $data['nome'], 'preco_base' => $data['preco']];
        $this->db->insert('produtos', $produto_data);
        $produto_id = $this->db->insert_id();

        $variacao_data = ['produto_id' => $produto_id, 'nome' => $data['variacao_nome']];
        $this->db->insert('produto_variacoes', $variacao_data);
        $variacao_id = $this->db->insert_id();

        $estoque_data = ['variacao_id' => $variacao_id, 'quantidade' => $data['estoque']];
        $this->db->insert('estoque', $estoque_data);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function updateProdutoEstoque($variacao_id, $data)
    {
        $this->db->where('id', $data['produto_id'])->update('produtos', ['nome' => $data['nome'], 'preco_base' => $data['preco']]);
        $this->db->where('id', $variacao_id)->update('produto_variacoes', ['nome' => $data['variacao_nome']]);
        $this->db->where('variacao_id', $variacao_id)->update('estoque', ['quantidade' => $data['estoque']]);
        return $this->db->affected_rows() > 0;
    }

    public function updateEstoque($variacao_id, $quantidade)
    {
        $this->db->set('quantidade', 'quantidade - ' . (int)$quantidade, FALSE);
        $this->db->where('variacao_id', $variacao_id);
        $this->db->update('estoque');
    }

    public function getEstoquePorIDVariacao($variacao_id)
    {
        $this->db->select('quantidade');
        $this->db->from('estoque');
        $this->db->where('variacao_id', $variacao_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->quantidade;
        }
        return 0;
    }
}
