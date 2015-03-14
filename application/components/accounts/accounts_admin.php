<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accounts_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('accounts/models/accounts_model');
  }
  
  /**
  * Просмотр меню компонента
  */
  function index() {
    return $this->render_template('admin/menu', array(
      'title' => 'Управление учетными записями',
      'items' => array(
        array(
          'title' => 'Группы пользователей',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'groups/',
          'class' => 'accounts-groups-icon'
        ),
        array(
          'title' => 'Пользователи',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'users/',
          'class' => 'accounts-users-icon'
        ),
        array(
          'title' => 'Параметры пользователей',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'user_params/',
          'class' => 'accounts-user_params-icon'
        ),
        array(
          'title' => 'Параметры по группам',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'user_group_params/',
          'class' => 'accounts-user_params-icon'
        )
      )
    ));
  }

  /**
  * Просмотр списка пользователей
  **/
  function users($category_id = 0) {
    return $this->render_template('templates/users', array(
      'category_id' => $category_id,
      'items'       => $this->accounts_model->get_users(array(),FALSE,$category_id),
      'groups'      => $this->accounts_model->get_groups(),
    ));
  }
  
  /**
   * Создание пользователя
   */  
  function create_user() {
    $user_params = $this->accounts_model->get_user_params();
    $fields_params = array();
    foreach ($user_params as $param) { 
      if (substr($param['system_name'],0,4) == 'desc')  {
        $fields_params[] = array(
          'view'       => 'fields/textarea',
          'title'     => $param['title'],
          'name'       => $param['system_name'],
          'maxlength' => 256,
          'value'     => (isset($item['params'][$param['system_name']]) ? $item['params'][$param['system_name']] : ''),
        );      
      } elseif(substr($param['system_name'],0,4) == 'file') {
        $fields_params[] = array(
          'view'       => 'fields/file',
          'title'     => $param['title'],
          'name'       => $param['system_name'],
          'maxlength' => 256,
        );      
      } else {
        $fields_params[] = array(
          'view'       => 'fields/text',
          'title'     => $param['title'],
          'name'       => $param['system_name'],
          'maxlength' => 256,
          'value'     => (isset($item['params'][$param['system_name']]) ? $item['params'][$param['system_name']] : ''),
        );      
      }
    }
    $fields_params[] = array(
      'view'     => 'fields/submit',
      'class'    => 'icon_small accept_i_s',
      'title'    => 'Создать',
      'type'     => 'ajax',
      'reaction' => $this->lang_prefix .'/admin'. $this->params['path']. 'users/'
    );
    
    return $this->render_template('admin/inner', array(
      'title' => 'Создание пользователя',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_user_process/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'       => 'fields/text',
                'title'     => 'Логин:',
                'name'       => 'username',
                'maxlength' => 256,
                'description' => '',
                'req'       => true
              ),
              array(
                'view' => 'fields/select',
                'title' => 'Группы пользователей:',
                'name' => 'groups[]',
                'multiple' => true,
                'options' => $this->accounts_model->get_groups(),
                'value_field' => 'id',
                'text_field' => 'title'
              ),
              array(
                'view'       => 'fields/password',
                'title'     => 'Пароль:',
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
                'view'   => 'fields/checkbox',
                'title' => 'Пользователь активен',
                'name'   => 'active'
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']. 'users/'
              )
            )
          ),
          array(
            'title'   => 'Дополнительные параметры',
            'fields'   => $fields_params
          )
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] . 'users/'
    ), TRUE);
  }
  
  function _create_user_process() {
    $params = array(
      'username' => htmlspecialchars(trim($this->input->post('username'))),
      'password' => htmlspecialchars(trim($this->input->post('password'))),
      'ip' => '',
      'active' => ($this->input->post('active') ? 1 : 0),
    );
    $add_params = array(
      're_password' => htmlspecialchars(trim($this->input->post('re_password'))),
    );

    $multiparams = array();
    $user_params = $this->accounts_model->get_user_params();
    foreach ($user_params as $user_param) {    
      if ($user_param['system_name'] == 'email') {
        if ($this->input->post('email') && !preg_match('/^[-0-9a-z_\.]+@[-0-9a-z^\.]+\.[a-z]{2,4}$/i', $this->input->post('email'))) {          
          send_answer(array('errors' => array('Некорректный email')));
        }
        $multiparams['email'] = htmlspecialchars(trim($this->input->post('email')));
      } elseif (substr($user_param['system_name'],0,4) == 'file') {
        $file = '';
        if ($_FILES[$user_param['system_name']]['name']) {
          $file = upload_file($_FILES[$user_param['system_name']]);      
          if (!$file) {
            send_answer(array('errors' => array('Ошибка при загрузке файла')));
          }
        }              
        $multiparams[$user_param['system_name']] = $file;
      } else {
        $multiparams[$user_param['system_name']] = htmlspecialchars(trim($this->input->post($user_param['system_name'])));
      }
    }
    
    $errors = $this->_validate_user_params('create', array_merge($params,$add_params));
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    if ($this->accounts_model->get_user(array('users.username' => $params['username']))) {
      send_answer(array('errors' => 'Логин занят'));
    }
    
    $params['password'] = md5($params['password']);
    $id = $this->accounts_model->create_user($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать пользователя')));
    }

    $groups = $this->input->post('groups');
    if ($groups) {
      if (!$this->accounts_model->set_user_groups($id, $groups)) {
        $this->accounts_model->user_del($id);
        exit('Не удалось сохранить пользователя');
      }
    }
    
    if (!$this->main_model->set_params('users', $id, $multiparams)) {
      $this->accounts_model->user_del($id);
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    send_answer();
  }  
  
  function _validate_user_params($status, $params) {
    $errors = array();
    if (!$params['username']) { $errors[] = 'Не указан логин'; }
    if (!preg_match('/^[A-Za-z0-9_@.-]{1,}$/', $params['username'])) { $errors[] = 'Некорректное значение для логина'; }
    if ($status == 'create' || ($status == 'edit' && $params['password'])) {
      if (!$params['password']) { $errors[] = 'Не указан пароль'; }
      if ($params['password'] != $params['re_password']) { $errors[] = 'Пароль не совпадает с повтором'; }      
    }
    return $errors;
  }  
  
  /**
   * Редактирование пользователя
   */  
  function edit_user($id) {
    $item = $this->accounts_model->get_user(array('users.id' => $id));
    $user_params = $this->accounts_model->get_user_params();
    $fields_params = array();
    foreach ($user_params as $param) { 
      if (substr($param['system_name'],0,4) == 'desc')  {
        $fields_params[] = array(
          'view'       => 'fields/textarea',
          'title'     => $param['title'],
          'name'       => $param['system_name'],
          'maxlength' => 256,
          'value'     => (isset($item['params'][$param['system_name']]) ? $item['params'][$param['system_name']] : ''),
        );      
      } elseif(substr($param['system_name'],0,4) == 'file') {
        $fields_params[] = array(
          'view'       => 'fields/file',
          'title'     => $param['title'],
          'name'       => $param['system_name'],
          'maxlength' => 256,
          'value'     => (isset($item['params'][$param['system_name']]) && $item['params'][$param['system_name']] ? $item['params'][$param['system_name']] : ''),
        );      
      } else {
        $fields_params[] = array(
          'view'       => 'fields/text',
          'title'     => $param['title'],
          'name'       => $param['system_name'],
          'maxlength' => 256,
          'value'     => (isset($item['params'][$param['system_name']]) ? $item['params'][$param['system_name']] : ''),
        );      
      }
    }
    $fields_params[] = array(
      'view'     => 'fields/submit',
      'class'    => 'icon_small accept_i_s',
      'title'    => 'Сохранить изменения',
      'type'     => 'ajax',
      'reaction' => 1
    );
    
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование пользователя',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_user_process/'.$id.'/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'         => 'fields/readonly',
                'title'       => 'IP:',
                'name'         => 'ip',
                'maxlength'   => 256,
                'description' => '',
                'value'       => $item['ip']
              ),
              array(
                'view'         => 'fields/text',
                'title'       => 'Логин:',
                'name'         => 'username',
                'maxlength'   => 256,
                'description' => '',
                'value'       => $item['username'],
                'req'       => true
              ),
              array(
                'view' => 'fields/select',
                'title' => 'Группы пользователей:',
                'name' => 'groups[]',
                'multiple' => true,
                'options' => $this->accounts_model->get_groups(),
                'value_field' => 'id',
                'text_field' => 'title',
                'value' => $item['groups']['ids']
              ),
              array(
                'view'   => 'fields/checkbox',
                'title' => 'Пользователь активен',
                'name'   => 'active',
                'checked' => ($item['active'] == 1 ? true : false)
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
          array(
            'title'   => 'Дополнительные параметры',
            'fields'   => $fields_params
          ),
          array(
            'title'   => 'Смена пароля',
            'fields'   => array(
              array(
                'view'       => 'fields/password',
                'title'     => 'Пароль:',
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
                'title'    => 'Сохранить изменения',
                'type'     => 'ajax',
                'reaction' => 1
              )
            )
          )
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] . 'users/'
    ), TRUE);
  }

  function _edit_user_process($id) {
    $item = $this->accounts_model->get_user(array('users.id' => $id));
    $params = array(
      'username' => htmlspecialchars(trim($this->input->post('username'))),
      'active' => ($this->input->post('active') ? 1 : 0),
    );
    
    if ($this->input->post('password')) {
      $params['password'] = htmlspecialchars(trim($this->input->post('password')));
    }
    
    if ($item['active'] == 0 && $params['active'] == 1) {
      $this->_send_mail_active($params);
    }
    
    $add_params = array(
      'password' => ($this->input->post('password') ? $params['password'] : ''),
      're_password' => htmlspecialchars(trim($this->input->post('re_password'))),
    );
      
    $errors = $this->_validate_user_params('edit',  array_merge($params,$add_params));
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    $item = $this->accounts_model->get_user(array('users.id' => $id));
    $multiparams = array();
    $user_params = $this->accounts_model->get_user_params();
    foreach ($user_params as $user_param) {    
      if ($user_param['system_name'] == 'email') {
        if ($this->input->post('email') && !preg_match('/^[-0-9a-z_\.]+@[-0-9a-z^\.]+\.[a-z]{2,4}$/i', $this->input->post('email'))) {          
          send_answer(array('errors' => array('Некорректный email')));
        }
        $multiparams['email'] = htmlspecialchars(trim($this->input->post('email')));
      } elseif (substr($user_param['system_name'],0,4) == 'file') {
        if ($_FILES[$user_param['system_name']]['name']) {
          $file = upload_file($_FILES[$user_param['system_name']]);      
          if (!$file) {
            send_answer(array('errors' => array('Ошибка при загрузке файла')));
          }
          resize_image($file,180,135);
          $multiparams[$user_param['system_name']] = $file;
        } elseif ($this->input->post($user_param['system_name'].'_delete')) {
          @unlink($_SERVER['DOCUMENT_ROOT'] . $item['params'][$user_param['system_name']]);
          $multiparams[$user_param['system_name']] = '';
        }       
      } else {
        $multiparams[$user_param['system_name']] = htmlspecialchars(trim($this->input->post($user_param['system_name'])));
      }
    }  
    if ($this->accounts_model->get_user(array('users.username' => $params['username'], 'id !=' => $id))) {
      send_answer(array('errors' => array('Логин занят')));
    }
    
    if ($this->input->post('password')) {
      $params['password'] = md5($params['password']);
    } 
    if (!$this->accounts_model->edit_user($id, $params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    $groups = $this->input->post('groups');
    if ($groups) {
      if (!$this->accounts_model->set_user_groups($id, $groups)) {
        exit('Не удалось сохранить группы');
      }
    }
    
    if (!$this->main_model->set_params('users', $id, $multiparams)) {
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    send_answer();
  }
  
  function _send_mail_active($params) {
    if ($this->project['project_email']) {
      $this->load->library('email');
      $this->email->from($this->project['project_email']);      
      $this->email->to($params['username']); 
      $this->email->subject('Реквизиты доступа "'.$this->project['domain'].'"');
      
      $message =  "<html><body>";
      $message .=  "<h2>Выражаем благодарность за использование нашего ресурса. Ваш аккаунт активен.</h2>";
      $message .=  "<h2>Реквизиты доступа в личный кабинет.</h2>";
      $message .=  "Логин: ".$params['username'];
      $message .=  "Пароль: ".(isset($params['password']) ? $params['password'] : 'сформирован Вами при регистрациии');
      $message .= "<br/>".make_link('Перейти в личный кабинет>>','/cabinet/');
      $message .= "<br/>Если Вы не помните пароль, его можно легко восстановить используя Ваш логин.";
      $message .= "<br/><br/>С уважением, <br/>администраторы сайта. ".$this->project['domain'];
      $message =  "</body></html>";

      $this->email->message($message);    
      if ($this->email->send()) {  
        return true;
      }  
    }  
    return false;
  }  
  
  /**
   *  Активация пользователя
   */
  function enable_user($id) {
    $this->accounts_model->edit_user((int)$id, array('active' => 1));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] . '/users/');
  }

  /**
   *  Деактивация пользователя
   */
  function disable_user($id) {
    $this->accounts_model->edit_user((int)$id, array('active' => 0));
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] . '/users/');
  }  
  
  /**
   *  Удаление пользователя
   */
  function delete_user($id) {
    $this->accounts_model->delete_user($id);

    send_answer();
  }
  
  /**
  * Просмотр групп пользователей 
  **/
  function groups() { 
    return $this->render_template('templates/groups', array(
      'items' => $this->accounts_model->get_groups()
    ));
  }
  
  /**
   * Создание группы
   */  
  function create_group() {
    return $this->render_template('admin/inner', array(
      'title' => 'Создание группы',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_group_process/',
        'blocks' => array(
          array(
            'title'   => 'Параметры',
            'fields'   => array(
              array(
                'view'       => 'fields/text',
                'title'     => 'Название группы:',
                'name'       => 'title',
                'maxlength' => 256,
                'description' => '',
                'req'       => true
              ),
              array(
                'view'       => 'fields/text',
                'title'     => 'Системное имя:',
                'name'       => 'system_name',
                'maxlength' => 256,
                'description' => '',
                'req'       => true
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']. 'groups/'
              )
            )
          )
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] . 'groups/'
    ), TRUE);
  }
  
  function _create_group_process() {
    $params = array(
      'title' => htmlspecialchars(trim($this->input->post('title'))),
      'system_name' => htmlspecialchars(trim($this->input->post('system_name'))),
    );
    
    $errors = $this->_validate_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    $id = $this->accounts_model->create_group($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать группу')));
    }
    
    send_answer();
  }  
  
  function _validate_params($params) {
    $errors = array();
    if (!$params['title']) { $errors[] = 'Не указано название'; }
    if (!$params['system_name']) { $errors[] = 'Не указано системное имя'; }
    if (!preg_match('/^[A-Za-z0-9_-]{1,}$/', $params['system_name'])) { $errors[] = 'Некорректное значение для системного имени'; }
    return $errors;
  }  
  
  /**
   * Редактирование группы
   */  
  function edit_group($id) {
    $item = $this->accounts_model->get_group($id);
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование группы',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_group_process/'.$id.'/',
        'blocks' => array(
          array(
            'title'   => 'Параметры',
            'fields'   => array(
              array(
                'view'         => 'fields/text',
                'title'       => 'Название группы:',
                'name'         => 'title',
                'maxlength'   => 256,
                'description' => '',
                'value'       => $item['title'],
                'req'       => true
              ),
              array(
                'view'         => 'fields/text',
                'title'       => 'Системное имя:',
                'name'         => 'system_name',
                'maxlength'   => 256,
                'description' => '',
                'value'       => $item['system_name'],
                'req'         => true
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Сохранить изменения',
                'type'     => 'ajax',
                'reaction' => 1
              )
            )
          )
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] . 'groups/'
    ), TRUE);
  }

  function _edit_group_process($id) {
    $params = array(
      'title' => htmlspecialchars(trim($this->input->post('title'))),
      'system_name' => htmlspecialchars(trim($this->input->post('system_name'))),
    );
    
    $errors = $this->_validate_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    }
    
    if (!$this->accounts_model->edit_group($id, $params)) {
      send_answer(array('errors' => array('Не удалось отредактировать группу')));
    }
    
    send_answer();
  }
  
  /**
   * Удаление группы
   */ 
  function delete_group($id) {
    $this->accounts_model->delete_group($id);

    send_answer();
  }
    
 /**
  * Просмотр списка параметров пользователей
  **/
  function user_params() { 
    return $this->render_template('templates/user_params', array(
      'items' => $this->accounts_model->get_user_params(),
    ));
  }
  
  /**
   * Создание параметра пользователя
   */  
  function create_user_param() {
    $languages = $this->languages_model->get_languages(1, 0);
    return $this->render_template('admin/inner', array(
      'title' => 'Создание параметра пользователя',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_user_param_process/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'         => 'fields/text',
                'title'       => 'Внутреннее имя:',
                'name'         => 'title',
                'maxlength'   => 256,
                'description' => '',
                'req'         => true
              ),
              array(
                'view'         => 'fields/text',
                'title'       => 'Системное имя:',
                'name'         => 'system_name',
                'maxlength'   => 256,
                'description' => '',
                'req'         => true
              ),
              array(
                'view'         => 'fields/text',
                'title'       => 'Название:',
                'name'         => 'name',
                'maxlength'   => 256,
                'description' => '',
                'languages' => $languages,
                'req'         => true
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path'] . 'user_params/'
              )
            )
          ),
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] . 'user_params/'
    ), TRUE);
  }
  
  function _create_user_param_process() {
    $params = array(
      'system_name' => htmlspecialchars(trim($this->input->post('system_name'))),
      'title' => htmlspecialchars(trim($this->input->post('title'))),
    );
    
    $languages = $this->languages_model->get_languages(1, 0);
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
      );
    }
    
    $errors = $this->_validate_user_param_params(array_merge($params,$multiparams));
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
      
    if ($this->accounts_model->get_user_param(array('system_name' => $params['system_name']))) {
      send_answer(array('errors' => array('Системное имя уже существует')));
    }
    
    $id = $this->accounts_model->create_user_param($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать параметр')));
    }
    
    if (!$this->main_model->set_params('user_params', $id, $multiparams)) {
      $this->accounts_model->delete_user_param($id);
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    send_answer();
  }  
  
  function _validate_user_param_params($params) {
    $errors = array();
    if (!$params['title']) { $errors[] = 'Не указано внутреннее название'; }
    if (!$params['system_name']) { $errors[] = 'Не указано системное имя'; }
    if (!preg_match('/^[A-Za-z0-9_@.-]{1,}$/', $params['system_name'])) { $errors[] = 'Некорректное значение для системного имени'; }
    $languages = $this->languages_model->get_languages(1, 0);
    $multiparams = array();
    foreach ($languages as $language) {
      if (!$params[$language['name']]['name']) {
        $errors[] = 'Не указано название ('.$language['name'].')';
      }
    }
    return $errors;
  }  
  
  /**
   * Редактирование параметра пользователя
   */  
  function edit_user_param($id) {
    $languages = $this->languages_model->get_languages(1, 0);
    $item = $this->accounts_model->get_user_param(array('id' => $id));
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование параметра',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_user_param_process/'.$id.'/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'         => 'fields/text',
                'title'       => 'Внутреннее имя:',
                'name'         => 'title',
                'maxlength'   => 256,
                'description' => '',
                'value'       => $item['title'],
                'req'         => true
              ),
              array(
                'view'         => 'fields/text',
                'title'       => 'Системное имя:',
                'name'         => 'system_name',
                'maxlength'   => 256,
                'description' => '',
                'value'       => $item['system_name'],
                'req'         => true
              ),
              array(
                'view'         => 'fields/text',
                'title'       => 'Название:',
                'name'         => 'name',
                'maxlength'   => 256,
                'description' => '',
                'value'       => $item['params'],
                'languages'   => $languages,
                'req'         => true
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
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] . 'user_params/'
    ), TRUE);
  }
  
  function _edit_user_param_process($id) {
    $params = array(
      'system_name' => htmlspecialchars(trim($this->input->post('system_name'))),
      'title' => htmlspecialchars(trim($this->input->post('title'))),
    );
    
    $languages = $this->languages_model->get_languages(1, 0);
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
      );
    }
    
    $errors = $this->_validate_user_param_params(array_merge($params,$multiparams));
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 

    if ($this->accounts_model->get_user_param(array('system_name' => $params['system_name'], 'id !=' => $id))) {
      send_answer(array('errors' => array('Системное имя уже существует')));
    }
    
    if (!$this->accounts_model->edit_user_param($id, $params)) {
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    if (!$this->main_model->set_params('user_params', $id, $multiparams)) {
      $this->accounts_model->delete_user_param($id);
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    send_answer();
  }  
  
  /**
   * Изменение позиции параметра пользователя
   */  
  function user_param_move($id, $dest) {
    $user_param = $this->accounts_model->get_user_param(array('id' => $id));
    $this->accounts_model->user_param_move($user_param, $dest);
    header('Location: '. $this->lang_prefix .'/admin'. $this->params['path'] . '/user_params/');
  }
  
  /**
   * Удаление параметра пользователя
   */  
  function delete_user_param($id) {
    $this->accounts_model->delete_user_param($id);

    send_answer();
  } 
  
  /** Распределение параметров пользователя по группам
  **/
  function user_group_params() {
    $data = array(
      'groups' => $this->accounts_model->get_groups(),
      'items' => $this->accounts_model->get_user_params(),
    );
    $user_group_params = $this->accounts_model->get_user_group_params();
    foreach ($user_group_params as &$item) {
      $data['user_group_param']['param_'.$item['user_param_id'].'_group_'.$item['group_id']] = 1;
    }
    unset($user_group_param);
    
    return $this->render_template('templates/user_group_params', $data);
  }

  function _edit_user_group_params() {
    $params = $this->accounts_model->get_user_params();
    $groups = $this->accounts_model->get_groups();
    $params_insert = array();
    foreach ($params as $param) {
      foreach ($groups as $group) {
        if ($this->input->post('param_'.$param['id'].'_group_'.$group['id'])) {
          $params_insert[] = array('group_id' => $group['id'], 'user_param_id' => $param['id']);
        }
      }
    }
    if (!$this->accounts_model->set_user_group_params($params_insert)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    send_answer();
  }

}