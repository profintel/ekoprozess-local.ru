<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Components_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }
  
  function get_components($parent = FALSE) {
    if ($parent) {
      $this->db->where('parent', $parent);
    }
    return $this->db->order_by('id')->get('components')->result_array();
  }
  
  function get_component($condition) {
    if (is_array($condition)) {
      $this->db->where('path', '/'. implode('/', $condition) .'/');
    } elseif (preg_match('/^\d+$/', $condition)) {
      $this->db->where('id', $condition);
    } else {
      $this->db->where('name', $condition);
    }
    return $this->db->get('components')->row_array();
  }
  
  function get_main_component() {
    return $this->db->get_where('components', array('main' => 1))->row_array();
  }
  
  function set_main($component_id) {
    $this->db->trans_begin();
    
    $this->db->update('components', array('main' => 0));
    $this->db->update('components', array('main' => 1), array('id' => $component_id));
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  function add_component($params) {
    $this->db->trans_begin();
    
    $this->db->insert('components', $params);
    $id = $this->db->insert_id();
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return $id;
  }
  
  function delete_component($condition) {
    if (preg_match('/^\d+$/', $condition)) {
      $this->db->where('id', $condition);
    } else {
      $this->db->where('name', $condition);
    }
    return (bool)$this->db->delete('components');
  }
  
}