<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Component extends PR_Controller {
  
  public $segments = array();
  public $language;
  
  function __construct() {
    parent::__construct();
    
    $this->placement = 'site';
    
    $this->load->model('components/models/components_model');
    $this->load->model('templates/models/templates_model');
  }
  
	function _remap() {
    $this->segments = $this->uri->segment_array();
    
    //Определение языка
    $this->langs = array_simple($this->languages_model->get_languages(1, 0), FALSE, 'name');
    $this->language = $this->_get_language();
    $this->lang_prefix = (count($this->langs) > 1 ? '/'. $this->language : '');
    
    while (array_shift($this->segments) != mb_strtolower(__CLASS__));
    if (!$this->segments) {
      $this->_error('Компонент не определен');
    }
    
    $component = array_shift($this->segments);
    $method = preg_replace('/^_+/', '', ($this->segments ? array_shift($this->segments) : 'index'));
    
    $component = $this->components_model->get_component($component);
    if (!$component) {
      $this->_error('Компонент не найден');
    }
    
    $this->load->component($component);
    
    if (method_exists($this->{$component['name']}, '_remap')) {
      call_user_func_array(array(&$this->{$component['name']}, '_remap'), $this->segments);
    } elseif (method_exists($this->{$component['name']}, $method)) {
      call_user_func_array(array(&$this->{$component['name']}, $method), $this->segments);
    } else {
      $this->_error('Метод не найден');
    }
  }
  
  function _error($error) {
    if ($this->input->is_ajax_request()) {
      send_answer(array('errors' => array($error)));
    } else {
      show_error($error);
    }
  }
  
}
