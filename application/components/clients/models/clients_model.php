<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Clients_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
      
  }

  /***Клиенты***/
  function get_clients($limit = 0, $offset = 0, $where = array(), $order_by = array()) {   
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
    $items = $this->db->get('clients')->result_array();
    
    return $items;
  }

  function get_clients_report($limit = 0, $offset = 0, $where = array(), $order_by = array()) {   
    $this->db->select('city.title as city,clients.*');
    $this->db->join('city', 'city.id = clients.city_id', 'LEFT');
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
    $items = $this->db->get('clients')->result_array();
    foreach ($items as $key => &$item) {
      $item['params'] = $this->main_model->get_params('client_params', $item['id']);
    }
    unset($item);
    
    return $items;
  }
  
  function get_clients_cnt($where = '') {
    if ($where) {
      $this->db->where($where);
    }
    return $this->db->count_all_results('clients');
  }

  function get_client($where = array()) {
    $item = $this->db->get_where('clients', $where)->row_array();
    if($item){
      $item['params'] = $this->main_model->get_params('client_params', $item['id']);
    }
    return $item;
  }

  function get_client_order() {
    return (int)$this->db->select_max('order', 'max_order')->get('clients')->row()->max_order + 1;
  }

  function create_client($params) {
    if ($this->db->insert('clients', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  function update_client($id, $params) {
    if ($this->db->update('clients', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_client($id) {
    if ($this->db->delete('clients', array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  /***Параметры таблицы клиентов***/
  function get_client_params($limit = 0, $offset = 0, $where = array(), $order_by = array()) {   
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
    $items = $this->db->get('client_params')->result_array();
    
    return $items;
  }
  
  function get_client_params_cnt($where = '') {
    if ($where) {
      $this->db->where($where);
    }
    return $this->db->count_all_results('client_params');
  }

  function get_client_param($where = array()) {
    $item = $this->db->get_where('client_params', $where)->row_array();
    return $item;
  }

  function get_client_param_order() {
    return (int)$this->db->select_max('order', 'max_order')->get('client_params')->row()->max_order + 1;
  }

  function create_client_param($params) {
    if ($this->db->insert('client_params', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  function update_client_param($id, $params) {
    if ($this->db->update('client_params', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_client_param($id) {
    if ($this->db->delete('client_params', array('id' => $id))) {
      return true;
    }
    return false;
  }
}