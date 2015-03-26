<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Родительский класс для всех классов ЦМС (кроме установщика), наследуется от класса контроллера
*/
class PR_Controller extends CI_Controller {
  
  public $user_id;
  public $admin_id;
  public $placement;
  public $project;
  
  function __construct() {
    parent::__construct();
    
    //Подключение конфиг-файла
    $this->load->config('pr_config');
    
    //Подключение БД
    $this->load->database();
    
    $this->load->model('projects/models/projects_model');
    $this->load->model('languages/models/languages_model');
    
    if ($this->input->is_cli_request()) {
      return;
    }
    
    $this->user_id  = (int)$this->session->userdata('user_id');
    $this->admin_id = (int)$this->session->userdata('admin_id');
    
    //Получение проекта
    $this->_find_project();
  }
  
  /**
  * Ищет проект по доменному имени
  */
  function _find_project() {
    $domain = preg_replace('/^www\./', '', mb_strtolower($_SERVER['HTTP_HOST']));
    $this->project = $this->projects_model->find_project($domain);
    if (!$this->project) {
      $this->load->library('idna_convert');
      $domain = $this->idna_convert->decode($domain);
      $this->project = $this->projects_model->find_project($domain);
    }
  }
  
  /**
  * Возвращает используемый язык
  * @return string
  */
  function _get_language() {
    if ($this->segments) {
      $language = $this->segments[1];
      if (isset($this->langs[$language])) {
        array_shift($this->segments);
        return $language;
      }
    }
    return $this->config->item('language');
  }
  
}
?>
