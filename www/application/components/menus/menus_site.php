<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menus_site extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('menus/models/menus_model');
  }
  
  function render($name, $depth = FALSE) {
    $menu = $this->menus_model->get_menu($name);
    if (!$menu) {
      return FALSE;
    }
    
    if (!$menu['template_id']) {
      return FALSE;
    }
    
    $depth = ($depth === FALSE ? $menu['depth'] : $depth);
    
    $pages = $this->menus_model->get_menu_pages($menu['id']);
    foreach ($pages as &$page) {
      $page['pages'] = (!$depth || $depth > 1 ? $this->_get_subpages($page['id'], ($depth ? $depth - 1 : $depth)) : array());
      $page['params'] = $this->main_model->get_params('pages', $page['id']);
    }
    unset($page);
    
    return $this->load_template((int)$menu['template_id'], array('pages' => $pages));
  }
  
  function _get_subpages($parent_id, $depth) {
    $pages = $this->menus_model->get_pages($parent_id);
    foreach ($pages as &$page) {
      $page['pages'] = (!$depth || $depth > 1 ? $this->_get_subpages($page['id'], ($depth ? $depth - 1 : $depth)) : array());
      $page['params'] = $this->main_model->get_params('pages', $page['id']);
    }
    unset($page);
    
    return $pages;
  }
  
}