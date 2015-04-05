<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Calendar_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
			
  }

  /***События***/
  function get_events($limit = 0, $offset = 0, $where = array(), $order_by = array()) {   
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    if ($order_by) {
      foreach ($order_by as $field => $dest) {
        $this->db->order_by($field, $dest);
      }      
    }
    if ($where) {
      $this->db->where($where);
    }
    $items = $this->db->get('admin_events')->result_array();
    foreach ($items as $key => &$item) {
      $item['allDay'] = ($item['allDay'] ? true : false);
    }
    unset($item);
  
    return $items;
  }
  
  function get_events_cnt($where = '') {
    if ($where) {
      $this->db->where($where);
    }
    return $this->db->count_all_results('admin_events');
  }

  function get_event($where = array(), $order_by = array()) {
    if ($where) {
      $this->db->where($where);
    }
    if ($order_by) {
      foreach ($order_by as $field => $dest) {
        $this->db->order_by($field, $dest);
      }      
    }
    return $this->db->get('admin_events')->row_array();
  }

  function create_event($params) {
    if ($this->db->insert('admin_events', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  function update_event($id, $params) {
    if ($this->db->update('admin_events', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_event($id) {
    if ($this->db->delete('admin_events', array('id' => $id))) {
      return true;
    }
    return false;
  }
}