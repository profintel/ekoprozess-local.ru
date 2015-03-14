<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Languages_site extends CI_Component {
  
  function __construct() {
    parent::__construct();
  }
    
  function render_template_languages() {
    $data['items'] = $this->languages_model->get_languages();
    
    return $this->render_template('templates/languages',$data);
  }
  
  function change_language($name) { 
    $path = str_replace((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http') .'://'. $this->project['domain'].'/','',$_SERVER['HTTP_REFERER']);
    $path = substr_replace($path,'',0,strpos($path,'/'));
 
    $language = $this->languages_model->get_language_name($name);
    if (!$language) {
      redirect($path);
    }
    $_SESSION['lang_prefix'] = $language['name'];
    
    redirect($path);
  }
}