<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cities_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }

  /***Регионы***/
  function get_regions($limit = 0, $offset = 0, $where = array(), $order_by = array()) {   
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    if ($order_by) {
      foreach ($order_by as $field => $dest) {
        $this->db->order_by($field, $dest);
      }      
    } else {
      $this->db->order_by('title','asc');
    }
    if ($where) {
      $this->db->where($where);
    }
    $items = $this->db->get('region')->result_array();
    
    return $items;
  }
  
  function get_regions_cnt($where = '') {
    if ($where) {
      $this->db->where($where);
    }
    return $this->db->count_all_results('region');
  }

  function get_region($where = array()) {
    $item = $this->db->get_where('region', $where)->row_array();
    return $item;
  }

  function create_region($params) {
    if ($this->db->insert('region', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  function update_region($id, $params) {
    if ($this->db->update('region', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_region($id) {
    if ($this->db->delete('region', array('id' => $id))) {
      return true;
    }
    return false;
  }

  /***Города***/  
  function get_cities($limit = 0, $offset = 0, $where = array(), $order_by = array()) {   
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    if ($order_by) {
      foreach ($order_by as $field => $dest) {
        $this->db->order_by($field, $dest);
      }      
    } else {
      $this->db->order_by('title','asc');
    }
    if ($where) {
      $this->db->where($where);
    }
    $items = $this->db->get('city')->result_array();
    
    return $items;
  }
  
  function get_cities_cnt($where = '') {
    if ($where) {
      $this->db->where($where);
    }
    return $this->db->count_all_results('city');
  }

  function get_city($where = array()) {
    $item = $this->db->get_where('city', $where)->row_array();
    return $item;
  }

  function create_city($params) {
    if ($this->db->insert('city', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  function update_city($id, $params) {
    if ($this->db->update('city', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_city($id) {
    if ($this->db->delete('city', array('id' => $id))) {
      return true;
    }
    return false; 
  }
}