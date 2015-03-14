<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_subscribes_site extends CI_Component {
  
  function __construct() {
    parent::__construct();
		
    $this->load->model('subscribes/models/subscribes_model');
  }
  
  function render_templates() {

		return $this->render_template('templates/form_subscribe');
	}
  
  function email_sign() {
    $params = array(
			'email' => $this->input->post('email')
		);
    
    if (!$params['email']) {
      exit('Не указан email');
    } elseif (!preg_match('/^[-0-9a-z_\.]+@[-0-9a-z^\.]+\.[a-z]{2,4}$/i', $params['email'])) {
      exit('Некорректный email');
    } elseif ($this->db->get_where('subscribes_emails',array('email' => $params['email']))->row()) {
      exit('Указанный email уже подписан на рассылки.');
    }
    
    if (!$this->db->insert('subscribes_emails', $params)) {
      exit('Произошла ошибка при подписке email.');
    }
    
    send_answer();
  }
  
  function unsubscribe_email() {
    $email = ($this->uri->getParam('email') ? $this->uri->getParam('email') : 0);
    if ($email) {
      $this->db->query('DELETE FROM pr_subscribes_emails WHERE email = "'.$email.'"');
    }
    header('Location: '. $this->lang_prefix . '/successfully_unsubscribed/');
  }
  
}