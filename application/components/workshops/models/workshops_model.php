<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Workshops_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }

  function get_workshops($where = array()) {
    if ($where) {
      $this->db->where($where);
    }
    $this->db->order_by('order');
    $items = $this->db->get('store_workshops')->result_array();

    return $items;
  }
  
  function get_workshop($where = array(), $full = false) {
    if ($where) {
      $this->db->where($where);
    }
    $item = $this->db->get('store_workshops')->row_array();

    return $item;
  }

  function get_workshop_order() {
    return (int)$this->db->select_max('order', 'max_order')->get_where('store_workshops')->row()->max_order + 1;
  }

  function create_workshop($params) {
    $this->db->trans_begin();
    
    $this->db->insert('store_workshops', $params);

    $id = $this->db->insert_id();
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return $id;
  }

  function update_workshop($id, $params) {
    if ($this->db->update('store_workshops', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }

  function move_workshop($workshop_id, $dest_id, $placement) {
    $this->db->trans_begin();
    
    $workshop = $this->get_workshop(array('id'=>$workshop_id));
    $workshop_ids = $this->get_workshop_childs_ids($workshop_id);
    
    if ($dest_id) {
      $dest = $this->get_workshop(array('id'=>$dest_id));

      $params = array(
        'order'      => $dest['order'] + 1
      );
    } else {
      $params = array(
        'order' => (int)$this->db->select_min('order', 'min_order')->get_where('store_workshops')->row()->min_order
      );
    }
    
    $this->update_workshop($workshop['id'], $params);
    
    $this->db->set('order', '`order` + 1', FALSE)->where(array(
      'order >='   => $params['order'],
      'id !='      => $workshop['id']
    ))->update('store_workshops');
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }

  function delete_workshop($id) {
    if ($this->db->delete('store_workshops', array('id' => $id))) {
      return true;
    }
    return false;
  }

}