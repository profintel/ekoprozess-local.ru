<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends PR_Controller {
  
  public $segments  = array();
  public $arguments = array();
  public $language;
  public $component;
  public $admin;
  
  function __construct() {
    parent::__construct();
    
    if (!$this->admin_id) {
      if ($this->input->post('is_ajax_form') || $this->input->is_ajax_request()) {
        send_answer(array('sysmsg' => 'UNAUTH'));
      } else {
        location('/autorization/', FALSE);
      }
      exit();
    }
    
    $this->placement = 'admin';
    
    $this->load->model('admin_model');
    $this->load->model('components/models/components_model');
    $this->load->model('administrators/models/administrators_model');
  }
  
  function _remap() {
    $this->segments = $this->uri->segment_array();
    
    $this->langs = array_simple($this->languages_model->get_languages(1, 1), FALSE, 'name');
    $this->language = $this->_get_language();
    $this->lang_prefix = (count($this->langs) > 1 ? '/'. $this->language : '');
    
    while (array_shift($this->segments) != mb_strtolower(__CLASS__));
    
    $this->component = $this->_find_component();
    if (!$this->component) {
      show_error('Компонент не найден');
    }
    $this->arguments = array_reverse($this->arguments);
    
    $this->load->component($this->component);
    
    $this->admin = $this->administrators_model->get_admin(array('id' => $this->admin_id));

    $html = $this->_run_component($this->component['name']);
    if (!isset($html)) {
      return;
    }

    if(exists_component('calendar')){
      $this->admin['events'] = $this->db->get_where('admin_events', array('admin_id'=>$this->admin_id,'check'=>0,'start >='=>date('Y-m-d H:i:s')))->result_array();
      $this->admin['red_events'] = $this->db->get_where('admin_events', array('admin_id'=>$this->admin_id,'check'=>0,'start <'=>date('Y-m-d H:i:s'),'end <'=>date('Y-m-d H:i:s')))->result_array();
      //Проверка и обновление календаря событий
      if ($this->main_model->exists_component('calendar')) {
        $this->db->update('admin_events',array('color' => '#fe7979'),array('admin_id'=>$this->admin_id,'check'=>0,'start <'=>date('Y-m-d H:i:s'),'end <'=>date('Y-m-d H:i:s')));
      }      
    }

    $this->load->view('admin/wrapper', array(
      'pr_version'      => $this->config->item('pr_version'),
      '_segments'       => $this->segments,
      '_arguments'      => $this->arguments,
      '_langs'          => $this->langs,
      '_language'       => $this->language,
      '_lang_prefix'    => $this->lang_prefix,
      '_component'      => $this->component,
      '_menu_primary'   => $this->admin_model->get_menu('primary'),
      '_menu_secondary' => $this->admin_model->get_menu('secondary'),
      '_admin'          => $this->admin,
      '_html'           => $html
    ));
  }
  
  function _find_component() {
    while ($this->segments) {
      $component = $this->components_model->get_component($this->segments);
      if ($component) {
        return $component;
      }
      $this->arguments[] = array_pop($this->segments);
    }
    //компонент по умолчанию
    $main_component = $this->components_model->get_main_component();
    if ($this->main_model->exists_component('permits')) {
      $permits = $this->components_model->get_component('permits');
      $this->load->component($permits);
      //проверяем есть ли права на компонент по умолчанию, если прав нет, ищем компонент на который права есть
      if(!$this->permits->_check_access($this->admin_id, $main_component['name'])){
        $permit_component = $this->permits_model->get_permit('admin_id = '.$this->admin_id.' AND (method = "index" OR method IS NULL)');
        if($permit_component){
          $component = $this->components_model->get_component($permit_component['component']);
          if($component){
            return $component;
          }
        }
      }
    }
    return $main_component;
  }
  
  function _run_component($name) {
    if (method_exists($this->$name, '_remap')) {
      $method = '_remap';
    } elseif ($this->arguments && method_exists($this->$name, $this->arguments[0])) {
      $this->segments[] = $method = array_shift($this->arguments);
    } elseif (method_exists($this->$name, 'index')) {
      $method = 'index';
    } else {
      show_404();
    }

    //если установлен компонент Права доступа проверям права на метод компонента
    if (exists_component('permits')) {
      $permits = $this->components_model->get_component('permits');
      $this->load->component($permits);
      if (!$this->permits->_check_access($this->admin_id, $name, $method)) {
        show_error('У вас нет прав для осуществления данной операции');
      }
    }
    //если установлен компонент Администраторы записываем в лог действия администраторов
    if(exists_component('administrators')){
      if($method != 'icon'){
        $method_title = $this->_parse_component($name, $method);
        $this->db->insert('admin_logs',array(
          'admin_id'  =>  $this->admin_id,
          'ip'        =>  $_SERVER['REMOTE_ADDR'],
          'component' =>  $name,
          'method'    =>  $method,
          'path'      =>  $_SERVER['REQUEST_URI'],
          'title'     =>  $method_title,
          'post'      =>  serialize($_POST)));
      }
    }

    $template = call_user_func_array(array(&$this->$name, $method), $this->arguments);

    return $this->_parse_template($template);
  }
  
  /**
  * Парсит контроллер компонента и возвращает описание_метода
  * @param $name - имя компонента
  * @param $method - имя_метода
  * @return string
  */
  function _parse_component($name, $method) {
    $result = '';
    $code = @file_get_contents(APPPATH .'components/'. $name .'/'. $name .'_admin.php');
    if ($code) {
      preg_match_all('/(?:\/\*\*?\s*\*\s*(.*?)\s*\*(?>\s*@.*?\/|\/)?\s*)?function\s+(.*?)\s*\(/s', $code, $matches, PREG_SET_ORDER);
      
      foreach ($matches as $match) {
        if (!preg_match('/^_/', $match[2])) {
          if($match[2] == $method){
            $result = preg_replace('/\s*\*\s*/', ' ', $match[1]);
          }
        }
      }
    }
    return $result;
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
}
