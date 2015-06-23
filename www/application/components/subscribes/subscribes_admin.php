<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Subscribes_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('subscribes/models/subscribes_model');
  }
  
  /**
  * Меню компонента
  */
  function index() {
    return $this->render_template('admin/menu', array(
      'title' => 'Управление рассылками',
      'items' => array(
        array(
          'title' => 'Рассылки',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'subscribes_list/',
          'class' => 'subscribes-icon'
        ),
        array(
          'title' => 'Сообщения email рассылок',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'subscribe_messages/',
          'class' => 'subscribe_messages-title'
        ),
        array(
          'title' => 'Сообщения sms рассылок',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'subscribe_sms/',
          'class' => 'subscribe_messages-title'
        ),
      )
    ));
  }
  
  /**
  * Рассылки
  */
  function subscribes_list() { 
    return $this->render_template('templates/admin_subscribes', array(
			'items' => $this->subscribes_model->get_subscribes(),
    ));
  }
  
	/**
	 *  Создание рассылки
	 */	
  function create_subscribe() {
		$languages = $this->languages_model->get_languages(1, 0);
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление рассылки',
			'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_subscribe_process/',
				'blocks' => array(
					array(
						'title' 	=> 'Основные параметры',
						'fields' 	=> array(
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Внутреннее имя:',
								'name' 			  => 'title',
								'maxlength'   => 256,
                'description' => 'Используется только внутри панели администрирования',
								'req' 			  => true
							),
							array(
								'view' 			=> 'fields/text',
								'title' 		=> 'Название:',
								'name' 			=> 'name',
								'languages' => $languages,
								'maxlength' => 256,
								'req' 			=> true
							),
							array(
								'view' 			  => 'fields/select',
								'title' 		  => 'Тип:',
                'description' => '',
								'name' 			  => 'type',
								'options'  	  => array(
                  array(
                    'title' => 'email',
                  ),
                  array(
                    'title' => 'sms',
                  )
                ),
								'value_field' => 'title'
							),
							array(
								'view' 			  => 'fields/select',
								'title' 		  => 'Проекты:',
                'description' => 'Рассылки отправляются пользователям указанных проектов',
								'name' 			  => 'projects[]',
								'options'  	  => $this->projects_model->get_projects(),
								'multiple' 	  => true
							),
							array(
								'view' 			  => 'fields/checkbox',
								'title' 		  => 'Зарегистрированные пользователи:',
                'description' => 'Автоматически подключить зарегистрированных на текущий момент пользователей к данной рассылке',
								'name' 			  => 'auto_subscribe',
							),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'] .'subscribes_list/'
              )
						)
					),
				)
			)),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] .'subscribes_list/'
    ), TRUE);
  }
	
  function _create_subscribe_process() {
    $params = array(
      'title'          => htmlspecialchars(trim($this->input->post('title'))),
      'type'           => htmlspecialchars(trim($this->input->post('type'))),
      'auto_subscribe' => ($this->input->post('auto_subscribe') ? 1 : 0)
    );
    
    $languages = $this->languages_model->get_languages(1, 0);
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
      );
    }
    
    $projects = $this->input->post('projects');
    
    $errors = $this->_validate_subscribes_params(array_merge($params, $multiparams, array('projects' => $projects)));
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    $id = $this->subscribes_model->create_subscribe($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать рассылку')));
    }            
    
    if (!$this->main_model->set_params('subscribes', $id, $multiparams)) {
      $this->subscribes_model->delete_subscribe($id);
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    if (!$this->subscribes_model->set_subscribe_projects($id, $projects)) {
      $this->subscribes_model->delete_subscribe($id);
      send_answer(array('errors' => array('Не удалось сохранить проекты')));
    }
    
    if ($params['auto_subscribe'] == 1) {
      if ($params['type'] == 'email') {
        if (!$this->subscribes_model->set_subscribe_user_emails()) {
          $this->subscribes_model->delete_subscribe($id);
          send_answer(array('errors' => array('Не удалось прикрепить электронные адреса зарегистрированных пользователей')));
        }
      } else {
        if (!$this->subscribes_model->set_subscribe_user_phones()) {
          $this->subscribes_model->delete_subscribe($id);
          send_answer(array('errors' => array('Не удалось прикрепить электронные адреса зарегистрированных пользователей')));
        }
      }
    }
    
    if ($params['type'] == 'email') {
      if (!$this->subscribes_model->set_subscribe_emails_subscribes($id)) {
        $this->subscribes_model->delete_subscribe($id);
        send_answer(array('errors' => array('Не удалось прикрепить электронные адреса к рассылке')));
      }
    } else {
      if (!$this->subscribes_model->set_subscribe_phones_subscribes($id)) {
        $this->subscribes_model->delete_subscribe($id);
        send_answer(array('errors' => array('Не удалось прикрепить номера телефонов к рассылке')));
      }
    }
    
    send_answer();
	}
  
  function _validate_subscribes_params($params) {
    $languages = $this->languages_model->get_languages(1, 0);
    $errors = array();
    if (!$params['title']) { $errors[] = 'Не указано внутреннее имя'; }
    if (!$params['type']) { $errors[] = 'Не указан тип рассылки'; }
    foreach ($languages as $language) {
      if (!$params[$language['name']]['name']) {
        $errors[] = 'Не указано название';
      }
    }
    if (!$params['projects']) { $errors[] = 'Не выбран ни один проект'; }
    return $errors;
  }
  
	/**
	 *  Редактирование рассылки
	 */	
  function edit_subscribe($id) {
		$item = $this->subscribes_model->get_subscribe(array('id' => $id));
    $languages = $this->languages_model->get_languages(1, 0);
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование рассылки',
			'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_subscribe_process/'.$id.'/',
				'blocks' => array(
					array(
						'title' 	=> 'Основные параметры',
						'fields' 	=> array(
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Внутреннее имя:',
								'name' 			  => 'title',
								'maxlength'   => 256,
                'description' => 'Используется только внутри панели администрирования',
                'value'       => $item['title'],
								'req' 			  => true
							),
							array(
								'view' 			=> 'fields/text',
								'title' 		=> 'Название:',
								'name' 			=> 'name',
								'languages' => $languages,
								'maxlength' => 256,
                'value'     => $item['params'],
								'req' 			=> true
							),
							array(
								'view' 			  => 'fields/text',
								'title' 		  => 'Тип:',
                'description' => '',
								'name' 			  => 'type',
								'options'  	  => array(array('title' => 'email'),array('title' => 'sms')),
								'value_field' => 'title',
								'readonly' 		=> true,
                'value'       => $item['type'],
							),
							array(
								'view' 			  => 'fields/select',
								'title' 		  => 'Проекты:',
                'description' => 'Рассылки отправляются пользователям указанных проектов',
								'name' 			  => 'projects[]',
								'options'  	  => $this->projects_model->get_projects(),
                'value'       => $item['projects'],
								'multiple' 	  => true
							),
							array(
								'view' 			  => 'fields/checkbox',
								'title' 		  => 'Зарегистрированные пользователи:',
                'description' => 'Автоматически подключить зарегистрированных на текущий момент пользователей к данной рассылке',
								'name' 			  => 'auto_subscribe',
								'disabled' 		=> true,
                'checked'     => ($item['auto_subscribe'] == 1 ? true : false),
							),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Сохранить изменения',
                'type'     => 'ajax',
                'reaction' => 1
              )
						)
					),
				)
			)),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] .'subscribes_list/'
    ), TRUE);
  }
	
  function _edit_subscribe_process($id) {
		$item = $this->subscribes_model->get_subscribe(array('id' => $id));
    $params = array(
      'title'          => htmlspecialchars(trim($this->input->post('title'))),
      'type'           => htmlspecialchars(trim($this->input->post('type')))
    );
    
    $languages = $this->languages_model->get_languages(1, 0);
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
      );
    }    
    $projects = $this->input->post('projects');
    
    $errors = $this->_validate_subscribes_params(array_merge($params, $multiparams, array('projects' => $projects)));
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->subscribes_model->edit_subscribe($id, $params)) {
      send_answer(array('errors' => array('Не удалось сохранить рассылку')));
    }    

    if (!$this->main_model->set_params('subscribes', $id, $multiparams)) {
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    if (!$this->subscribes_model->set_subscribe_emails_subscribes($id)) {
      $this->subscribes_model->delete_subscribe($id);
      send_answer(array('errors' => array('Не удалось прикрепить электронные адреса к рассылке')));
    }
    
    if (!$this->subscribes_model->set_subscribe_projects($id, $projects)) {
      exit('Не удалось сохранить проекты');
    }
      
    if ($item['auto_subscribe'] == 1) {
      if ($item['type'] == 'email') {
        if (!$this->subscribes_model->set_subscribe_user_emails()) {
          $this->subscribes_model->delete_subscribe($id);
          send_answer(array('errors' => array('Не удалось прикрепить электронные адреса зарегистрированных пользователей')));
        }
      } else {
        if (!$this->subscribes_model->set_subscribe_user_phones()) {
          $this->subscribes_model->delete_subscribe($id);
          send_answer(array('errors' => array('Не удалось прикрепить электронные адреса зарегистрированных пользователей')));
        }
      }
    }
    send_answer();
	}
  
	/**
	 *  Удаление рассылки
	 */			
  function delete_subscribe($id) {
    $this->subscribes_model->delete_subscribe((int)$id);
    send_answer();
  }

  /**
  * Сообщения рассылок
  **/
  function subscribe_messages($page = 1) {
    $in_page = 20;
    $all_count = $this->subscribes_model->get_subscribe_messages_count();
    $pages = get_pages($page, $all_count, $in_page);
    $pagination_data = array(
      'pages' => $pages,
      'page' => $page,
      'prefix' => '/admin/subscribes/subscribe_messages/'
    );
    
    return $this->render_template('templates/admin_subscribe_messages', array(
      'items' => $this->subscribes_model->get_subscribe_messages(array(), 'tm DESC', $in_page, $in_page * ($page - 1)),
      'pagination' => $this->load->view('admin/pagination', $pagination_data, true),
    ));
  }
  
	/**
	 *  Создание письма
	 */	
  function create_subscribe_message() {
    return $this->render_template('admin/inner', array(
      'title' => 'Создание письма',
			'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_subscribe_message_process/',
				'blocks' => array(
					array(
						'title' 	=> 'Основные параметры',
						'fields' 	=> array(
							array(
								'view' 			  => 'fields/select',
								'title' 		  => 'Рассылка:',
                'description' => '',
								'name' 			  => 'subscribe_id',
								'options'  	  => $this->subscribes_model->get_subscribes("email"),
								'multiple' 	  => false,
								'req' 			  => true
							),
							array(
                'view'  => 'fields/text',
                'title' => 'Тема письма:',
                'name'  => 'subject',
                'req'   => true
              ),
              array(
                'view'    => 'fields/editor',
                'title'   => 'Текст письма:',
                'name'    => 'body',
                'toolbar' => 'Full',
                'id'      => 'subscribe_message',
                'req'     => true,
              ),
              array(
                'view'        => 'fields/file',
                'title'       => 'Файлы:',
                'description' => 'Выберите файлы для прикрепления к письму',
                'name'        => 'files[]', 
                'multiple' 	  => true,
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'] .'subscribe_messages/'
              )
						)
					),
				)
			)),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] .'subscribe_messages/'
    ), TRUE);
  }
	
  function _create_subscribe_message_process() {
    $params = array(
      'subject'      => htmlspecialchars(trim($this->input->post('subject'))),
      'body'         => htmlspecialchars(trim($this->input->post('body'))),
      'subscribe_id' => $this->input->post('subscribe_id'),
    );
    
    $errors = $this->_validate_subscribes_message_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    $id = $this->subscribes_model->create_subscribe_message($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать письмо')));
    }
    
    if ($_FILES['files']['name']) {
      $files = multiple_upload_file($_FILES['files']);
      if (isset($files['files_path']) && $files['files_path']) {
        foreach ($files['files_path'] as $num=> $path) {
          $params = array(
            'file_'.$num => $path
          );
          $this->main_model->set_params('subscribe_messages', $id, $params);
        }
			}
		}
    
    send_answer();
	}
  
  function _validate_subscribes_message_params($params) {
    $errors = array();
    if (!$params['subject']) { $errors[] = 'Не указана тема письма'; }
    if (!$params['body']) { $errors[] = 'Не указан текст письма'; }
    return $errors;
  }
  
	/**
	 *  Удаление письма
	 */			
  function delete_subscribe_message($id) {
    $this->subscribes_model->delete_subscribe_message((int)$id);
    send_answer();
  }  	
  
  /**
	 *  Редактирование/Отправление письма
	 */	
  function edit_subscribe_message($id) {
    $item = $this->subscribes_model->get_subscribe_message(array('id' => $id));
    $button_send = array(
      'view'        => 'fields/submit',
      'class'       => 'icon_small email_go',
      'title'       => 'Сохранить и отправить',
      'type'        => 'ajax',
      'failure'     => '1/',
      'reaction'    => $this->lang_prefix .'/admin'. $this->params['path'] .'subscribe_messages/'
    );
    $button_edit = array(
      'view'     => 'fields/submit',
      'class'    => 'icon_small accept_i_s',
      'title'    => 'Сохранить изменения',
      'type'     => 'ajax',
      'reaction' => 1
    );
    $form = array(
      'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_subscribe_message_process/'.$id.'/'.$item['subscribe_id'].'/',
      'blocks' => array(
        'main_params' => array(
          'title' 	=> 'Основные параметры',
          'fields' 	=> array(
            array(
              'view' 			  => 'fields/select',
              'title' 		  => 'Рассылка:',
              'description' => '',
              'name' 			  => 'subscribe_id',
              'options'  	  => $this->subscribes_model->get_subscribes("email"),
              'multiple' 	  => false,
              'value' 	    => $item['subscribe_id'],
              'req' 			  => true
            ),
            array(
              'view'  => 'fields/text',
              'title' => 'Тема письма:',
              'name'  => 'subject',
              'value' => $item['subject'],
              'req'   => true
            ),
            array(
              'view'    => 'fields/editor',
              'title'   => 'Текст письма:',
              'name'    => 'body',
              'toolbar' => 'Full',
              'id'      => 'subscribe_message',
              'value'   => $item['body'],
              'req'     => true,
            ),
            array(
              'view'     => 'fields/file',
              'title'    => 'Файлы:',
              'name'     => 'files[]',
              'value'    => $item['files'],
              'description' => 'Выберите файлы для прикрепления к письму',
              'multiple' => true,
            )
          )
        ),
      )
    );
    if ($item['sended'] != 1) {
      array_push($form['blocks']['main_params']['fields'], $button_send, $button_edit);
    }
    
    return $this->render_template('admin/inner', array(
      'title' => ($item['sended'] != 1 ? 'Редактирование/Отправление письма' : 'Дата рассылки письма: '.date('d.m.Y H:i', strtotime($item['tm']))),
			'html' => $this->view->render_form($form),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] .'subscribe_messages/'
    ), TRUE);
  }
	
  function _edit_subscribe_message_process($id, $subscribe_id, $send = 0) {
    $params = array(
      'subject'      => htmlspecialchars_decode(trim($this->input->post('subject'))),
      'body'         => trim($this->input->post('body')),
      'subscribe_id' => $this->input->post('subscribe_id'),
    );
    
    $errors = $this->_validate_subscribes_message_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->subscribes_model->edit_subscribe_message($id, $params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }    
    
    if (isset($_FILES['files']['name'][0]) && $_FILES['files']['name'][0]) {
      $files_del = $this->subscribes_model->get_subscribe_message_files($id);
      foreach ($files_del as $file) {
        $this->subscribes_model->delete_subscribe_message_file($file['id']);
      }

      $files = multiple_upload_file($_FILES['files']);
      if (isset($files['files_path']) && $files['files_path']) {
        foreach ($files['files_path'] as $num=> $path) {
          $file_params = array(
            'file_'.$num => $path,
          );
          $this->main_model->set_params('subscribe_messages', $id, $file_params);
        }
			}
		} else {
      $files = $this->subscribes_model->get_subscribe_message_files($id);
      foreach ($files as $file) {
        if ($this->input->post('files_'.$file['id'].'_delete')) {
          $this->subscribes_model->delete_subscribe_message_file($file['id']);
        }
      }
    }
    
    if ($send == 1) {
      $files = $this->subscribes_model->get_subscribe_message_files($id);
      if ($this->project['project_email']) {        
        $this->load->library('email');
        
        $emails = $this->db->query('SELECT pr_subscribes_emails.email FROM pr_subscribes_emails
                                    JOIN pr_subscribes_emails_subscribes ON pr_subscribes_emails_subscribes.email_id = pr_subscribes_emails.id 
                                    WHERE pr_subscribes_emails_subscribes.subscribe_id ='.$subscribe_id)->result_array();

        foreach($emails as $num=> $email) {          
          $this->send_mail($this->project['project_email'], $email['email'], $params['subject'], $params['body'], $files);
        }        
      }

      if (!$this->subscribes_model->edit_subscribe_message($id, array('sended' => 1))) {
        send_answer(array('errors' => array('Не удалось изменить статус письма')));
      }
    }
    send_answer();
	}
  
	function send_mail($from, $email, $subject = '', $body = '', $files = array()) {
    $this->email->clear(true);
    
    $this->email->from($from);
		$this->email->to($email);
		$this->email->subject(htmlspecialchars_decode($subject));		
    
    $message  = "<html><body>";
    $message .= htmlspecialchars_decode($body);
    $message .= "<div style='font-size:12px; color:#BDBDBD; margin-top:30px'>
                  Вы получили это письмо, так как подписаны на рассылки ".$this->project['domain'].".
                </div>";
    $message .= "</body></html>";
		
    $this->email->message($message);
    
    if ($files) {      
      foreach ($files as $file) {
        $this->email->attach(".".$file['value']);
      }
		} 
    
		$this->email->send();
	}
  
  /**
  * Сообщения sms рассылок
  **/
  function subscribe_sms($page = 1) {
    $in_page = 20;
    $all_count = $this->subscribes_model->get_subscribe_sms_count();
    $pages = get_pages($page, $all_count, $in_page);
    $pagination_data = array(
      'pages' => $pages,
      'page' => $page,
      'prefix' => '/admin/subscribes/subscribe_sms/'
    );
    
    return $this->render_template('templates/admin_subscribe_sms', array(
      'items' => $this->subscribes_model->get_subscribe_sms_all(array(), 'tm DESC', $in_page, $in_page * ($page - 1)),
      'pagination' => $this->load->view('admin/pagination', $pagination_data, true),
    ));
  }
  
	/**
	 *  Создание sms
	 */	
  function create_subscribe_sms() {
    return $this->render_template('admin/inner', array(
      'title' => 'Создание sms',
			'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_subscribe_sms_process/',
				'blocks' => array(
					array(
						'title' 	=> 'Основные параметры',
						'fields' 	=> array(
							array(
								'view' 			  => 'fields/select',
								'title' 		  => 'Рассылка:',
                'description' => '',
								'name' 			  => 'subscribe_id',
								'options'  	  => $this->subscribes_model->get_subscribes("sms"),
								'multiple' 	  => false,
								'req' 			  => true
							),
							array(
                'view'  => 'fields/text',
                'title' => 'Заголовок:',
                'description' => 'отображается в административной панели',
                'name'  => 'subject',
                'req'   => true
              ),
							array(
                'view'  => 'fields/text',
                'title' => 'Подпись отправителя:',
                'description' => 'Из личного кабинета на http://web.iqsms.ru/user/settings/',
                'name'  => 'sender',
                'value'  => 'CMC DUCKOHT',
                'req'   => true
              ),
              array(
                'view'    => 'fields/textarea',
                'title'   => 'Текст сообщения:',
                'name'    => 'body',
                'req'     => true,
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'] .'subscribe_sms/'
              )
						)
					),
				)
			)),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] .'subscribe_sms/'
    ), TRUE);
  }
	
  function _create_subscribe_sms_process() {
    $params = array(
      'subject'      => htmlspecialchars(trim($this->input->post('subject'))),
      'sender'       => htmlspecialchars(trim($this->input->post('sender'))),
      'body'         => htmlspecialchars(trim($this->input->post('body'))),
      'subscribe_id' => $this->input->post('subscribe_id'),
    );
    
    $errors = $this->_validate_subscribes_sms_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    $id = $this->subscribes_model->create_subscribe_sms($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать письмо')));
    }
    
    send_answer();
	}
  
  function _validate_subscribes_sms_params($params) {
    $errors = array();
    if (!$params['subscribe_id']) { $errors[] = 'Не выбрана рассылка'; }
    if (!$params['subject']) { $errors[] = 'Не указан заголовок'; }
    if (!$params['sender']) { $errors[] = 'Не указана подпись отправителя'; }
    if (!$params['body']) { $errors[] = 'Не указан текст сообщения'; }
    return $errors;
  }
  
	/**
	 *  Удаление sms
	 */			
  function delete_subscribe_sms($id) {
    $this->subscribes_model->delete_subscribe_sms((int)$id);
    send_answer();
  }  	
  
  /**
	 *  Редактирование/Отправление sms
	 */	
  function edit_subscribe_sms($id) {
    $item = $this->subscribes_model->get_subscribe_sms(array('id' => $id));
    $button_send = array(
      'view'        => 'fields/submit',
      'class'       => 'icon_small email_go',
      'title'       => 'Сохранить и отправить',
      'type'        => 'ajax',
      'failure' => '1/',
      'reaction'    => $this->lang_prefix .'/admin'. $this->params['path'] .'subscribe_sms/'
    );
    $button_edit = array(
      'view'     => 'fields/submit',
      'class'    => 'icon_small accept_i_s',
      'title'    => 'Сохранить изменения',
      'type'     => 'ajax',
      'reaction' => 1
    );
    $form = array(
      'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_subscribe_sms_process/'.$id.'/'.$item['subscribe_id'].'/',
      'blocks' => array(
        'main_params' => array(
          'title' 	=> 'Основные параметры',
          'fields' 	=> array(
            array(
              'view' 			  => 'fields/select',
              'title' 		  => 'Рассылка:',
              'description' => '',
              'name' 			  => 'subscribe_id',
              'options'  	  => $this->subscribes_model->get_subscribes("sms"),
              'multiple' 	  => false,
              'value' 	    => $item['subscribe_id'],
              'req' 			  => true
            ),
            array(
              'view'  => 'fields/text',
              'title' => 'Заголовок:',
              'description' => 'отображается в административной панели',
              'name'  => 'subject',
              'value' => $item['subject'],
              'req'   => true
            ),
            array(
              'view'  => 'fields/text',
              'title' => 'Подпись отправителя:',
              'description' => 'Из личного кабинета на http://web.iqsms.ru/user/settings/',
              'name'  => 'sender',
              'value' => $item['sender'],
              'req'   => true
            ),
            array(
              'view'    => 'fields/textarea',
              'title'   => 'Текст сообщения:',
              'name'    => 'body',
              'value'   => $item['body'],
              'req'     => true,
            ),
          )
        ),
      )
    );
    if ($item['sended'] != 1) {
      array_push($form['blocks']['main_params']['fields'], $button_send, $button_edit);
    }
    
    return $this->render_template('admin/inner', array(
      'title' => ($item['sended'] != 1 ? 'Редактирование/Отправление sms' : 'Дата рассылки sms: '.date('d.m.Y H:i', strtotime($item['tm']))),
			'html' => $this->view->render_form($form),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] .'subscribe_sms/'
    ), TRUE);
  }
	
  function _edit_subscribe_sms_process($id, $subscribe_id, $send = 0) {
    $params = array(
      'subject'      => htmlspecialchars(trim($this->input->post('subject'))),
      'sender'       => htmlspecialchars(trim($this->input->post('sender'))),
      'body'         => htmlspecialchars(trim($this->input->post('body'))),
      'subscribe_id' => $this->input->post('subscribe_id'),
    );
    
    $errors = $this->_validate_subscribes_sms_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->subscribes_model->edit_subscribe_sms($id, $params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    if ($send == 1) {
      $phones = $this->db->query('SELECT pr_subscribes_phones.phone, pr_subscribes_phones.user_id FROM pr_subscribes_phones
                                  JOIN pr_subscribes_phones_subscribes ON pr_subscribes_phones_subscribes.phone_id = pr_subscribes_phones.id 
                                  WHERE pr_subscribes_phones_subscribes.subscribe_id ='.$subscribe_id)->result_array();

      // Отправка sms сообщений
      $send = $this->main_model->send_sms($id, $params['sender'], $phones, $params['body']);
      if ($send['error']) {
        send_answer(array('errors' => array($send['error'])));
      }

      /*
      if (!$this->subscribes_model->edit_subscribe_sms($id, array('sended' => 1))) {
        send_answer(array('errors' => array('Не удалось изменить статус сообщения на "Отправлено"')));
      }
      */
    }
    send_answer();
	}
  
  /* 
  * Получение статустов отправленных сообщений очереди validate_status_queue/QueueMessages
  */
  function validate_status_queue($queue_name) {
    $this->load->library('IqsmsJsonGate');
    $gate = new IqsmsJsonGate('spkvi002401', '903076');
    echo "<pre>";
    print_r ($gate->statusQueue($queue_name, 100));
    echo "</pre>"; 
  }
}