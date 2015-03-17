<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Administrators_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('administrators/models/administrators_model');
  }
  
  /**
  * Меню компонента
  */
  function index() {
    return $this->render_template('admin/menu', array(
      'title' => 'Управление списком администраторов',
      'items' => array(
        array(
          'title' => 'Моя учетная запись',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'profile/',
          'class' => 'accounts-profile-icon'
        ),
        array(
          'title' => 'Администраторы',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'admins/',
          'class' => 'accounts-admins-icon'
        )
      )
    ));
  }
  
  /**
  * Вывод списка администраторов
  */
  function admins() {    
    return $this->render_template('templates/admins', array(
      'admins' => $this->administrators_model->get_admins()
    ));
  }
  
  /**
   * Создание администратора
   */  
  function create_admin() {
    $languages = $this->languages_model->get_languages(1, 0);
    return $this->render_template('admin/inner', array(
      'title' => 'Создание администратора',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_admin_process/',
        'blocks' => array(
          array(
            'title'   => 'Параметры',
            'fields'   => array(
              array(
                'view'       => 'fields/text',
                'title'     => 'Логин:',
                'name'       => 'username',
                'maxlength' => 256,
                'description' => 'Имя пользователя',
                'req'       => true
              ),
              array(
                'view'       => 'fields/password',
                'title'     => 'Пароль:',
                'name'       => 'password',
                'maxlength' => 256,
                'description' => 'Текущий пароль',
                'req'       => true
              ),
              array(
                'view'       => 'fields/password',
                'title'     => 'Повтор пароля:',
                'name'       => 're_password',
                'maxlength' => 256,
                'req'       => true
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']. 'admins/'
              )
            )
          )
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] . 'admins/'
    ), TRUE);
  }
  
  function _create_admin_process() {
    $params = array(
      'username' => htmlspecialchars(trim($this->input->post('username'))),
      'password' => htmlspecialchars(trim($this->input->post('password')))
    );
    $add_params = array(
      're_password' => htmlspecialchars(trim($this->input->post('re_password'))),
    );

    $errors = $this->_validate_create_params(array_merge($params, $add_params));
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    $params['password'] = md5($params['password']);
    $id = $this->administrators_model->create_admin($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать учетную запись')));
    }
    
    send_answer();
  }  
  
  function _validate_create_params($params) {
    $errors = array();
    if (!$params['username']) { $errors[] = 'Не указан логин'; }
    if ($this->db->get_where('admins', array('username' => $params['username']))->num_rows()) {
      $errors[] = 'Пользователь с таким логином уже существует в базе данных'; 
    }
    if (!$params['password']) { $errors[] = 'Не указан пароль'; }
    if ($params['password'] != $params['re_password']) { $errors[] = 'Пароль не совпадает с повтором'; }
    return $errors;
  }  
  
  /**
   * Смена пароля текущего пользователя
   */  
  function profile() {
    $languages = $this->languages_model->get_languages(1, 0);
    return $this->render_template('admin/inner', array(
      'title' => 'Смена пароля',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_admin_process/'.$this->admin_id.'/',
        'blocks' => array(
          array(
            'title'   => 'Смена пароля',
            'fields'   => array(
              array(
                'view'       => 'fields/password',
                'title'     => 'Пароль:',
                'name'       => 'old_password',
                'maxlength' => 256,
                'description' => 'Текущий пароль',
                'req'       => true
              ),
              array(
                'view'       => 'fields/password',
                'title'     => 'Новый пароль:',
                'name'       => 'password',
                'maxlength' => 256,
                'req'       => true
              ),
              array(
                'view'       => 'fields/password',
                'title'     => 'Повтор пароля:',
                'name'       => 're_password',
                'maxlength' => 256,
                'req'       => true
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Отправить',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          )
        )
      )),
    ), TRUE);
  }  
  
  /**
   * Смена пароля администратора
   */  
  function edit_admin($id) {
    $languages = $this->languages_model->get_languages(1, 0);
    return $this->render_template('admin/inner', array(
      'title' => 'Смена пароля',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_admin_process/'.$id.'/',
        'blocks' => array(
          array(
            'title'   => 'Смена пароля',
            'fields'   => array(
              array(
                'view'       => 'fields/password',
                'title'     => 'Пароль:',
                'name'       => 'old_password',
                'maxlength' => 256,
                'description' => 'Текущий пароль',
                'req'       => true
              ),
              array(
                'view'       => 'fields/password',
                'title'     => 'Новый пароль:',
                'name'       => 'password',
                'maxlength' => 256,
                'req'       => true
              ),
              array(
                'view'       => 'fields/password',
                'title'     => 'Повтор пароля:',
                'name'       => 're_password',
                'maxlength' => 256,
                'req'       => true
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Отправить',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']. 'admins/'
              )
            )
          )
        )
      )),
    ), TRUE);
  }

  function _edit_admin_process($id) {
    $params = array(
      'password' => htmlspecialchars(trim($this->input->post('password'))),
    );
    $add_params = array(
      'old_password' => htmlspecialchars(trim($this->input->post('old_password'))),
      're_password' => htmlspecialchars(trim($this->input->post('re_password'))),
    );

    $errors = $this->_validate_edit_params(array_merge($params, $add_params));
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    $params['password'] = md5($params['password']);    
    if (!$this->administrators_model->edit_admin($id, $params)) {
      send_answer(array('errors' => array('Не удалось отредактировать учетную запись')));
    }
    
    send_answer();
  }
  
  function _validate_edit_params($params) {
    $errors = array();
    if (!$params['old_password']) { $errors[] = 'Не указан пароль'; }
    if (!$params['password']) { $errors[] = 'Не указан новый пароль'; }
    if ($params['password'] != $params['re_password']) { $errors[] = 'Пароль не совпадает с повтором'; }
    return $errors;
  }    
  
  /**
   * Удаление администратора
  **/    
  function delete_admin($id) {
    if ($this->admin_id == $id) {
      send_answer(array('errors' => array('Невозможно удалить свою учетную запись.')));
    } else {
      $this->administrators_model->delete_admin($id);
    }
    send_answer();
  }    
}