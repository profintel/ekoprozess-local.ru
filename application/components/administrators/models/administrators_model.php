<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Administrators_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
			
  }
  
  function get_admins() {
    $this->db->order_by('username');
    $items = $this->db->get('pr_admins')->result_array();
    
    return $items;
  }
	
  function create_admin($params) {    
		if ($this->db->insert('pr_admins', $params)) {
      $id = $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
			$this->db->insert('pr_permits', array('component' => 'projects', 'method' => 'index', 'admin_id' => $id));
			return $id;
    }
    return false;
  }

  function edit_admin($id, $params) {
    if ($this->db->update('pr_admins', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }	
	
  function delete_admin($id) {
		if ($this->db->delete('pr_admins', array('id' => $id))) {
      return true;
    }
    return false;
  }	
}