<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Administrators_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
      
    $this->load->model('gallery/models/gallery_model');
  }
  
  function get_admins($where=array()) {
    if($where){
      $this->db->where($where);
    }
    $this->db->order_by('username');
    $items = $this->db->get('pr_admins')->result_array();
    foreach ($items as $key => &$item) {
      $item['params'] = $this->main_model->get_params('admins', $item['id']);
      foreach ($item['params'] as $key => $value) {
        $item[$key] = $value;
      }
    }
    unset($item);
    
    return $items;
  }
  
  function get_admin($where=array()) {
    if($where){
      $this->db->where($where);
    }
    $item = $this->db->get('pr_admins')->row_array();
    if($item){
      $item['image'] = "";
      $item['images'] = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/administrators/admin_'.$item['id'].'/'),1,0);
      foreach ($item['images'] as $image) {
        $item['image'] = $image['image'];
      }
      $item['params'] = $this->main_model->get_params('admins', $item['id']);
    }
    
    return $item;
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