<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
      
    $this->load->model('cities/models/cities_model');
    $this->load->model('clients/models/clients_model');
    $this->load->model('products/models/products_model');
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

  /**
  * Список приходов
  */
  function get_comings($limit = 0, $offset = 0, $where = array(), $order_by = array(), $product_id = array()) {
    $this->db->select('store_comings.*');
    //для проверки прав на работу по всем клиентам
    if(is_array($where) && @$where['clients.admin_id']){
      $this->db->join('clients','clients.id = store_comings.client_id');      
    }
    if ($where) {
      $this->db->where($where);
    }
    if ($product_id) {
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      $this->db->join('store_comings t2','t2.parent_id = store_comings.id');
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
      $this->db->order_by('date_second','asc');
      $this->db->order_by('tm','asc');
    }
    $this->db->group_by('store_comings.id');
    $items = $this->db->get('store_comings')->result_array();
    unset($where);
    foreach ($items as $key => &$item) {
      if($item['client_id']){
        $item['client'] = $this->clients_model->get_client(array('id'=>$item['client_id']));
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
            $where .= 'pr_store_comings.product_id = '.$value;
          }
          $where .= ')';
        }
        $item['childs'] = $this->get_comings(0,0,$where);
        $item['gross'] = $item['net'] = $item['price'] = $item['sum'] = 0;
        foreach ($item['childs'] as $key => &$child) {
          $child['product'] = $this->products_model->get_product(array('id' => $child['product_id']));
          $item['gross'] += $child['gross'];
          $item['net'] += $child['net'];
        }
        unset($child);
      }
    }
    unset($item);
    
    return $items;
  }
  
  /**
  * Количество приходов по условию
  */
  function get_comings_cnt($where = '', $product_id = array()) {
    $this->db->select('COUNT(DISTINCT(pr_store_comings.id)) as cnt');
    //для проверки прав на работу по всем клиентам
    if(is_array($where) && @$where['clients.admin_id']){
      $this->db->join('clients','clients.id = store_comings.client_id');      
    }
    if ($where) {
      $this->db->where($where);
    }
    if ($product_id) {
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      $this->db->join('store_comings t2','t2.parent_id = store_comings.id');
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
    return $this->db->get('store_comings')->row()->cnt;
  }

  /**
  * Одна единица прихода
  */
  function get_coming($where = array()) {
    $this->db->select('store_comings.*');
    $item = $this->db->get_where('store_comings', $where)->row_array();
    if($item){
      if($item['client_id']){
        $item['client'] = $this->clients_model->get_client(array('id'=>$item['client_id']));
        if($item['client']){
          $item['client_title'] = $item['client']['title'];
          if($item['client']['city_id']){
            $item['city'] = $this->cities_model->get_city(array('id' => $item['client']['city_id']));
          }
        }
      }
      $item['childs'] = $this->get_comings(0,0,array('parent_id'=>$item['id']),array('id'=>'asc'));
      foreach ($item['childs'] as $key => &$child) {
        $child['product'] = $this->products_model->get_product(array('id' => $child['product_id']));
        // остаток сырья на складе по клиенту
        if($child['active']){
          // если расход отправлен на склад, выводим остатки на момент добавления расхода на склад
          $rest = $this->get_rest(array('coming_id' => $child['parent_id'],'client_id' => $child['client_id'],'product_id' => $child['product_id']));
          $child['rest'] = ($rest ? $rest['rest'] : 0.00);
          $child['rest_product'] = ($rest ? $rest['rest_product'] : 0.00);
        } else {
          // если расход НЕ отправлен на склад, выводим остатки по последней строке из движения по сырью и клиенту
          $rest = $this->get_rest(array('store_type_id' => $child['store_type_id'],'client_id' => $child['client_id'],'product_id' => $child['product_id']));
          $child['rest'] = ($rest ? $rest['rest'] : 0.00);
          $rest = $this->get_rest(array('store_type_id' => $child['store_type_id'],'product_id' => $child['product_id']));
          $child['rest_product'] = ($rest ? $rest['rest_product'] : 0.00);
        }
      }
      unset($child);
    }

    return $item;
  }

  function create_coming($params) {
    if ($this->db->insert('store_comings', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  function update_coming($id, $params) {
    if ($this->db->update('store_comings', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_coming($cond) {
    if(!$cond || (!is_int($cond) && !is_array($cond))){
      return false;
    }
    if(is_int($cond)){
      $this->db->where(array('id' => $cond));
    } else {
      $this->db->where($cond);
    }
    if ($this->db->delete('store_comings')) {
      return true;
    }
    return false;
  }
  
  /*
  * Подсчитывает остаток сырья на складе
  * @param params - тип склада, вид вторсырья, ...
  */
  function calculate_rest($params) {
    $this->db->select('(SUM(coming)-SUM(expenditure)) as sum');
    $this->db->where($params);
    return $this->db->get('store_movement_products')->row()->sum;
  }
  
  /*
  * Выводит последний подсчитанный остаток сырья на складе
  * @param params - тип склада, вид вторсырья, ...
  */
  function get_rest($params) {
    $this->db->select('rest, rest_product');
    $this->db->where($params);
    $this->db->order_by('id','DESC');
    return $this->db->get('store_movement_products')->row_array();
  } 

  /*
  * Формирует отсет движения товара на складе
  * @param params - тип склада, клиент, вид вторсырья, ...
  */
  function get_rests($limit = 0, $offset = 0, $where = array(), $order_by = array()) {   
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    $this->db->order_by('id','asc');
    
    if ($where) {
      $this->db->where($where);
    }
    $items = $this->db->get('store_movement_products')->result_array();
    foreach ($items as $key => &$item) {
      $item['client'] = $this->clients_model->get_client(array('id'=>$item['client_id']));
      $item['product'] = $this->products_model->get_product(array('id' => $item['product_id']));
    }
    unset($item);
    return $items;
  }
  
  function get_rests_cnt($where = '') {
    if ($where) {
      $this->db->where($where);
    }
    return $this->db->count_all_results('store_movement_products');
  }

  /*
  * Добавление строки по движению сырья на складе
  */
  function create_movement_products($params) {
    if ($this->db->insert('store_movement_products', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  function update_movement_products($id, $params) {
    if ($this->db->update('store_movement_products', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_movement_products($params) {
    if ($this->db->delete('store_movement_products', $params)) {
      return true;
    }
    return false;
  }

  /**
  * Список расходов
  */
  function get_expenditures($limit = 0, $offset = 0, $where = array(), $order_by = array(), $product_id = array()) {
    $this->db->select('store_expenditures.*');
    //для проверки прав на работу по всем клиентам
    if(is_array($where) && @$where['clients.admin_id']){
      $this->db->join('clients','clients.id = store_expenditures.client_id');      
    }
    if ($where) {
      $this->db->where($where);
    }
    if ($product_id) {
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      $this->db->join('store_expenditures t2','t2.parent_id = store_expenditures.id');
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
    $this->db->group_by('store_expenditures.id');
    $items = $this->db->get('store_expenditures')->result_array();
    unset($where);
    foreach ($items as $key => &$item) {
      if($item['client_id']){
        $item['client'] = $this->clients_model->get_client(array('id'=>$item['client_id']));
        if($item['client']){
          $item['client_title'] = $item['client']['title_full'];
        }
      }
      if($item['store_workshop_id']){
        $item['workshop'] = $this->workshops_model->get_workshop(array('id'=>$item['store_workshop_id']));
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
            $where .= 'pr_store_expenditures.product_id = '.$value;
          }
          $where .= ')';
        }
        $item['childs'] = $this->get_expenditures(0,0,$where);
        $item['gross'] = $item['net'] = $item['price'] = $item['sum'] = 0;
        foreach ($item['childs'] as $key => &$child) {
          $child['product'] = $this->products_model->get_product(array('id' => $child['product_id']));
          $item['gross'] += $child['gross'];
          $item['net'] += $child['net'];
        }
        unset($child);
      }
    }
    unset($item);
    
    return $items;
  }
  
  /**
  * Количество расходов по условию
  */
  function get_expenditures_cnt($where = '', $product_id = array()) {
    $this->db->select('COUNT(DISTINCT(pr_store_expenditures.id)) as cnt');
    //для проверки прав на работу по всем клиентам
    if(is_array($where) && @$where['clients.admin_id']){
      $this->db->join('clients','clients.id = store_expenditures.client_id');      
    }
    if ($where) {
      $this->db->where($where);
    }
    if ($product_id) {
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      $this->db->join('store_expenditures t2','t2.parent_id = store_expenditures.id');
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
    return $this->db->get('store_expenditures')->row()->cnt;
  }

  /**
  * Одна единица расхода
  */
  function get_expenditure($where = array()) {
    $this->db->select('store_expenditures.*');
    $item = $this->db->get_where('store_expenditures', $where)->row_array();
    if($item){
      if($item['client_id']){
        $item['client'] = $this->clients_model->get_client(array('id'=>$item['client_id']));
        if($item['client']){
          $item['client_title'] = $item['client']['title'];
          if($item['client']['city_id']){
            $item['city'] = $this->cities_model->get_city(array('id' => $item['client']['city_id']));
          }
        }
      }
      $item['childs'] = $this->get_expenditures(0,0,array('parent_id'=>$item['id']),array('id'=>'asc'));
      foreach ($item['childs'] as $key => &$child) {
        $child['product'] = $this->products_model->get_product(array('id' => $child['product_id']));
        // остаток сырья на складе по клиенту
        if($child['active']){
          // если расход отправлен на склад, выводим остатки на момент добавления расхода на склад
          $rest = $this->get_rest(array('expenditure_id' => $child['parent_id'],'client_id' => $child['client_id'],'product_id' => $child['product_id']));
          $child['rest'] = ($rest ? $rest['rest'] : 0.00);
          $child['rest_product'] = ($rest ? $rest['rest_product'] : 0.00);
        } else {
          // если расход НЕ отправлен на склад, выводим остатки по последней строке из движения по сырью и клиенту
          $rest = $this->get_rest(array('store_type_id' => $child['store_type_id'],'client_id' => $child['client_id'],'product_id' => $child['product_id']));
          $child['rest'] = ($rest ? $rest['rest'] : 0.00);
          $rest = $this->get_rest(array('store_type_id' => $child['store_type_id'],'product_id' => $child['product_id']));
          $child['rest_product'] = ($rest ? $rest['rest_product'] : 0.00);
        }
      }
      unset($child);
    }

    return $item;
  }

  function create_expenditure($params) {
    if ($this->db->insert('store_expenditures', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  function update_expenditure($id, $params) {
    if ($this->db->update('store_expenditures', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_expenditure($cond) {
    if(!$cond || (!is_int($cond) && !is_array($cond))){
      return false;
    }
    if(is_int($cond)){
      $this->db->where(array('id' => $cond));
    } else {
      $this->db->where($cond);
    }
    if ($this->db->delete('store_expenditures')) {
      return true;
    }
    return false;
  }
}