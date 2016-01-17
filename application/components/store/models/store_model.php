<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
      
  }

  /**
  * Типы продукции
  * @param $where - массив с параметрами поиска
  *        $limit - к-во строк в результате
  *        $offset - стартовая строка поиска
  *        $order_by - массив с параметрами сортировки результата
  * @return array
  */  
  function get_store_types($where = array(), $limit = 0, $offset = 0, $order_by = array()) {   
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    if ($order_by) {
      foreach ($order_by as $field => $dest) {
        $this->db->order_by($field, $dest);
      }      
    } else {
      $this->db->order_by('order','asc');
    }
    if ($where) {
      $this->db->where($where);
    }
    $items = $this->db->get('store_types')->result_array();
    
    return $items;
  }

  /**
  * Тип продукции
  * @param $where - массив с параметрами поиска
  * @return array
  */
  function get_store_type($where = array()) {
    return $this->db->get_where('store_types', $where)->row_array();
  }
}