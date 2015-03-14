<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site extends PR_Controller {
  
  public $segments  = array();
  public $arguments = array();
  public $language;
  public $page;
  public $user;
  
  function __construct() {
    parent::__construct();
    $this->placement = 'site';
    
    $this->load->model('components/models/components_model');
    $this->load->model('templates/models/templates_model');
    $this->load->model('accounts/models/accounts_model');
    $this->load->model('gallery/models/gallery_model');
  }
  
	function _remap() {
    if (!$this->project) {
      show_404();
    }
    
    //Редирект на основной домен при необходимости
    if (isset($this->project['redirect']) && $this->project['redirect']) {
      location((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http') .'://'. $this->project['domain'] . $_SERVER['REQUEST_URI']);
    }
  
    //Проверка доступности проекта
    if (!$this->project['active'] && !$this->admin_id) {
      show_404();
    }
    
    $this->segments   = $this->uri->segment_array(); //Сегменты урла
    $this->_GET       = $this->uri->getParams(); //GET-параметры
    
    //Определение языка
    $this->langs = array_simple($this->languages_model->get_languages(1, 0), FALSE, 'name');
    $this->language = $this->_get_language();
    if (isset($_SESSION['lang_prefix'])) {
      $this->language = $_SESSION['lang_prefix'];
    }
    $this->lang_prefix = (count($this->langs) > 1 ? '/'. $this->language : '');
    
    //Поиск страницы
    $this->page = $this->_find_page();
    if (!$this->page) {
      show_404();
    }
    /*if ($this->page['is_main'] && $this->arguments) {
      show_404();
    }*/      
    
    $this->arguments = array_reverse($this->arguments);

    //Проверка указания языка
    $lang_in_uri = preg_match('/^\/'. $this->language .'\//', $_SERVER['REQUEST_URI']);
    if (count($this->langs) > 1) {
      if (!$lang_in_uri) {
        location($this->lang_prefix . $_SERVER['REQUEST_URI']);
      }
    } elseif ($lang_in_uri) {
      location('/'. ($this->segments ? implode('/', $this->segments) .'/' : '') . ($this->_GET ? '?'. http_build_query($this->_GET) : ''));
    }
    
    $page_template = $this->templates_model->get_template((int)$this->page['template_id']);
    if(isset($this->arguments[0]) && $this->arguments[0] == 'sitemap') {
      $this->_sitemap();
    } elseif ($this->arguments && (!$page_template || !$page_template['component_id'])) {
      show_404();      
    }
    
    $this->_check_access(); //Проверка доступности страницы
    
    //Получение данных пользователя
    if ($this->main_model->exists_component('accounts')) {
      $this->user = $this->accounts_model->get_user(array('users.id' => (int)$this->user_id),true);
      if ($this->user) {
        //Проставляем IP, если отсутствует
        if (!$this->user['ip']) {
          $this->accounts_model->edit_user($this->user['id'], array('ip' => $_SERVER['REMOTE_ADDR']));
        }
      }      
    }
        
    //Редирект на указанную страницу переадресации
    if (isset($this->page['redirect']) && $this->page['redirect']) {
      location($this->page['redirect']);
    }

    //Вывод страницы
    $this->_render_page($page_template);    
	}
  
  function _find_page() {
    while ($this->segments) {
      $page = $this->projects_model->find_page($this->project['id'], $this->segments);
      if ($page) {
        $page['crumbs'] = $this->projects_model->get_crumbs($this->project['id'], $this->segments);
        $page['childs'] = $this->projects_model->get_pages($this->project['id'], $page['id']);
          foreach ($page['childs'] as &$child) {
              $child_gallery = $this->gallery_model->get_link(array('item_id' => $child['id']));
              if ($child_gallery) {
                  $child_images = $this->gallery_model->get_gallery_images(array('id' => $child_gallery['gallery_id']),5,0);
                  if ($child_images) {
                      $child['images'] = $child_images;
                  }
              }
              unset($child_gallery);
              unset($child_images);
          }
          $images = $this->gallery_model->get_gallery_images(array('path' => '/'.$page['alias'].'/'),1,0);
          if ($images) {
              $page['image'] = $images[0];
          }
        if ($page['is_main']) {
          location(
            $this->lang_prefix
            .'/'. ($this->arguments ? implode('/', array_reverse($this->arguments)) .'/' : '')
            . ($this->_GET ? '?'. http_build_query($this->_GET) : '')
          );
        }
        
        return $page;
      }
      $this->arguments[] = array_pop($this->segments);
    }
    return $this->projects_model->get_main_page($this->project['id']);
  }
  
  function _check_access() {
    if ($this->admin_id) {
      return;
    }
    
    if (!$this->page['active']) {
      show_404();
    }
    
    if ($this->page['access_type_id'] != 1 && !$this->user_id) {
      show_404();
    }
  }
  
  function _render_page($page_template) {
    $rendered_page = $this->_render_template($page_template, $this->_get_data_arr());
    
    $rendered_main = $this->_render_template((int)$this->page['main_template_id'], array_merge(
      $this->_get_data_arr(),
      array('_content' => $rendered_page))
    );
    
    echo $this->_parse_template($rendered_main, $this->_get_data_arr());
    
  }
  
  function _get_data_arr() {
    return array(
      '_segments'    => $this->segments,
      '_arguments'   => $this->arguments,
      '_langs'       => $this->langs,
      '_language'    => $this->language,
      '_lang_prefix' => $this->lang_prefix,
      '_project'     => $this->project,
      '_page'        => $this->page,
      '_user'        => $this->user
    );
  }
  
  function _render_template($condition = FALSE, $data = array()) {
    if (!$condition) {
      return $this->load->view('default', $data, TRUE);
    }
    
    $template = (is_array($condition) ? $condition : $this->templates_model->get_template($condition));
    if (!$template) {
      return FALSE;
    }
    
    if (!$template['component_id']) {
      return $this->load->view('templates/'. $template['path'], $data, TRUE);
    }
    
    $component = $this->components_model->get_component((int)$template['component_id']);
    $this->load->component($component);
    
    return $this->{$component['name']}->show_template($template, $this->arguments);
  }
  
  function _parse_template($template, $data = array()) {    
    preg_match_all('/{{(.+):(.+)}}/u', $template, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
      if (method_exists($this, '_handle_'. $match[1])) {
        $content = call_user_func_array(array(&$this, '_handle_'. $match[1]), array($match[2], $data));
        if ($content !== FALSE) {
          $template = str_replace($match[0], $this->_parse_template($content, $data), $template);
        }
      }
    }
    
    return $template;
  }
  
  function _handle_tpl($name, $data = array()) {
    return $this->_render_template($name, $data);
  }
  
  function _handle_cmp($line, $data = array()) {
    $parts = explode('->', $line);
    
    $component = $this->components_model->get_component($parts[0]);
    if (!$component) {
      return FALSE;
    }
    $this->load->component($component);    
    
    $method = (isset($parts[1]) && $parts[1] ? $parts[1] : 'index');
    $parts = explode('<-', $method);
    $method = array_shift($parts);
    
    if (method_exists($this->{$component['name']}, '_remap')) {
      return call_user_func_array(array(&$this->{$component['name']}, '_remap'), $parts);
    } elseif (method_exists($this->{$component['name']}, $method)) {      
      return call_user_func_array(array(&$this->{$component['name']}, $method), $parts);      
    } else {      
      return FALSE;
    }
  }
  
  function _sitemap() {
    header ("content-type: text/xml");
    echo $this->main_model->sitemap();
    exit;
  }
}
