<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forms_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }
  
  function get_forms_types() {
    return $this->db->order_by('title')->get('forms_types')->result_array();
  }
  
  function get_forms_type($id) {
    return $this->db->get_where('forms_types', array('id' => $id))->row_array();
  }
  
  function create_forms_type($params) {
    $this->db->trans_begin();
    
    $this->db->insert('forms_types', $params);
    $id = $this->db->insert_id();
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return $id;
  }
  
  function update_forms_type($id, $params) {
    return $this->db->update('forms_types', $params, array('id' => $id));
  }
  
  function delete_forms_type($id) {
    $this->db->trans_begin();
    
    $forms = $this->db->get_where('forms', array('type_id' => $id))->result_array();
    foreach ($forms as $form) {
      $this->delete_form($form['id']);
    }
    
    $this->db->delete('forms_types', array('id' => $id));
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  function get_forms() {
    $forms = $this->db->order_by('id')->get('forms')->result_array();
    
    foreach ($forms as &$form) {
      $form['fields_amount'] = $this->db->from('forms_fields')->where(array('form_id' => $form['id']))->count_all_results();
    }
    unset($form);
    
    return $forms;
  }
  
  function get_form($condition) {
    if (is_int($condition)) {
      $this->db->where('forms.id', $condition);
    } else {
      $this->db->where('forms.name', $condition);
    }
    
    return $this->db
      ->select('forms.*, templates.path AS template_path', FALSE)
      ->join('templates', 'templates.id = forms.template_id', 'LEFT')
      ->get('forms')
      ->row_array();
  }
  
  function create_form($params) {
    $this->db->trans_begin();
    
    $this->db->insert('forms', $params);
    $id = $this->db->insert_id();
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return $id;
  }
  
  function update_form($id, $params) {
    return $this->db->update('forms', $params, array('id' => $id));
  }
  
  function delete_form($id) {
    $this->db->trans_begin();
    
    $fields = $this->get_forms_fields($id);
    foreach ($fields as $field) {
      $this->delete_forms_field($field['id']);
    }
    
    $this->db->delete('forms', array('id' => $id));
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  function get_forms_fields($form_id) {
    $fields = $this->db
      ->select('forms_fields.*, templates.path AS template_path', FALSE)
      ->join('templates', 'templates.id = forms_fields.template_id', 'LEFT')
      ->order_by('forms_fields.order')
      ->get_where('forms_fields', array('forms_fields.form_id' => $form_id))
      ->result_array();
    
    foreach ($fields as &$field) {
      $field['params'] = $this->main_model->get_params('forms_fields', $field['id']);
    }
    unset($field);
    
    return $fields;
  }
  
  function get_forms_field($id) {
    $field = $this->db->get_where('forms_fields', array('id' => $id))->row_array();
    if ($field) {
      $field['params'] = $this->main_model->get_params('forms_fields', $field['id']);
    }
    return $field;
  }
  
  function create_forms_field($params) {
    $this->db->trans_begin();
    
    $params['order'] = ++$this->db
      ->select_max('order', 'max_order')
      ->get_where('forms_fields', array('form_id' => $params['form_id']))
      ->row()->max_order;
    
    $this->db->insert('forms_fields', $params);
    $id = $this->db->insert_id();
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return $id;
  }
  
  function update_forms_field($id, $params) {
    return $this->db->update('forms_fields', $params, array('id' => $id));
  }
  
  function up_forms_field($id) {
    $this->db->trans_begin();
    
    $field = $this->get_forms_field($id);
    $prev_field = $this->db
      ->order_by('order DESC')
      ->limit(1)
      ->get_where('forms_fields', array(
        'form_id' => $field['form_id'],
        'order <' => $field['order']
      ))
      ->row_array();
    
    if ($prev_field) {
      $this->update_forms_field($prev_field['id'], array('order' => $field['order']));
      $this->update_forms_field($field['id'], array('order' => $prev_field['order']));
    }
    
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  function down_forms_field($id) {
    $this->db->trans_begin();
    
    $field = $this->get_forms_field($id);
    $prev_field = $this->db
      ->order_by('order ASC')
      ->limit(1)
      ->get_where('forms_fields', array(
        'form_id' => $field['form_id'],
        'order >' => $field['order']
      ))
      ->row_array();
    
    if ($prev_field) {
      $this->update_forms_field($prev_field['id'], array('order' => $field['order']));
      $this->update_forms_field($field['id'], array('order' => $prev_field['order']));
    }
    
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  function delete_forms_field($id) {
    $this->db->trans_begin();
    
    $this->db->delete('forms_fields', array('id' => $id));
    $this->db->delete('params', array('category' => 'forms_fields', 'owner_id' => $id));
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  function get_options($table) {
    if (!$this->db->table_exists($table)) {
      return array();
    }
    
    return $this->db->get($table)->result_array();
  }
  
}