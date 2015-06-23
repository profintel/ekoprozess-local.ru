<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Subscribes_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
		
    $this->load->model('accounts/models/accounts_model');
  }
  
  /** 
  *  Рассылки
  **/
  function get_subscribes($type = "") {
    if ($type) {
      $this->db->where(array('type' => $type));
    }
    return $this->db->get('subscribes')->result_array();
  }
  
  function get_subscribe($where = array()) {
    $item = $this->db->get_where('subscribes', $where)->row_array();
    if ($item) {
      $item['params'] = $this->main_model->get_params('subscribes', $item['id']);

      $item['projects'] = array();
      $projects = $this->db->get_where('pr_subscribe_projects', array('subscribe_id' => $item['id']))->result_array();
      foreach ($projects as $project) {
        $item['projects'][] = $project['project_id'];
      } 
    }
    return $item;
  } 
  
  function create_subscribe($params) {
    if ($this->db->insert('subscribes', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  }  
  
  function edit_subscribe($id, $params) {
    if ($this->db->update('subscribes', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_subscribe($id) {
    $messages = $this->db->get_where('subscribe_messages', array('subscribe_id' => $id))->result_array();
    foreach ($messages as $message) {
      $this->delete_subscribe_message($message['id']);
    }
    $this->db->delete('subscribes', array('id' => $id));
    $this->db->delete('pr_params', array('category' => 'subscribes'));
  }  
  
  function set_subscribe_projects($id, $projects) {
    $this->db->query('DELETE FROM pr_subscribe_projects WHERE subscribe_id = '. $id .' AND project_id NOT IN ('. ($projects ? implode(',', $projects) : 0) .')');
    $now = array();
    $now_projects = $this->db->get_where('pr_subscribe_projects', array('subscribe_id' => $id))->result_array();
    foreach ($now_projects as $project) {
      $now[] = $project['project_id'];
    }
    foreach ($projects as $project_id) {
      if (!in_array($project_id, $now)) {
        if (!$this->db->insert('pr_subscribe_projects', array('subscribe_id' => $id, 'project_id' => $project_id))) {
          return false;
        }
      }
    }
    return true;
  }
  
  function set_subscribe_user_emails() {
    $users = $this->accounts_model->get_users(array(),true); 
    $emails = $this->get_subscribe_emails();
    $now = array();
    foreach ($emails as $email) {
      $now[] = $email['user_id'];
    }
    foreach ($users as $user) {
      $user_email = "";
      if (!in_array($user['id'], $now)) {
        if (preg_match('/^[-0-9a-z_\.]+@[-0-9a-z^\.]+\.[a-z]{2,4}$/i', $user['username'])) {
          $user_email = $user['username'];
        }
        if (isset($user['params']['email']) && $user['params']['email']) { 
          $user_email = $user['params']['email'];
        }
        if ($user_email && !$this->db->insert('subscribes_emails', array('user_id' => $user['id'], 'email' => $user_email))) {
          continue;
        }
      }
    }
    return true;
  }

  function set_subscribe_emails_subscribes($id) {    
    $now = array();
    $now_emails = $this->db->get_where('subscribes_emails_subscribes', array('subscribe_id' => $id))->result_array();
    foreach ($now_emails as $email) {
      $now[] = $email['email_id'];
    }
    $emails = $this->get_subscribe_emails();
    foreach ($emails as $email) {
      if (!in_array($email['id'], $now)) {
        if (!$this->db->insert('subscribes_emails_subscribes', array('subscribe_id' => $id, 'email_id' => $email['id']))) {
          return false;
        }
      }
    }
    return true;
  }
  
  /**
  * Сообщения рассылок
  **/
  function get_subscribe_messages_count($where = array()) {
    if ($where) {
      $this->db->where($where);
    }
    $this->db->select('count(*) as cnt');
    return $this->db->get('subscribe_messages')->row()->cnt;
  }
  
  function get_subscribe_messages($where = array(), $limit = 0, $offset = 0) {
    if ($where) {
      $this->db->where($where);
    }
    return $this->db->get('subscribe_messages')->result_array();
  }
    
  function get_subscribe_emails($where = array()) {
    if ($where) {
      $this->db->where($where);
    }
    return $this->db->get('subscribes_emails')->result_array();
  }
  
  function get_subscribe_message_files($message_id) {
    $this->db->like('name', 'file_');
    return $this->db->get_where('pr_params', array('category' => 'subscribe_messages', 'owner_id' => $message_id))->result_array();
  }
  
  function get_subscribe_message($where = array()) {
    $item = $this->db->get_where('subscribe_messages', $where)->row_array();
    if ($item) {
      $item['files'] = $this->get_subscribe_message_files($item['id']);
    }
    return $item;
  } 
  
  function create_subscribe_message($params) {
    if ($this->db->insert('subscribe_messages', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  } 

  function delete_subscribe_message_file($param_id) {
    $item = $this->db->get_where('pr_params', array('id' => $param_id))->row_array();
    @unlink($_SERVER['DOCUMENT_ROOT'] . $item['value']);
    $this->db->delete('pr_params', array('id' => $param_id));
  }   

  function edit_subscribe_message($id, $params) {
    if ($this->db->update('subscribe_messages', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_subscribe_message($message_id) {
    $params = $this->db->query('SELECT id FROM pr_params WHERE category="subscribe_messages" AND owner_id = '.$message_id.' AND name LIKE "file_%"')->result_array();
    foreach($params as $param){
      $this->delete_subscribe_message_file($param['id']) ;
    }
    
    $this->db->delete('subscribe_messages', array('id' => $message_id));
  }
  
  /**
  * Сообщения sms рассылок
  **/
  function get_subscribe_sms_count($where = array()) {
    if ($where) {
      $this->db->where($where);
    }
    $this->db->select('count(*) as cnt');
    return $this->db->get('subscribe_sms')->row()->cnt;
  }
  
  function get_subscribe_sms_all($where = array(), $limit = 0, $offset = 0) {
    if ($where) {
      $this->db->where($where);
    }
    return $this->db->get('subscribe_sms')->result_array();
  }
    
  function get_subscribe_phones($where = array()) {
    if ($where) {
      $this->db->where($where);
    }
    return $this->db->get('subscribes_phones')->result_array();
  }
  
  function get_subscribe_sms($where = array()) {
    $item = $this->db->get_where('subscribe_sms', $where)->row_array();
    return $item;
  } 
  
  function create_subscribe_sms($params) {
    if ($this->db->insert('subscribe_sms', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
    return false;
  } 
  
  function edit_subscribe_sms($id, $params) {
    if ($this->db->update('subscribe_sms', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function delete_subscribe_sms($sms_id) {
    $this->db->delete('subscribe_sms', array('id' => $sms_id));
  }
  
  function set_subscribe_phones_subscribes($id) {    
    $now = array();
    $now_phones = $this->db->get_where('subscribes_phones_subscribes', array('subscribe_id' => $id))->result_array();
    foreach ($now_phones as $phone) {
      $now[] = $phone['phone_id'];
    }
    $phones = $this->get_subscribe_phones();
    foreach ($phones as $phone) {
      if (!in_array($phone['id'], $now)) {
        if (!$this->db->insert('subscribes_phones_subscribes', array('subscribe_id' => $id, 'phone_id' => $phone['id']))) {
          return false;
        }
      }
    }
    return true;
  }
  
  function set_subscribe_user_phones() {
    $users = $this->accounts_model->get_users(array(),true); 
    $phones = $this->get_subscribe_phones();
    $now = array();
    foreach ($phones as $phone) {
      $now[] = $phone['user_id'];
    }
    foreach ($users as $user) {
      $user_phone = "";
      if (!in_array($user['id'], $now)) {
        if (isset($user['phone']) && $user['phone']) {
          $user_phone = $user['phone'];
        }
        if (isset($user['params']['phone']) && $user['params']['phone']) { 
          $user_phone = $user['params']['phone'];
        }
        
        if ($user_phone) {
          $phone = preg_replace("/[^0-9]+/", "", $user_phone);
          $phone = preg_replace("/(?:\\+)?[78](?:\\s|-)*[\\(]?(\\d{3})?[\\)]?(?:\\s|-)*(\\d{3})(?:\\s|-)*(\\d{2})(?:\\s|-)*(\\d{2})/", "+7$1$2$3$4", $user_phone);
          if(!preg_match("/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{10}$/", $user_phone)){
            continue;
          }
          if (!$this->db->insert('subscribes_phones', array('user_id' => $user['id'], 'phone' => $user_phone))) {
            continue;
          }
        }
      }
    }
    return true;
  }
}