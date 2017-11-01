<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Confirmation extends CI_Model {

    var $status = array(
        0 => 'Pending',
        1 => 'Approved',
    );
    var $table = 'confirmations';

    function create($data = array()) {
        $this->db->insert($this->table, $data);
        if ($this->db->affected_rows() == 1) {

            return TRUE;
        }
        return FALSE;
    }

    function getByOrderId($orderId) {
        $this->db->where('order_id', $orderId);
        $query = $this->db->get($this->table, 1);
        if ($query->num_rows() == 1) {
            return $query->row_array();
        }
    }

    function getById($id) {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table, 1);
        if ($query->num_rows() == 1) {
            return $query->row_array();
        }
    }

    function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        if ($this->db->affected_rows() == 1) {

            return TRUE;
        }
        return FALSE;
    }

    function update($data, $id) {
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        if ($this->db->affected_rows() == 1) {

            return TRUE;
        }
        return FALSE;
    }

}
