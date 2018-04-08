<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acceptance_payments_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
    
  }

  /***  раздел Бухгалтерия  ***/

  function get_acceptance_payments($limit = 0, $offset = 0, $where = array(), $order_by = array(), $childs = false) {
    $this->db->select('
      client_acceptance_payments.*,
      client_acceptance_payment_parent.comment,
      (SUM(client_acceptance_payment_childs.price * client_acceptance_payment_childs.net) + pr_client_acceptance_payments.add_expenses) as sumAcceptance,
      (
        (
          SUM(client_acceptance_payment_childs.price * client_acceptance_payment_childs.net)    
            + 
          pr_client_acceptance_payments.add_expenses
        )
      - 
        (
          (
            SUM(client_acceptance_payment_childs.price * client_acceptance_payment_childs.net)       
              + 
            pr_client_acceptance_payments.add_expenses
          )
          *
          pr_client_acceptance_payments.sale_percent/100
        )
      ) as sum,
      clients.title_full as client_title,
      client_childs.id as client_child_id,
      client_childs.title_full as client_child_title,
      status.color as status_color');
    $this->db->join('client_acceptance_payments as client_acceptance_payment_parent','client_acceptance_payments.parent_id = client_acceptance_payment_parent.id','left');
    $this->db->join('client_acceptance_payments as client_acceptance_payment_childs','client_acceptance_payments.id = client_acceptance_payment_childs.parent_id','left');
    $this->db->join('clients','clients.id = client_acceptance_payments.client_id');
    $this->db->join('clients as client_childs','client_childs.id = client_acceptance_payments.client_child_id','left');
    $this->db->join('client_acceptances as acceptance','acceptance.id = client_acceptance_payments.acceptance_id','left');
    $this->db->join('client_acceptance_statuses as status','status.id = client_acceptance_payments.status_id','left');
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
      $this->db->order_by('client_acceptance_payments.tm','desc');
    }
    $this->db->group_by('client_acceptance_payments.id');
    $items = $this->db->get('client_acceptance_payments')->result_array();
    // echo $this->db->last_query();exit;
    foreach ($items as $key => &$item) {
      // если оплата по наличному рассчету смотрим карту клиента из поля Деятельность и вид расчета
      if($item['method']=='cash' && $item['client_child_id']){
        $item['client_params'] = $this->main_model->get_params('client_params', $item['client_child_id']);
      }
      if($item['method']=='cash' && !$item['client_child_id']){
        $item['client_params'] = $this->main_model->get_params('client_params', $item['client_id']);
      }

      if($childs){
        $item['childs'] =  $this->db->get_where('client_acceptance_payments', array('client_acceptance_payments.parent_id'=>$item['id']))->result_array();
        foreach ($item['childs'] as $key => &$child) {
          $child['sum'] = $child['price']*$child['net'];
          $child['product'] = $this->products_model->get_product(array('id' => $child['product_id']));
        }
        unset($child);
      }
    }
    // echo $this->db->last_query();exit;
    return $items;
  }
  
  function get_acceptance_payments_cnt($where = '') {
    $this->db->select('COUNT(DISTINCT(pr_client_acceptance_payments.id)) as cnt');

    if ($where) {
      $this->db->where($where);
    }
    return $this->db->get('client_acceptance_payments')->row()->cnt;
  }

  function get_acceptance_payment($where = array(), $full = true) {
    $this->db->select('
      client_acceptance_payments.*,
      client_acceptance_payment_parent.comment as comment_parent');
    $this->db->join('client_acceptance_payments as client_acceptance_payment_parent','client_acceptance_payments.parent_id = client_acceptance_payment_parent.id','left');
    if($full){
      $this->db->select('
      SUM(client_acceptance_payment_childs.gross) as gross,
      SUM(client_acceptance_payment_childs.net) as net,
      (SUM(client_acceptance_payment_childs.price * client_acceptance_payment_childs.net) + pr_client_acceptance_payments.add_expenses) as sumAcceptance,
      (
        (
          SUM(client_acceptance_payment_childs.price * client_acceptance_payment_childs.net)    
            + 
          pr_client_acceptance_payments.add_expenses
        )
      - 
        (
          (
            SUM(client_acceptance_payment_childs.price * client_acceptance_payment_childs.net)       
              + 
            pr_client_acceptance_payments.add_expenses
          )
          *
          pr_client_acceptance_payments.sale_percent/100
        )
      ) as sum,
      client_childs.title_full as client_child_title,
      clients.title_full as client_title, 
      clients.admin_id as client_admin_id,
      status.color as status_color,
      status.title as status');
      $this->db->join('clients','clients.id = client_acceptance_payments.client_id','left');
      // данные по компании, если указан client_child_id
      $this->db->join('clients as client_childs','client_childs.id = client_acceptance_payments.client_child_id','left');
      $this->db->join('client_acceptance_payments as client_acceptance_payment_childs','client_acceptance_payments.id = client_acceptance_payment_childs.parent_id','left');
      // данные по статусу акта
      $this->db->join('client_acceptance_statuses as status','status.id = client_acceptance_payments.status_id','left');
    }
    $this->db->limit(1);
    $this->db->group_by('client_acceptance_payments.id');
    $item = $this->db->get_where('client_acceptance_payments', $where)->row_array();
    // echo $this->db->last_query();exit;
    if($item && $full){
      $item['childs'] =  $this->db->get_where('client_acceptance_payments', array('client_acceptance_payments.parent_id'=>$item['id']))->result_array();
      foreach ($item['childs'] as $key => &$child) {
        $child['sum'] = $child['price']*$child['net'];
        $child['product'] = $this->products_model->get_product(array('id' => $child['product_id']));
      }
      unset($child);
    }

    return $item;
  }
  
  function create_acceptance_payment($params) {
    //акт приемки
    $item = $this->acceptances_model->get_acceptance(array('client_acceptances.id'=>(int)$params['acceptance_id']));
    if(!$item){
      return false;
    }
    
    $this->db->trans_begin();

    // если данный акт уже есть в таблице, объединяем оплаты по parent_id
    $payment = $this->get_acceptance_payment(array('client_acceptance_payments.acceptance_id'=>$item['id']));
    if($payment){
      $parent_id = $payment['parent_id'];
    } else {
      // создаем родительскую строку, чтобы можно было прирепит несольк акто к дной строке оплаты
      $this->db->insert('client_acceptance_payments', array('id'=>null));
      $parent_id = $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }

    // создаем дочернюю строку с актом

    // копируем все параметры указанного акта приемки
    $params = array_merge($params, array(
      'parent_id'             => $parent_id,
      'acceptance_id'         => $item['id'],
      'acceptance_parent_id'  => $item['parent_id'],
      'client_id'             => $item['client_id'],
      'client_child_id'       => $item['client_child_id'],
      'store_coming_id'       => $item['store_coming_id'],
      'date'                  => $item['date'],
      'date_time'             => $item['date_time'],
      'status_id'             => 4,
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
    $this->db->insert('client_acceptance_payments', $params);
    $payment_id = $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;

    // копируем параметры прихода
    $item['childs'] = $this->acceptances_model->get_acceptances(0,0,array('client_acceptances.parent_id'=>$item['id']),array('order'=>'asc','id'=>'asc'));
    // var_dump($params);exit;
    foreach ($item['childs'] as $key => $child) {
      $child_params = array(
        'parent_id'             => $payment_id,
        'acceptance_id'         => $child['id'],
        'acceptance_parent_id'  => $child['parent_id'],
        'client_id'             => $child['client_id'],
        'client_child_id'       => $child['client_child_id'],
        'store_coming_id'       => $child['store_coming_id'],
        'date'                  => $child['date'],
        'date_time'             => $child['date_time'],
        'status_id'             => 4,
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

    return $parent_id;
  }

  function update_acceptance_payment($id, $params) {
    if ($this->db->update('client_acceptance_payments', $params, array('id' => (int)$id))) {
      return true;
    }
    return false;
  }

  function set_acceptance_payment($acceptance_id) {
    $acceptance = $this->acceptances_model->get_acceptance(array('client_acceptances.id'=>(int)$acceptance_id));
    if(!$acceptance){
      return false;
    }

    //строка акта в оплате
    $item = $this->get_acceptance_payment(array(
      'pr_client_acceptance_payments.id'=>$acceptance['payment_acceptance_id'],
      'pr_client_acceptance_payments.acceptance_id'=>$acceptance['id'],
      'pr_client_acceptance_payments.client_id'=>$acceptance['client_id'],
      'pr_client_acceptance_payments.client_child_id'=>$acceptance['client_child_id']
    ));
    if(!$item){
      return true;
    }
    // удаляем строки вторсырья
    if (!$this->db->delete('client_acceptance_payments', array('parent_id' => $item['id']))) {
      return false;
    }
    // удаляем акт
    if (!$this->db->delete('client_acceptance_payments', array('id' => $item['id']))) {
      return false;
    }
    // удаляем пустые родительские строки
    $this->delete_acceptances_empty();
    // создаем акт
    $payment_id = $this->create_acceptance_payment(
      array(
        'acceptance_id'   => $item['acceptance_id'],
        'method'          => $item['method'],
        'sale_percent'    => $item['sale_percent'],
        'tm'              => $item['tm'],
      ));

    return $payment_id;
  }
  
  function delete_acceptance_payment($id) {
    if ($this->db->delete('client_acceptance_payments', array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_acceptances_empty() {
    if ($this->db->query('DELETE t1 FROM pr_client_acceptance_payments as t1 LEFT JOIN pr_client_acceptance_payments as childs ON childs.parent_id = t1.id WHERE t1.parent_id IS NULL AND childs.id IS NULL')) {
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