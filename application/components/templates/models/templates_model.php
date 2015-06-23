<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Templates_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }
  
  function get_templates() {
    return $this->db
      ->select('templates.*, components.title AS component', FALSE)
      ->join('components', 'components.id = templates.component_id', 'LEFT')
      ->order_by('custom, component_id, id')
      ->get('templates')
      ->result_array();
  }
  
  function get_template($condition) {
    if (is_int($condition)) {
      $this->db->where('id', $condition);
    } else {
      $this->db->where('name', $condition);
    }
    
    return $this->db->get('templates')->row_array();
  }
  
  function install($templates) {
    $result = array('added' => 0, 'deleted' => 0);
    
    if ($templates) {
      $names = array_simple($templates, 'name');
      
      $this->db->trans_begin();
      
      $result['deleted'] = $this->db->where_not_in('name', $names)->where('custom', 0)->count_all_results('templates');
      $this->db->where_not_in('name', $names)->delete('templates', array('custom' => 0));
      
      foreach ($templates as $template) {
        if (!$this->get_template($template['name'])) {
          $result['added']++;
          $this->db->insert('templates', $template);
        }
      }
      
      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        return FALSE;
      }
      
      $this->db->trans_commit();
    }
    
    return $result;
  }
  
  function create_template($params) {
    $this->db->trans_begin();
    
    $this->db->insert('templates', $params);
    $id = $this->db->insert_id();
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return $id;
  }
  
  function update_template($id, $params) {
    return $this->db->update('templates', $params, array('id' => $id));
  }
  
  function delete_template($id) {
    return (bool)$this->db->delete('templates', array('id' => $id));
  }
  
}