<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery_admin extends CI_Component {
  
  function __construct() {
    parent::__construct();

    $this->load->model('gallery/models/gallery_model');
    $this->load->model('templates/models/templates_model');
  }
  
  /**
  *  Просмотр всех галерей
  */
  function index($parent_id = 0, $page = 1) {
    $data = array(
      'parent_id' => $parent_id,
      'category'  => $this->gallery_model->get_gallery_one(array('id' => $parent_id)),
      'albums'    => $this->gallery_model->get_gallery_albums($parent_id)
    );

    return $this->render_template('templates/admin_albums', $data);
  }
  
  /**
  * Возвращает шаблон с объектами галереи
  * @params: $id - id альбома
  */
  function render_gallery_items($id, $type = '') {
    $data = array(
      'items'     => $this->gallery_model->get_gallery_images(array('id' => $id)),
      'move_path' => '/admin/gallery/move_gallery_image/',
      'type'      => $type,
    );

    return $this->render_template('templates/admin_gallery_images', $data);
  }
  
  /**
  * Возвращает шаблон с объектами галереи
  * @params: $id - id альбома
  */
  function render_gallery_item($id, $type = '') {
    $item = $this->gallery_model->get_gallery_image(array('gallery_images.id' => $id));
    if(!$item){
      return false;
    }
    $template = 'templates/admin_gallery_item';    
    if($item['type'] == 'image' && in_array($item['ext'], array('jpeg', 'jpg', 'gif', 'png'))){
      $template = 'templates/admin_gallery_image';
    }
    if($item['type'] == 'image' && $item['ext'] == 'swf'){
      $template = 'templates/admin_gallery_swf';
    }
    if($item['type'] == 'video'){
      $template = 'templates/admin_gallery_video';
    }
    if($item['type'] == 'youtube'){
      $template = 'templates/admin_gallery_youtube';
    }
    if($type == 'list'){
      $template = 'templates/admin_gallery_item';
    }
    return $this->render_template($template, array('item'=>$item));
  }

  /**
   * Перемещение объектов галереи
  **/ 
  function move_gallery_image() {
    $id = (int)str_replace('item-', '', $this->input->post('page'));
    $item = $this->gallery_model->get_gallery_image(array('gallery_images.id' => $id));
    if (!$item) {
      send_answer(array('messages' => array('Перемещаемый объект не найден')));
    }
    
    $dest_id = $this->input->post('dest');
    $dest_id = (int)str_replace('item-', '', $dest_id);
    $dest = $this->gallery_model->get_gallery_image(array('gallery_images.id' => $dest_id));
    if (!$dest) {
      send_answer(array('messages' => array('Целевой объект не найден')));
    }

    if (!$this->gallery_model->move_gallery_image($id, $dest_id)) {
      send_answer(array('messages' => array('Не удалось переместить')));
    }
    
    send_answer();
  }
  
  /**
   *  Создание альбома
  **/
  function create_album() {
    $pages = $this->db->get('pr_pages')->result_array();
    if ($pages) {
      foreach ($pages as &$page) {
        $project = $this->db->get('pr_projects',array('id', $page['project_id']))->row_array();
        $page['title'] = $project['title'].': '.$page['title'];
      }
      unset($page);
    }
    $languages = $this->languages_model->get_languages(1, 0);
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление альбома',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_create_album_process/',
        'blocks' => array(
          array(
            'title'   => 'Основные параметры',
            'fields'   => array(
              array(
                'view'         => 'fields/text',
                'title'       => 'Внутреннее имя:',
                'name'         => 'title',
                'id'           => 'item-title',
                'maxlength'   => 256,
                'description' => 'Используется только внутри панели администрирования',
                'req'         => true
              ),
              array(
                'view'         => 'fields/text',
                'title'       => 'Системное имя:',
                'name'         => 'system_name',
                'id'           => 'item-alias',
                'description' => 'Используется для отображения изображений данной категории в теле страницы',
                'maxlength'   => 256,
                'req'         => true
              ),
              array(
                'view'       => 'fields/text',
                'title'     => 'Название:',
                'name'       => 'name',
                'languages' => $languages,
                'maxlength' => 256
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Шаблон:',
                'name'    => 'template_id',
                'options' => $this->templates_model->get_templates(),
                'value'   => $this->templates_model->get_template("site_gallery"),
                'req'     => TRUE
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          ),
          array(
            'title' => 'Дополнительные параметры',
            'fields' => array(
              array(
                'view'         => 'fields/select',
                'title'        => 'Связанные страницы:',
                'description'  => 'Укажите страницы, на которой будут отображаться изображения данного альбома',
                'name'         => 'pages[]',
                'options'      => $pages,
                'multiple'     => true
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Ширина миниатюры:',
                'description' => 'Ширина миниатюры по умолчанию для изображений альбома',
                'name'        => 'thumb_width',
                'maxlength'   => 4,
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Высота миниатюры:',
                'description' => 'Высота миниатюры по умолчанию для изображений альбома',
                'name'        => 'thumb_height',
                'maxlength'   => 4,
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Ширина плеера:',
                'description' => 'Ширина плеера по умолчанию для просмотра видео альбома',
                'maxlength'   => 4,
                'name'        => 'width_player'
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Высота плеера:',
                'description' => 'Высота плеера по умолчанию для просмотра видео альбома',
                'maxlength'   => 4,
                'name'        => 'height_player'
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']
              )
            )
          )
        )
      )),
    ), TRUE);
  }
  
  function _create_album_process() {
    $languages = $this->languages_model->get_languages(1, 0);
    
    $params = array(
      'template_id' => (int)$this->input->post('template_id'),
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'system_name' => htmlspecialchars(trim($this->input->post('system_name'))),
      'path'        => "/".htmlspecialchars(trim($this->input->post('system_name')))."/"
    );
 
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
      );
    }
    $multiparams['thumb_width']   = (int)$this->input->post('thumb_width');
    $multiparams['thumb_height']  = (int)$this->input->post('thumb_height');
    $multiparams['width_player']  = (int)$this->input->post('width_player');
    $multiparams['height_player'] = (int)$this->input->post('height_player');
    
    if (!$this->gallery_model->_validate_gallery_system_name($params['system_name'])) {
      send_answer(array('errors' => array('Системное имя уже существует')));
    }

    $errors = $this->_validate_album_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 
    
    $id = $this->gallery_model->create_album($params);
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать альбом')));
    }

    $links = array(
      'page_id' => $this->input->post('pages')
    );
    if (!$this->gallery_model->set_gallery_links($id, $links)) {
      send_answer(array('errors' => array('Не удалось сохранить связи')));
    }
    
    if (!$this->main_model->set_params('gallery', $id, $multiparams)) {
      $this->delete_gallery($id);
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }

    send_answer();
  }
  
  function _validate_album_params($params) {
    $errors = array();
    if (!$params['template_id']) { $errors[] = 'Не указан шаблон'; }
    if (!$params['title']) { $errors[] = 'Не указано внутреннее имя'; }
    if (!$params['system_name']) { $errors[] = 'Не указано системное имя'; }
    if (!preg_match('/^[A-Za-z0-9_-]{1,}$/', $params['system_name'])) { $errors[] = 'Некорректное значение для системного имени'; }
    return $errors;
  }
  
  /**
   *  Редактирование альбома
   * @param $id - id альбома
   */    
  function edit_album($id) {
    $pages = $this->db->get('pr_pages')->result_array();
    if ($pages) {
      foreach ($pages as &$page) {
        $project = $this->db->get('pr_projects',array('id', $page['project_id']))->row_array();
        $page['title'] = $project['title'].': '.$page['title'];
      }
      unset($page);
    }
    $languages = $this->languages_model->get_languages(1, 0);
    $item =  $this->gallery_model->get_gallery_one(array('id' => $id));

    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование альбома галереи '.$item['title'],
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_album_process/'.$id.'/',
        'blocks' => array(
          array(
            'title' => 'Основные параметры',
            'fields' => array(
              array(
                'view'         => 'fields/'.($item['system_name'] != 'gallery_system' ? 'text' : 'readonly'),
                'title'        => 'Внутреннее имя:',
                'name'         => 'title',
                'id'           => 'item-title',
                'maxlength'   => 256,
                'value'       => $item['title'],
                'description' => 'Используется только внутри панели администрирования',
                'req'         => true
              ),
              array(
                'view'         => 'fields/'.($item['system_name'] != 'gallery_system' ? 'text' : 'readonly'),
                'title'       => 'Системное имя:',
                'name'         => 'system_name',
                'id'           => 'item-alias',
                'maxlength'   => 256,
                'value'       => $item['system_name'],
                'description' => 'Используется для отображения в теле страницы',
                'req'         => true
              ),
              array(
                'view'       => 'fields/text',
                'title'     => 'Название:',
                'name'       => 'name',
                'languages' => $languages,
                'value'     => $item['params'],
                'maxlength' => 256
              ),
              array(
                'view'    => 'fields/select',
                'title'   => 'Шаблон:',
                'name'    => 'template_id',
                'options' => $this->templates_model->get_templates(),
                'value'   => $item['template_id'],
                'req'     => TRUE
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Редактировать',
                'type'     => 'ajax',
                'reaction' => 1
              )
            )
          ),
          array(
            'title' => 'Дополнительные параметры',
            'fields' => array(
              array(
                'view'         => 'fields/select',
                'title'       => 'Связанные страницы:',
                'description' => 'Укажите страницу, на которой будут отображаться изображения данного альбома',
                'name'         => 'pages[]',
                'options'      => $pages,
                'value'        => (isset($item['page_id']) ? $item['page_id'] : 0),
                'multiple'     => true
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Ширина миниатюры:',
                'description' => 'Ширина миниатюры по умолчанию для изображений альбома',
                'name'        => 'thumb_width',
                'value'       => (isset($item['params']['thumb_width']) ? $item['params']['thumb_width'] : ""),
                'maxlength'   => 4,
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Высота миниатюры:',
                'description' => 'Высота миниатюры по умолчанию для изображений альбома',
                'name'        => 'thumb_height',
                'value'       => (isset($item['params']['thumb_height']) ? $item['params']['thumb_height'] : ''),
                'maxlength'   => 4,
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Ширина плеера:',
                'description' => 'Ширина плеера по умолчанию для просмотра видео альбома',
                'maxlength'   => 4,
                'name'        => 'width_player',
                'value'       => (isset($item['params']['width_player']) ? $item['params']['width_player'] : ''),
              ),
              array(
                'view'        => 'fields/text',
                'title'       => 'Высота плеера:',
                'description' => 'Высота плеера по умолчанию для просмотра видео альбома',
                'maxlength'   => 4,
                'name'        => 'height_player',
                'value'       => (isset($item['params']['height_player']) ? $item['params']['height_player'] : ''),
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Редактировать',
                'type'     => 'ajax',
                'reaction' => 1
              )
            )
          )
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] . $item['parent_id'] .'/'
    ), TRUE);
  } 
  
  function _edit_album_process($id) {
    $languages = $this->languages_model->get_languages(1, 0);
    $item =  $this->gallery_model->get_gallery_one(array('id' => $id));
    
    $params = array(
      'template_id' => (int)$this->input->post('template_id'),
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'system_name' => htmlspecialchars(trim($this->input->post('system_name'))),
      'path'        => "/".htmlspecialchars(trim($this->input->post('system_name')))."/"
    );
 
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
      );
    }
    $multiparams['thumb_width']   = (int)$this->input->post('thumb_width');
    $multiparams['thumb_height']  = (int)$this->input->post('thumb_height');
    $multiparams['width_player']  = (int)$this->input->post('width_player');
    $multiparams['height_player'] = (int)$this->input->post('height_player');
    
    if (!$this->gallery_model->_validate_gallery_system_name($params['system_name'],$id)) {
      send_answer(array('errors' => array('Системное имя уже существует')));
    }
    
    $errors = $this->_validate_album_params($params);
    if ($errors) {
      send_answer(array('errors' => $errors));
    } 

    if (!$this->gallery_model->edit_gallery($id, $params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    $links = array(
      'page_id' => $this->input->post('pages')
    );
    if (!$this->gallery_model->set_gallery_links($id, $links)) {
      send_answer(array('errors' => array('Не удалось сохранить связи')));
    }
    
    if (!$this->main_model->set_params('gallery', $id, $multiparams)) {
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    send_answer();
  }
    
  /**
   * Удаление изображения
   * @param id изображения
   */      
  function delete_image($id) {
    $this->gallery_model->delete_image(array('id' => (int)$id));
    send_answer();
  }
  
  /**
   * Удаление галереи изображений
   * @param id галереи
   */    
  function delete_gallery($gallery_id) {
    $this->gallery_model->delete_gallery_images(array('id' => $gallery_id));
    send_answer();
  }
  
  /**
  * Добавление изображения
  **/
  function add_image($album_id) {
    $languages = $this->languages_model->get_languages(1, 0);
    $album =  $this->gallery_model->get_gallery_one(array('id' => $album_id));
    $pages = $this->db->get('pr_pages')->result_array();
    if ($pages) {
      foreach ($pages as &$page) {
        $project = $this->db->get('pr_projects',array('id', $page['project_id']))->row_array();
        $page['title'] = $project['title'].': '.$page['title'];
      }
      unset($page);
    }
    
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление изображения в альбом "'.$album['title'].'"',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_add_image_process/'.$album_id.'/',
        'blocks' => array(
          array(
            'title' => 'Основные параметры',
            'fields' => array(
              array(
                'view'         => 'fields/text',
                'title'        => 'Внутреннее имя:',
                'name'         => 'title',
                'id'           => 'item-title',
                'maxlength'   => 256,
                'description' => 'Используется только внутри панели администрирования',
                'req'         => true
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']. $album_id .'/'
              )
            )
          ),
          array(
            'title'  => 'Параметры изображения',
            'fields' => array(
              array(
                'view'  => 'fields/checkbox',
                'title' => 'Обложка альбома:',
                'name'  => 'main'
              ),
              array(
                'view'  => 'fields/file',
                'title' => 'Изображение (jpg, gif, png):',
                'name'  => 'image',
                'req'   => true
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Ширина миниатюры:',
                'name'      => 'thumb_width',
                'maxlength' => 4,
                'value'     => (isset($album['params']['thumb_width']) ? $album['params']['thumb_width'] : ""),
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Высота миниатюры:',
                'name'      => 'thumb_height',
                'value'     => (isset($album['params']['thumb_height']) ? $album['params']['thumb_height'] : ""),
                'maxlength' => 4,
              ),  
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']. $album_id .'/'
              )
            )
          ),
          array(
            'title' => 'Дополнительные параметры',
            'fields' => array(
              array(
                'view' => 'fields/textarea',
                'title' => 'Название / Альт-текст:',
                'name' => 'name',
                'languages' => $languages
              ),
              array(
                'view' => 'fields/textarea',
                'title' => 'Описание:',
                'name' => 'description',
                'languages' => $languages
              ),
              array(
                'view'         => 'fields/select',
                'title'        => 'Связанные страницы:',
                'description'  => 'Укажите страницу, на которую будет отправлять ссылка изображения',
                'name'         => 'page_id',
                'options'      => $pages,
                'empty'        => true,
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']. $album_id .'/'
              )
            )
          ),
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] . $album_id .'/'
    ), TRUE);
  }
  
  function _add_image_process($album_id) {
    $languages = $this->languages_model->get_languages(1, 0);
    
    $params = array(
      'type'        => 'image',
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'gallery_id'  => $album_id,
      'page_id'     => ($this->input->post('page_id') ? $this->input->post('page_id') : null),
      'main'        => ($this->input->post('main') ? 1 : 0),
      'order'       => $this->gallery_model->get_images_order()
    );
    
    if ($params['main'] == 1) {
      $this->gallery_model->edit_gallery_images($album_id,array('main' => 0));
    }
    
    if (!$params['title']) {
      send_answer(array('errors' => array('Не указано внутреннее имя')));
    }
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
        'description' => htmlspecialchars(trim($this->input->post('description_'. $language['name']))),
      );
    }
    $multiparams['thumb_width'] = (int)$this->input->post('thumb_width');
    $multiparams['thumb_height'] = (int)$this->input->post('thumb_height');
    
    
    if ($_FILES['image']['name']) {
      $params['image'] = upload_file($_FILES['image']);      
      $thumbs = array();
      if (!$params['image']) {
        send_answer(array('errors' => array('Ошибка при загрузке изображения')));
      }
      if (!$this->gallery_model->validate_file($params['image'], array('jpeg', 'jpg', 'gif', 'png'))) {
        @unlink($_SERVER['DOCUMENT_ROOT'] . $params['image']);
        send_answer(array('errors' => array('Неподдерживаемый формат изображения')));
      }
      if (!resize_image($params['image'], 180, 135)) {
        send_answer(array('errors' => array('Не удалось создать стандартную миниатюру')));
      };      
      if ($multiparams['thumb_width'] || $multiparams['thumb_height']) {
        if (!resize_image($params['image'], $multiparams['thumb_width'], $multiparams['thumb_height'])) {
          send_answer(array('errors' => array('Не удалось создать миниатюру')));
        };
      };
    } else {
      send_answer(array('errors' => array('Не загружено изображение')));
    }
    
    $id = $this->gallery_model->create_gallery_image($params);   
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать изображение')));
    };
    
    $thumb_params = array(
      'image_id' => $id, 
      'thumb'  => $this->gallery_model->thumb($params['image'],180,135), 
      'width'  => 180, 
      'height' => 135
    );
    if (!$this->gallery_model->create_thumb_gallery_image($thumb_params)){
      $this->gallery_model->delete_image(array('id' => $id));
      send_answer(array('errors' => array('Не удалось сохранить стандартную миниатюру')));
    }

    if ($multiparams['thumb_width'] || $multiparams['thumb_height']) {
      $thumb_params = array(
        'image_id' => $id, 
        'thumb'  => $this->gallery_model->thumb($params['image'],$multiparams['thumb_width'],$multiparams['thumb_height']), 
        'width'  => $multiparams['thumb_width'], 
        'height' => $multiparams['thumb_height']
      );
      if (!$this->gallery_model->create_thumb_gallery_image($thumb_params)){
        $this->gallery_model->delete_image(array('id' => $id));
        send_answer(array('errors' => array('Не удалось сохранить миниатюру')));
      }
    };
    
    if (!$this->main_model->set_params('gallery_image', $id, $multiparams)) {
      $this->gallery_model->delete_image(array('id' => $id));
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    send_answer();
  }

  /*
  * Возвращает шаблон Замена файла
  */
  function replace_file($image_id){
    $image = $this->gallery_model->get_gallery_image(array('gallery_images.id' => (int)$image_id));
    if(!$image){
      send_answer(array('errors'=>array('Не найден файл')));
    }
    
      // форма для замены файла с помощью модального окна
    echo $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_replace_file_process/'.$image_id.'/',
        'blocks' => array(
          array(
            'title'  => 'Замена файла',
            'fields' => array(
              array(
                'view'  => 'fields/file',
                'title' => 'Файл:',
                'name'  => 'file',
                'req'   => true
              ),
              array(
                'view'     => 'fields/submit',
                'title'    => 'Загрузить',
                'type'     => 'ajax',
                'reaction' => 'reload'
              )
            )
          )
        )
      ));
  }

  /*
  * Замена файла
  */
  function _replace_file_process($image_id){
    $image = $this->gallery_model->get_gallery_image(array('gallery_images.id' => (int)$image_id));
    if(!$image){
      send_answer(array('errors'=>array('Не найден файл')));
    }
    if ($_FILES['file']['name']) {
      $this->gallery_model->delete_image_files(array('id' => $image_id));
      
      $file = upload_file($_FILES['file'],false);
      if (!$file) {
        send_answer(array('errors' => array('Ошибка при загрузке файла')));
      }

      $params = array(
        'image' => $file
      );

      if(!$this->gallery_model->edit_gallery_image($image_id,$params)){
        @unlink($_SERVER['DOCUMENT_ROOT'] . $file);
        send_answer(array('errors' => array('Ошибка при сохранении в базу')));
      }
      
    } else {
      send_answer(array('errors'=>array('Загрузите файл')));
    }
    send_answer();
  }
  
  /**
  * Редактирование изображения
  **/
  function edit_image($id) {
    $languages = $this->languages_model->get_languages(1, 0);
    $item =  $this->gallery_model->get_gallery_image(array('gallery_images.id' => (int)$id));
    $pages = $this->db->get('pr_pages')->result_array();
    if ($pages) {
      foreach ($pages as &$page) {
        $project = $this->db->get('pr_projects',array('id', $page['project_id']))->row_array();
        $page['title'] = $project['title'].': '.$page['title'];
      }
      unset($page);
    }
    
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование изображения',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_image_process/'.$id.'/',
        'blocks' => array(
          array(
            'title' => 'Основные параметры',
            'fields' => array(
              array(
                'view'         => 'fields/text',
                'title'        => 'Внутреннее имя:',
                'description' => 'Используется только внутри панели администрирования',
                'name'         => 'title',
                'value'       => $item['title'],
                'id'           => 'item-title',
                'maxlength'   => 256,
                'req'         => true
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Редактировать',
                'type'     => 'ajax',
                'reaction' => 1
              )
            )
          ),
          array(
            'title'  => 'Параметры изображения',
            'fields' => array(
              array(
                'view'    => 'fields/checkbox',
                'title'   => 'Обложка альбома:',
                'name'    => 'main',
                'checked' => $item['main'],
              ),
              array(
                'view'  => 'fields/file',
                'title' => 'Изображение (jpg, gif, png):',
                'name'  => 'image',
                'value' => $item['image'],
                'req'   => true
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Ширина миниатюры:',
                'name'      => 'thumb_width',
                'value'     => (isset($item['params']['thumb_width']) ? $item['params']['thumb_width'] : ""),
                'maxlength' => 4,
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Высота миниатюры:',
                'name'      => 'thumb_height',
                'value'     => $item['params']['thumb_height'],
                'maxlength' => 4,
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Редактировать',
                'type'     => 'ajax',
                'reaction' => 1
              )
            )
          ),
          array(
            'title' => 'Дополнительные параметры',
            'fields' => array(
              array(
                'view'      => 'fields/textarea',
                'title'     => 'Название / Альт-текст:',
                'name'      => 'name',
                'value'     => $item['params'],
                'languages' => $languages
              ),
              array(
                'view'      => 'fields/textarea',
                'title'     => 'Описание:',
                'name'      => 'description',
                'value'     => $item['params'],
                'languages' => $languages
              ),
              array(
                'view'         => 'fields/select',
                'title'        => 'Связанные страницы:',
                'description'  => 'Укажите страницу, на которую будет отправлять ссылка изображения',
                'name'         => 'page_id',
                'options'      => $pages,
                'value'        => $item['page_id'],
                'empty'        => true
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Редактировать',
                'type'     => 'ajax',
                'reaction' => 1
              )
            )
          ),
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] . $item['gallery_id'] .'/'
    ), TRUE);
  }
  
  function _edit_image_process($id) {
    $item =  $this->gallery_model->get_gallery_image(array('gallery_images.id' => $id));
    $languages = $this->languages_model->get_languages(1, 0);
    
    $params = array(
      'type'        => 'image',
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'page_id'     => ($this->input->post('page_id') ? $this->input->post('page_id') : null),
      'main'        => ($this->input->post('main') ? 1 : 0)
    ); 
    
    if ($params['main'] == 1) {
      $this->gallery_model->edit_gallery_images($item['gallery_id'],array('main' => 0));
    }
    
    if (!$params['title']) {
      send_answer(array('errors' => array('Не указано внутреннее имя')));
    }
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
        'description' => htmlspecialchars(trim($this->input->post('description_'. $language['name']))),
      );
    }
    $multiparams['thumb_width'] = (int)$this->input->post('thumb_width');
    $multiparams['thumb_height'] = (int)$this->input->post('thumb_height');
    
    
    if ($_FILES['image']['name']) {      
      $this->gallery_model->delete_image_files(array('id' => $id));
      
      $params['image'] = upload_file($_FILES['image']);      
      $thumbs = array();
      if (!$params['image']) {
        send_answer(array('errors' => array('Ошибка при загрузке изображения')));
      }
      if (!$this->gallery_model->validate_file($params['image'], array('jpeg', 'jpg', 'gif', 'png'))) {
        @unlink($_SERVER['DOCUMENT_ROOT'] . $params['image']);
        send_answer(array('errors' => array('Неподдерживаемый формат изображения')));
      }
      if (!resize_image($params['image'], 180, 135)) {
        send_answer(array('errors' => array('Не удалось создать стандартную миниатюру')));
      };       
      $thumb_params = array(
        'image_id' => $id,
        'thumb'  => $this->gallery_model->thumb($params['image'],180,135),
        'width'  => 180,
        'height' => 135
      );
      if (!$this->gallery_model->create_thumb_gallery_image($thumb_params)){
        send_answer(array('errors' => array('Не удалось сохранить стандартную миниатюру')));
      }
      
      if ($multiparams['thumb_width'] || $multiparams['thumb_height']) {
        if (!resize_image($params['image'], $multiparams['thumb_width'], $multiparams['thumb_height'])) {
          send_answer(array('errors' => array('Не удалось создать миниатюру')));
        };
        $thumb_params = array(
          'image_id' => $id, 
          'thumb'  => $this->gallery_model->thumb($params['image'],$multiparams['thumb_width'],$multiparams['thumb_height']), 
          'width'  => $multiparams['thumb_width'], 
          'height' => $multiparams['thumb_height']
        );
        if (!$this->gallery_model->create_thumb_gallery_image($thumb_params)){
          send_answer(array('errors' => array('Не удалось сохранить миниатюру')));
        }
      };    
    } elseif (($multiparams['thumb_width'] != $item['params']['thumb_width']) || ($multiparams['thumb_height'] != $item['params']['thumb_height'])) {
      send_answer(array('errors' => array('Чтобы изменить парамеры миниатюры необходимо выбрать новое изображение')));
    } elseif ($this->input->post('image_delete')) {
      send_answer(array('errors' => array('Чтобы заменить изображение необходимо выбрать новое изображение')));
    }
    
    if (!$this->gallery_model->edit_gallery_image($id,$params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    if (!$this->main_model->set_params('gallery_image', $id, $multiparams)) {
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    send_answer();
  }
  
  /**
  * Добавление видео
  **/
  function add_video($album_id) {
    $languages = $this->languages_model->get_languages(1, 0);
    $album =  $this->gallery_model->get_gallery_one(array('id' => $album_id));
    
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление видео в альбом "'.$album['title'].'"',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_add_video_process/'.$album_id.'/',
        'blocks' => array(
          array(
            'title' => 'Основные параметры',
            'fields' => array(
              array(
                'view'         => 'fields/text',
                'title'        => 'Внутреннее имя:',
                'name'         => 'title',
                'id'           => 'item-title',
                'maxlength'   => 256,
                'description' => 'Используется только внутри панели администрирования',
                'req'         => true
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']. $album_id .'/'
              )
            )
          ),
          array(
            'title'  => 'Параметры видео',
            'fields' => array(
              array(
                'view'  => 'fields/file',
                'title' => 'Видео-файл (mp4, mov, flv):',
                'name'  => 'image',
                'req'   => true
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Ширина плеера:',
                'name'      => 'width_player',
                'maxlength' => 4,
                'value'     => (isset($album['params']['width_player']) ? $album['params']['width_player'] : ""),
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Высота плеера:',
                'name'      => 'height_player',
                'value'     => (isset($album['params']['height_player']) ? $album['params']['height_player'] : ""),
                'maxlength' => 4,
              ),  
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']. $album_id .'/'
              )
            )
          ),
          array(
            'title' => 'Дополнительные параметры',
            'fields' => array(
              array(
                'view'      => 'fields/textarea',
                'title'     => 'Название / Альт-текст:',
                'name'      => 'name',
                'languages' => $languages
              ),
              array(
                'view'      => 'fields/textarea',
                'title'     => 'Описание:',
                'name'      => 'description',
                'languages' => $languages
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']. $album_id .'/'
              )
            )
          ),
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] . $album_id .'/'
    ), TRUE);
  }
  
  function _add_video_process($album_id) {
    $languages = $this->languages_model->get_languages(1, 0);
    
    $params = array(
      'type'        => 'video',
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'gallery_id'  => $album_id,
      'order'       => $this->gallery_model->get_images_order()
    );
    
    if (!$params['title']) {
      send_answer(array('errors' => array('Не указано внутреннее имя')));
    }
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
        'description' => htmlspecialchars(trim($this->input->post('description_'. $language['name']))),
      );
    }
    $multiparams['width_player'] = (int)$this->input->post('width_player');
    $multiparams['height_player'] = (int)$this->input->post('height_player');
      
    if ($_FILES['image']['name']) {
      $params['image'] = upload_file($_FILES['image']);      
      $thumbs = array();
      if (!$params['image']) {
        send_answer(array('errors' => array('Ошибка при загрузке файла')));
      }
      if (!$this->gallery_model->validate_file($params['image'], array('mp4', 'mov', 'flv'))) {
        @unlink($_SERVER['DOCUMENT_ROOT'] . $params['image']);
        send_answer(array('errors' => array('Неподдерживаемый формат файла')));
      }
    } else {
      send_answer(array('errors' => array('Не загружен файл')));
    }
    
    $id = $this->gallery_model->create_gallery_image($params);   
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать')));
    };
    
    if (!$this->main_model->set_params('gallery_image', $id, $multiparams)) {
      $this->gallery_model->delete_image(array('id' => $id));
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    send_answer();
  }
  
  /**
  * Редактирование видео
  **/
  function edit_video($id) {
    $languages = $this->languages_model->get_languages(1, 0);
    $item =  $this->gallery_model->get_gallery_image(array('gallery_images.id' => $id));
    
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование видео',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_video_process/'.$id.'/',
        'blocks' => array(
          array(
            'title' => 'Основные параметры',
            'fields' => array(
              array(
                'view'         => 'fields/text',
                'title'        => 'Внутреннее имя:',
                'description' => 'Используется только внутри панели администрирования',
                'name'         => 'title',
                'value'       => $item['title'],
                'id'           => 'item-title',
                'maxlength'   => 256,
                'req'         => true
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Редактировать',
                'type'     => 'ajax',
                'reaction' => 1
              )
            )
          ),
          array(
            'title'  => 'Параметры видео',
            'fields' => array(
              array(
                'view'  => 'fields/file',
                'title' => 'Видео-файл (mp4, mov, flv):',
                'name'  => 'image',
                'value' => $item['image'],
                'req'   => true
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Ширина плеера:',
                'name'      => 'width_player',
                'value'     => $item['params']['width_player'],
                'maxlength' => 4,
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Высота плеера:',
                'name'      => 'height_player',
                'value'     => $item['params']['height_player'],
                'maxlength' => 4,
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Редактировать',
                'type'     => 'ajax',
                'reaction' => 1
              )
            )
          ),
          array(
            'title' => 'Дополнительные параметры',
            'fields' => array(
              array(
                'view'      => 'fields/textarea',
                'title'     => 'Название / Альт-текст:',
                'name'      => 'name',
                'value'     => $item['params'],
                'languages' => $languages
              ),
              array(
                'view'      => 'fields/textarea',
                'title'     => 'Описание:',
                'name'      => 'description',
                'value'     => $item['params'],
                'languages' => $languages
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Редактировать',
                'type'     => 'ajax',
                'reaction' => 1
              )
            )
          ),
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] . $item['gallery_id'] .'/'
    ), TRUE);
  }
  
  function _edit_video_process($id) {
    $languages = $this->languages_model->get_languages(1, 0);
    
    $params = array(
      'type'  => 'video',
      'title' => htmlspecialchars(trim($this->input->post('title')))
    ); 
    
    if (!$params['title']) {
      send_answer(array('errors' => array('Не указано внутреннее имя')));
    }
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
        'description' => htmlspecialchars(trim($this->input->post('description_'. $language['name']))),
      );
    }
    $multiparams['width_player'] = (int)$this->input->post('width_player');
    $multiparams['height_player'] = (int)$this->input->post('height_player');
    
    
    if ($_FILES['image']['name']) {
      $this->gallery_model->delete_image_files(array('id' => $id));
      
      $params['image'] = upload_file($_FILES['image']);
      if (!$params['image']) {
        send_answer(array('errors' => array('Ошибка при загрузке файла')));
      }
      if (!$this->gallery_model->validate_file($params['image'], array('mp4', 'mov', 'flv'))) {
        @unlink($_SERVER['DOCUMENT_ROOT'] . $params['image']);
        send_answer(array('errors' => array('Неподдерживаемый формат файла')));
      }   
    } elseif ($this->input->post('image_delete')) {
      send_answer(array('errors' => array('Чтобы заменить файл необходимо выбрать новый файл')));
    }
    
    if (!$this->gallery_model->edit_gallery_image($id,$params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    if (!$this->main_model->set_params('gallery_image', $id, $multiparams)) {
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    send_answer();
  }
  /**
  * Добавление видео c youtoube
  **/
  function add_youtube($album_id) {
    $languages = $this->languages_model->get_languages(1, 0);
    $album =  $this->gallery_model->get_gallery_one(array('id' => $album_id));
    
    return $this->render_template('admin/inner', array(
      'title' => 'Добавление видео c youtoube в альбом "'.$album['title'].'"',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_add_youtube_process/'.$album_id.'/',
        'blocks' => array(
          array(
            'title' => 'Основные параметры',
            'fields' => array(
              array(
                'view'         => 'fields/text',
                'title'        => 'Внутреннее имя:',
                'name'         => 'title',
                'id'           => 'item-title',
                'maxlength'   => 256,
                'description' => 'Используется только внутри панели администрирования',
                'req'         => true
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']. $album_id .'/'
              )
            )
          ),
          array(
            'title'  => 'Параметры видео',
            'fields' => array(
              array(
                'view'        => 'fields/text',
                'title'       => 'Ссылка на страницу с видео:',
                'description' => 'вид ссылки: watch?v=sS1r8fs7IfE',
                'name'        => 'image',
                'req'         => true
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Ширина плеера:',
                'name'      => 'width_player',
                'maxlength' => 4,
                'value'     => (isset($album['params']['width_player']) ? $album['params']['width_player'] : ""),
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Высота плеера:',
                'name'      => 'height_player',
                'value'     => (isset($album['params']['height_player']) ? $album['params']['height_player'] : ""),
                'maxlength' => 4,
              ),  
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']. $album_id .'/'
              )
            )
          ),
          array(
            'title' => 'Дополнительные параметры',
            'fields' => array(
              array(
                'view'      => 'fields/textarea',
                'title'     => 'Название / Альт-текст:',
                'name'      => 'name',
                'languages' => $languages
              ),
              array(
                'view'      => 'fields/textarea',
                'title'     => 'Описание:',
                'name'      => 'description',
                'languages' => $languages
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Создать',
                'type'     => 'ajax',
                'reaction' => $this->lang_prefix .'/admin'. $this->params['path']. $album_id .'/'
              )
            )
          ),
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] . $album_id .'/'
    ), TRUE);
  }
  
  function _add_youtube_process($album_id) {
    $languages = $this->languages_model->get_languages(1, 0);
    
    $params = array(
      'type'        => 'youtube',
      'title'       => htmlspecialchars(trim($this->input->post('title'))),
      'gallery_id'  => $album_id,
      'image'       => htmlspecialchars(trim($this->input->post('image'))),
      'order'       => $this->gallery_model->get_images_order()
    );
    
    if (!$params['title']) {
      send_answer(array('errors' => array('Не указано внутреннее имя')));
    }
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
        'description' => htmlspecialchars(trim($this->input->post('description_'. $language['name']))),
      );
    }
    $multiparams['width_player'] = (int)$this->input->post('width_player');
    $multiparams['height_player'] = (int)$this->input->post('height_player');
    
    if (!$params['image']) {
      send_answer(array('errors' => array('Не указана ссылка на видео')));
    }
    
    $id = $this->gallery_model->create_gallery_image($params);   
    if (!$id) {
      send_answer(array('errors' => array('Не удалось создать')));
    };
    
    if (!$this->main_model->set_params('gallery_image', $id, $multiparams)) {
      $this->gallery_model->delete_image(array('id' => $id));
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    send_answer();
  }
  
  /**
  * Редактирование видео с youtoube
  **/
  function edit_youtube($id) {
    $languages = $this->languages_model->get_languages(1, 0);
    $item =  $this->gallery_model->get_gallery_image(array('gallery_images.id' => $id));
    
    return $this->render_template('admin/inner', array(
      'title' => 'Редактирование видео c youtoube',
      'html' => $this->view->render_form(array(
        'action' => $this->lang_prefix .'/admin'. $this->params['path'] .'_edit_youtube_process/'.$id.'/',
        'blocks' => array(
          array(
            'title' => 'Основные параметры',
            'fields' => array(
              array(
                'view'         => 'fields/text',
                'title'        => 'Внутреннее имя:',
                'description' => 'Используется только внутри панели администрирования',
                'name'         => 'title',
                'value'       => $item['title'],
                'id'           => 'item-title',
                'maxlength'   => 256,
                'req'         => true
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Редактировать',
                'type'     => 'ajax',
                'reaction' => 1
              )
            )
          ),
          array(
            'title'  => 'Параметры видео',
            'fields' => array(
              array(
                'view'  => 'fields/text',
                'title' => 'Ссылка на страницу с видео:',
                'description' => 'вид ссылки: watch?v=sS1r8fs7IfE',
                'name'  => 'image',
                'value' => $item['image'],
                'req'   => true
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Ширина плеера:',
                'name'      => 'width_player',
                'value'     => $item['params']['width_player'],
                'maxlength' => 4,
              ),
              array(
                'view'      => 'fields/text',
                'title'     => 'Высота плеера:',
                'name'      => 'height_player',
                'value'     => $item['params']['height_player'],
                'maxlength' => 4,
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Редактировать',
                'type'     => 'ajax',
                'reaction' => 1
              )
            )
          ),
          array(
            'title' => 'Дополнительные параметры',
            'fields' => array(
              array(
                'view'      => 'fields/textarea',
                'title'     => 'Название / Альт-текст:',
                'name'      => 'name',
                'value'     => $item['params'],
                'languages' => $languages
              ),
              array(
                'view'      => 'fields/textarea',
                'title'     => 'Описание:',
                'name'      => 'description',
                'value'     => $item['params'],
                'languages' => $languages
              ),
              array(
                'view'     => 'fields/submit',
                'class'    => 'icon_small accept_i_s',
                'title'    => 'Редактировать',
                'type'     => 'ajax',
                'reaction' => 1
              )
            )
          ),
        )
      )),
      'back' => $this->lang_prefix .'/admin'. $this->params['path'] . $item['gallery_id'] .'/'
    ), TRUE);
  }
  
  function _edit_youtube_process($id) {
    $languages = $this->languages_model->get_languages(1, 0);
    
    $params = array(
      'type'  => 'youtube',
      'title' => htmlspecialchars(trim($this->input->post('title'))),
      'image' => htmlspecialchars(trim($this->input->post('image')))
    ); 
    
    if (!$params['title']) {
      send_answer(array('errors' => array('Не указано внутреннее имя')));
    }
    
    $multiparams = array();
    foreach ($languages as $language) {
      $multiparams[$language['name']] = array(
        'name' => htmlspecialchars(trim($this->input->post('name_'. $language['name']))),
        'description' => htmlspecialchars(trim($this->input->post('description_'. $language['name']))),
      );
    }
    $multiparams['width_player'] = (int)$this->input->post('width_player');
    $multiparams['height_player'] = (int)$this->input->post('height_player');
    
    if (!$params['image']) {
      send_answer(array('errors' => array('Не указана ссылка на видео')));
    }
    
    if (!$this->gallery_model->edit_gallery_image($id,$params)) {
      send_answer(array('errors' => array('Не удалось сохранить изменения')));
    }
    
    if (!$this->main_model->set_params('gallery_image', $id, $multiparams)) {
      send_answer(array('errors' => array('Не удалось сохранить параметры')));
    }
    
    send_answer();
  }  
}