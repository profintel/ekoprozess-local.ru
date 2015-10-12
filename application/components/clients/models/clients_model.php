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

  /***Товары***/
  function get_products($where = array()) {
    if ($where) {
      $this->db->where($where);
    }
    $this->db->order_by('order');
    $items = $this->db->get('products')->result_array();
    
    foreach ($items as &$item) {
      $item['childs'] = $this->get_products(array('parent_id'=>$item['id']));
    }
    unset($item);

    return $items;
  }
  
  function get_product($where = array(), $full = false) {
    if ($where) {
      $this->db->where($where);
    }
    $item = $this->db->get('products')->row_array();
    if ($item) {
      $item['childs']   = $this->get_products(array('parent_id'=>$item['id']));
    }

    return $item;
  }

  function get_product_order($parent_id) {
    return (int)$this->db->select_max('order', 'max_order')->get_where('products', array(
      'parent_id' => $parent_id
    ))->row()->max_order + 1;
  }

  function create_product($params) {
    $this->db->trans_begin();
    
    $this->db->insert('products', $params);

    $id = $this->db->insert_id();
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return $id;
  }

  function update_product($id, $params) {
    if ($this->db->update('products', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }

  function move_product($product_id, $dest_id, $placement) {
    $this->db->trans_begin();
    
    $product = $this->get_product(array('id'=>$product_id));
    $product_ids = $this->get_product_childs_ids($product_id);
    
    if ($dest_id) {
      $dest = $this->get_product(array('id'=>$dest_id));
      
      if ($placement == 'inner') {
        $params = array(
          'parent_id'  => $dest['id'],
          'order'      => (int)$this->db->select_min('order', 'min_order')->get_where('products', array('parent_id' => $dest['id']))->row()->min_order
        );
      } else {
        $params = array(
          'parent_id'  => $dest['parent_id'],
          'order'      => $dest['order'] + 1
        );
      }
    } else {
      $params = array(
        'parent_id'  => NULL,
        'order'      => (int)$this->db->select_min('order', 'min_order')->get_where('products', array('parent_id' => NULL))->row()->min_order
      );
    }
    
    if ($params['parent_id']) {
      $parent = $this->get_product(array('id'=>$params['parent_id']));
    }

    if (in_array($params['parent_id'],$product_ids)) {
      return FALSE;
    }

    if ($product['id'] == $params['parent_id']) {
      return FALSE;
    }
    
    $this->update_product($product['id'], $params);
    
    $this->db->set('order', '`order` + 1', FALSE)->where(array(
      'parent_id'  => $params['parent_id'],
      'order >='   => $params['order'],
      'id !='      => $product['id']
    ))->update('products');
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }

  /**
  * Возвращает массив с id дочерних каталогов
  */
  function get_product_childs_ids($product_id, $ids=array()) {
    $ids[] = $product_id;
    $items = $this->db->get_where('products',array('parent_id'=>$product_id))->result_array();
    foreach ($items as $item) {
      $ids = $this->get_product_childs_ids($item['id'], $ids);
    }
    return $ids;
  }
  
  function delete_product($id) {
    if ($this->db->delete('products', array('id' => $id))) {
      return true;
    }
    return false;
  }

  /***Акты приемки***/
  function get_acceptances($limit = 0, $offset = 0, $where = array(), $order_by = array(), $product_id = 0) {
    $this->db->select('client_acceptances.*');
    if ($product_id) {
      $this->db->join('client_acceptances t2','t2.parent_id = client_acceptances.id');
      $this->db->where(array('t2.product_id'=>$product_id));
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
    if ($where) {
      $this->db->where($where);
    }
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
        $where = array('parent_id'=>$item['id']);
        if ($product_id) {
          $where['client_acceptances.product_id'] = $product_id;
        }
        $item['childs'] = $this->get_acceptances(0,0,$where);
        $item['gross'] = $item['net'] = $item['price'] = $item['sum'] = 0;
        foreach ($item['childs'] as $key => &$child) {
          $child['product'] = $this->get_product(array('id' => $child['product_id']));
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
  
  function get_acceptances_cnt($where = '', $product_id = 0) {
    if ($where) {
      $this->db->where($where);
    }
    if ($product_id) {
      $this->db->join('client_acceptances t2','t2.parent_id = client_acceptances.id');
      $this->db->where(array('t2.product_id'=>$product_id));
    }
    return $this->db->count_all_results('client_acceptances');
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
        $child['product'] = $this->get_product(array('id' => $child['product_id']));
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