<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cupom_model extends CI_Model
{
    public function getCupomByCodigo($code)
    {
        $today = date('Y-m-d');
        $this->db->where('codigo', $code);
        $this->db->where('ativo', 1);
        $this->db->where('data_validade >=', $today);
        return $this->db->get('cupons')->row_array();
    }

    public function getAllCupons()
    {
        $this->db->order_by('data_validade', 'DESC');
        return $this->db->get('cupons')->result_array();
    }

    public function create($data)
    {
        return $this->db->insert('cupons', $data);
    }

    public function alteraStatusCupom($id)
    {
        $this->db->set('ativo', '1 - ativo', FALSE);
        $this->db->where('id', $id);
        return $this->db->update('cupons');
    }
}
