<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_subscribes_admin extends CI_Component {
  
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
          'title' => 'Сообщения рассылок',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'subscribe_messages/',
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
								'title' 		  => 'Проекты:',
                'description' => 'Рассылки отправляются пользователям указанных проектов',
								'name' 			  => 'projects[]',
								'options'  	  => $this->projects_model->get_projects(),
								'multiple' 	  => true
							),
							array(
								'view' 			  => 'fields/checkbox',
								'title' 		  => 'Зарегистрированные пользователи:',
                'description' => 'Автоматически подключать зарегистрированных пользователей к данной рассылке',
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
      if (!$this->subscribes_model->set_subscribe_user_emails()) {
        $this->subscribes_model->delete_subscribe($id);
        send_answer(array('errors' => array('Не удалось прикрепить электронные адреса зарегистрированных пользователей')));
      }
    }
    
    if (!$this->subscribes_model->set_subscribe_emails_subscribes($id)) {
      $this->subscribes_model->delete_subscribe($id);
      send_answer(array('errors' => array('Не удалось прикрепить электронные адреса к рассылке')));
    }
    
    send_answer();
	}
  
  function _validate_subscribes_params($params) {
    $languages = $this->languages_model->get_languages(1, 0);
    $errors = array();
    if (!$params['title']) { $errors[] = 'Не указано внутреннее имя'; }
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
                'description' => 'Автоматически подключать зарегистрированных пользователей к данной рассылке',
								'name' 			  => 'auto_subscribe',
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
    $params = array(
      'title'          => htmlspecialchars(trim($this->input->post('title'))),
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
    
    if (!$this->subscribes_model->edit_subscribe($id, $params)) {
      send_answer(array('errors' => array('Не удалось сохранить рассылку')));
    }    

    if (!$this->main_model->set_params('subscribes', $id, $multiparams)) {
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    if ($params['auto_subscribe'] == 1) {
      if (!$this->subscribes_model->set_subscribe_user_emails()) {
        $this->subscribes_model->delete_subscribe($id);
        send_answer(array('errors' => array('Не удалось прикрепить электронные адреса зарегистрированных пользователей')));
      }
    }
    
    if (!$this->subscribes_model->set_subscribe_emails_subscribes($id)) {
      $this->subscribes_model->delete_subscribe($id);
      send_answer(array('errors' => array('Не удалось прикрепить электронные адреса к рассылке')));
    }
    
    if (!$this->subscribes_model->set_subscribe_projects($id, $projects)) {
      exit('Не удалось сохранить проекты');
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
      'title' => 'Добавление рассылки',
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
								'options'  	  => $this->subscribes_model->get_subscribes(),
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
      'uri_postfix' => '1/',
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
      'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_subscribe_message_process/'.$id.'/',
      'blocks' => array(
        'main_params' => array(
          'title' 	=> 'Основные параметры',
          'fields' 	=> array(
            array(
              'view' 			  => 'fields/select',
              'title' 		  => 'Рассылка:',
              'description' => '',
              'name' 			  => 'subscribe_id',
              'options'  	  => $this->subscribes_model->get_subscribes(),
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
	
  function _edit_subscribe_message_process($id, $send = 0) {
    $params = array(
      'subject'      => htmlspecialchars(trim($this->input->post('subject'))),
      'body'         => htmlspecialchars(trim($this->input->post('body'))),
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
          $params = array(
            'file_'.$num => $path,
          );
          $this->main_model->set_params('subscribe_messages', $id, $params);
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
        $subject = $params['subject'];
        $message  = '<html><body>';
        $message .= $params['body'];
        $message .= '</body></html>';
        
        $emails = $this->db->query('SELECT pr_subscribes_emails.email FROM pr_subscribes_emails
                                    JOIN pr_subscribes_emails_subscribes ON pr_subscribes_emails_subscribes.email_id = pr_subscribes_emails.id 
                                    WHERE pr_subscribes_emails_subscribes.subscribe_id ='.$id)->result_array();        
        foreach($emails as $email) {
          $this->send_mail($this->project['project_email'], $email['email'], $subject, $message, $files);
        }        
      }
      
      if (!$this->subscribes_model->edit_subscribe_message($id, array('sended' => 1))) {
        send_answer(array('errors' => array('Не удалось изменить статус письма')));
      }
    }
    send_answer();
	}
  
	function send_mail($from, $email, $subject = '', $message = '', $files = array()) {
    $this->email->from($from);
		$this->email->to($email);
		$this->email->subject($subject);
		if ($files) {
      foreach ($files as $file) {
        $this->email->attach($file['value']);
      }
		}
    $message .= "<div style='font-size:10px; color:#BDBDBD; margin-top:30px'>
                Вы получили это письмо, так как подписаны на рассылки ".$this->project['domain'].".  
                Если вы больше не хотите получать наши рассылки, вы можете 
                <a href='".$this->project['domain']."/component/subscribes/unsubscribe_email?email=".$email."'>отписаться</a>
                </div>";
		
    $this->email->message($message);
		$this->email->send();
	}
}