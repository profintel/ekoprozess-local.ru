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
        ),
        array(
          'title' => 'Действия администраторов',
          'link'  => $this->lang_prefix .'/admin'. $this->params['path'] .'logs/',
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
                'view'  => 'fields/text',
                'title' => 'Email:',
                'name'  => 'email',
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
      'email'    => htmlspecialchars(trim($this->input->post('email'))),
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
    if ($params['email'] && !preg_match('/^[-0-9a-z_\.]+@[-0-9a-z^\.]+\.[a-z]{2,4}$/i', $params['email'])) { 
      $errors['email'] = 'Некорректный Email'; 
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
            'title'   => 'Основные параметры',
            'fields'   => array(              
              array(
                'view'  => 'fields/text',
                'title' => 'Email:',
                'name'  => 'email',
                'value' => $item['email']
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Сохранить',
                'type'     => 'ajax',
                'reaction' => 'reload'
              )
            )
          ),
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
      'email'    => htmlspecialchars(trim($this->input->post('email'))),
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
   
    if ($params['email'] !== $item['email']) {
      if ($params['email'] && !preg_match('/^[-0-9a-z_\.]+@[-0-9a-z^\.]+\.[a-z]{2,4}$/i', $params['email'])) {
        send_answer(array('errors' => array('Некорректный Email')));
      }
      if (!$this->administrators_model->edit_admin($id, array('email'=>$params['email']))) {
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
  
  /**
  * Просмотр действий администраторов
  */
  function logs($page = 1) {
    $where = array();
    $error = '';
    $get_params = array(
      'date_start'  => ($this->uri->getParam('date_start') ? date('Y-m-d 00:00:00',strtotime($this->uri->getParam('date_start'))) : ''),
      'date_end'    => ($this->uri->getParam('date_end') ? date('Y-m-d 00:00:00',strtotime($this->uri->getParam('date_end'))) : ''),
      'component'   => ($this->uri->getParam('component') ? htmlspecialchars($this->uri->getParam('component')) : ''),
      'method'      => ($this->uri->getParam('method') ? htmlspecialchars($this->uri->getParam('method')) : ''),
      'path'        => ($this->uri->getParam('path') ? htmlspecialchars($this->uri->getParam('path')) : ''),
    );
    if($get_params['date_start']){
      $where['admin_logs.tm >='] = $get_params['date_start'];
    }
    if($get_params['date_end']){
      $where['admin_logs.tm <='] = $get_params['date_end'];
    }
    if($get_params['component']){
      $where['admin_logs.component'] = $get_params['component'];
    }
    if($get_params['method']){
      $where['admin_logs.method'] = $get_params['method'];
    }
    if($get_params['path']){
      $where['admin_logs.path LIKE '] = '%'.$get_params['path'].'%';
    }

    $in_page = 50;
    $all_count = $this->administrators_model->get_admin_logs_cnt($where);
    $pages = get_pages($page, $all_count, $in_page);$postfix = '';
    foreach ($get_params as $key => $get_param) {
      if(is_array($get_param)){
        $postfix .= $key.'[]='.implode('&'.$key.'[]=', $get_param).'&';
      } else {
        $postfix .= $key.'='.$get_param.'&';
      }
    }
    $pagination_data = array(
      'pages'  => $pages,
      'page'   => $page,
      'prefix' => '/admin/administrators/logs/',
      'postfix' => '/?'.$postfix
    );
    $items = $this->administrators_model->get_admin_logs($where,array(),$in_page, $in_page * ($page - 1));  

    return $this->render_template('templates/admin_logs', array(
      'items'       => $items,
      'pagination'  => $this->load->view('admin/pagination', $pagination_data, true),
      'get_params'  => $get_params,
      'form'        => $this->view->render_form(array(
        'method' => 'GET',
        'action' => $this->lang_prefix .'/admin'. $this->params['path'].'logs/' ,        
        'enctype' => '',
        'blocks' => array(
          array(
            'title'         => 'Параметры поиска',
            'fields'   => array(
              array(
                'view'        => 'fields/datetime',
                'title'       => 'Дата (от):',
                'name'        => 'date_start',
                'value'       => ($get_params['date_start']? date('d.m.Y',strtotime($get_params['date_start'])) : ''),
              ),
              array(
                'view'        => 'fields/datetime',
                'title'       => 'Дата (до):',
                'name'        => 'date_end',
                'value'       => ($get_params['date_end']? date('d.m.Y',strtotime($get_params['date_end'])) : ''),
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Компонент:',
                'name'        => 'component',
                'value'       => $get_params['component'],
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Метод:',
                'name'        => 'method',
                'value'       => $get_params['method'],
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Путь:',
                'name'        => 'path',
                'value'       => $get_params['path'],
              ),
              array(
                'view'          => 'fields/submit',
                'title'         => 'Сформировать',
                'type'          => '',
                'reaction'      => '',
              )
            )
          )
        )
      )),
    ));
  } 
}