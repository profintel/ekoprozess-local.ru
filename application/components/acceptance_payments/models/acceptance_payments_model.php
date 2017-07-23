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
      if($item['client_child_id']){
        $item['client_params'] = $this->main_model->get_params('clients', $item['client_id']);
      }
      if(!$item['client_child_id'] || !$item['client_params']['bank_ru'] || !$item['client_params']['bank_account_ru']){
        $item['client_params'] = $this->main_model->get_params('clients', $item['client_id']);
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
      SUM(client_acceptance_childs.price * client_acceptance_childs.net) as sum,
      clients.admin_id as client_admin_id');
    $this->db->join('client_acceptances','client_acceptances.id = client_acceptance_payments.acceptance_id');
    $this->db->join('client_acceptances as client_acceptance_childs','client_acceptances.id = client_acceptance_childs.parent_id');
    $this->db->join('clients','clients.id = client_acceptances.client_id');
    $this->db->limit(1);
    $this->db->group_by('client_acceptance_payments.id');
    $item = $this->db->get_where('client_acceptance_payments', $where)->row_array();

    return $item;
  }
  
  function create_acceptance_payment($params) {
    if ($this->db->insert('client_acceptance_payments', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
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
}