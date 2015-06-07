<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cities_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }

  /***Федеральные округа***/
  function get_regions_federal($limit = 0, $offset = 0, $where = array(), $order_by = array()) {   
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    if ($order_by) {
      foreach ($order_by as $field => $dest) {
        $this->db->order_by($field, $dest);
      }      
    } else {
      $this->db->order_by('id','asc');
    }
    if ($where) {
      $this->db->where($where);
    }
    $items = $this->db->get('region_federal')->result_array();
    
    return $items;
  }
  
  function get_regions_federal_cnt($where = '') {
    if ($where) {
      $this->db->where($where);
    }
    return $this->db->count_all_results('region_federal');
  }

  function get_region_federal($where = array()) {
    $item = $this->db->get_where('region_federal', $where)->row_array();
    if($item){
      $item['regions'] = $this->db->get_where('region_federal_regions', array('federal_id'  => $item['id']))->result_array();
      $item['regions'] = array_simple($item['regions'], 'region_id');
    }
    return $item;
  }

  function create_region_federal($params) {
    if ($this->db->insert('region_federal', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  function update_region_federal($id, $params) {
    if ($this->db->update('region_federal', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function set_region_federal_regions($id, $regions) {    
    $now = array();
    if (!$this->db->query('DELETE FROM pr_region_federal_regions WHERE federal_id = '. $id .' AND region_id NOT IN ('. ($regions ? implode(',', $regions) : 0) .')')) {
      return false;
    }
    if ($regions) {
      $now_items = $this->db->get_where('pr_region_federal_regions', array('federal_id' => $id))->result_array();
      foreach ($now_items as $now_item) {
        $now[] = $now_item['region_id'];
      }
      foreach ($regions as $region_id) {
        if (!in_array($region_id, $now)) {
          if (!$this->db->insert('pr_region_federal_regions', array('federal_id' => $id, 'region_id' => $region_id))) {
            return false;
          }
        }
      }
    }
    return true;
  }
  
  function delete_region_federal($id) {
    if ($this->db->delete('region_federal', array('id' => $id))) {
      return true;
    }
    return false;
  }

  /***Регионы***/
  function get_regions($limit = 0, $offset = 0, $where = array(), $order_by = array(), $region_federal_id = 0) {   
    $this->db->select('region.*');
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
    if ($region_federal_id) {
      $this->db->join('region_federal_regions','region_federal_regions.region_id = region.id');
      $this->db->where(array('region_federal_regions.federal_id' => $region_federal_id));
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
  
  /**
  * Запрос на список городов
  * @param $limit
  *        $offset
  *        $where
  *        $order_by
  *        $region_federal_id - id Федерального округа
  */
  function get_cities($limit = 0, $offset = 0, $where = array(), $order_by = array(), $region_federal_id = 0) {   
    $this->db->select('city.*');
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
    if ($region_federal_id) {
      $this->db->join('region','region.id = city.region_id');
      $this->db->join('region_federal_regions','region_federal_regions.region_id = region.id');
      $this->db->where(array('region_federal_regions.federal_id' => $region_federal_id));
    }
    if ($where) {
      $this->db->where($where);
    }
    $items = $this->db->get('city')->result_array();
    foreach ($items as $key => &$item) {
      $item['title'] = $item['title_full'];
    }
    unset($item);
    
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