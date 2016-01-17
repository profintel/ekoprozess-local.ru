<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
      
  }

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
}