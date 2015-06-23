<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menus_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }
  
  function get_menus($project_id = 0) {
    if ($project_id) {
      $this->db->where('menus.project_id', $project_id);
    }
    return $this->db
      ->select('menus.*, projects.title AS project', FALSE)
      ->join('projects', 'projects.id = menus.project_id')
      ->order_by('projects.id, menus.title')
      ->get('menus')
      ->result_array();
  }
  
  function get_menu($condition) {
    if (is_int($condition)) {
      $this->db->where('menus.id', $condition);
    } else {
      $this->db->where('menus.name', $condition);
    }
    return $this->db
      ->select('menus.*, projects.title AS project', FALSE)
      ->join('projects', 'projects.id = menus.project_id')
      ->get('menus')
      ->row_array();
  }
  
  function create_menu($params) {
    $this->db->trans_begin();
    
    $this->db->insert('menus', $params);
    $id = $this->db->insert_id();
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return $id;
  }
  
  function update_menu($id, $params) {
    return $this->db->update('menus', $params, array('id' => $id));
  }
  
  function delete_menu($id) {
    return (bool)$this->db->delete('menus', array('id' => $id));
  }
  
  function get_page_menus($page_id) {
    return $this->db->get_where('menus_pages', array('page_id' => $page_id))->result_array();
  }
  
  function set_page_menus($page_id, $menus) {
    $this->db->trans_begin();
    
    if ($menus) {
      foreach ($menus as $menu_id) {
        if (!$this->db->get_where('menus_pages', array('page_id' => $page_id, 'menu_id' => $menu_id))->num_rows()) {
          $this->db->insert('menus_pages', array('page_id' => $page_id, 'menu_id' => $menu_id));
        }
      }
      
      $this->db->where_not_in('menu_id', $menus);
    }
    $this->db->delete('menus_pages', array('page_id' => $page_id));
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  function get_menu_pages($menu_id) {
    return $this->db
      ->select('pages.*', FALSE)
      ->join('pages', 'pages.id = menus_pages.page_id')
      ->order_by('order')
      ->get_where('menus_pages', array('menus_pages.menu_id' => $menu_id, 'pages.in_menu' => 1, 'active' => 1))
      ->result_array();
  }
  
  function get_pages($parent_id) {
    return $this->db
      ->order_by('order')
      ->get_where('pages', array('parent_id' => $parent_id, 'pages.in_menu' => 1, 'active' => 1))
      ->result_array();
  }
  
}