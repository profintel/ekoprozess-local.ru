<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_Component {
  
  public $params;
  
  private $template_paths;
  
	function __construct() {
    $this->template_paths = array(
      APPPATH .'views/',
      APPPATH .'views/templates/',
      APPPATH .'components/'
    );
	}
  
	function __get($key) {
		$CI =& get_instance();
		return $CI->$key;
	}
  
  function __set($key, $value) {
		$CI =& get_instance();
		$CI->$key = $value;
  }

  function render_template($template, $data = array()) {
    $this->template_paths[] = APPPATH .'components/'. $this->params['name'] .'/';
    
    $path = FALSE;
    foreach ($this->template_paths as $template_path) {
      if (file_exists($template_path . $template .'.php')) {
        $path = $template_path . $template .'.php';
        break;
      }
    }
    if (!$path) {
      show_error('Не найден шаблон '. $template);
    }
    
    $data['_segments']    = $this->segments;
    $data['_arguments']   = $this->arguments;
    $data['_langs']       = $this->langs;
    $data['_language']    = $this->language;
    $data['_lang_prefix'] = $this->lang_prefix;
    $data['_component']   = $this->params;
    $data['_project']     = ($this->placement == 'site' ? $this->project : NULL);
    $data['_page']        = ($this->placement == 'site' ? $this->page : NULL);
    $data['_user']        = ($this->placement == 'site' ? $this->user : NULL);
		extract($data);
    
		ob_start();
		include($path);
		$buffer = ob_get_contents();
		@ob_end_clean();
    
		return $buffer;
  }
  
  function load_template($condition, $data = array()) {
    $template = $this->templates_model->get_template($condition);
    if (!$template['component_id']) {
      return $this->render_template($template['path'], $data);
    }
    
    return $this->show_template($template, $data);
  }
  
  function show_template($template, $data = array()) {
    $component = $this->components_model->get_component((int)$template['component_id']);
    
    if ($component['name'] == $this->params['name']) {
      $obj = &$this;
    } else {
      $this->load->component($component);
      $obj = &$this->{$component['name']};
    }
    
    if (method_exists($obj, '_remap')) {
      $method = '_remap';
    } elseif (method_exists($obj, 'render_template_'. $template['name'])) {
      $method = 'render_template_'. $template['name'];
    } elseif (method_exists($obj, 'render_templates')) {
      $method = 'render_templates';
    } else {
      return $obj->render_template($template['path'], $data);
    }
    
    return call_user_func_array(array($obj, $method), array_merge(array($template['path']), $data));
  }
  
}