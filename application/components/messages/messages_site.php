<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Messages_site extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('forms/models/forms_model');
    $this->load->model('messages/models/messages_model');
    $this->load->model('components/models/components_model');
    $this->load->model('accounts/models/accounts_model');
  }
  
  /** Обработка формы, отправленной с сайта
  *   @param $name - системное имя формы
  **/
  function processing_comment($component_name) {
    $component = $this->components_model->get_component($component_name);
    if ($component) {
      if (!$this->user_id) {
        send_answer(array('errors' => array(('Не определен пользователь'))));
      }

      $form_params['comment'] = array('name'=>'comment','title' =>'Комментарий','value' => htmlspecialchars(trim($this->input->post('comment'))),'field' => 'textarea','req' => 'true');
      $owner_id =(int)$this->input->post('owner_id');
      $parent_id = (int)$this->input->post('parent_id');
        
      $params = array(
        array(
          'block_params' 		=> array('title' => 'Параметры сообщения', 'system_name' => 'main_params'),
          'message_params'	=> $form_params
        )
      );
      
      $errors = $this->_validate_params_comment($form_params);
      if ($errors) {
        send_answer(array('errors' => $errors));
      }
      
      $message_id = $this->messages_model->send_to_messages($component_name, $owner_id, true, $parent_id, 'Комментарий', $this->user_id, $params);
      if (!$message_id) {
        send_answer(array('errors' => array(('Не удалось сохранить сообщение'))));
      }
      
      if (!$this->messages_model->_send_email($message_id, $params, '','Новый комментарий к компоненту "'.$component['title'].'"')) {
        send_answer(array('errors' => array(('Не удалось отправить сообщение администратору'))));
      } 
      
      send_answer();
    }
  }
  
  function _validate_params_comment($params) {
    $errors = array();
    if (!$params['comment']['value']) {
      $errors[] = 'Не указан текст сообщения';
    }
    return $errors;
  }
  
  function render_comments($component_name, $owner_id) {    
    $data = array(
      'component_name' => $component_name,
      'owner_id' => $owner_id,
      'comments' => $this->messages_model->get_messages($component_name, array('active' => 1, 'owner_id' => $owner_id, 'parent_id' => 0))
    );
    return $this->render_template('templates/comments',$data);
  }
  
  function render_comments_childs($component_name, $owner_id, $parent_id) {    
    $data = array(
      'component_name' => $component_name,
      'owner_id' => $owner_id,
      'comments' => $this->messages_model->get_messages($component_name, array('active' => 1, 'parent_id' => $parent_id))
    );
    return $this->render_template('templates/comments_childs',$data);
  }
}