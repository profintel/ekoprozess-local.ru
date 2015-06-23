<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Languages_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }
  
  function get_languages($active = FALSE, $admin = FALSE) {
    if ($active !== FALSE) {
      $this->db->where('active', $active);
    }
    if ($admin !== FALSE) {
      $this->db->where('admin', $admin);
    }
    return $this->db->order_by('id')->get('languages')->result_array();
  }
  
  function get_language($id) {
    return $this->db->get_where('languages', array('id' => $id))->row_array();
  }
  
  function get_language_name($name) {
    return $this->db->get_where('languages', array('name' => $name))->row_array();
  }
  
  function create_language($params) {
    $this->db->trans_begin();
    
    $this->db->insert('languages', $params);
    $id = $this->db->insert_id();
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return $id;
  }
  
  function update_language($id, $params) {
    return (bool)$this->db->update('languages', $params, array('id' => $id));
  }
  
  function delete_language($id) {
    return (bool)$this->db->delete('languages', array('id' => $id));
  }
  
}