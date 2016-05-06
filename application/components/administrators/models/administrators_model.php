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
      $this->db->insert('pr_permits', array('component' => 'clients', 'method' => 'index', 'admin_id' => $id));
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

  function get_admin_logs_cnt($where = '') {
    if ($where) {
      $this->db->where($where);
    }
    return $this->db->count_all_results('admin_logs');
  }
  
  function get_admin_logs($where = array(), $order_by = array(), $limit = 0, $offset = 0) {
    $this->db->select('admin_logs.*, admins.username as username');
    $this->db->join('admins','admins.id=admin_logs.admin_id');
    if ($where) {
      $this->db->where($where);
    }
    if ($order_by) {
      foreach ($order_by as $field => $dest) {
        $this->db->order_by($field, $dest);
      }
    } else {
      $this->db->order_by('tm', 'DESC');
    }
    if ($limit) {
      $this->db->limit($limit, $offset);
    }    
    $items = $this->db->get('admin_logs')->result_array();
    foreach ($items as $key => &$item) {
      if(unserialize($item['post'])){
        $item['post'] = print_r(unserialize($item['post']), true);
      } else {
        $item['post'] = '';
      }
    }
    unset($item);
    // echo $this->db->last_query();
    
    return $items;
  }
}