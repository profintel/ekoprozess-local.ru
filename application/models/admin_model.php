<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }
  
  function get_menu($type) {
    $items = $this->db->order_by('title')->get_where('components', array('menu' => $type))->result_array();
    if ($this->db->get_where('components', array('name' => 'permits'))->num_rows()) {
      $results = array();
      foreach ($items as $key => $item) {
        if ($this->permits_model->check_access($this->admin_id, $item['name'], 'index')) {
          $results[] = $item;
        }
      }
      return $results;
    }

    return $items;
  }
  
  function get_admins() {
    return $this->db->order_by('username')->get('admins')->result_array();
  }
  
  function get_admin($id) {
    return $this->db->get_where('admins', array('id' => $id))->row_array();
  }
  
}