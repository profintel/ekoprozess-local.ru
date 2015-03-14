<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Languages_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
  }
  
  /**
   * Просмотр списка доступных языков
  **/ 
  function index() {
    return $this->render_template('templates/index', array(
      'languages' => $this->languages_model->get_languages(FALSE, 0)
    ));
  }
  
  /**
   * Активация языка
  **/ 
  function enable($id) {
    $this->languages_model->update_language((int)$id, array('active' => 1));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path']);
  }
  
  /**
   * Деактивация языка
  **/ 
  function disable($id) {
    $this->languages_model->update_language((int)$id, array('active' => 0));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path']);
  }
  
  /**
   * Создание языка
  **/ 
  function create() {
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление языка',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_process/',
        'blocks' => array(
          array(
            'title'  => 'Основные параметры',
            'fields' => array(
              array(
                'view'  => 'fields/text',
                'title' => 'Название:',
                'name'  => 'title',
                'req'   => TRUE
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Системное имя:',
                'description' => 'Двухбуквенное обозначение языка',
                'name'        => 'name',
                'maxlength'   => 2,
                'req'         => TRUE
              ),
              array(
                'view'  => 'fields/file',
                'title' => 'Иконка:',
                'name'  => 'icon',
                'req'   => TRUE
              ),
              array(
                'view'  => 'fields/checkbox',
                'title' => 'Включен',
                'name'  => 'active'
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Добавить',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          )
        )
      ))
    ), TRUE);
  }
  
  function _create_process() {
    $params = array(
      'name'   => trim($this->input->post('name')),
      'title'  => htmlspecialchars(trim($this->input->post('title'))),
      'icon'   => $_FILES['icon']['name'],
      'active' => ($this->input->post('active') ? 1 : 0)
    );
    
    $errors = $this->_validate($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    $params['icon'] = upload_file($_FILES['icon']);
    if (!$params['icon']) {
      send_answer(array('errors' => array('Не удалось загрузить файл иконки')));
    }
    
    if (!$this->languages_model->create_language($params)) {
      unlinks($params['icon']);
      send_answer(array('errors' => array('Не удалось добавить язык')));
    }
    
    send_answer();
  }
  
  function _validate($params, $id = 0) {
    $errors = array();
    if (!$params['title']) {
      $errors[] = 'Не указано название';
    }
    if (!$this->form_validation->exact_length($params['name'], 2) || !$this->form_validation->alpha($params['name'])) {
      $errors[] = 'Недопустимое системное имя';
    }
    if (!$this->main_model->is_available('languages', $params['name'], $id)) {
      $errors[] = 'Указанное системное имя занято';
    }
    if (isset($params['icon']) && !$params['icon']) {
      $errors[] = 'Не выбрана иконка';
    }
    return $errors;
  }
 
  /**
   * Редактирование языка
  **/  
  function edit($id) {
    $language = $this->languages_model->get_language((int)$id);
    if (!$language) {
      show_error('Язык не найден');
    }
    
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование языка',
      'html'  => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_process/'. $language['id'] .'/',
        'blocks' => array(
          array(
            'title'  => 'Основные параметры',
            'fields' => array(
              array(
                'view'  => 'fields/text',
                'title' => 'Название:',
                'name'  => 'title',
                'value' => $language['title'],
                'req'   => TRUE
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Системное имя:',
                'description' => 'Двухбуквенное обозначение языка',
                'name'        => 'name',
                'value'       => $language['name'],
                'maxlength'   => 2,
                'req'         => TRUE
              ),
              array(
                'view'    => 'fields/file',
                'title'   => 'Иконка:',
                'name'    => 'icon',
                'value'   => $language['icon'],
                'req'     => TRUE
              ),
              array(
                'view'    => 'fields/checkbox',
                'title'   => 'Включен',
                'name'    => 'active',
                'checked' => $language['active']
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Сохранить изменения',
                'type'     => 'ajax',
                'reaction' => 'reload'
              )
            )
          )
        )
      ))
    ), TRUE);
  }
  
  function _edit_process($id) {
    $language = $this->languages_model->get_language((int)$id);
    if (!$language) {
      show_error('Язык не найден');
    }
    
    $params = array(
      'name'   => trim($this->input->post('name')),
      'title'  => htmlspecialchars(trim($this->input->post('title'))),
      'active' => ($this->input->post('active') ? 1 : 0)
    );
    
    if ($this->input->post('icon_delete')) {
      $params['icon'] = '';
    }
    if ($_FILES['icon']['name']) {
      $params['icon'] = $_FILES['icon']['name'];
    }
    
    $errors = $this->_validate($params, $language['id']);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (isset($params['icon'])) {
      $params['icon'] = upload_file($_FILES['icon']);
      if (!$params['icon']) {
        send_answer(array('errors' => array('Не удалось загрузить файл иконки')));
      }
    }
    
    if (!$this->languages_model->update_language($language['id'], $params)) {
      unlinks($params['icon']);
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    if (isset($params['icon'])) {
      unlinks($language['icon']);
    }
    
    send_answer();
  }
  
  /**
   * Удаление языка
  **/ 
  function delete($id) {
    $language = $this->languages_model->get_language((int)$id);
    if (!$language) {
      send_answer(array('errors' => array('Язык не найден')));
    }
    
    if (!$this->languages_model->delete_language($language['id'])) {
      send_answer(array('errors' => array('Не удалось удалить язык')));
    }
    
    unlinks($language['icon']);
    send_answer();
  }
  
}