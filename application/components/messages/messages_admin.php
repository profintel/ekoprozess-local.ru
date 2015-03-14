<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Messages_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
			
		$this->load->model('messages/models/messages_model');
		$this->load->model('publication/models/publication_model');
		$this->load->model('projects/models/projects_model');
  }
	/**
	 * Просмотр сообщений
	 * @param $component_name - название компонента, с которого были получены сообщения
	 */	
  function index($component_name = '', $owner_id = 0) {
    if (!$component_name) {
      $data = array(
        'items'            => $this->messages_model->get_categories(),
        'component_name'   => $component_name,
        'owner_id'         => $owner_id,
        'categorie_childs' => false,
        'title'            => "Управление сообщениями",
      );
    } else {
      switch ($component_name) {
        case 'publication':
          if ($owner_id) {
            $categorie_child = $this->publication_model->get_publication_one(array('id' => $owner_id));
            $data = array(
              'items'            => $this->messages_model->get_messages($component_name, array('owner_id' => $owner_id)),
              'component_name'   => $component_name,
              'owner_id'         => $owner_id,
              'categorie_childs' => true,
              'title'            => 'Управление сообщениями категории "Публикации": "'.$categorie_child['title'].'"',
            );         
          } else {
            $data = array(
              'component_name' => $component_name,
              'owner_id'         => $owner_id,
              'categorie_childs' => true,
              'title'            => 'Управление сообщениями категории "Публикации"',
            );
            $data['items'] = array();
            $items = $this->messages_model->get_categorie_childs($component_name);
            foreach ($items as $item) {
              $data['items'][] = $this->publication_model->get_publication_one(array('id' => $item['owner_id']));
            }      
          }
        break;
        case 'projects':
          if ($owner_id) {
            $categorie_child = $this->projects_model->get_page($owner_id);
            $data = array(
              'items'            => $this->messages_model->get_messages($component_name, array('owner_id' => $owner_id)),
              'component_name'   => $component_name,
              'owner_id'         => $owner_id,
              'categorie_childs' => true,
              'title'            => 'Управление сообщениями категории "Проекты": "'.$categorie_child['title'].'"',
            );         
          } else {
            $data = array(
              'component_name' => $component_name,
              'owner_id'         => $owner_id,
              'categorie_childs' => true,
              'title'            => 'Управление сообщениями категории "Проекты"',
            );
            $data['items'] = array();
            $items = $this->messages_model->get_categorie_childs($component_name);
            foreach ($items as $item) {
              $data['items'][] = $this->projects_model->get_page($item['owner_id']);
            }      
          }          
        break;
        default: 
          $data = array(
            'items'            => $this->messages_model->get_messages($component_name),
            'component_name'   => $component_name,
            'owner_id'         => $owner_id,
            'categorie_childs' => false,
            'title'            => "Управление сообщениями",
          );
        break;
      }
    }
    return $this->render_template('templates/index', $data);
  }
	
	/**
	 * Редактирование сообщения
	 * @param $id - id сообщения
	 */
  function edit_message($id) {
		$blocks = array();
		$item =  $this->messages_model->get_message($id);
    $fields = array(
      array(
        'view' => 'fields/readonly',
        'title' => 'Дата:',
        'value' => $item['tm']
      ),
      array(
        'view' => 'fields/readonly',
        'title' => 'IP-адрес:',
        'value' => $item['user_ip']
      ),
      array(
        'view' => 'fields/readonly',
        'title' => 'Пользователь:',
        'value' => (isset($item['user']) ? $item['user']['username'] : '')
      )
    );
		foreach ($item['params'] as $param) {      
      foreach ($param['message_params'] as $message_param) {
				switch ($message_param['field']) {
					case 'checkbox':
						$fields[] = array(
							'view' 	=> 'fields/checkbox',
							'title' => $message_param['title'].':',
							'name' 	=> $message_param['name'],
							'checked' => ($message_param['value'] == 1 ? true : false),
							'req'   => ($message_param['req'] == 1 ? 1 : 0) 
						);						
					break;
					default:
						$fields[] = array(
							'view' 	=> 'fields/'.$message_param['field'],
							'title' => $message_param['title'],
							'name' 	=> $message_param['name'],
							'value' => $message_param['value'],
							'req'   => ($message_param['req'] == 1 ? 1 : 0) 
						);						
					break;
				}

			}
      $fields[] = array(
        'view' 	=> 'fields/checkbox',
        'title' => 'Сообщение опубликовано:',
        'name' 	=> 'active',
        'checked' => ($item['active'] == 1 ? true : false)
      );	
			$fields[] = array(
				'view'     => 'fields/submit',
				'class'    => 'icon_small accept_i_s',
				'title'    => 'Редактировать',
				'type'     => 'ajax',
				'reaction' => 1
			);
			
			$blocks[] = array(
				'title' 	=> $param['title'],
				'fields' 	=> $fields
			);
		}
		
		return $this->render_template('admin/inner', array(
      'title' => 'Редактирование сообщения',
			'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_message_process/'.$id.'/',
				'blocks' => $blocks
			)),
			'back' => $this->lang_prefix .'/admin'. $this->params['path'] . $item['component_name'] .'/'
    ), TRUE);
  }
	
  function _edit_message_process($id) {
		$params = array();
		$item =  $this->messages_model->get_message($id);
		if ($item['params']) {
			foreach ($item['params'] as $param) {
				foreach ($param['message_params'] as $message_param) {
					$params[$message_param['name']] = array('name' => $message_param['name'], 'value' => htmlspecialchars_decode($this->input->post($message_param['name'])));
				}	
			}	
		}
		
    $errors = $this->_validate_params($id);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 

    if (!$this->messages_model->set_message_params($id, $params)) {
      send_answer(array('errors' => array('Не удалось отредактировать')));
    }
    
    send_answer();
	}	
	
  function _validate_params($id) {
		$error = array();
		$item =  $this->messages_model->get_message($id);
		if ($item['params']) {
			foreach ($item['params'] as $param) {
				foreach ($param['message_params'] as $message_param) {
					if ($message_param['req'] == 1 && !$this->input->post($message_param['name'])){
						$error[] = 'Не указан параметр "'.$message_param['title'].'"';
					}
				}	
			}	
		}
		return $error;
	}	

	/**
	 * Удаление категории сообщений
	 * @param $id - id сообщения
	 */			
  function delete_category($component_name, $owner_id = 0) {
    $params['component_name'] = $component_name;
    if ($owner_id) {
      $params['owner_id'] = $owner_id;
    }
    $this->messages_model->delete_messages($params);
		send_answer();
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] . $component_name .'/');
	}	
	
	/**
	 * Удаление сообщения
	 * @param $id - id сообщения
	 */			
  function delete_message($component_name, $id) {
		$message_params = $this->db->get_where('pr_messages_params', array('message_id' => $id))->result_array();
		foreach ($message_params as $param) {
			if ($param['name'] == "image" && $param['value']) {
				$image = $param['value'];
				$parts = explode('.', $image);
				$ext = strtolower(array_pop($parts));
				$name = implode('.', $parts);
				@unlink($_SERVER['DOCUMENT_ROOT'] . $name .'_'. 180 .'_'. 135 .'.'. $ext);
				@unlink($_SERVER['DOCUMENT_ROOT'] . $image);				
			}
		}    
		$this->messages_model->delete_message($id);
		send_answer();
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] . $component_name .'/');
	}

	/**
	 *  Включение сообщения
	 * @param $id - id сообщения
	 */	  
  function enable_message($component_name, $id) {
    $this->messages_model->edit_message((int)$id, array('active' => 1));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] . $component_name .'/');
  }

	/**
	 *  Выключение сообщения
	 * @param $id - id сообщения
	 */	    
  function disable_message($component_name, $id) {
    $this->messages_model->edit_message((int)$id, array('active' => 0));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] . $component_name .'/');
  }		

}