<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accounts_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
      
  }  

  /**
  * Группы пользователей
  **/  
  function get_groups($where = array()) {
    if ($where) {
      $this->db->where($where);
    }
    $this->db->order_by('title');
    $items = $this->db->get('pr_groups')->result_array();
    foreach ($items as &$item) {
      $this->db->select('COUNT(DISTINCT pr_user_groups.user_id) as cnt');
      $this->db->where('user_groups.group_id = '.$item['id']);
      $this->db->join('users', 'users.id = user_groups.user_id');
      $item['users_cnt'] = $this->db->get('user_groups')->row()->cnt;
    }
    unset($item);
    return $items;
  }
  
  function get_group($id) {
    return $this->db->get_where('pr_groups',array('id' => $id))->row_array();
  }
  
  function create_group($params) {
    if ($this->db->insert('pr_groups', $params)) {
      $id = $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
      
      return $id;
    }
    return false;
  }

  function edit_group($id, $params) {
    if ($this->db->update('pr_groups', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }  
  
  function delete_group($id) {
    if ($this->db->delete('pr_groups', array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  /**
  * Пользователи
  **/  
  function get_users($where = array(), $full = false, $group_id = 0, $limit = 0, $offset = 0) {
    $this->db->where(array('pr_users.deleted' => 0));
    if ($where) {
      $this->db->where($where);
    }
    if ($group_id) {
      $this->db->join('user_groups', 'user_groups.user_id = users.id');
      $this->db->where('user_groups.group_id', $group_id);
      $this->db->group_by('users.id');
    }
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    $this->db->order_by('username');
    $this->db->select('users.*');
    $items =  $this->db->get('pr_users')->result_array();
    if ($full) {
      foreach ($items as &$item) {
        $item['params'] = $this->main_model->get_params('users', $item['id']);
      }
      unset($item);
    }
    return $items;
  }
  
  function get_user($where = array(), $user_field_params = true, $params = true) {
    $this->db->where(array('pr_users.deleted' => 0));
    if ($where) {
      $this->db->where($where);
    }
    $item = $this->db->get('pr_users')->row_array();
    if ($item) {
      $item['groups'] = array('data' => array(), 'ids' => array());
      $this->db->select('groups.system_name as system_name, user_groups.*');
      $this->db->join('groups', 'groups.id = user_groups.group_id');
      $this->db->group_by('user_groups.group_id');
      $groups = $this->db->get_where('user_groups',array('user_id' => $item['id']))->result_array();
      foreach ($groups as $group) {
        $item['groups']['ids'][] = $group['group_id'];
        $item['groups']['system_names'][] = $group['system_name'];
        $item['groups']['data'][$group['group_id']] = $group;
      }      
      
      if ($params) {
        $item['params'] = $this->main_model->get_params('users', $item['id']);
      }
      
      if ($user_field_params) {
        $item['field_params'] = array();
        $field_params = $this->db->get('pr_user_params')->result_array();
        if ($field_params) {
          foreach ($field_params as $field_param) {
            $params = $this->db->get_where('pr_params', array('category' => 'user_params','owner_id' => $field_param['id']))->result_array();
            foreach ($params as $param) {
              $item['field_params'][$field_param['system_name']][$param['name']] = $param['value'];
            }            
          }
        }
      }
    }
    return $item;
  }
  
  function create_user($params) {    
    if ($this->db->insert('pr_users', $params)) {
      $id = $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
      
      return $id;
    }
    return false;
  }
  
  function edit_user($id, $params) {
    if ($this->db->update('pr_users', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }  
  
  function delete_user($id, $full = false) {
    if ($full) {      
      if ($this->db->delete('pr_users', array('id' => $id))) {      
        $this->db->delete('pr_params', array('category' => 'users', 'owner_id' => $id));
        return true;
      }
    } else {
      if ($this->db->update('pr_users', array('deleted' => 1), array('id' => $id))) {
        return true;
      }
    }
    return false;
  }  
  
  function set_user_groups($ids, $groups) {
    if (!is_array($ids)) {
      $ids = array($ids);
    }
    foreach ($ids as $id) {
      $now = array();
      if (!$this->db->query('DELETE FROM pr_user_groups WHERE user_id = '. $id .' AND group_id NOT IN ('. ($groups ? implode(',', $groups) : 0) .')')) {
        return false;
      }
      if ($groups) {
        $now_groups = $this->db->get_where('pr_user_groups', array('user_id' => $id))->result_array();
        foreach ($now_groups as $now_group) {
          $now[] = $now_group['user_id'];
        }
        foreach ($groups as $group_id) {
          if (!in_array($group_id, $now)) {
            if (!$this->db->insert('pr_user_groups', array('user_id' => $id, 'group_id' => $group_id))) {
              return false;
            }
          }
        }
      }
    }
    return true;
  }    
  
  /**Параметры пользователей**/
  function get_user_param($where = array()) {
    if ($where) {
      $this->db->where($where);
    }
    $item = $this->db->get('pr_user_params')->row_array();
    if ($item) {
      $item['params'] = $this->main_model->get_params('user_params', $item['id']);
    }
    return $item;
  }  
  
  function get_user_params($group_id = 0) {    
    if ($group_id) {
      $this->db->where('pr_user_group_params.group_id = '.$group_id);
      $this->db->join('pr_user_group_params', 'pr_user_group_params.user_param_id = pr_user_params.id');
      $this->db->group_by('pr_user_params.id');
    }
    $this->db->select('pr_user_params.*');
    $this->db->order_by('order');
    $items = $this->db->get('pr_user_params')->result_array();
    foreach ($items as &$item) {
      $item['params'] = $this->main_model->get_params('user_params', $item['id']);
    }
    return $items;
  }   
  
  function create_user_param($params) {
    $max_order = $this->db->query('SELECT max(`order`) as max_order FROM pr_user_params')->row()->max_order;
    $params['order'] = $max_order + 1;
    if ($this->db->insert('pr_user_params', $params)) {
      return $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    }
  } 
  
  function edit_user_param($id, $params) {
    if ($this->db->update('pr_user_params', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }

  function set_params($id, $params) {    
    foreach ($params as $param) {
      if (!$this->db->get_where('pr_user_params',array('system_name' => $param['system_name']))->row()) {
        $user_params = array(
          'system_name' => $param['system_name'],
          'title'       => $param['title']
        );

        $languages = $this->languages_model->get_languages(1, 0);
        $param_multiparams = array();
        foreach ($languages as $language) {
          $param_multiparams[$language['name']] = array(
            'name' => $param['title'],
          );
        }
        
        $id_param = $this->create_user_param($user_params);
        if (!$id_param) {
          return false;
        }
        $this->main_model->set_params('user_params', $id_param, $param_multiparams);
      }
      $multiparams[$param['system_name']] = $param['value'];
    }
    
    if ($this->main_model->set_params('users', $id, $multiparams)) {
      return true;
    }
    return false; 
  }
  
  function get_user_group_params() {    
     return $this->db->get('pr_user_group_params')->result_array(); 
  }
  
  function set_user_group_params($params) {    
    if($this->db->query('DELETE FROM pr_user_group_params')) {
      foreach ($params as $param) {
        $this->db->insert('pr_user_group_params', $param);
      }
      return true;
    }
     return false; 
  }
  
  function set_designer_params_active($params) {    
    if($this->db->query('DELETE FROM pr_designer_params_active')) {
      foreach ($params as $param) {
        $this->db->insert('pr_designer_params_active', $param);
      }
      return true;
    }
    return false; 
  }
    
  function user_param_move($user_param, $dest) {
    if ($dest > 0) {
      $this->db->where('order >', $user_param['order']);
      $this->db->order_by('order');
    } else {
      $this->db->where('order <', $user_param['order']);
      $this->db->order_by('order DESC');
    }
    $next = $this->db->get('pr_user_params')->row_array();
    if ($next) {
      $this->db->update('pr_user_params', array('order' => $user_param['order']), array('id' => $next['id']));
      $this->db->update('pr_user_params', array('order' => $next['order']), array('id' => $user_param['id']));
    }
  }
  
  function delete_user_param($id) {
    if ($this->db->delete('pr_user_params', array('id' => $id))) {
      $this->db->delete('pr_params', array('category' => 'user_params', 'owner_id' => $id));
      return true;
    }
    return false;
  }    
}