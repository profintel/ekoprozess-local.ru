<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Permits_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }
  
  function check_access($admin_id, $component, $method = '') {
    $admin = $this->admin_model->get_admin($admin_id);
    if (!$admin) {
      return FALSE;
    }
    
    if ($admin['superuser']) {
      return TRUE;
    }

    $groups = $this->db->get_where('pr_admin_groups', array('admin_id'  => $admin_id))->result_array();
    $groups = array_simple($groups, 'group_id');
    if ($groups) {
      $this->db->where_in('group_id', $groups);
      if ($this->db->get_where('pr_admin_group_permits', array(
        'component' => $component,
        'method'    => NULL
      ))->num_rows()) {
        return TRUE;
      }
      
      $this->db->where_in('group_id', $groups);
      if ($this->db->get_where('pr_admin_group_permits', array(
        'component' => $component,
        'method'    => $method
      ))->num_rows()) {
        return TRUE;
      }
    }
    
    if ($this->db->get_where('permits', array(
      'component' => $component,
      'method'    => NULL,
      'admin_id'  => $admin_id
    ))->num_rows()) {
      return TRUE;
    }
    
    if ($this->db->get_where('permits', array(
      'component' => $component,
      'method'    => $method,
      'admin_id'  => $admin_id
    ))->num_rows()) {
      return TRUE;
    }
    
    return FALSE;
  }
  
  function set_permits($admins, $component, $method = '') {
    if (!$method) {
      $method = NULL;
    }
    
    $this->db->trans_begin();
    
    if ($admins) {
      $this->db->where_not_in('admin_id', $admins);
    }
    $this->db->delete('permits', array('component' => $component, 'method' => $method));
    
    $permits = $this->db->get_where('permits', array('component' => $component, 'method' => $method))->result_array();
    $permits = array_simple($permits, 'admin_id');
    
    foreach ($admins as $admin_id) {
      if (!in_array($admin_id, $permits)) {
        $this->set_permit($admin_id, $component, $method);
      }
    }
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  function set_permit($admin_id, $component, $method = '') {
    if (!$method) {
      $method = NULL;
    }
    
    if ($this->check_access($admin_id, $component, $method)) {
      return TRUE;
    }
    
    return (bool)$this->db->insert('permits', array('component' => $component, 'method' => $method, 'admin_id' => $admin_id));
  }
  
  function check_group_access($group_id, $component, $method = '') {    
    if ($this->db->get_where('pr_admin_group_permits', array(
      'component' => $component,
      'method'    => NULL,
      'group_id'  => $group_id
    ))->num_rows()) {
      return TRUE;
    }
    
    if ($this->db->get_where('pr_admin_group_permits', array(
      'component' => $component,
      'method'    => $method,
      'group_id'  => $group_id
    ))->num_rows()) {
      return TRUE;
    }
    
    return FALSE;
  }
  
  function set_group_permits($groups, $component, $method = '') {
    if (!$method) {
      $method = NULL;
    }
    
    $this->db->trans_begin();
    
    if ($groups) {
      $this->db->where_not_in('group_id', $groups);
    }
    $this->db->delete('pr_admin_group_permits', array('component' => $component, 'method' => $method));
    
    $permits = $this->db->get_where('pr_admin_group_permits', array('component' => $component, 'method' => $method))->result_array();
    $permits = array_simple($permits, 'group_id');
    
    foreach ($groups as $group_id) {
      if (!in_array($group_id, $permits)) {
        $this->set_group_permit($group_id, $component, $method);
      }
    }
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  function set_group_permit($group_id, $component, $method = '') {
    if (!$method) {
      $method = NULL;
    }
    
    if ($this->check_group_access($group_id, $component, $method)) {
      return TRUE;
    }
    
    return (bool)$this->db->insert('pr_admin_group_permits', array('component' => $component, 'method' => $method, 'group_id' => $group_id));
  }
  
  function set_superusers($admins) {
    $this->db->trans_begin();
    
    if ($admins) {
      $this->db->where_not_in('id', $admins);
    }
    $this->db->update('admins', array('superuser' => 0));
    
    if ($admins) {
      $this->db->where_in('id', $admins)->update('admins', array('superuser' => 1));
    }
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  function get_groups($where = array()) {
    if ($where) {
      $this->db->where($where);
    }
    $this->db->order_by('title');
    $items = $this->db->get('pr_admin_group_types')->result_array();
    foreach ($items as &$item) {
      $this->db->select('COUNT(DISTINCT pr_admin_groups.admin_id) as cnt');
      $this->db->where('admin_groups.group_id = '.$item['id']);
      $this->db->join('admins', 'admins.id = admin_groups.admin_id');
      $item['admins_cnt'] = $this->db->get('admin_groups')->row()->cnt;
    }
    unset($item);
    return $items;
  }
  
  function get_group($id) {
    $item = $this->db->get_where('pr_admin_group_types',array('id' => $id))->row_array();
    if ($item) {
      $item['admins'] = array('ids' => array());
      $admins = $this->db->get_where('admin_groups',array('group_id' => $item['id']))->result_array();
      foreach ($admins as $admin) {
        $item['admins']['ids'][] = $admin['admin_id'];
      }
    }

    return $item;
  }
  
  function create_group($params) {
    if ($this->db->insert('pr_admin_group_types', $params)) {
      $id = $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
      
      return $id;
    }
    return false;
  }

  function edit_group($id, $params) {
    if ($this->db->update('pr_admin_group_types', $params, array('id' => $id))) {
      return true;
    }
    return false;
  }  
  
  function delete_group($id) {
    if ($this->db->delete('pr_admin_group_types', array('id' => $id))) {
      return true;
    }
    return false;
  }
  
  function set_group_admins($ids, $admins) {
    if (!is_array($ids)) {
      $ids = array($ids);
    }
    foreach ($ids as $id) {
      $now = array();
      if (!$this->db->query('DELETE FROM pr_admin_groups WHERE group_id = '. $id .' AND admin_id NOT IN ('. ($admins ? implode(',', $admins) : 0) .')')) {
        return false;
      }
      if ($admins) {
        $now_items = $this->db->get_where('pr_admin_groups', array('group_id' => $id))->result_array();
        foreach ($now_items as $now_item) {
          $now[] = $now_item['admin_id'];
        }
        foreach ($admins as $admin_id) {
          if (!in_array($admin_id, $now)) {
            if (!$this->db->insert('pr_admin_groups', array('group_id' => $id, 'admin_id' => $admin_id))) {
              return false;
            }
          }
        }
      }
    }
    return true;
  } 
}