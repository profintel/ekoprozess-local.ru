<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acceptance_payments_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
    
  }

  /***  раздел Бухгалтерия  ***/

  function get_acceptance_payments($limit = 0, $offset = 0, $where = array(), $order_by = array()) {
    $this->db->select('
      client_acceptance_payments.id,
      client_acceptance_payments.method,
      client_acceptance_payments.acceptance_id,
      client_acceptance_payments.sale_percent,
      client_acceptance_payments.comment,
      client_acceptances.date,
      client_acceptances.client_id,
      client_acceptances.add_expenses,
      client_acceptances.status_id,
      client_acceptance_childs.price,
      client_acceptance_childs.net,
      SUM(client_acceptance_childs.price * client_acceptance_childs.net) as sum,
      clients.title_full as client_title,
      client_childs.id as client_child_id,
      client_childs.title_full as client_child_title,
      status.color as status_color');
    $this->db->join('client_acceptances','client_acceptances.id = client_acceptance_payments.acceptance_id');
    $this->db->join('client_acceptances as client_acceptance_childs','client_acceptances.id = client_acceptance_childs.parent_id');
    $this->db->join('clients','clients.id = client_acceptances.client_id');
    $this->db->join('clients as client_childs','client_childs.id = client_acceptances.client_child_id','left');
    $this->db->join('client_acceptance_statuses as status','status.id = client_acceptances.status_id','left');
    if ($where) {
      $this->db->where($where);
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
      $this->db->order_by('client_acceptance_payments.id','asc');
    }
    $this->db->group_by('client_acceptance_payments.id');
    $items = $this->db->get('client_acceptance_payments')->result_array();
    foreach ($items as $key => &$item) {
      // если оплата по наличному рассчету смотрим карту клиента из поля Деятельность и вид расчета
      if($item['method']=='cash' && $item['client_child_id']){
        $item['client_params'] = $this->main_model->get_params('client_params', $item['client_child_id']);
      }
      if($item['method']=='cash' && !$item['client_child_id']){
        $item['client_params'] = $this->main_model->get_params('client_params', $item['client_id']);
      }
      // сумма с вычетом доп.стоимости акта
      $item['sum'] = $item['sum'] - $item['add_expenses'];
      // учитываем скидку
      if($item['method'] == 'cash' && $item['sale_percent']){
        $item['sum'] = $item['sum'] - $item['sum']*($item['sale_percent']/100);
      }
    }
    // echo $this->db->last_query();exit;
    return $items;
  }
  
  function get_acceptance_payments_cnt($where = '') {
    $this->db->select('COUNT(DISTINCT(pr_client_acceptance_payments.id)) as cnt');
    $this->db->join('pr_client_acceptances','pr_client_acceptances.id = client_acceptance_payments.acceptance_id');
    $this->db->join('client_acceptance_statuses as status','status.id = client_acceptances.status_id','left');

    if ($where) {
      $this->db->where($where);
    }
    return $this->db->get('client_acceptance_payments')->row()->cnt;
  }

  function get_acceptance_payment($where = array(), $full = true) {
    $this->db->select('
      client_acceptance_payments.*,
      client_acceptances.date,
      client_acceptances.status_id,
      client_acceptances.client_id,
      client_acceptances.add_expenses,
      clients.title_full as client_title, 
      SUM(client_acceptance_childs.price * client_acceptance_childs.net) as sum,
      clients.admin_id as client_admin_id');
    $this->db->join('client_acceptances','client_acceptances.id = client_acceptance_payments.acceptance_id');
    $this->db->join('client_acceptances as client_acceptance_childs','client_acceptances.id = client_acceptance_childs.parent_id');
    $this->db->join('clients','clients.id = client_acceptances.client_id');
    $this->db->limit(1);
    $this->db->group_by('client_acceptance_payments.id');
    $item = $this->db->get_where('client_acceptance_payments', $where)->row_array();
    
    $item['childs'] = $this->acceptances_model->get_acceptances(0,0,array('client_acceptances.parent_id'=>$item['id']),array('order'=>'asc','id'=>'asc'));
    foreach ($item['childs'] as $key => &$child) {
      $child['product'] = $this->products_model->get_product(array('id' => $child['product_id']));
      $child['sum'] = $child['price']*$child['net'];
      $item['gross'] += $child['gross'];
      $item['net'] += $child['net'];
      $item['price'] += ($child['price']*$child['net']);
      $item['sum'] = $item['price']-$item['add_expenses'];
    }
    unset($child);

    return $item;
  }
  
  function create_acceptance_payment($params) {
    // копируем все параметры указанного акта приемки
    $item = $this->acceptances_model->get_acceptance(array('client_acceptances.id'=>(int)$params['acceptance_id']));
    if(!$item){
      return false;
    }
    array_merge($params, array(
      'acceptance_id'         => $item['id'],
      'acceptance_parent_id'  => $item['parent_id'],
      'client_id'             => $item['client_id'],
      'client_child_id'       => $item['client_child_id'],
      'store_coming_id'       => $item['store_coming_id'],
      'date'                  => $item['date'],
      'date_time'             => $item['date_time'],
      'status_id'             => $item['status_id'],
      'company'               => $item['company'],
      'date_num'              => $item['date_num'],
      'transport'             => $item['transport'],
      'product_id'            => $item['product_id'],
      'price'                 => $item['price'],
      'weight_ttn'            => $item['weight_ttn'],
      'weight_pack'           => $item['weight_pack'],
      'weight_defect'         => $item['weight_defect'],
      'cnt_places'            => $item['cnt_places'],
      'cnt_places'            => $item['cnt_places'],
      'gross'                 => $item['gross'],
      'net'                   => $item['net'],
      'add_expenses'          => $item['add_expenses'],
      'comment_acceptance'    => $item['comment'],
    ));

    // копируем параметры прихода
    $item['childs'] = $this->acceptances_model->get_acceptances(0,0,array('client_acceptances.parent_id'=>$item['id']),array('order'=>'asc','id'=>'asc'));
    
    $this->db->trans_begin();
    $this->db->insert('client_acceptance_payments', $params);

    foreach ($item['childs'] as $key => $child) {
      $child_params = array(
        'acceptance_id'         => $child['id'],
        'acceptance_parent_id'  => $child['parent_id'],
        'client_id'             => $child['client_id'],
        'client_child_id'       => $child['client_child_id'],
        'store_coming_id'       => $child['store_coming_id'],
        'date'                  => $child['date'],
        'date_time'             => $child['date_time'],
        'status_id'             => $child['status_id'],
        'company'               => $child['company'],
        'date_num'              => $child['date_num'],
        'transport'             => $child['transport'],
        'product_id'            => $child['product_id'],
        'price'                 => $child['price'],
        'weight_ttn'            => $child['weight_ttn'],
        'weight_pack'           => $child['weight_pack'],
        'weight_defect'         => $child['weight_defect'],
        'cnt_places'            => $child['cnt_places'],
        'cnt_places'            => $child['cnt_places'],
        'gross'                 => $child['gross'],
        'net'                   => $child['net'],
        'add_expenses'          => $child['add_expenses'],
        'comment_acceptance'    => $child['comment'],
      );
      $this->db->insert('client_acceptance_payments', $child_params);
    }

    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();

    return true;
  }

  function update_acceptance_payment($id, $params) {
    if ($this->db->update('client_acceptance_payments', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_acceptance_payment($id) {
    if ($this->db->delete('client_acceptance_payments', array('id' => $id))) {
      return true;
    }
    return false;
  }

  function get_acceptance_payments_emails($where = array(), $order_by = array(), $limit = 0, $offset = 0, $group_by = array()) {
    $this->db->select('pr_client_acceptance_payments_emails.*,admins.username as username');
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
    $this->db->join('admins','admins.id=pr_client_acceptance_payments_emails.admin_id');
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    $items = $this->db->get('pr_client_acceptance_payments_emails')->result_array();
    // echo $this->db->last_query();

    return $items;
  }

  function create_acceptance_payments_email($params) {
    if ($this->db->insert('pr_client_acceptance_payments_emails', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }
  
  function delete_acceptances_payments_emails($where = array()) {
    if ($this->db->delete('pr_client_acceptance_payments_emails',$where)) {
      return true;
    }
    return false;
  }

}