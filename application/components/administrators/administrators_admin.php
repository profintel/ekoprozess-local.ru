<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Administrators_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();
    
    $this->load->model('administrators/models/administrators_model');
    $this->load->model('gallery/models/gallery_model');
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
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'edit_admin/'.$this->admin_id,
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
  * Просмотр списка администраторов
  */
  function admins() {
    $items = $this->administrators_model->get_admins();
    foreach ($items as $key => &$value) {
      $value['title'] = $value['username'];
    }
    unset($value);
    return $this->render_template('admin/items', array(
      'items'           => $items,
      'component_item'  => array('name' => 'admin', 'title' => 'администратора')
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
            'title'   => 'Основные параметры',
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
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']. 'admins/'
              )
            )
          ),
          array(
            'title' => 'Дополнительные параметры',
            'fields' => array(
              array(
                'view'  => 'fields/file',
                'title' => 'Фото (gif, png, jpg):',
                'name'  => 'image'
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'ФИО:',
                'name'        => 'name',
                'languages'   => $languages,
                'maxlength'   => 256,
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Должность:',
                'name'        => 'post',
                'languages'   => $languages,
                'maxlength'   => 256,
              ),
              array(
                'view'     => 'fields/submit',
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

    $languages = $this->languages_model->get_languages(1, 0);
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
        'post' => htmlspecialchars(trim($this->input->post('post_'. $language['name']))),
      );
    }

    if (!$this->main_model->set_params('admins', $id, $multiparams)) {
      $this->delete_admin($id);
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    if ($_FILES['image']['name']) {
      $image = upload_file($_FILES['image']);      
      if (!$image) {
        $this->delete_admin($id);
        send_answer(array('errors' => array('Ошибка при загрузке изображения')));
      }
      if (!$this->gallery_model->validate_file($image, array('jpeg', 'jpg', 'gif', 'png'))) {
        $this->delete_admin($id);
        @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
        send_answer(array('errors' => array('Неподдерживаемый формат изображения')));
      }
      if (!resize_image($image, 180, 135)) {
        $this->delete_admin($id);
        send_answer(array('errors' => array('Не удалось создать миниатюру')));
      }
      if (!resize_image($image, 32, 32)) {
        $this->delete_admin($id);
        send_answer(array('errors' => array('Не удалось создать миниатюру')));
      }
      $gallery_params = array(
        array(
          'system_name' => 'gallery_system',
          'title' => 'Системная',      
          'childrens'   => array(
            array(
              'system_name' => $this->component['name'],
              'title'       => $this->component['title'],
              'childrens'   => array(
                array(
                  'system_name' => 'admin_'.$id,
                  'title'       => $params['username'],
                  'images'      => array(
                    array (
                      'image'         => $image,
                      'images_thumbs' => array(
                        array(
                          'thumb' => $this->gallery_model->thumb($image,180,135),
                          'width' => 180,
                          'height' => 135
                        ),
                        array(
                          'thumb' => $this->gallery_model->thumb($image,32,32),
                          'width' => 32,
                          'height' => 32
                        )
                      )
                    )
                  )
                )
              ),
            ),
          ),
        )
      );
      if (!$this->gallery_model->add_gallery_images($gallery_params)) {
        $this->delete_admin($id);
        send_answer(array('errors' => array('Не удалось сохранить изображения')));
      }
    }

    send_answer();
  }  
  
  function _validate_create_params($params) {
    $errors = array();
    if (!$params['username']) { $errors['username'] = 'Не указан логин'; }
    if ($this->db->get_where('admins', array('username' => $params['username']))->num_rows()) {
      $errors[] = 'Пользователь с таким логином уже существует в базе данных'; 
    }
    if (!$params['password']) { $errors['password'] = 'Не указан пароль'; }
    if (!$params['re_password']) { $errors['re_password'] = 'Не указан повтор пароля'; }
    if ($params['password'] != $params['re_password']) { $errors['re_password'] = 'Пароль не совпадает с повтором'; }
    return $errors;
  }   
  
  /**
   * Редактирование параметров администратора
   */  
  function edit_admin($id) {
    $languages = $this->languages_model->get_languages(1, 0);
    $item = $this->administrators_model->get_admin(array('id' => $id));
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование администратора "'.$item['username'].'"',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_admin_process/'.$id.'/',
        'blocks' => array(
          array(
            'title'   => 'Смена пароля',
            'fields'   => array(
              array(
                'view'        => 'fields/password',
                'title'       => 'Новый пароль:',
                'name'        => 'password',
                'maxlength'   => 256,
                'req'         => true
              ),
              array(
                'view'        => 'fields/password',
                'title'       => 'Повтор пароля:',
                'name'        => 're_password',
                'maxlength'   => 256,
                'req'         => true
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => '',
                'title'    => 'Сохранить',
                'type'     => 'ajax',
                'reaction' => 'reload'
              )
            )
          ),
          array(
            'title' => 'Дополнительные параметры',
            'fields' => array(
              array(
                'view'  => 'fields/file',
                'title' => 'Фото (gif, png, jpg):',
                'name'  => 'image',
                'value' => $item['image']
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'ФИО:',
                'name'        => 'name',
                'languages'   => $languages,
                'value'       => $item['params'],
                'maxlength'   => 256,
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Должность:',
                'name'        => 'post',
                'languages'   => $languages,
                'value'       => $item['params'],
                'maxlength'   => 256,
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
      'back' => $this->lang_prefix .'/admin'. $this->params['path']. 'admins/'
    ), TRUE);
  }

  function _edit_admin_process($id) {
    $item = $this->administrators_model->get_admin(array('id' => $id));
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
   
    if ($params['password']) {
      $params['password'] = md5($params['password']); 
      if (!$this->administrators_model->edit_admin($id, $params)) {
        send_answer(array('errors' => array('Не удалось отредактировать учетную запись')));
      }
    }

    $languages = $this->languages_model->get_languages(1, 0);
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
        'post' => htmlspecialchars(trim($this->input->post('post_'. $language['name']))),
      );
    }
    if (!$this->main_model->set_params('admins', $id, $multiparams)) {
      $this->delete_admin($id);
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }

    if ($_FILES['image']['name']) {
      if ($item['image']) {
        $this->gallery_model->delete_image(array('image' => $item['image']));
      }
      $image = upload_file($_FILES['image']);
      if (!$image) {
        send_answer(array('errors' => array('Ошибка при загрузке изображения')));
      }
      if (!$this->gallery_model->validate_file($image, array('jpeg', 'jpg', 'gif', 'png'))) {
        @unlink($_SERVER['DOCUMENT_ROOT'] . $image);
        send_answer(array('errors' => array('Неподдерживаемый формат изображения')));
      }
      if (!resize_image($image, 180, 135)) {
        send_answer(array('errors' => array('Не удалось создать миниатюру')));
      }
      if (!resize_image($image, 32, 32)) {
        send_answer(array('errors' => array('Не удалось создать миниатюру')));
      }
    } elseif ($this->input->post('image_delete')) {
      $this->gallery_model->delete_image(array('image' => $item['image']));
    }
    if (isset($image) && $image) {
      $gallery_params = array(
        array(
          'system_name' => 'gallery_system',
          'title' => 'Системная',      
          'childrens'   => array(
            array(
              'system_name' => $this->component['name'],
              'title'       => $this->component['title'],
              'childrens'   => array(
                array(
                  'system_name' => 'admin_'.$id,
                  'title'       => $item['username'],
                  'images'      => array(
                    array (
                      'image'         => $image,
                      'images_thumbs' => array(
                        array(
                          'thumb' => $this->gallery_model->thumb($image,180,135),
                          'width' => 180,
                          'height' => 135
                        ),
                        array(
                          'thumb' => $this->gallery_model->thumb($image,32,32),
                          'width' => 32,
                          'height' => 32
                        )
                      )
                    )
                  )
                )
              ),
            ),
          ),
        )
      );
      if (!$this->gallery_model->add_gallery_images($gallery_params)) {
        $this->delete_admin($id);
        send_answer(array('errors' => array('Не удалось сохранить изображения')));
      }
    }
    
    send_answer();
  }
  
  function _validate_edit_params($params) {
    $errors = array();
    // if (!$params['password']) { $errors['password'] = 'Не указан новый пароль'; }
    if ($params['password'] && $params['password'] != $params['re_password']) { $errors[] = 'Пароль не совпадает с повтором'; }
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