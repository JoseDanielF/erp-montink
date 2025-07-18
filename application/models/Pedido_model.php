<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pedido_model extends CI_Model
{
    public function create($data)
    {
        $this->db->trans_start();

        $this->db->insert('pedidos', $data['pedido']);
        $pedido_id = $this->db->insert_id();

        foreach ($data['itens'] as $item) {
            $item['pedido_id'] = $pedido_id;
            $this->db->insert('pedido_itens', $item);

            $this->db->set('quantidade', 'quantidade - ' . (int)$item['quantidade'], FALSE);
            
            $this->db->where('variacao_id', $item['produto_variacao_id']); 
            $this->db->update('estoque');
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        return $pedido_id;
    }

    public function updateStatus($pedido_id, $status)
    {
        $this->db->where('id', $pedido_id)->update('pedidos', ['status' => $status]);
    }

    public function deletePedido($pedido_id)
    {
        $this->db->where('id', $pedido_id)->delete('pedidos');
    }

    public function getAllPedidos()
    {
        $this->db->select('*');
        $this->db->from('pedidos');
        $this->db->order_by('pedidos.criado_em', 'DESC');
        return $this->db->get()->result_array();
    }

    public function getDetalhesPedido($pedido_id)
    {
        $this->db->where('id', $pedido_id);
        $query = $this->db->get('pedidos');
        return $query->row_array();
    }

    public function getItensPedido($pedido_id)
    {
        $this->db->where('pedido_id', $pedido_id);
        $query = $this->db->get('pedido_itens');
        return $query->result_array();
    }
}
