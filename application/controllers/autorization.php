<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Класс авторизации
*/
class Autorization extends PR_Controller {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('autorization_model');
  }
  
	function index() {
    if ($this->admin_id) {
      location('/admin/', FALSE);
    }
    
    $data = array(
      'pr_version' => $this->config->item('pr_version'),
      
      'form' => $this->view->render_form(array(
        'action' => '/autorization/process/',
        'blocks' => array(
          array(
            'title'  => 'Авторизация',
            'fields' => array(
              array(
                'view'      => 'fields/text',
                'title'     => 'Логин:',
                'name'      => 'username',
                'autofocus' => TRUE
              ),
              array(
                'view'  => 'fields/password',
                'title' => 'Пароль:',
                'name'  => 'password'
              ),
              array(
                'view'     => 'fields/submit',
                'id'       => 'autorization-submit',
                'class'    => 'icon_small door_in_i_s',
                'title'    => 'Войти',
                'type'     => 'ajax',
                'reaction' => '/admin/'
              )
            )
          )
        )
      ))
    );
    
    $this->load->view('autorization', $data);
	}
  
  function process() {
    $admin = $this->autorization_model->check_admin($this->input->post('username'), $this->input->post('password'));
    
    if (!$admin) {
      send_answer(array('errors' => array('Неверное имя пользователя или пароль')));
    }
    
    $this->session->set_userdata('admin_id', $admin['id']);
    
    send_answer();
  }
  
  function close() {
    $this->session->destroy();
    redirect('/autorization/');
  }
  
}
