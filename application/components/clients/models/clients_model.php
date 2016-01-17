<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Clients_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
      
    $this->load->model('products/models/products_model');
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
    $this->db->select('clients.*, city.title_full as city_title, city.number as city_number, city.dist_ekb as city_dist_ekb');
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
    //Дополнительные параметры для события
    $client_param_desc = $this->clients_model->get_client_param();
    foreach ($items as $key => &$item) {
      $item['params'] = $this->main_model->get_params('client_params', $item['id']);
      $item['admin'] = $this->administrators_model->get_admin(array('id' => $item['admin_id']));
      $item['last_event'] = $this->calendar_model->get_event(array('client_id' => $item['id']),array('tm'=>'desc'));
      $item['red_events_cnt'] = $this->calendar_model->get_events_cnt(array('client_id'=>$item['id'], 'check'=>0, 'start <'=>date('Y-m-d H:i:s'),'end <'=>date('Y-m-d H:i:s')));
      $item['blue_events_cnt'] = $this->calendar_model->get_events_cnt(array('client_id'=>$item['id'], 'check'=>0, 'start >='=>date('Y-m-d H:i:s')));
      //параметры для добавления события
      $item['event_params'] = json_encode(array(
        'start'       => date("Y-m-d H:i:s", mktime(0,0,0,date("m"),date("d")+1,date("Y"))),
        'client_id'   => $item['id'],
        'title'       => @$item['city_title'].' '.$item['title'],
        //1 параметр - описание с телефонами, добавляем в событие по умолчанию
        'description' => @$item['params']['param_'.@$client_param_desc['id'].'_'.$this->language],
        'allDay'      => true,
      ));
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
      $item['main_params'] = $this->main_model->get_params('clients', $item['id']);
      $item['admin'] = $this->administrators_model->get_admin(array('id' => $item['admin_id']));
      $item['docs'] = $this->gallery_model->get_gallery_images(array('path' => '/gallery_system/clients/'.$item['id'].'/docs/'));
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

  function get_client_param($where = array(), $order_by = array()) {
    if ($order_by) {
      foreach ($order_by as $field => $dest) {
        $this->db->order_by($field, $dest);
      }
    } else {
      $this->db->order_by('order','asc');
    }
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

  /***Акты приемки***/
  function get_acceptances($limit = 0, $offset = 0, $where = array(), $order_by = array(), $product_id = array()) {
    $this->db->select('client_acceptances.*');
    if ($where) {
      $this->db->where($where);
    }
    if ($product_id) {
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      $this->db->join('client_acceptances t2','t2.parent_id = client_acceptances.id');
      $product_where = '';
      if ($where) {
        $product_where .= '(';
      }
      foreach ($product_id as $key => $value) {
        if($key != 0){
          $product_where .= ' OR ';
        }
        $product_where .= 't2.product_id = '.$value;
      }
      if ($where) {
        $product_where .= ')';
      }
      $this->db->where($product_where);
    }
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    if ($order_by) {
      foreach ($order_by as $field => $dest) {
        $this->db->order_by($field,$dest);
      }
    } else {
      $this->db->order_by('date','asc');
      $this->db->order_by('tm','asc');
    }
    $this->db->group_by('client_acceptances.id');
    $items = $this->db->get('client_acceptances')->result_array();
    foreach ($items as $key => &$item) {
      $item['client_title'] = $item['company'];
      if($item['client_id']){
        $item['client'] = $this->get_client(array('id'=>$item['client_id']));
        if($item['client']){
          $item['client_title'] = $item['client']['title_full'];
        }
      }
      //считаем общие параметры
      if(is_null($item['parent_id'])){
        $where = 'parent_id = '.$item['id'];
        if ($product_id) {
          $where .= ' AND (';
          foreach ($product_id as $key => $value) {
            if($key != 0){
              $where .= ' OR ';
            }
            $where .= 'pr_client_acceptances.product_id = '.$value;
          }
          $where .= ')';
        }
        $item['childs'] = $this->get_acceptances(0,0,$where);
        $item['gross'] = $item['net'] = $item['price'] = $item['sum'] = 0;
        foreach ($item['childs'] as $key => &$child) {
          $child['product'] = $this->products_model->get_product(array('id' => $child['product_id']));
          $child['sum'] = $child['price']*$child['net'];
          $item['gross'] += $child['gross'];
          $item['net'] += $child['net'];
          $item['price'] += ($child['price']*$child['net']);
          $item['sum'] = $item['price']-$item['add_expenses'];
        }
        unset($child);
      }
    }
    unset($item);
    
    return $items;
  }
  
  function get_acceptances_cnt($where = '', $product_id = array()) {
    $this->db->select('COUNT(DISTINCT(pr_client_acceptances.id)) as cnt');
    if ($where) {
      $this->db->where($where);
    }
    if ($product_id) {
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      $this->db->join('client_acceptances t2','t2.parent_id = client_acceptances.id');
      $product_where = '';
      if ($where) {
        $product_where .= '(';
      }
      foreach ($product_id as $key => $value) {
        if($key != 0){
          $product_where .= ' OR ';
        }
        $product_where .= 't2.product_id = '.$value;
      }
      if ($where) {
        $product_where .= ')';
      }
      $this->db->where($product_where);
    }
    return $this->db->get('client_acceptances')->row()->cnt;
  }

  function get_acceptance($where = array()) {
    $this->db->select('client_acceptances.*');
    $item = $this->db->get_where('client_acceptances', $where)->row_array();
    if($item){
      $item['client_title'] = $item['company'];
      if($item['client_id']){
        $item['client'] = $this->get_client(array('id'=>$item['client_id']));
        if($item['client']){
          $item['client_title'] = $item['client']['title'];
          if($item['client']['city_id']){
            $item['city'] = $this->cities_model->get_city(array('id' => $item['client']['city_id']));
          }
        }
      }
      $item['childs'] = $this->get_acceptances(0,0,array('parent_id'=>$item['id']),array('id'=>'asc'));
      foreach ($item['childs'] as $key => &$child) {
        $child['product'] = $this->products_model->get_product(array('id' => $child['product_id']));
      }
      unset($child);
    }

    return $item;
  }

  function create_acceptance($params) {
    if ($this->db->insert('client_acceptances', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  function update_acceptance($id, $params) {
    if ($this->db->update('client_acceptances', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_acceptance($id) {
    if ($this->db->delete('client_acceptances', array('id' => $id))) {
      return true;
    }
    return false;
  }

  function get_acceptance_emails($where = array(), $order_by = array()) {
    $this->db->select('client_acceptance_emails.*,admins.username as username');
    $this->db->order_by('tm','desc');
    if ($where) {
      $this->db->where($where);
    }
    $this->db->join('admins','admins.id=client_acceptance_emails.admin_id');
    $items = $this->db->get('client_acceptance_emails')->result_array();

    return $items;
  }

  function create_acceptance_email($params) {
    if ($this->db->insert('client_acceptance_emails', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }
}