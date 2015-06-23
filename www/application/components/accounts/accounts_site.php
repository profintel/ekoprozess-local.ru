<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accounts_site extends CI_Component {
  
  function __construct() {
    parent::__construct();
		
		$this->load->model('accounts/models/accounts_model');
		$this->load->model('forms/models/forms_model');
  }
    
  function render_template_cabinet () {
    if (!$this->user) { header('Location: '. $this->lang_prefix . '/login/'); }
    $data = array(
      'user_params' => $this->accounts_model->get_user_params(),
      'type' => ($this->uri->getParam('type') ? $this->uri->getParam('type') : ''),
      'error' => '',
    );
      
    switch ($data['type']) {
      case 'fill_balance':
        $data['error_id'] = ($this->uri->getParam('error') ? $this->uri->getParam('error') : '');        
        switch ($data['error_id']) {
          case '1':
            $data['error'] = 'Не найден аккаунт. Обратитесь к администраторам сайта.';
          break;
          case '2':
            $data['error'] = 'Некорректное значение для цены.';
          break;
          case '3':
            $data['error'] = 'Не удалось создать заказ на пополнение. Обратитесь к администраторам сайта.';
          break;
        }
      break;
      case 'account':
        $data['account_id'] = ($this->uri->getParam('account_id') ? $this->uri->getParam('account_id') : '');        
        $data['account'] = $this->db->get_where('user_transactions_balance',array('id' => (int)$data['account_id']))->row_array();
      break;  
    }        
    
    return $this->render_template('templates/cabinet', $data);    
  }
  
  function render_template_login () {
    $data = array(
      'status' => ($this->uri->getParam('status') ? $this->uri->getParam('status') : ''),
    );    

    return $this->render_template('templates/login', $data);
  }
  
  /** Авторизиция
  **/
  function autorization() {
    $params = array(
      'username' => htmlspecialchars(trim($this->input->post('username'))),
      'password' => htmlspecialchars(trim($this->input->post('password')))
    );
    
    $errors = $this->_check_autorization_params($params);
    if ($errors) {
      send_answer(array('errors' => array($errors[0])));
    }
    
    $params['password'] = md5($params['password']);
    
    $user = $this->db->get_where('users', array('username' => $params['username'], 'password' => $params['password']))->row_array();
    if ($user) {			
      if ($user['active'] == 1) {
        $this->session->set_userdata('user_id', $user['id']);
      }	else {
        send_answer(array('errors' => array('Ваша учетная запись не активирована.')));
      }
    } else {	
      send_answer(array('errors' => array('Неверный логин или пароль')));
    }
    
    send_answer();
  }
  
  function _check_autorization_params($params) {
    $errors = array();
    if (!$params['username']) { $errors[] = 'Не указан логин'; }
    if (!$params['password']) { $errors[] = 'Не указан пароль'; }
    return $errors;
  }
  
  function logout() {
    $this->session->unset_userdata('user_id');
    header('Location: '. $this->lang_prefix . '/cabinet/');
  }
  
  /** Регистрация
  **/  
  function registration() {
    $form_type = $this->db->get_where('forms', array('name' => 'registration'))->row_array();
    if ($form_type) {
      $form_fields = $this->forms_model->get_forms_fields($form_type['id']);
      $main_params = $inner_params = $multiparams = $portfolio_images = array();
      foreach ($form_fields as $form_field) { 
        if ($form_field['type'] == 'submit') { continue; }
          switch ($form_field['attr_name']) {
          case 'username':
          case 'password':
            $main_params[$form_field['attr_name']] = htmlspecialchars(trim($this->input->post($form_field['attr_name'])));
            $main_params['ip'] = $_SERVER['REMOTE_ADDR'];
          break;
          case 're_password':
          case 'captcha':
            $inner_params[$form_field['attr_name']] = htmlspecialchars(trim($this->input->post($form_field['attr_name'])));
          break;
          case 'subscribe':
            $subscribe = ($this->input->post('subscribe') ? 1 : 0);
          break;
          case 'agreement':
            
          break;
          default:
            $multiparams[$form_field['attr_name']] = array(
              'system_name' => $form_field['attr_name'],
              'title'       => $form_field['params']['title_'.$this->language],
              'value'       => htmlspecialchars(trim($this->input->post($form_field['attr_name'])))
            );
          break;                
        }
      }
      $errors = $this->_check_registration_multiparams($form_fields, $multiparams);
      if ($errors) {
        send_answer(array('errors' => array($errors[0])));
      }
      $errors = $this->_check_registration_params(array_merge($main_params, $inner_params));
      if ($errors) {
        send_answer(array('errors' => array($errors[0])));
      }
      
      $simvols = array ("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",
											  "A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
      $string = "";
      for ($i = 0; $i < 6; $i++) {
        shuffle ($simvols);
        $string = $string.$simvols[1];
      }
      $main_params['confirmation_code'] = $string;
      $main_params['active'] = 0;
      $main_params['password'] = md5($main_params['password']);
      $id = $this->accounts_model->create_user($main_params);
      if (!$id) {
        send_answer(array('errors' => array('Ошибка при создании аккаунта.')));
      }       

      if (!$this->accounts_model->set_params($id, $multiparams)) {
        $this->accounts_model->delete_user($id);
        send_answer(array('errors' => array('Не удалось сохранить параметры')));
      }
      
      if (isset($subscribe) && $subscribe == 1) {        
        $user_email = (isset($multiparams['email']) && $multiparams['email']['value'] ? $multiparams['email']['value'] : $main_params['username']);
        if (!$this->db->insert('subscribes_emails', array('user_id' => $id, 'email' => $user_email))) {
          $this->accounts_model->delete_user($id);
          send_answer(array('errors' => array('Не удалось подписать на рассылку')));
        }
      }
      
      if(!$this->_send_email($main_params, $multiparams, 'Вы успешно зарегистрировались', 'Зарегистрировался новый пользователь', $id)) {
        send_answer(array('errors' => array('Не удалось отправить сообщение администратору')));
      }

      send_answer();
    }
	}
  
  function _check_registration_params($params) {
    $errors = array();
    if (!preg_match('/^[-0-9a-z_\.]+@[-0-9a-z^\.]+\.[a-z]{2,4}$/i', $params['username'])) { 
      $errors[] = 'Некорректный Логин/Email'; 
    }
    $user = $this->db->get_where('pr_users', array('username' => $params['username']))->row_array();
    if ($user && $user['deleted'] == 0) { 
      $errors[] = 'Указанный email уже существует в базе. Необходимо перейти на страницу входа на сайт'; 
    } elseif ($user && $user['deleted'] == 1) {
      $errors[] = 'Указанный email уже существует в базе с пометкой "Удален". Укажите другой email или обратитесь к администраторам сайта.'; 
    }
    if (!$params['password']) { 
      $errors[] = 'Не указан пароль'; 
    }
    if ($params['password'] != $params['re_password']) { 
      $errors[] = 'Пароль не совпадает с повтором'; 
    }
    if (!$this->session->userdata('captcha')) { 
      $errors[] = 'Произошла ошибка при формировании проверочного числа.';
    }
    if (isset($params['captcha']) && ($params['captcha'] != $this->session->userdata('captcha'))) { 
      $errors[] = 'Неверное проверочное число'; 
    }
    return $errors;
  }
  
  function _check_registration_multiparams($form_fields, $multiparams) {
    $errors = array();
    $main_name_params = array('username', 'password', 're_password', 'captcha', 'agreement');
    foreach ($form_fields as $form_field) {
      if ($form_field['required'] == 1 && $form_field['type'] != 'submit' && !in_array($form_field['attr_name'],$main_name_params)) {                
        if (!$multiparams[$form_field['attr_name']]['value']) { 
          $errors[] = 'Не указан параметр "'.$form_field['params']['title_'.$this->language].'"'; 
        }           
        if (isset($multiparams['email']) && $multiparams['email']['value'] && !preg_match('/^[-0-9a-z_\.]+@[-0-9a-z^\.]+\.[a-z]{2,4}$/i', $multiparams['email']['value'])) {
          $errors[] = 'Некорректный Email';
        }
      }
    }
    return $errors;
  }
  
  function _send_email($main_params, $message_params, $subject_user, $subject_admin, $user_id) {
		if ($this->project['project_email']) {
			$this->load->library('email');
			$this->email->from($this->project['project_email']);
			$this->email->to($this->project['project_email']); 
			$this->email->subject($subject_admin.' "'.$this->project['domain'].'"');
      
      $email_user = $main_params['username'];
			
      $message =  "<html><body>";
			$message .=  "<h2>Новый пользователь в системе.</h2>";
      foreach($message_params as $message_param){
        if ($message_param['system_name'] == 'email') {
          $email_user = $message_param['value'];
        }
        if ($message_param['value']) {
          $message .= $message_param['title'].': '.htmlspecialchars_decode($message_param['value'])."<br/>";
        }
			}
      $message .= "<br/>".make_link('Перейти к пользователю>>','/admin/accounts/edit_user/'. $user_id .'/');
      $message .=  "</body></html>";
      
			$this->email->message($message); 
			$this->email->send();
			
			if ($email_user) {
				$this->email->from($this->project['project_email']);
				$this->email->to($email_user);
				$this->email->subject($subject_user.' "'.$this->project['domain'].'"');

        $message =  "<html><body>";
        $message .= '<h2>Здравствуйте.'."<br/>".'Спасибо за регистрацию на нашем сайте. '."</h2>";
        $message .= 'Для активации Вашей учётной записи перейдите по ссылке. '."<br/>"."<br/>";
        $message .= "<br/>".make_link('Активировать свою учетную запись>>','/activation_account?type='. md5('form_send_phone') .'&id='. md5($user_id))."<br/>"."<br/>";        
        $message .= 'Просим прощения, если это письмо попало к Вам случайно, в этом случае оставьте его без внимания.'."<br/>"."<br/>";
        $message .= 'С уважением,' ."<br/>". 'администраторы сайта '.$this->project['domain'];        
        $message .=  "</body></html>";

				$this->email->message($message);		
				if ($this->email->send()) {	
					return true;
				}  
			}	  
    }	
    return false;
  }
  
  /** Активация аккаунта
  **/
  function render_template_activation_account() {
    $type = ($this->uri->getParam('type') ? $this->uri->getParam('type') : '');
    switch ($type) {
      case md5('form_send_phone'):
        $user_id = ($this->uri->getParam('id') ? $this->uri->getParam('id') : '');
        $users = $this->accounts_model->get_users();
        foreach ($users as $user) {
          if (md5($user['id']) == $user_id) {
            $data['user'] = $user;
          }
        }
        if (isset($data['user']) && $data['user']) {
          $data = array(
            'user' => $this->accounts_model->get_user(array('id' => $data['user']['id']))
          );    
          return $this->render_template('templates/activation_account', $data);
        } else {
          header('Location: '. $this->lang_prefix . '/login?error=error_no_user');
        }        
      break;
      case md5('confirmation_code'):
        $user_id = ($this->uri->getParam('id') ? $this->uri->getParam('id') : '');
        $users = $this->accounts_model->get_users();
        foreach ($users as $user) {
          if (md5($user['id']) == $user_id) {
            $data['user'] = $user;
          }
        }
        return $this->render_template('templates/confirmation_code', $data);
      break;
    }
  }
  
  function activation_account_phone() {
    $user_id = (int)$this->input->post('user_id');
    $phone = $this->input->post('user_phone');
    if (!$user_id) {
      send_answer(array('errors' => array('Ошибка: не определен ID аккаунта. Обратитесь к администраторам сайта.')));
    }
    if (!$phone) {
      send_answer(array('errors' => array('Ошибка: не указан номер телефона.')));
    }
    $phone = preg_replace("/[^0-9]+/", "", $phone);
    $phone = preg_replace("/(?:\\+)?[78](?:\\s|-)*[\\(]?(\\d{3})?[\\)]?(?:\\s|-)*(\\d{3})(?:\\s|-)*(\\d{2})(?:\\s|-)*(\\d{2})/", "+7$1$2$3$4", $phone);
    if(!preg_match("/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{10}$/", $phone)){
      send_answer(array('errors' => array('Ошибка: некорректный номер телефона.'.$phone)));
    }
    if (!$this->db->update('users', array('phone' => $phone), array('id' => $user_id))) {
      send_answer(array('errors' => array('Не удалось сохранить номер телефона. Обратитесь к администраторам сайта.')));
    }
    if (!$this->send_sms_confirmation_code($user_id)) {
      send_answer(array('errors' => array('Не удалось отправить sms. Попробуйте получить код попозже или обратитесь к администраторам сайта.')));
    }
    send_answer();
  }
  
  /** Отправка смс с кодом подтверждения
  **/
  function send_sms_confirmation_code($user_id) {
    $user = $this->accounts_model->get_user(array('id' => $user_id));

    return true;
  }
  
  function activation_account() {
    $user_id = (int)$this->input->post('user_id');
    $confirmation_code = $this->input->post('confirmation_code');
    $user = $this->accounts_model->get_user(array('id' => $user_id));
    if (!$user) {
      send_answer(array('errors' => array('Не найден аккаунт. Обратитесь к администраторам сайта.')));
    }
    if ($user['confirmation_code'] == $confirmation_code) {
      if ($this->db->update('users', array('active' => 1), array('id' => $user_id))) {
        $this->session->set_userdata('user_id', $user['id']);
        send_answer();
      } else {
        send_answer(array('errors' => array('Не удалось активировать аккаунт. Обратитесь к администраторам сайта.')));
      }
    } else {
      send_answer(array('errors' => array('Неверный код подтверждения.')));
    }  
  }
  
  /** Редактирование параметров пользователя
  **/  
  function edit_profile($group_id = 0) {
    if (!$this->user_id) { return false; }
    $user_params = $this->accounts_model->get_user_params(($group_id ? $group_id : ''));
    foreach ($user_params as $item) {
      if (substr($item['system_name'],0,4) == 'file') {
        if ($_FILES[$item['system_name']]['name']) {
          $file = upload_file($_FILES[$item['system_name']]);      
          if (!$file) {
            send_answer(array('errors' => array('Ошибка при загрузке файла')));
          }
          resize_image($file,180,135);
          $multiparams[$item['system_name']] = $file;
        } elseif ($this->input->post($item['system_name'].'_delete')) {
          $multiparams[$item['system_name']] = '';
        }
      } else {
        $multiparams[$item['system_name']] = $this->input->post($item['system_name']);
      }
    }
    if (!$this->main_model->set_params('users', $this->user_id, $multiparams)) {
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    send_answer();
  }
  
  /** Пополнение счета 
  * cash_withdrawal - снятие наличных
  * deposits - пополнение
  **/
  function fill_balance() {
    if (!$this->user_id) {
      return header('Location: '. $this->lang_prefix . '/cabinet?type=fill_balance&error=1');
    }
    if (!preg_match('/^\d+(\.\d+)?$/', $this->input->post('sum'))) {
      return header('Location: '. $this->lang_prefix . '/cabinet?type=fill_balance&error=2');
    }
    
    $sum = number_format((int)$this->input->post('sum'), 2, '.', '');
    if (!$this->db->insert('user_transactions_balance', array('user_id' => $this->user_id, 'status' => 'deposits', 'sum' => $sum, 'comment' => 'Формирование заказа на пополнение счета'))) {
      return header('Location: '. $this->lang_prefix . '/cabinet?type=fill_balance&error=3');
    }
    $account_id = $this->db->query("SELECT LAST_INSERT_ID() as id")->row()->id;
    return header('Location: '. $this->lang_prefix . '/cabinet?type=account&account_id='.$account_id);
  }
}