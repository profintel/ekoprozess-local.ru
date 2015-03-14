<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Projects_model extends CI_Model {
  
  function __construct() {
    parent::__construct();
  }
  
  function get_projects($active = FALSE) {
    if ($active !== FALSE) {
      $this->db->where('active', $active);
    }
    
    $projects = $this->db->order_by('id')->get('projects')->result_array();
    foreach ($projects as &$project) {
      $project['params'] = $this->main_model->get_params('projects', $project['id']);
    }
    unset($project);
    
    return $projects;
  }
  
  function get_project($id) {
    $project = $this->db->get_where('projects', array('id' => $id))->row_array();
    if ($project) {
      $project['params'] = $this->main_model->get_params('projects', $project['id']);
    }
    return $project;
  }
  
  function create_project($params) {
    $this->db->trans_begin();
    
    $this->db->insert('projects', $params);
    $id = $this->db->insert_id();
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return $id;
  }
  
  function update_project($id, $params) {
    return (bool)$this->db->update('projects', $params, array('id' => $id));
  }
  
  function delete_project($id) {
    $this->db->trans_begin();
    
    $root_pages = $this->db->get_where('pages', array('project_id' => $id, 'parent_id' => NULL))->result_array();
    foreach ($root_pages as $page) {
      $this->_delete_page($page['id']);
    }
    
    $this->db->delete('projects', array('id' => $id));
    $this->db->delete('params', array('category' => 'projects', 'owner_id' => $id));
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  function get_project_aliases($id) {
    return $this->db->order_by('id')->get_where('projects_aliases', array('project_id' => $id))->result_array();
  }
  
  function get_project_alias($id) {
    return $this->db->get_where('projects_aliases', array('id' => $id))->row_array();
  }
  
  function create_project_alais($params) {
    $this->db->trans_begin();
    
    $this->db->insert('projects_aliases', $params);
    $id = $this->db->insert_id();
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return $id;
  }
  
  function update_project_alias($id, $params) {
    return (bool)$this->db->update('projects_aliases', $params, array('id' => $id));
  }
  
  function delete_project_alias($id) {
    return (bool)$this->db->delete('projects_aliases', array('id' => $id));
  }
  
  function get_access_types() {
    return $this->db->order_by('id')->get('access_types')->result_array();
  }
  
  function get_pages($project_id = FALSE, $parent_id = NULL) {
    if ($project_id !== FALSE) {
      $this->db->where('pages.project_id', $project_id);
    }
    
    $pages = $this->db
      ->select('pages.*, pages_states.state', FALSE)
      ->join('pages_states', 'pages_states.page_id = pages.id', 'LEFT')
      ->order_by('pages.order')
      ->get_where('pages', array('pages.parent_id' => $parent_id))
      ->result_array();
    foreach ($pages as &$page) {
      $page['childs']   = $this->db->where('parent_id', $page['id'])->count_all_results('pages');
      $page['pages']    = ($page['state'] && $page['childs'] ? $this->get_pages($project_id, $page['id']) : array());
    }
    unset($page);
    
    return $pages;
  }
  
  function get_page($id) {
    $page = $this->db->get_where('pages', array('id' => $id))->row_array();
    if ($page) {
      $page['params'] = $this->main_model->get_params('pages', $page['id']);
    }
    
    return $page;
  }
  
  function get_page_order($project_id, $parent_id) {
    return (int)$this->db->select_max('order', 'max_order')->get_where('pages', array(
      'project_id' => $project_id,
      'parent_id' => $parent_id
    ))->row()->max_order + 1;
  }
  
  function is_available_alias($project_id, $parent_id, $alias, $id = 0) {
    if ($id) {
      $this->db->where('id !=', $id);
    }
    
    if ($this->db->get_where('pages', array('project_id' => $project_id, 'parent_id' => $parent_id, 'alias' => $alias))->num_rows()) {
      return FALSE;
    }
    
    return TRUE;
  }
  
  function create_page($params) {
    $this->db->trans_begin();
    
    $this->db->insert('pages', $params);
    $id = $this->db->insert_id();
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return $id;
  }
  
  function update_page($id, $params) {
    return (bool)$this->db->update('pages', $params, array('id' => $id));
  }
  
  function update_pages_recursively($parent_id, $params) {
    $this->db->update('pages', $params, array('parent_id' => $parent_id));
    
    $pages = $this->db->get_where('pages', array('parent_id' => $parent_id))->result_array();
    foreach ($pages as $page) {
      $this->update_pages_recursively($page['id'], $params);
    }
  }
  
  function delete_page($id) {
    $this->db->trans_begin();
    
    $this->_delete_page($id);
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  function _delete_page($id) {
    $childs = $this->db->get_where('pages', array('parent_id' => $id))->result_array();
    foreach ($childs as $child) {
      $this->_delete_page($child['id']);
    }
    
    $this->db->delete('params', array('category' => 'pages', 'owner_id' => $id));
    $this->db->delete('pages', array('id' => $id));
  }
  
  function set_page_state($page_id, $admin_id, $state) {
    if ($this->db->get_where('pages_states', array('page_id' => $page_id, 'admin_id' => $admin_id))->num_rows()) {
      return (bool)$this->db->update('pages_states', array('state' => $state), array('page_id' => $page_id, 'admin_id' => $admin_id));
    } else {
      return (bool)$this->db->insert('pages_states', array('page_id' => $page_id, 'admin_id' => $admin_id, 'state' => $state));
    }
  }
  
  function move_page($page_id, $dest_id, $project_id, $placement) {
    $this->db->trans_begin();
    
    $page = $this->get_page($page_id);
    
    if ($dest_id) {
      $dest = $this->get_page($dest_id);
      
      if ($placement == 'inner') {
        $params = array(
          'parent_id'  => $dest['id'],
          'project_id' => $dest['project_id'],
          'order'      => (int)$this->db->select_min('order', 'min_order')->get_where('pages', array('parent_id' => $dest['id']))->row()->min_order
        );
      } else {
        $params = array(
          'parent_id'  => $dest['parent_id'],
          'project_id' => $dest['project_id'],
          'order'      => $dest['order'] + 1
        );
      }
    } else {
      $params = array(
        'parent_id'  => NULL,
        'project_id' => $project_id,
        'order'      => (int)$this->db->select_min('order', 'min_order')->get_where('pages', array('project_id' => $project_id, 'parent_id' => NULL))->row()->min_order
      );
    }
    
    if ($params['parent_id']) {
      $parent = $this->get_page($params['parent_id']);
    }
    
    $params['alias'] = $page['alias'];
    if (!$this->is_available_alias($params['project_id'], $params['parent_id'], $params['alias'], $page['id'])) {
      $i = 0;
      while (TRUE) {
        $i++;
        if ($this->is_available_alias($params['project_id'], $params['parent_id'], $params['alias'] .'_'. $i)) {
          $params['alias'] = $params['alias'] .'_'. $i;
          break;
        }
      }
    }
    
    $params['path'] = ($params['parent_id'] ? $parent['path'] : '/') . $params['alias'] .'/';
    
    if ($this->db->get_where('pages', array('project_id' => $params['project_id'], 'is_main' => 1, 'id !=' => $page['id']))->num_rows()) {
      $params['is_main'] = 0;
    }
    
    $this->update_page($page['id'], $params);
    
    $this->update_pages_recursively($page['id'], array('project_id' => $params['project_id']));
    
    $this->db->set('order', '`order` + 1', FALSE)->where(array(
      'project_id' => $params['project_id'],
      'parent_id'  => $params['parent_id'],
      'order >='   => $params['order'],
      'id !='      => $page['id']
    ))->update('pages');
    
    if ($params['project_id'] != $page['project_id'] && $this->main_model->exists_component('menus')) {
      $this->load->model('menus/models/menus_model');
      $this->menus_model->set_page_menus($page['id'], array());
    }
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  function clone_page($id) {
    $this->db->trans_begin();
    
    $this->_clone_page($id);
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  function _clone_page($id, $parent_id = FALSE) {
    $page = $this->get_page($id);
    $page_id = $page['id'];
    $multiparams = $page['params'];
    if ($parent_id) {
      $page['parent_id'] = $parent_id;
    }
    if ($page['parent_id']) {
      $parent = $this->get_page($page['parent_id']);
    }
    
    if (!$this->is_available_alias($page['project_id'], $page['parent_id'], $page['alias'])) {
      $i = 0;
      while (TRUE) {
        $i++;
        if ($this->is_available_alias($page['project_id'], $page['parent_id'], $page['alias'] .'_'. $i)) {
          $page['title'] = $page['title'] .' (копия '. $i .')';
          $page['alias'] = $page['alias'] .'_'. $i;
          break;
        }
      }
    }
    
    $page['path'] = ($page['parent_id'] ? $parent['path'] : '/') . $page['alias'] .'/';
    $page['active']  = 0;
    $page['is_main'] = 0;
    $page['order']   = $this->get_page_order($page['project_id'], $page['parent_id']);
    
    unset($page['id']);
    unset($page['tm']);
    unset($page['params']);
    
    $id = $this->create_page($page);
    $this->main_model->set_params('pages', $id, $multiparams);
    
    $childs = $this->db->get_where('pages', array('parent_id' => $page_id))->result_array();
    foreach ($childs as $child) {
      $this->_clone_page($child['id'], $id);
    }
  }
  
  function reset_main($project_id, $page_id) {
    return (bool)$this->db->update('pages', array('is_main' => 0), array('project_id' => $project_id, 'id !=' => $page_id));
  }
  
  function update_paths_recursively($parent_id) {
    $parent = $this->get_page($parent_id);
    
    $pages = $this->db->get_where('pages', array('parent_id' => $parent['id']))->result_array();
    foreach ($pages as $page) {
      if (!$this->update_page($page['id'], array('path' => $parent['path'] . $page['alias'] .'/'))) {
        return FALSE;
      }
      if (!$this->update_paths_recursively($page['id'])) {
        return FALSE;
      }
    }
    
    return TRUE;
  }
  
  function get_backups($page_id, $limit = 0, $offset = 0) {
    if ($limit) {
      $this->db->limit($limit, $offset);
    }
    return $this->db
      ->select('pages_history.id, pages_history.tm, admins.username')
      ->join('admins', 'admins.id = pages_history.admin_id', 'LEFT')
      ->order_by('pages_history.id DESC')
      ->get_where('pages_history', array('pages_history.page_id' => $page_id))
      ->result_array();
  }
  
  function get_backup($id) {
    return $this->db->get_where('pages_history', array('id' => $id))->row_array();
  }
  
  function create_backup($page, $admin_id) {
    return (bool)$this->db->insert('pages_history', array(
      'page_id'  => $page['id'],
      'data'     => serialize($page),
      'admin_id' => $admin_id,
      'tm'       => $page['tm']
    ));
  }
  
  function restore_backup($id) {
    $this->db->trans_begin();
    
    $backup = $this->projects_model->get_backup($id);
    $page = $this->get_page($backup['page_id']);
    $data = unserialize($backup['data']);
    $multiparams = $data['params'];
    if ($page['parent_id']) {
      $parent = $this->get_page($page['parent_id']);
    }
    
    if (!$this->is_available_alias($page['project_id'], $page['parent_id'], $page['alias'], $page['id'])) {
      $i = 0;
      while (TRUE) {
        $i++;
        if ($this->is_available_alias($page['project_id'], $page['parent_id'], $page['alias'] .'_'. $i, $page['id'])) {
          $data['alias'] = $data['alias'] .'_'. $i;
          break;
        }
      }
    }
    
    $data['path'] = ($page['parent_id'] ? $parent['path'] : '/') . $data['alias'] .'/';
    
    if (isset($data['main_template_id']) && $data['main_template_id'] && !$this->templates_model->get_template((int)$data['main_template_id'])) {
      unset($data['main_template_id']);
    }
    if (isset($data['template_id']) && $data['template_id'] && !$this->templates_model->get_template((int)$data['template_id'])) {
      unset($data['template_id']);
    }
    
    unset($data['id']);
    unset($data['project_id']);
    unset($data['parent_id']);
    unset($data['active']);
    unset($data['is_main']);
    unset($data['is_searchable']);
    unset($data['is_menu']);
    unset($data['access_type_id']);
    unset($data['order']);
    unset($data['last_modified']);
    unset($data['params']);
    
    $this->update_page($page['id'], $data);
    $this->main_model->set_params('pages', $page['id'], $multiparams);
    
    if ($data['path'] != $page['path']) {
      $this->update_paths_recursively($page['id']);
    }
    
    $this->db->delete('pages_history', array('page_id' => $page['id'], 'id >=' => $backup['id']));
    
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return FALSE;
    }
    
    $this->db->trans_commit();
    return TRUE;
  }
  
  function delete_backup($id) {
    return (bool)$this->db->delete('pages_history', array('id' => $id));
  }
  
  function find_project($domain) {
    $project = $this->db->get_where('projects', array('domain' => $domain))->row_array();
    if (!$project) {
      $project = $this->db
        ->select('projects.*, projects_aliases.redirect', FALSE)
        ->join('projects_aliases', 'projects_aliases.project_id = projects.id')
        ->get_where('projects', array('projects_aliases.name' => $domain))
        ->row_array();
    }
    
    if ($project) {
      $project['params'] = $this->main_model->get_params('projects', $project['id']);
    }
    
    return $project;
  }
  
  function find_page($project_id, $segments) {
    $page = $this->db->get_where('pages', array('project_id' => $project_id, 'path' => '/'. implode('/', $segments) .'/'))->row_array();
    if ($page) {
      $page['params'] = $this->main_model->get_params('pages', $page['id']);
      
      $this->load->model('messages/models/messages_model'); 
    }    
    return $page;
  }
  
  function get_main_page($project_id) {
    $page = $this->db->get_where('pages', array('project_id' => $project_id, 'is_main' => 1))->row_array();
    if ($page) {
      $page['params'] = $this->main_model->get_params('pages', $page['id']);
    }
    return $page;
  }
  
  function get_crumbs($project_id, $segments, $crumbs = array()) {    
    $page = $this->db->get_where('pages', array('project_id' => $project_id, 'path' => '/'. implode('/', $segments) .'/'))->row_array();
    if ($page) {      
      $page['params'] = $this->main_model->get_params('pages', $page['id']);
      $crumbs[] = array(
        'title' => (isset($page['params']['name_'. $this->language]) && $page['params']['name_'. $this->language] ? $page['params']['name_'. $this->language] : ''),
        'path' => $page['path']
      );
    }
    array_pop($segments);
    if ($segments) {
      $crumbs = $this->get_crumbs($project_id, $segments, $crumbs);
    } else {
      $crumbs = array_reverse($crumbs);
    }
    
    return $crumbs;
  }
  
}