<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Workshops_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('workshops/models/workshops_model');
  }

  /**
  * Просмотр списка цехов
  **/
  function index() {
    return $this->render_template('admin/items', array(
      'title'           => 'Список цехов',
      'search_path'     => '/admin'.$this->params['path'].'workshop/',
      'search_title'    => '',
      'component_item'  => array('name' => 'workshop', 'title' => ''),
      'move_path'       => '/admin/clients/_move_workshop/',
      'items'           => $this->workshops_model->get_workshops(),
      'back'            => $this->lang_prefix .'/admin/libs/',
    ));
  }

  /**
   *  Создание цеха
   */  
  function create_workshop() {
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление цеха',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_workshop_process/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Название:',
                'name'        => 'title',
                'id'          => 'admin-item-title',
                'maxlength'   => 256,
                'req'         => true
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          ),
        )
      )),
      'back'  => $this->lang_prefix .'/admin'. $this->params['path']
    ), true);
  }

  function _create_workshop_process() {    
    $params = array(
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'order'       => $this->workshops_model->get_workshop_order($this->input->post('parent_id'))
    );

    $errors = $this->_validate_workshop_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    $id = $this->workshops_model->create_workshop($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать объект')));
    }

    send_answer();
  }
  
  function _validate_workshop_params($params) {
    $errors = array();
    if (!$params['title']) { $errors[] = 'Не указано название'; }
    return $errors;
  }

  /*
  *  Редактирование цеха
  */  
  function edit_workshop($id) {
    $item = $this->workshops_model->get_workshop(array('id' => $id));
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование цеха',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_workshop_process/'.$id.'/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'  => 'fields/text',
                'id'    => 'admin-item-title',
                'title' => 'Название:',
                'name'  => 'title',
                'value' => $item['title'],
                'req'   => true
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Сохранить',
                'type'     => 'ajax',
                'reaction' => 'reload'
              )
            )
          )
        )
      )),
      'back'  => $this->lang_prefix .'/admin'. $this->params['path']
    ), true);
  }

  function _edit_workshop_process($id) {    
    $params = array(
      'title'     => htmlspecialchars(trim($this->input->post('title'))),
    );

    $errors = $this->_validate_workshop_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    if (!$this->workshops_model->update_workshop($id,$params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }

    send_answer();
  }
  
  /**
   * Перемещение цеха
  **/
  function _move_workshop() {
    $item_id = (int)str_replace('item-', '', $this->input->post('page'));
    $item = $this->workshops_model->get_workshop(array('id'=>(int)$item_id));
    
    if (!$item) {
      send_answer(array('messages' => array('Перемещаемый объект не найден')));
    }
    
    $dest_id = (int)str_replace('item-', '', $this->input->post('dest'));
    $dest = $this->workshops_model->get_workshop(array('id'=>(int)$dest_id));
    if (!$dest) {
      send_answer(array('messages' => array('Целевой объект не найден')));
    }
    
    $placement = trim($this->input->post('placement'));
    
    if (!$this->workshops_model->move_workshop($item_id, $dest_id, $placement)) {
      send_answer(array('messages' => array('Не удалось переместить объект')));
    }
    
    send_answer();
  }

  /**
   *  Удаление цеха
   * @param $id - id цеха
   */      
  function delete_workshop($id) {
    $this->workshops_model->delete_workshop((int)$id);
    send_answer();
  }
}