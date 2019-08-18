<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acceptances_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('cities/models/cities_model');
    $this->load->model('clients/models/clients_model');
    $this->load->model('products/models/products_model');
  }

  function get_acceptances($limit = 0, $offset = 0, $where = array(), $order_by = array(), $product_id = array()) {
    $this->db->select('
      client_acceptances.*,
      clients.title_full as client_title,
      client_childs.title_full as client_child_title, 
      status.color as status_color,
      payment.parent_id as payment_id,
      client_acceptance_emails.id as email
      ');
    $this->db->join('clients','clients.id = client_acceptances.client_id');
    $this->db->join('clients as client_childs','client_childs.id = client_acceptances.client_child_id','left');
    $this->db->join('client_acceptance_statuses as status','status.id = client_acceptances.status_id','left');
    $this->db->join('client_acceptance_payments as payment','payment.acceptance_id = pr_client_acceptances.id  AND payment.client_id = pr_client_acceptances.client_id AND (payment.client_child_id = pr_client_acceptances.client_child_id OR payment.client_child_id IS NULL)', 'left');
    $this->db->join('client_acceptance_emails','client_acceptance_emails.acceptance_id = client_acceptances.id','left');

    if ($where) {
      $this->db->where($where);
    }
    if ($product_id) {
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      $this->db->join('client_acceptances t2','t2.parent_id = client_acceptances.id');
      // join-им чтобы вывести отчет по группе продукции
      $this->db->join('products t3','t3.id = t2.product_id');
      $product_where = '';
      if ($where) {
        $product_where .= '(';
      }
      foreach ($product_id as $key => $value) {
        if($key != 0){
          $product_where .= ' OR ';
        }
        $product_where .= 't3.id = '.$value.' OR t3.parent_id = '.$value;
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
      $this->db->order_by('date','desc');
      $this->db->order_by('id','asc');
    }
    $this->db->group_by('client_acceptances.id');
    $items = $this->db->get('client_acceptances')->result_array();
    // echo $this->db->last_query();exit;
    unset($where);
    foreach ($items as $key => &$item) {
      if(is_null($item['parent_id'])){
        $this->db->select('
          client_acceptances.*,
          t2.title_full as product_title,
          store_comings.weight_defect as coming_weight_defect
        ');
        // join-им чтобы вывести название товара и отчет по группе продукции
        $this->db->join('products t2','t2.id = client_acceptances.product_id');
        // приход, чтобы вывести в таблице актов %засора прихода
        $this->db->join('store_comings','store_comings.id = client_acceptances.store_coming_id','left');
        // Делаем запрос на дочерние акты, для отображения видов сырья в акте
        $where = 'client_acceptances.parent_id = '.$item['id'];
        if ($product_id) {          
          $where .= ' AND (';
          foreach ($product_id as $key => $value) {
            if($key != 0){
              $where .= ' OR ';
            }
            $where .= 't2.id = '.$value.' OR t2.parent_id = '.$value;
          }
          $where .= ')';
        }
        $this->db->where($where);
        $this->db->order_by('client_acceptances.order','asc');
        $this->db->order_by('client_acceptances.id','asc');
        $item['childs'] = $this->db->get('client_acceptances')->result_array();

        //считаем общие параметры
        $item['gross'] = $item['net'] = $item['price'] = $item['sum'] = 0;
        foreach ($item['childs'] as $key => &$child) {
          $child['sum'] = $child['price']*$child['net'];
          $item['gross'] += $child['gross'];
          $item['net'] += $child['net'];
          $item['price'] += ($child['price']*$child['net']);
          $item['sum'] = $item['price']+$item['add_expenses'];
        }
        unset($child);
      }
    }
    unset($item);
    
    return $items;
  }
  
  function get_acceptances_cnt($where = '', $product_id = array()) {
    $this->db->select('COUNT(DISTINCT(pr_client_acceptances.id)) as cnt');
    $this->db->join('clients','clients.id = client_acceptances.client_id');

    if ($where) {
      $this->db->where($where);
    }
    if ($product_id) {
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      $this->db->join('client_acceptances t2','t2.parent_id = client_acceptances.id');
      // join-им чтобы вывести отчет по группе продукции
      $this->db->join('products t3','t3.id = t2.product_id');
      $product_where = '';
      if ($where) {
        $product_where .= '(';
      }
      foreach ($product_id as $key => $value) {
        if($key != 0){
          $product_where .= ' OR ';
        }
        $product_where .= 't3.id = '.$value.' OR t3.parent_id = '.$value;
      }
      if ($where) {
        $product_where .= ')';
      }
      $this->db->where($product_where);
    }
    return $this->db->get('client_acceptances')->row()->cnt;
  }
  
  function get_acceptances_total($where = '', $product_id = array()) {
    $this->db->select('SUM(client_acceptance_childs.price*client_acceptance_childs.net)+SUM(pr_client_acceptances.add_expenses) as sum_total');
    $this->db->select('SUM(client_acceptance_childs.price*client_acceptance_childs.net) as sum');
    $this->db->select('SUM(pr_client_acceptances.add_expenses) as add_expenses');
    $this->db->select('SUM(client_acceptance_childs.gross) as gross');
    $this->db->select('SUM(client_acceptance_childs.net) as net');
    $this->db->join('client_acceptances as client_acceptance_childs','client_acceptances.id = client_acceptance_childs.parent_id');
    $this->db->join('clients','clients.id = client_acceptances.client_id');

    if ($where) {
      $this->db->where($where);
    }
    if ($product_id) {
      if(!is_array($product_id)){
        $product_id = array($product_id);
      }
      // join-им чтобы вывести отчет по группе продукции
      $this->db->join('pr_products','pr_products.id = client_acceptance_childs.product_id');
      $product_where = '';
      if ($where) {
        $product_where .= '(';
      }
      foreach ($product_id as $key => $value) {
        if($key != 0){
          $product_where .= ' OR ';
        }
        $product_where .= 'pr_products.id = '.$value.' OR pr_products.parent_id = '.$value;
      }
      if ($where) {
        $product_where .= ')';
      }
      $this->db->where($product_where);
    }

    return $this->db->get('client_acceptances')->row_array();
  }

  function get_acceptance($where = array(), $full = true) {
    $this->db->select('client_acceptances.*,
      clients.title_full as client_title, 
      clients.admin_id as client_admin_id,
      clients.email as client_email,
      client_childs.title_full as client_child_title,
      client_childs.admin_id as client_child_admin_id,
      status.title as status_title,
      client_childs.email as client_child_email,
      payment.parent_id as payment_id,
      payment.id as payment_acceptance_id');
    // данные по клиенту
    $this->db->join('clients','clients.id = client_acceptances.client_id');
    // данные по компании, если указан client_child_id
    $this->db->join('clients as client_childs','client_childs.id = client_acceptances.client_child_id','left');
    $this->db->join('client_acceptance_statuses as status','status.id = client_acceptances.status_id','left');
    $this->db->join('client_acceptance_payments as payment','payment.acceptance_id = pr_client_acceptances.id  AND payment.client_id = pr_client_acceptances.client_id AND (payment.client_child_id = pr_client_acceptances.client_child_id OR payment.client_child_id IS NULL)', 'left');
    $item = $this->db->get_where('client_acceptances', $where)->row_array();

    if($item && $full){
      $item['client'] = array(
        'id'          => $item['client_id'],
        'admin_id'    => $item['client_admin_id'],
        'client_title'=> $item['client_title'],
        'email'       => $item['client_email'],
      );
      $item['client_child'] = array(
        'id'          => $item['client_child_id'],
        'admin_id'    => $item['client_child_admin_id'],
        'client_title'=> $item['client_child_title'],
        'email'       => $item['client_child_email'],
      );
      $item['childs'] = $this->get_acceptances(0,0,array('client_acceptances.parent_id'=>$item['id']),array('order'=>'asc','id'=>'asc'));
      foreach ($item['childs'] as $key => &$child) {
        $child['product'] = $this->products_model->get_product(array('id' => $child['product_id']));
        $child['sum'] = $child['price']*$child['net'];
        $item['gross'] += $child['gross'];
        $item['net'] += $child['net'];
        $item['price'] += ($child['price']*$child['net']);
        $item['sum'] = $item['price']+$item['add_expenses'];
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

  function get_acceptance_emails($where = array(), $order_by = array(), $limit = 0, $offset = 0, $group_by = array()) {
    $this->db->select('client_acceptance_emails.*,admins.username as username');
    if ($order_by) {
      foreach ($order_by as $field => $dest) {
        $this->db->order_by($field,$dest);
      }
    } else {
      $this->db->order_by('tm','desc');
    }
    if ($group_by) {
      foreach ($group_by as $key => $field) {
        $this->db->group_by($field);
      }
    }
    if ($where) {
      $this->db->where($where);
    }
    $this->db->join('admins','admins.id=client_acceptance_emails.admin_id');
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    $items = $this->db->get('client_acceptance_emails')->result_array();
    // echo $this->db->last_query();

    return $items;
  }

  function create_acceptance_email($params) {
    if ($this->db->insert('client_acceptance_emails', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  /*
  * Метод для проверки параметров,указанных в вторсырье
  * @param: $field_param - поле вторсырья, котрое надо проверить
  * @return: boolean
  */
  function check_acceptance_products($acceptance_id,$field_param){
    $item = $this->get_acceptance(array('client_acceptances.id'=>(int)$acceptance_id));
    if(!$item) return false;
    
    foreach ($item['childs'] as $key => $child) {
      if(!$child[$field_param]){
        return false;
      }
    }

    return true;
  }


  /*
  * Возвращает список статусов для актов приемки
  */
  function get_acceptance_statuses($where = array(), $order_by = array()){
    if ($where) {
      $this->db->where($where);
    }
    if ($order_by) {
      foreach ($order_by as $field => $dest) {
        $this->db->order_by($field,$dest);
      }
    }

    $items = $this->db->get('pr_client_acceptance_statuses')->result_array();
    
    return $items;
  }

  function get_acceptance_status($where = array()) {
    $item = $this->db->get_where('pr_client_acceptance_statuses', $where)->row_array();
    return $item;
  }

  function create_acceptance_status($params) {
    if ($this->db->insert('pr_client_acceptance_statuses', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }

  function update_acceptance_status($id, $params) {
    if ($this->db->update('pr_client_acceptance_statuses', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
}